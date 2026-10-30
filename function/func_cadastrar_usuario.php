<?php
  require_once __DIR__ . '/../lib/auth.php';
  exigir_login();
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
    <meta http-equiv="refresh" content="2; url=../admin/usuario.php">
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

            $verifica = "SELECT * FROM usuario WHERE email = :email";
            $stmt = $conn->prepare($verifica);
            $stmt->bindValue(":email", $_POST['email']);
            $stmt->execute(); 

            if($stmt->rowCount() === 0) {
                extract($_POST);
                $sql = "INSERT INTO usuario VALUES(0, :email, :senha, :name)";
                $insert = $conn->prepare($sql);
                $insert->bindValue(":email", $email);
                $insert->bindValue(":senha", sha1($senha));
                $insert->bindValue(":name", $name);
                try {
                    $insert->execute();
                    $sucesso = true;
                } catch (PDOException $e) {
                    // Com STRICT_TRANS_TABLES o banco recusa dado inválido (ex.: nome ou e-mail longo demais)
                    error_log('Falha ao cadastrar usuário: ' . $e->getMessage());
                    $sucesso = false;
                }
                if ($sucesso) {
        ?>
                <div class="alert success">
                    <i class="far fa-check-circle"></i>
                    <p class="lead">Usuário cadastrado com sucesso!</p>
                </div>
        <?php
                } else {
        ?>
                <div class="alert danger">
                    <i class="fas fa-times"></i>
                    <p class="lead">Não foi possível cadastrar o usuário. Verifique os dados informados.</p>
                </div>
        <?php
                }
        ?>

        <?php
            } else {
        ?>
                <div class="alert danger">
                    <i class="fas fa-times"></i>
                    <p class="lead">Email já existente!</p> 
                </div>
        <?php
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