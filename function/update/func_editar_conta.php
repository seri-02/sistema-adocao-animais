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

    // Troca de senha opcional: só acontece se a nova senha for informada
    $nova_senha = (string) ($_POST['nova_senha'] ?? '');
    $trocar_senha = $nova_senha !== '';
    if ($trocar_senha) {
        if (mb_strlen($nova_senha) < SENHA_TAMANHO_MINIMO) {
            $erros[] = 'A nova senha deve ter pelo menos ' . SENHA_TAMANHO_MINIMO . ' caracteres.';
        }
        if ($nova_senha !== (string) ($_POST['confirmacao_nova_senha'] ?? '')) {
            $erros[] = 'A confirmação da nova senha não confere.';
        }
        $stmt = $conn->prepare('SELECT senha FROM usuario WHERE id_usuario = :id_usuario');
        $stmt->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        $senha_atual = (string) ($_POST['senha_atual'] ?? '');
        if ($senha_atual === '' || !senha_confere($senha_atual, (string) $stmt->fetchColumn())) {
            $erros[] = 'A senha atual está incorreta.';
        }
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
    <link rel="stylesheet" href="../../css/style.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" integrity="sha512-1sCRPdkRXhBV2PBLUdRb4tMg1w2YPf37qatUFeS7zlBy7jJI8Lf4VHwWfZZfpXtYSLy85pkm9GaYVYMfw5BC1A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="icon" href="">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if (!$erros) { ?>
    <meta http-equiv="refresh" content="2; url=../../minha_conta.php">
    <?php } ?>
    <title>Abrigo dos Gatos</title>
</head>
<body id="adm">
    <header>
        <span>
            <a href="../../index.php">Abrigo dos gatos</a>
        </span>
    </header>

    <main>
        <?php if (!$erros) { ?>
        <div class="alert success">
            <i class="far fa-check-circle"></i>
            <p class="lead">Dados atualizados com sucesso!</p>
        </div>
        <?php } else { ?>
        <div class="alert danger">
            <i class="fas fa-times"></i>
            <?php foreach ($erros as $erro) { ?>
                <p class="lead"><?= e($erro) ?></p>
            <?php } ?>
            <p><a href="javascript:history.back()">Voltar ao formulário</a></p>
        </div>
        <?php } ?>
    </main>

    <footer>
        <div id="baixo">
			<div class="centro">
				<span class="credito">&copy; Abrigo dos gatos |</span>
				<span>Design by <a href="#">ANA LAURA </a>2022</span>
			</div>
		</div>
    </footer>
</body>
</html>
