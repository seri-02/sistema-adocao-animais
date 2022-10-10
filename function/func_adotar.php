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
    <meta http-equiv="refresh" content="2; url=adocoes.php">
    <title>Abrigo dos Gatos</title>
</head>
<body id="adm">
    <header>
        <span>
            <a href="index.php">Abrigo dos gatos</a> 
        </span>
    </header>
    
    <main>
        <?php
            require_once '../lib/conn.php';
            extract($_POST);

            session_start();
            if(isset($_GET['id'])){
                $id_gato = $_SESSION['id_gato'] = (int)$_GET['id'];
            } 
            else {
                $id_gato = $_SESSION['id_gato'];
            }

            $sql_gato = "SELECT adocoes FROM gato WHERE id_gato = :id";
            $stmt = $conn->prepare($sql_gato);
            $stmt->bindParam(":id",$id);
            $stmt->execute();
            $gato = $stmt->fetch(PDO::FETCH_OBJ);

            $sql = "INSERT INTO adocoes VALUES(0, :fk_id_gato, :nome_adotante, :email_adotante, :telefone)";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':fk_id_gato', $id_gato);
            $stmt->bindValue(':nome_adotante', $nome_adotante);
            $stmt->bindValue(':email_adotante', $email_adotante);
            $stmt->bindValue(':telefone', $telefone);
            $stmt->execute();

            $updateGato = "UPDATE gato SET adotado = 0 WHERE id_gato = :id_gato";
            $update = $conn->prepare($updateGato);
            $update->bindValue(':id_gato', $id_gato);
            $update->execute();
        ?>
        <div class="alert success">
            <i class="far fa-check-circle"></i>
            <p class="lead">Gato adotado com sucesso</p> 
        </div>
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