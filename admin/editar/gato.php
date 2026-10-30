<?php
    require_once __DIR__ . '/../../lib/auth.php';
    require_once __DIR__ . '/../../lib/escape.php';
    require_once __DIR__ . '/../../lib/animal.php';
    $usuario_logado = exigir_perfis(PERFIS_EQUIPE);

    require_once '../../lib/conn.php';

    $id = (int) ($_GET['id'] ?? 0);
    $sql = "SELECT * FROM animal WHERE id_animal = :id_animal";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":id_animal", $id);
    $stmt->execute();
    $animal = $stmt->fetch(PDO::FETCH_OBJ) ?: null;
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
        <span>Editar animal</span>
        <?php if ($animal === null) { ?>
            <div class="alert danger">
                <i class="fas fa-times"></i>
                <p class="lead">Animal não encontrado.</p>
            </div>
        <?php } else { ?>
        <form action="../../function/update/func_editar_gato.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id_animal" value="<?= e($animal->id_animal) ?>">
            <?php require __DIR__ . '/../parciais/campos_animal.php'; ?>

            <button type="submit">
                Editar
            </button>
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