<?php
    require_once __DIR__ . '/lib/auth.php';
    // Sessão válida não volta ao fluxo de login
    redirecionar_se_autenticado();

    $usuario_menu = null;
    $estilo_menu = 'botoes';
    $erro = isset($_GET['erro']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <link rel="stylesheet" href="css/style.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" integrity="sha512-1sCRPdkRXhBV2PBLUdRb4tMg1w2YPf37qatUFeS7zlBy7jJI8Lf4VHwWfZZfpXtYSLy85pkm9GaYVYMfw5BC1A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="icon" href="">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abrigo dos Gatos - Entrar</title>
</head>
<body id="adm">
    <header class="header-adm">
        <span>
            <a href="index.php">Abrigo dos gatos</a>
        </span>
        <?php require __DIR__ . '/parciais/menu_conta.php'; ?>
    </header>

    <main>
        <span>Entrar</span>
        <?php if ($erro) { ?>
        <div class="alert danger">
            <i class="fas fa-times"></i>
            <p class="lead">Email e/ou senha incorretos</p>
        </div>
        <?php } ?>
        <form action="function/logar.php" method="post">
            <input type="hidden" name="origem" value="publico">
            <div class="campo">
                <input type="email" name="email" id="email" required>
                <span>Email</span>
            </div>
            <div class="campo">
                <input type="password" name="senha" id="senha" required>
                <span>Senha</span>
            </div>
            <button type="submit">
                Entrar
            </button>
        </form>
        <p>Ainda não tem conta? <a href="cadastro.php">Cadastre-se</a></p>
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
