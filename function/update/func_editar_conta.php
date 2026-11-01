<?php
    // Edição dos PRÓPRIOS dados pelo adotante.
    // O usuário alterado é sempre o autenticado: id, perfil e ativo nunca são lidos do POST.
    require_once __DIR__ . '/../../lib/auth.php';
    $usuario_logado = exigir_perfis([PERFIL_ADOTANTE], 'login.php');

    require_once __DIR__ . '/../../lib/conn.php';
    require_once __DIR__ . '/../../lib/escape.php';
    require_once __DIR__ . '/../../lib/usuario.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ../../editar_conta.php');
        exit;
    }

    $id_usuario = (int) $usuario_logado['id_usuario'];
    [$dados, $erros] = ler_formulario_adotante($_POST, null);
    $campos_senha_malformados = tem_campo_nao_textual($_POST, ['senha_atual', 'nova_senha', 'confirmacao_nova_senha']);

    // Troca de senha opcional: só acontece se a nova senha for informada
    $nova_senha = valor_texto($_POST, 'nova_senha');
    $trocar_senha = $nova_senha !== '';
    if ($trocar_senha) {
        if (mb_strlen($nova_senha) < SENHA_TAMANHO_MINIMO) {
            $erros[] = 'A nova senha deve ter pelo menos ' . SENHA_TAMANHO_MINIMO . ' caracteres.';
        }
        if ($nova_senha !== valor_texto($_POST, 'confirmacao_nova_senha')) {
            $erros[] = 'A confirmação da nova senha não confere.';
        }
        $stmt = $conn->prepare('SELECT senha FROM usuario WHERE id_usuario = :id_usuario');
        $stmt->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        $senha_atual = valor_texto($_POST, 'senha_atual');
        if ($senha_atual === '' || !senha_confere($senha_atual, (string) $stmt->fetchColumn())) {
            $erros[] = 'A senha atual está incorreta.';
        }
    }

    if ($campos_senha_malformados) {
        $erros = [MSG_DADOS_INVALIDOS];
    }

    if (!$erros && email_em_uso($conn, $dados['email'], $id_usuario)) {
        $erros[] = MSG_EMAIL_CONTA_DUPLICADA;
    }

    // Tudo validado antes: um único UPDATE, sem alteração parcial
    if (!$erros) {
        try {
            $sql = "UPDATE usuario SET nome = :nome, email = :email, telefone = :telefone"
                 . ($trocar_senha ? ', senha = :senha' : '')
                 . " WHERE id_usuario = :id_usuario AND perfil = 'ADOTANTE' AND ativo = 1";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':nome', $dados['nome']);
            $stmt->bindValue(':email', $dados['email']);
            $stmt->bindValue(':telefone', $dados['telefone'], $dados['telefone'] === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            if ($trocar_senha) {
                $stmt->bindValue(':senha', password_hash($nova_senha, PASSWORD_DEFAULT));
            }
            $stmt->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $stmt->execute();

            $_SESSION['nome'] = $dados['nome'];
            $_SESSION['email'] = $dados['email'];
            if ($trocar_senha) {
                session_regenerate_id(true);
            }
        } catch (PDOException $e) {
            if (eh_email_duplicado($e)) {
                $erros[] = MSG_EMAIL_CONTA_DUPLICADA;
            } else {
                error_log('Falha ao editar conta: ' . $e->getMessage());
                $erros[] = 'Não foi possível salvar as alterações. Nenhuma alteração foi feita.';
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if (!$erros) { ?>
    <meta http-equiv="refresh" content="2; url=../../minha_conta.php">
    <?php } ?>
    <title>Editar meus dados - Patas &amp; Lares</title>
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23C86B4A'%3E%3Cellipse cx='12' cy='16' rx='5' ry='4.2'/%3E%3Ccircle cx='5.5' cy='10' r='2.2'/%3E%3Ccircle cx='9.5' cy='5.8' r='2.2'/%3E%3Ccircle cx='14.5' cy='5.8' r='2.2'/%3E%3Ccircle cx='18.5' cy='10' r='2.2'/%3E%3C/svg%3E">
    <link rel="stylesheet" href="../../css/minha-conta.css">
</head>
<body class="minha-conta">
    <!-- Resposta simples: só a marca, sem o menu completo -->
    <header class="topo">
        <div class="container topo-barra">
            <a href="../../index.php" class="marca">
                <svg class="marca-icone" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <ellipse cx="12" cy="16" rx="5" ry="4.2"/>
                    <circle cx="5.5" cy="10" r="2.2"/>
                    <circle cx="9.5" cy="5.8" r="2.2"/>
                    <circle cx="14.5" cy="5.8" r="2.2"/>
                    <circle cx="18.5" cy="10" r="2.2"/>
                </svg>
                Patas &amp; Lares
            </a>
        </div>
    </header>

    <main id="conteudo" class="container">
        <section class="cartao cartao-resposta" aria-labelledby="titulo-resposta">
            <?php if (!$erros) { ?>
            <h1 id="titulo-resposta">Dados atualizados com sucesso!</h1>
            <p class="cartao-intro">Você será direcionado para Minha conta.</p>
            <a class="botao botao-primario" href="../../minha_conta.php">Ir para Minha conta</a>
            <?php } else { ?>
            <h1 id="titulo-resposta">Não foi possível salvar as alterações</h1>
            <div class="alerta-erro" role="alert">
                <span class="alerta-icone" aria-hidden="true">!</span>
                <div>
                    <?php foreach ($erros as $erro) { ?>
                    <p><?= e($erro) ?></p>
                    <?php } ?>
                </div>
            </div>
            <a class="botao botao-secundario" href="javascript:history.back()">Voltar ao formulário</a>
            <?php } ?>
        </section>
    </main>

    <footer class="rodape rodape-simples">
        <div class="container rodape-base">
            <p>&copy; 2026 Patas &amp; Lares · Projeto acadêmico</p>
            <p>Desenvolvido por Jonathas e Gloria</p>
        </div>
    </footer>
</body>
</html>
