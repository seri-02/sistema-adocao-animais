<?php
    // Cadastro PÚBLICO de adotante. Perfil e situação são definidos aqui, nunca pelo navegador:
    // nenhum campo além de nome/email/telefone/senha/confirmação é lido do POST.
    require_once __DIR__ . '/../lib/auth.php';
    require_once __DIR__ . '/../lib/usuario.php';
    redirecionar_se_autenticado();

    require_once __DIR__ . '/../lib/conn.php';
    require_once __DIR__ . '/../lib/escape.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ../cadastro.php');
        exit;
    }

    [$dados, $erros] = ler_formulario_adotante($_POST, 'senha');

    if (!$erros && email_em_uso($conn, $dados['email'])) {
        $erros[] = MSG_EMAIL_CONTA_DUPLICADA;
    }

    if (!$erros) {
        try {
            $stmt = $conn->prepare(
                "INSERT INTO usuario (nome, email, senha, telefone, perfil, ativo)
                 VALUES (:nome, :email, :senha, :telefone, 'ADOTANTE', 1)"
            );
            $stmt->bindValue(':nome', $dados['nome']);
            $stmt->bindValue(':email', $dados['email']);
            $stmt->bindValue(':senha', password_hash($dados['senha'], PASSWORD_DEFAULT));
            $stmt->bindValue(':telefone', $dados['telefone'], $dados['telefone'] === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->execute();
        } catch (PDOException $e) {
            // Cadastro simultâneo com o mesmo e-mail: o UNIQUE recusa e a mensagem é a mesma
            if (eh_email_duplicado($e)) {
                $erros[] = MSG_EMAIL_CONTA_DUPLICADA;
            } else {
                error_log('Falha ao cadastrar adotante: ' . $e->getMessage());
                $erros[] = 'Não foi possível concluir o cadastro. Tente novamente.';
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
    <meta http-equiv="refresh" content="3; url=../login.php">
    <?php } ?>
    <title>Criar conta - Patas &amp; Lares</title>
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23C86B4A'%3E%3Cellipse cx='12' cy='16' rx='5' ry='4.2'/%3E%3Ccircle cx='5.5' cy='10' r='2.2'/%3E%3Ccircle cx='9.5' cy='5.8' r='2.2'/%3E%3Ccircle cx='14.5' cy='5.8' r='2.2'/%3E%3Ccircle cx='18.5' cy='10' r='2.2'/%3E%3C/svg%3E">
    <link rel="stylesheet" href="../css/auth.css">
</head>
<body class="autenticacao">
    <!-- Resposta simples: só a marca, sem o menu completo (o endpoint não carrega estado de sessão para o menu) -->
    <header class="topo">
        <div class="container topo-barra">
            <a href="../index.php" class="marca">
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
        <section class="auth-card" aria-labelledby="titulo-auth">
            <?php if (!$erros) { ?>
            <h1 id="titulo-auth">Conta criada com sucesso!</h1>
            <p class="auth-intro">Você será direcionado para a página de login.</p>
            <a class="botao botao-primario" href="../login.php">Ir para o login</a>
            <?php } else { ?>
            <h1 id="titulo-auth">Não foi possível criar sua conta</h1>
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
