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
    <link rel="stylesheet" href="../css/style.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" integrity="sha512-1sCRPdkRXhBV2PBLUdRb4tMg1w2YPf37qatUFeS7zlBy7jJI8Lf4VHwWfZZfpXtYSLy85pkm9GaYVYMfw5BC1A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="icon" href="">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if (!$erros) { ?>
    <meta http-equiv="refresh" content="3; url=../login.php">
    <?php } ?>
    <title>Abrigo dos Gatos</title>
</head>
<body id="adm">
    <header>
        <span>
            <a href="../index.php">Abrigo dos gatos</a>
        </span>
    </header>

    <main>
        <?php if (!$erros) { ?>
        <div class="alert success">
            <i class="far fa-check-circle"></i>
            <p class="lead">Conta criada com sucesso!</p>
            <p><a href="../login.php">Entrar</a></p>
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
