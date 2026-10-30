<?php
    require_once __DIR__ . '/lib/auth.php';
    require_once __DIR__ . '/lib/escape.php';
    // Somente ADOTANTE; perfil e situação são conferidos no banco a cada acesso
    $usuario_logado = exigir_perfis([PERFIL_ADOTANTE], 'login.php');

    require_once __DIR__ . '/lib/conn.php';
    $stmt = $conn->prepare('SELECT nome, email, telefone FROM usuario WHERE id_usuario = :id_usuario');
    $stmt->bindValue(':id_usuario', (int) $usuario_logado['id_usuario'], PDO::PARAM_INT);
    $stmt->execute();
    $conta = $stmt->fetch(PDO::FETCH_OBJ);

    $usuario_menu = $usuario_logado;
    $estilo_menu = 'botoes';
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
    <title>Abrigo dos Gatos - Minha conta</title>
</head>
<body id="adm">
    <header class="header-adm">
        <span>
            <a href="index.php">Abrigo dos gatos</a>
        </span>
        <?php require __DIR__ . '/parciais/menu_conta.php'; ?>
    </header>

    <main>
        <span>Minha conta</span>
        <p>Conta de adotante</p>
        <ul class="dados-conta">
            <li><strong>Nome:</strong> <?= e($conta->nome) ?></li>
            <li><strong>Email:</strong> <?= e($conta->email) ?></li>
            <li><strong>Telefone:</strong> <?= e($conta->telefone ?? 'Não informado') ?></li>
        </ul>
        <a href="editar_conta.php"><button>Editar meus dados</button></a>
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
