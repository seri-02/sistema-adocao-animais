<?php
    require_once 'lib/auth.php';
    require_once 'lib/conn.php';
    require_once 'lib/escape.php';
    require_once 'lib/animal.php';

    iniciar_sessao();
    if (isset($_GET['id'])) {
        $_SESSION['id_animal'] = (int) $_GET['id'];
    }
    $id = (int) ($_SESSION['id_animal'] ?? 0);

    $stmt = $conn->prepare("SELECT * FROM animal WHERE id_animal = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $animal = $stmt->fetch(PDO::FETCH_OBJ) ?: null;

    $usuario_menu = usuario_logado_opcional();
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
    <title>Abrigo dos Gatos</title>
</head>
<body id="adm">
    <header class="header-adm">
        <span>
            <a href="index.php">Abrigo dos gatos</a> 
        </span>
        <a href="adocoes.php"><button class="sair"><i class="fa-solid fa-chevron-left"></i> Voltar</button></a>
        <?php require __DIR__ . '/parciais/menu_conta.php'; ?>
    </header>
    
    <section class="gatos">
        <?php if ($animal === null) { ?>
        <h1 class="nome-gato">Animal não encontrado</h1>
        <?php } else { ?>
        <h1 class="nome-gato"><?= e($animal->nome) ?></h1>
        <div class="row-gato">
            <div class="img-adocao">
                <?php if ($animal->imagem) { ?>
                <img src="image/<?= e($animal->imagem) ?>" alt="<?= e($animal->nome) ?>" style="height: 50vh; object-fit = cover;">
                <?php } ?>
                <ul class="dados-animal">
                    <li><strong>Espécie:</strong> <?= e(rotulo(ESPECIES, $animal->especie)) ?></li>
                    <li><strong>Sexo:</strong> <?= e(rotulo(SEXOS, $animal->sexo)) ?></li>
                    <li><strong>Idade:</strong> <?= e(calcular_idade($animal->data_nascimento_estimada)) ?></li>
                    <li><strong>Porte:</strong> <?= e(rotulo(PORTES, $animal->porte)) ?></li>
                    <li><strong>Castrado:</strong> <?= e(rotulo_sim_nao($animal->castrado)) ?></li>
                    <li><strong>Vacinado:</strong> <?= e(rotulo_sim_nao($animal->vacinado)) ?></li>
                    <li><strong>Condição de saúde:</strong> <?= e($animal->condicao_saude ?? NAO_INFORMADO) ?></li>
                    <li><strong>Comportamento:</strong> <?= e($animal->comportamento ?? NAO_INFORMADO) ?></li>
                    <li><strong>Descrição:</strong> <?= e($animal->descricao ?? NAO_INFORMADO) ?></li>
                </ul>
            </div>

            <div>
                <p><strong>Situação:</strong> <?= e(rotulo(STATUS_ANIMAL, $animal->status)) ?></p>
                <?php if ($animal->status === 'DISPONIVEL') { ?>
                    <?php if ($usuario_menu === null) { ?>
                    <p>Entre na sua conta para solicitar a adoção deste animal.</p>
                    <p><a href="login.php">Entrar</a> | <a href="cadastro.php">Cadastrar</a></p>
                    <?php } elseif ($usuario_menu['perfil'] === PERFIL_ADOTANTE) { ?>
                    <!-- Os dados do adotante vêm da conta autenticada: só o animal é enviado -->
                    <form action="function/func_solicitar_adocao.php" method="post" id="form-adotante">
                        <input type="hidden" name="id_animal" value="<?= e($animal->id_animal) ?>">
                        <button type="submit" class="adotar">Solicitar adoção</button>
                    </form>
                    <?php } ?>
                <?php } elseif ($animal->status === 'INDISPONIVEL') { ?>
                <p>Este animal está indisponível para adoção no momento.</p>
                <?php } ?>
            </div>
        </div>
        <?php } ?>
       
    </section>

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