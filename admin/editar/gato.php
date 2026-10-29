<?php
    require_once __DIR__ . '/../../lib/auth.php';
    require_once __DIR__ . '/../../lib/escape.php';
    exigir_login();

    require_once '../../lib/conn.php';

    $id = (int)$_GET['id'];
    $sql = "SELECT * FROM gato WHERE id_gato = :id_gato";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":id_gato", $id);
    $stmt->execute();
    $gato = $stmt->fetch(PDO::FETCH_OBJ);
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
        <span>Editar gato</span>
        <form action="../../function/update/func_editar_gato.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id_gato" value="<?= e($gato->id_gato) ?>">
            <div class="campo">  
                <input type="text" name="name" id="name" required value="<?= e($gato->nome) ?>">
                <span>Nome do gato</span>
            </div>

            <div class="campo">
                <textarea name="descricao" id="descricao" cols="30" rows="10" maxlength="100" required><?= e($gato->descricao) ?></textarea>
                <span>Descrição</span>
            </div>      

            <button type="submit">
                Editar
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