<?php
    require_once __DIR__ . '/lib/auth.php';
    require_once __DIR__ . '/lib/escape.php';
    require_once __DIR__ . '/lib/usuario.php';
    $usuario_logado = exigir_perfis([PERFIL_ADOTANTE], 'login.php');

    require_once __DIR__ . '/lib/conn.php';
    // A senha (hash) nunca é lida para a tela
    $stmt = $conn->prepare('SELECT nome, email, telefone FROM usuario WHERE id_usuario = :id_usuario');
    $stmt->bindValue(':id_usuario', (int) $usuario_logado['id_usuario'], PDO::PARAM_INT);
    $stmt->execute();
    $conta = $stmt->fetch(PDO::FETCH_OBJ);

    $usuario_menu = $usuario_logado;
    $estilo_menu = 'botoes';
?>
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
    <title>Abrigo dos Gatos - Editar meus dados</title>
</head>
<body id="adm">
    <header class="header-adm">
        <span>
            <a href="index.php">Abrigo dos gatos</a>
        </span>
        <?php require __DIR__ . '/parciais/menu_conta.php'; ?>
    </header>

    <main class="form-cadastros">
        <span>Editar meus dados</span>
        <form action="function/update/func_editar_conta.php" method="post">
            <div class="campo">
                <input type="text" name="nome" id="nome" maxlength="100" required value="<?= e($conta->nome) ?>">
                <span>Nome</span>
            </div>
            <div class="campo">
                <input type="email" name="email" id="email" maxlength="150" required value="<?= e($conta->email) ?>">
                <span>Email</span>
            </div>
            <div class="campo">
                <input type="tel" name="telefone" id="telefone" maxlength="20" value="<?= e($conta->telefone) ?>">
                <span>Telefone (opcional)</span>
            </div>

            <p>Para trocar a senha, preencha os três campos abaixo. Deixe em branco para manter a senha atual.</p>
            <div class="campo">
                <input type="password" name="senha_atual" id="senha_atual" autocomplete="current-password">
                <span>Senha atual</span>
            </div>
            <div class="campo">
                <input type="password" name="nova_senha" id="nova_senha" minlength="<?= e(SENHA_TAMANHO_MINIMO) ?>" autocomplete="new-password">
                <span>Nova senha (mínimo <?= e(SENHA_TAMANHO_MINIMO) ?> caracteres)</span>
            </div>
            <div class="campo">
                <input type="password" name="confirmacao_nova_senha" id="confirmacao_nova_senha" minlength="<?= e(SENHA_TAMANHO_MINIMO) ?>" autocomplete="new-password">
                <span>Confirmar nova senha</span>
            </div>
            <button type="submit">Salvar</button>
        </form>
        <p><a href="minha_conta.php">Voltar para Minha conta</a></p>
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
