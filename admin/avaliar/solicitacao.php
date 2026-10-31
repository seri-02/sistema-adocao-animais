<?php
    require_once __DIR__ . '/../../lib/auth.php';
    require_once __DIR__ . '/../../lib/escape.php';
    $usuario_logado = exigir_perfis(PERFIS_EQUIPE);

    require_once __DIR__ . '/../../lib/conn.php';
    require_once __DIR__ . '/../../lib/animal.php';
    require_once __DIR__ . '/../../lib/solicitacao.php';

    $id = filter_var($_GET['id'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $solicitacao = $id === false ? null : buscar_solicitacao_equipe($conn, $id);
    if ($solicitacao === null) {
        http_response_code(404);
    }

    $mensagem = $_SESSION['mensagem_analise'] ?? null;
    unset($_SESSION['mensagem_analise']);
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
    <title>Abrigo dos Gatos - Análise de solicitação</title>
</head>
<body id="adm">
    <header class="header-adm">
        <span>
            <a href="../../index.php">Abrigo dos gatos</a>
        </span>
        <a href="../solicitacoes.php"><button class="sair"><i class="fa-solid fa-chevron-left"></i> Voltar</button></a>
    </header>

    <main class="form-cadastros">
        <span>Análise de solicitação</span>

        <?php if ($mensagem !== null) { ?>
        <div class="alert success">
            <i class="far fa-check-circle"></i>
            <p class="lead"><?= e($mensagem) ?></p>
        </div>
        <?php } ?>

        <?php if ($solicitacao === null) { ?>
        <div class="alert danger">
            <i class="fas fa-times"></i>
            <p class="lead">Solicitação não encontrada.</p>
        </div>
        <?php } else { ?>
        <h4>Animal</h4>
        <?php if ($solicitacao->animal_imagem) { ?>
        <img src="../../image/<?= e($solicitacao->animal_imagem) ?>" alt="<?= e($solicitacao->animal_nome) ?>" width="200">
        <?php } ?>
        <ul>
            <li><strong>Nome:</strong> <?= e($solicitacao->animal_nome) ?></li>
            <li><strong>Espécie:</strong> <?= e(rotulo(ESPECIES, $solicitacao->animal_especie)) ?></li>
            <li><strong>Sexo:</strong> <?= e(rotulo(SEXOS, $solicitacao->animal_sexo)) ?></li>
            <li><strong>Idade:</strong> <?= e(calcular_idade($solicitacao->animal_nascimento)) ?></li>
            <li><strong>Situação atual:</strong> <?= e(rotulo(STATUS_ANIMAL, $solicitacao->animal_status)) ?></li>
        </ul>

        <h4>Adotante</h4>
        <ul>
            <li><strong>Nome:</strong> <?= e($solicitacao->adotante_nome) ?></li>
            <li><strong>Email:</strong> <?= e($solicitacao->adotante_email) ?></li>
            <li><strong>Telefone:</strong> <?= e($solicitacao->adotante_telefone ?? NAO_INFORMADO) ?></li>
        </ul>

        <h4>Solicitação</h4>
        <ul>
            <li><strong>Data:</strong> <?= e(formatar_data_hora($solicitacao->data_solicitacao)) ?></li>
            <li><strong>Situação:</strong> <?= e(rotulo(STATUS_SOLICITACAO, $solicitacao->status)) ?></li>
            <?php if ($solicitacao->status === 'CANCELADA') { ?>
            <li><strong>Cancelada pelo adotante em:</strong> <?= e(formatar_data_hora($solicitacao->data_decisao)) ?></li>
            <?php } elseif ($solicitacao->status !== 'PENDENTE') { ?>
            <li><strong>Avaliador:</strong> <?= e($solicitacao->avaliador_nome) ?></li>
            <li><strong>Data da decisão:</strong> <?= e(formatar_data_hora($solicitacao->data_decisao)) ?></li>
            <li><strong>Justificativa:</strong> <?= $solicitacao->justificativa === null ? e(NAO_INFORMADO) : nl2br(e($solicitacao->justificativa)) ?></li>
            <?php } ?>
        </ul>

        <?php if ($solicitacao->status === 'PENDENTE' && (int) $solicitacao->id_adotante === (int) $usuario_logado['id_usuario']) { ?>
        <p>Você não pode analisar sua própria solicitação de adoção.</p>
        <?php } elseif ($solicitacao->status === 'PENDENTE') { ?>
        <!-- Só a decisão e a justificativa são enviadas: avaliador, status e datas são definidos no servidor -->
        <form action="../../function/update/func_analisar_solicitacao.php" method="post">
            <input type="hidden" name="id_solicitacao" value="<?= e($solicitacao->id_solicitacao) ?>">
            <div>
                <label for="justificativa">Justificativa (obrigatória para reprovar; opcional ao aprovar)</label>
                <textarea name="justificativa" id="justificativa" cols="30" rows="5" maxlength="<?= e(JUSTIFICATIVA_TAMANHO_MAXIMO) ?>"></textarea>
            </div>
            <button type="submit" name="decisao" value="APROVAR">Aprovar</button>
            <button type="submit" name="decisao" value="REPROVAR">Reprovar</button>
        </form>
        <?php } ?>
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
