<?php
    require_once __DIR__ . '/../../lib/auth.php';
    require_once __DIR__ . '/../../lib/escape.php';
    require_once __DIR__ . '/../../lib/animal.php';
    $usuario_logado = exigir_perfis(PERFIS_EQUIPE);
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
            <a href="../index.php">Abrigo dos gatos</a> 
        </span>
        <a href="../painel.php"><button class="sair"><i class="fa-solid fa-chevron-left"></i> Voltar</button></a>
    </header>

    <div class="tabela">
        <h3>Lista de Animais</h3>
        <table>
            <thead>
                <tr>
                    <th class="opcao">#</th>
                    <th>Nome</th>
                    <th>Espécie</th>
                    <th>Sexo</th>
                    <th>Idade</th>
                    <th>Porte</th>
                    <th>Status</th>
                    <th>Excluir</th>
                    <th>Editar</th>
                </tr>
            </thead>

            <tbody>
                <?php
                    require_once '../../lib/conn.php';
                    $sql = "SELECT * FROM animal ORDER BY id_animal ASC";
                    $stmt = $conn->query($sql);
                    $animais = $stmt->fetchAll(PDO::FETCH_OBJ);
                    foreach ($animais as $animal) {
                        ?>
                    <tr>
                        <td class="id"><?= e($animal->id_animal) ?></td>
                        <td><?= e($animal->nome) ?></td>
                        <td><?= e(rotulo(ESPECIES, $animal->especie)) ?></td>
                        <td><?= e(rotulo(SEXOS, $animal->sexo)) ?></td>
                        <td><?= e(calcular_idade($animal->data_nascimento_estimada)) ?></td>
                        <td><?= e(rotulo(PORTES, $animal->porte)) ?></td>
                        <td><?= e(rotulo(STATUS_ANIMAL, $animal->status)) ?></td>
                        <td><a href="../../function/delete/gato.php?id=<?= e($animal->id_animal) ?>"><i class="fa-solid fa-trash"></i></a></td>
                        <td><a href="../editar/gato.php?id=<?= e($animal->id_animal) ?>"><i class="fa-solid fa-user-pen"></i></a></td>
                    </tr>
                <?php
                    }
                ?>
            </tbody>
        </table>
    </div>

    <footer id="footer-adm">
        <div id="baixo">
			<div class="centro">
				<span class="credito">&copy; Abrigo dos gatos |</span>
				<span>Design by <a href="#">ANA LAURA </a>2022</span>
			</div>
		</div>
    </footer>
</body>
</html>