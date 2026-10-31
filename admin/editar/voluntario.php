<?php
    require_once __DIR__ . '/../../lib/auth.php';
    require_once __DIR__ . '/../../lib/escape.php';
    $usuario_logado = exigir_perfis(PERFIS_EQUIPE);

    require_once __DIR__ . '/../../lib/conn.php';
    require_once __DIR__ . '/../../lib/voluntario.php';

    // Id inválido (vazio, texto, negativo, decimal, array) ou inexistente: sem formulário, sem warning
    $id = ler_id_voluntario($_GET['id'] ?? null);
    $voluntario = false;
    if ($id !== false) {
        $stmt = $conn->prepare('SELECT * FROM voluntario WHERE id_voluntario = :id_voluntario');
        $stmt->bindValue(':id_voluntario', $id, PDO::PARAM_INT);
        $stmt->execute();
        $voluntario = $stmt->fetch(PDO::FETCH_OBJ);
    }
    if (!$voluntario) {
        http_response_code(404);
    }
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
        <span>Editar Voluntário</span>
        <?php if (!$voluntario) { ?>
        <div class="alert danger">
            <i class="fas fa-times"></i>
            <p class="lead">Voluntário não encontrado.</p>
            <p><a href="../tabelas/tabela_voluntario.php">Voltar para voluntários</a></p>
        </div>
        <?php } else { ?>
        <form action="../../function/update/func_editar_voluntario.php" method="post">
            <input type="hidden" id="id_voluntario" name="id_voluntario" value="<?= e($voluntario->id_voluntario) ?>">
                <div class="campo">
                    <input type="text" name="nome" id="nome" required value="<?= e($voluntario->nome) ?>">
                    <span>Nome</span>
                </div>
                <div class="campo">
                    <input type="email" name="email" id="email" required value="<?= e($voluntario->email) ?>">
                    <span>Email</span>
                </div>
                <div class="campo">
                    <input type="number" name="idade" id="idade" required value="<?= e($voluntario->idade) ?>">
                    <span>Idade</span>
                </div>
                <div class="campo">
                    <input type="text" name="dia_disponivel" id="dia_disponivel" required value="<?= e($voluntario->dia_disponivel) ?>">
                    <span>Dias disponível</span>
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