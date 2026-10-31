<?php
    // Fluxo LEGADO de adoção imediata: DESATIVADO na F3a.
    // A adoção agora começa por uma solicitação (function/func_solicitar_adocao.php).
    // Este endpoint não acessa o banco: não grava em `adocoes` nem altera o status do animal,
    // qualquer que seja o método ou os dados enviados. Mantido só para responder de forma
    // controlada a links/formulários antigos.
    http_response_code(410);
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
    <meta http-equiv="refresh" content="5; url=../adocoes.php">
    <title>Abrigo dos Gatos</title>
</head>
<body id="adm">
    <header>
        <span>
            <a href="../index.php">Abrigo dos gatos</a>
        </span>
    </header>

    <main>
        <div class="alert danger">
            <i class="fas fa-times"></i>
            <p class="lead">A adoção agora é feita por solicitação: entre na sua conta de adotante e escolha "Solicitar adoção" na página do animal.</p>
            <p><a href="../adocoes.php">Voltar ao catálogo</a></p>
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
