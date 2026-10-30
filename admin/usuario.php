<?php
    require_once __DIR__ . '/../lib/auth.php';
    require_once __DIR__ . '/../lib/escape.php';
    require_once __DIR__ . '/../lib/usuario.php';
    $usuario_logado = exigir_perfis([PERFIL_ADMIN]);
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
        <a href="painel.php"><button class="sair"><i class="fa-solid fa-chevron-left"></i> Voltar</button></a>
    </header>
    
    <main class="form-cadastros">
        <span>Cadastro de usuário da equipe</span>
        <form action="../function/func_cadastrar_usuario.php" method="post">
            <div class="campo">
                <input type="text" name="nome" id="nome" maxlength="100" required>
                <span>Nome</span>
            </div>
            <div class="campo">
                <input type="email" name="email" id="email" maxlength="150" required>
                <span>Email</span>
            </div>
            <div class="campo">
                <input type="tel" name="telefone" id="telefone" maxlength="20">
                <span>Telefone</span>
            </div>
            <div class="campo">
                <select name="perfil" id="perfil" required>
                    <?php foreach (PERFIS_CADASTRO_ADMIN as $perfil) { ?>
                        <option value="<?= e($perfil) ?>"><?= e(PERFIS[$perfil]) ?></option>
                    <?php } ?>
                </select>
                <span>Perfil</span>
            </div>
            <div class="campo">
                <input type="password" name="senha" id="senha" minlength="<?= e(SENHA_TAMANHO_MINIMO) ?>" autocomplete="new-password" required>
                <span>Senha (mínimo <?= e(SENHA_TAMANHO_MINIMO) ?> caracteres)</span>
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