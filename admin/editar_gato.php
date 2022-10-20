<?php
    require_once '../lib/conn.php';
    session_start();
    if(!isset($_SESSION['id_user'])) {
        header('Location: ../index.php');
    }

    if(isset($_GET['id'])){
        $id = $_SESSION['id_gato'] = (int)$_GET['id'];
    } 
    else {
        $id = $_SESSION['id_gato'];
    }
    $sql_gatos = "SELECT * FROM gato WHERE id_gato = :id";
    $stmt = $conn->prepare($sql_gatos);
    $stmt->bindParam(":id",$id);
    $stmt->execute();
    $gato = $stmt->fetch(PDO::FETCH_OBJ);
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
    <title>Abrigo dos Gatos</title>
</head>
<body id="adm">
    <header class="header-adm">
        <span>
            <a href="../index.php">Abrigo dos gatos</a> 
        </span>
        <a href="tabela_gatos.php"><button class="sair"><i class="fa-solid fa-chevron-left"></i> Voltar</button></a>
    </header>
    
    <main class="form-cadastros">
        <span>Editar gato</span>
        <form action="../function/func_editar_gato.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $gato->id_gato ?>">
            <div class="campo">
                <input type="text" name="name" id="name" required value="<?= $gato->nome ?>">
                <span>Nome do gato</span>
            </div>
            <div class="campo">
                <textarea name="descricao" id="descricao" cols="30" rows="10" maxlength="100" required value="<?= $gato->descricao ?>"></textarea>
                <span>Descrição</span>
            </div>
            <div class="campo">
            <input type="file" name="img" id="img" class="form-control" required value="<?= $gato->img ?>">
            </div>
            <button type="submit">
                Cadastrar
            </button>
        </form>
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