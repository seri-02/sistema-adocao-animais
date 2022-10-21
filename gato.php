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
    <title>Abrigo dos Gatos</title>
</head>
<body id="adm">
    <header class="header-adm">
        <span>
            <a href="index.php">Abrigo dos gatos</a> 
        </span>
        <a href="adocoes.php"><button class="sair"><i class="fa-solid fa-chevron-left"></i> Voltar</button></a>
    </header>
    
    <section class="gatos">
        <?php
            require_once "lib/conn.php";
            session_start();
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
        <h1 class="nome-gato"><?= $gato->nome ?></h1>
        <div class="row-gato">
            <div class="img-adocao">
                <img src="image/<?= $gato->img ?>" style="height: 50vh; object-fit = cover;">
            </div>

            <form action="function/func_adotar.php" method="post" id="form-adotante">
                <div class="campo">
                    <input type="text" id="nome_adotante" name="nome_adotante" required>
                    <span>Nome</sp>
                </div>

                <div class="campo">
                    <input type="email" id="email_adotante" name="email_adotante" required>
                    <span>Email</span>
                </div>

                <div class="campo"> 
                    <input type="tel" id="telefone" name="telefone" required>
                    <span>Telefone</span>
                </div>

                <div>
                    <button type="submit" class="adotar">Adotar</button>
                </div>
            </form>
        </div>
       
    </section>

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