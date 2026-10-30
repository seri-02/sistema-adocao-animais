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
    require_once __DIR__ . '/../lib/config.php';
    extract($_POST);

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    $mail = new PHPMailer(true);
    try {
        // Sem configuração completa não há tentativa de conexão; o usuário vê só a mensagem genérica
        $configuracoes_smtp = ['SMTP_HOST', 'SMTP_PORT', 'SMTP_SECURE', 'SMTP_USER', 'SMTP_PASS', 'MAIL_FROM', 'MAIL_FROM_NAME', 'MAIL_TO'];
        foreach ($configuracoes_smtp as $chave) {
            if (!defined($chave) || constant($chave) === '') {
                throw new Exception('SMTP não configurado em config/config.php');
            }
        }

        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port = SMTP_PORT;

        $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
        $mail->addAddress(MAIL_TO, 'Abrigo para Gatos');
        $mail->addReplyTo($email, $msg);

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = "Mensagem de $email";
        $mail->Body = "Conteudo da mensagem: <br> $msg";

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