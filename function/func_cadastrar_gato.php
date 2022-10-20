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

    <link rel="icon" href="">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="2; url=../admin/gato.php">
    <title>Abrigo dos Gatos</title>
</head>
<body id="adm">
    <header>
        <span>
            <a href="../index.php">Abrigo dos gatos</a> 
        </span>
    </header>
    
    <main>
    <?php
        require_once '../lib/conn.php';
        require __DIR__ . "/../src/Uploader.php";
        require __DIR__ . "/../src/Image.php";

        $image = new CoffeeCode\Uploader\Image("../image", false, false);
        $upload = $image->upload($_FILES['img'], $_POST['name']);

        if ($_FILES) {
            extract($_POST);
            try {
                $novo_nome = $_POST['name'];
                $sql = "INSERT INTO gato VALUES(0, :nome, :descricao, 1, :nomeimagem)";
                $stmt = $conn->prepare($sql);
                $stmt->bindValue(':nome', $name);
                $stmt->bindValue(':descricao', $descricao);
                $stmt->bindValue(':nomeimagem', $novo_nome);
                $stmt->execute();
        ?>

        <div class="alert success">
            <i class="far fa-check-circle"></i>
            <p class="lead">Gato cadastrado com sucesso!</p> 
        </div>

        <?php
                } catch (Exception $e) {
                    ?>
                    <div class="alert danger">
                        <i class="fas fa-times"></i>
                        <p class="lead">Extenção de imagem inválida</p> 
                    </div>
                    <?php
                }
            }
        ?>
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