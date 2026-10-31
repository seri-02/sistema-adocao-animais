<?php
    // Cadastro PÚBLICO de voluntário (seja_voluntario.php). Somente POST.
    // Só nome/email/idade/dia_disponivel são lidos e validados (lib/voluntario.php);
    // qualquer outro campo enviado é ignorado.
    require_once __DIR__ . '/../lib/conn.php';
    require_once __DIR__ . '/../lib/escape.php';
    require_once __DIR__ . '/../lib/voluntario.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ../seja_voluntario.php');
        exit;
    }

    [$dados, $erros] = ler_formulario_voluntario($_POST);

    if (!$erros) {
        try {
            cadastrar_voluntario($conn, $dados);
        } catch (PDOException $e) {
            error_log('Falha ao cadastrar voluntário: ' . $e->getMessage());
            $erros[] = 'Não foi possível concluir o cadastro. Verifique os dados informados.';
        }
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
    <?php if (!$erros) { ?>
    <meta http-equiv="refresh" content="2; url=../seja_voluntario.php">
    <?php } ?>
    <title>Seja voluntário - Patas &amp; Lares</title>
</head>
<body id="adm">
    <header>
        <span>
            <a href="../index.php">Patas &amp; Lares</a>
        </span>
    </header>

    <main>
        <?php if (!$erros) { ?>
        <div class="alert success">
            <i class="far fa-check-circle"></i>
            <p class="lead">Voluntário cadastrado com sucesso!</p>
        </div>
        <?php } else { ?>
        <div class="alert danger">
            <i class="fas fa-times"></i>
            <?php foreach ($erros as $erro) { ?>
                <p class="lead"><?= e($erro) ?></p>
            <?php } ?>
            <p><a href="javascript:history.back()">Voltar ao formulário</a></p>
        </div>
        <?php } ?>
    </main>

    <footer>
        <div id="baixo">
			<div class="centro">
				<span class="credito">&copy; 2026 Patas &amp; Lares · Projeto acadêmico</span>
			</div>
		</div>
    </footer>
</body>
</html>
