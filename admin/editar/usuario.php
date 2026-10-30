<?php
    require_once __DIR__ . '/../../lib/auth.php';
    require_once __DIR__ . '/../../lib/escape.php';
    require_once __DIR__ . '/../../lib/usuario.php';
    $usuario_logado = exigir_perfis([PERFIL_ADMIN]);

    require_once '../../lib/conn.php';
    $id = (int) ($_GET['id'] ?? 0);
    // A senha (hash) nunca é lida para a tela
    $stmt = $conn->prepare("SELECT id_usuario, nome, email, telefone, perfil, ativo FROM usuario WHERE id_usuario = :id_usuario");
    $stmt->bindValue(':id_usuario', $id, PDO::PARAM_INT);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_OBJ) ?: null;

    // Na própria conta, perfil e situação não são editáveis (evita o administrador se bloquear)
    $propria_conta = $usuario !== null && (int) $usuario->id_usuario === (int) $usuario_logado['id_usuario'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <link rel="stylesheet" href="../../css/style.css">

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
            <a href="../../index.php">Abrigo dos gatos</a>
        </span>
        <a href="../painel.php"><button class="sair"><i class="fa-solid fa-chevron-left"></i> Voltar</button></a>
    </header>

    <main class="form-cadastros">
        <span>Editar usuário</span>
        <?php if ($usuario === null) { ?>
            <div class="alert danger">
                <i class="fas fa-times"></i>
                <p class="lead">Usuário não encontrado.</p>
            </div>
        <?php } else { ?>
        <form action="../../function/update/func_editar_usuario.php" method="post">
            <input type="hidden" name="id_usuario" value="<?= e($usuario->id_usuario) ?>">
            <div class="campo">
                <input type="text" name="nome" id="nome" maxlength="100" required value="<?= e($usuario->nome) ?>">
                <span>Nome</span>
            </div>
            <div class="campo">
                <input type="email" name="email" id="email" maxlength="150" required value="<?= e($usuario->email) ?>">
                <span>Email</span>
            </div>
            <div class="campo">
                <input type="tel" name="telefone" id="telefone" maxlength="20" value="<?= e($usuario->telefone) ?>">
                <span>Telefone</span>
            </div>
            <?php if ($propria_conta) { ?>
                <p>Perfil: <strong><?= e(PERFIS[$usuario->perfil] ?? $usuario->perfil) ?></strong> · Situação: <strong>Ativo</strong>
                   (não é possível alterar o próprio perfil nem desativar a própria conta)</p>
            <?php } else { ?>
                <div class="campo">
                    <select name="perfil" id="perfil" required>
                        <?php foreach (PERFIS as $perfil => $rotulo) { ?>
                            <option value="<?= e($perfil) ?>"<?= $usuario->perfil === $perfil ? ' selected' : '' ?>><?= e($rotulo) ?></option>
                        <?php } ?>
                    </select>
                    <span>Perfil</span>
                </div>
                <div class="campo">
                    <select name="ativo" id="ativo" required>
                        <option value="1"<?= (int) $usuario->ativo === 1 ? ' selected' : '' ?>>Ativo</option>
                        <option value="0"<?= (int) $usuario->ativo === 0 ? ' selected' : '' ?>>Inativo</option>
                    </select>
                    <span>Situação</span>
                </div>
            <?php } ?>
            <div class="campo">
                <input type="password" name="senha" id="senha" minlength="<?= e(SENHA_TAMANHO_MINIMO) ?>" autocomplete="new-password">
                <span>Nova senha (deixe vazio para manter)</span>
            </div>
            <button type="submit">Atualizar</button>
        </form>
        <?php } ?>
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
