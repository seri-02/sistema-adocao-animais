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
    <meta http-equiv="refresh" content="2; url=../index.php">
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
    require_once '../vendor/autoload.php';
    extract($_POST);

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    $mail = new PHPMailer(true);
    try {
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();
        $mail->Host ='smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'analauraalmeidaventura@gmail.com';
        $mail->Password = 'ckkbtsvnybntmawp';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        $mail->setFrom('analauraalmeidaventura@gmail.com', 'Ana Laura'); 
        $mail->addAddress('analauraalmeidaventura@gmail.com', 'Abrigo para Gatos'); 
        $mail->addReplyTo($email, $msg);

        $mail->isHTML(true);
        $mail->Subject = "Mensagem de $email";
        $mail->Body = "Conteúdo da mensagem $msg";

        $mail->send();
    ?>
        <div class="alert success">
            <i class="far fa-check-circle"></i>
            <p class="lead">Mensagem enviada com sucesso</p> 
        </div>
<?php
    } 
    catch (Exception $e) {
?>
        <div class="alert danger">
            <i class="fas fa-times"></i>
            <p class="lead">Mensagem não pode ser entregue</p> 
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