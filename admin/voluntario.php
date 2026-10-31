<?php
    // Cadastro de voluntário pela equipe (ADMIN/FUNCIONARIO). Mesmos campos e mesmas regras do
    // cadastro público (seja_voluntario.php): a validação vem de lib/voluntario.php.
    require_once __DIR__ . '/../lib/auth.php';
    require_once __DIR__ . '/../lib/escape.php';
    require_once __DIR__ . '/../lib/voluntario.php';
    $usuario_logado = exigir_perfis(PERFIS_EQUIPE);
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
    <title>Cadastro de voluntário - Patas &amp; Lares</title>
</head>
<body id="adm">
    <header class="header-adm">
        <span>
            <a href="../index.php">Patas &amp; Lares</a>
        </span>
        <a href="painel.php"><button class="sair"><i class="fa-solid fa-chevron-left"></i> Voltar</button></a>
    </header>

    <main class="form-cadastros">
        <span>Cadastro de voluntário</span>
        <form action="../function/func_cadastrar_voluntario_admin.php" method="post">
            <div class="campo">
                <input type="text" name="nome" id="nome" maxlength="<?= e(VOLUNTARIO_NOME_MAXIMO) ?>" required>
                <span>Nome</span>
            </div>
            <div class="campo">
                <input type="email" name="email" id="email" maxlength="<?= e(VOLUNTARIO_EMAIL_MAXIMO) ?>" required>
                <span>Email</span>
            </div>
            <div class="campo">
                <input type="number" name="idade" id="idade" min="1" step="1" required>
                <span>Idade</span>
            </div>
            <div class="campo">
                <input type="text" name="dia_disponivel" id="dia_disponivel" maxlength="<?= e(VOLUNTARIO_DIA_MAXIMO) ?>" required>
                <span>Dias disponíveis</span>
            </div>
            <button type="submit">Cadastrar</button>
        </form>
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
