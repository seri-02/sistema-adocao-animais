<?php
    session_start();
    if(!isset($_SESSION['id_user'])) {
        header('Location: ../index.php');
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

    <script src="../js/jquery.js"></script>

    <link rel="icon" href="">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abrigo dos Gatos</title>
</head>
<body id="adm">
    <header class="header-adm">
        <span>
            <a href="../index.php">Abrigo dos gatos</a> 
        </span>
        <a href="../function/sair.php"><button class="sair">Sair <i class="fa-solid fa-right-to-bracket"></i></button></a>
    </header>
    
    <h3 id="bem-vindo">Bem vindo!</h3>
    
    <main id="painel">
        <h4>Listagens</h4>
        <span>
            <a href="tabelas/tabela_adocoes.php"><button class="btn-opcao">Adoções</button></a> 
            <a href="tabelas/tabela_gatos.php"><button class="btn-opcao">Gatos</button></a> 
            <a href="tabelas/tabela_usuario.php"><button id="btn-usuario">Usuário</button></a>
            <a href="tabelas/tabela_voluntario.php"><button id="btn-voluntario">Voluntário</button></a>
        </span>
    </main>

    <div id="cadastros">
        <h4>Cadastros</h4>
        <div class="dropdown">
            <button class="dropbtn">Cadastre <i class="fa-solid fa-arrow-down"></i></button>
            <div class="dropdown-content">
                <a href="gato.php">Gato</a>
                <a href="usuario.php">Usuário</a>
            </div>
        </div>
    </div>


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