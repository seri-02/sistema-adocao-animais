<?php
    require_once __DIR__ . '/../lib/auth.php';
    require_once __DIR__ . '/../lib/escape.php';
    $usuario_logado = exigir_perfis(PERFIS_EQUIPE);

    require_once __DIR__ . '/../lib/conn.php';
    require_once __DIR__ . '/../lib/animal.php';
    require_once __DIR__ . '/../lib/solicitacao.php';

    // Filtro simples por situação; valor desconhecido mostra todas
    $filtro = is_string($_GET['status'] ?? null) && array_key_exists($_GET['status'], STATUS_SOLICITACAO) ? $_GET['status'] : null;
    $solicitacoes = listar_solicitacoes_equipe($conn, $filtro);
    $pendentes = contar_solicitacoes_pendentes($conn);
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
    <title>Abrigo dos Gatos - Solicitações de adoção</title>
</head>
<body id="adm">
    <header class="header-adm">
        <span>
            <a href="../index.php">Abrigo dos gatos</a>
        </span>
        <a href="painel.php"><button class="sair"><i class="fa-solid fa-chevron-left"></i> Voltar</button></a>
    </header>

    <div class="tabela">
        <h3>Solicitações de adoção</h3>
        <p><?= e($pendentes) ?> pendente(s) aguardando análise.</p>
        <p>
            Mostrar:
            <a href="solicitacoes.php">Todas</a>
            <?php foreach (STATUS_SOLICITACAO as $valor => $rotulo) { ?>
            | <a href="solicitacoes.php?status=<?= e($valor) ?>"><?= e($rotulo) ?></a>
            <?php } ?>
        </p>
        <table>
            <thead>
                <tr>
                    <th>Animal</th>
                    <th>Adotante</th>
                    <th>Email</th>
                    <th>Data da solicitação</th>
                    <th>Situação</th>
                    <th>Avaliador</th>
                    <th>Data da decisão</th>
                    <th>Justificativa</th>
                    <th>Ação</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($solicitacoes as $solicitacao) { ?>
                <tr>
                    <td><?= e($solicitacao->animal_nome) ?></td>
                    <td><?= e($solicitacao->adotante_nome) ?></td>
                    <td><?= e($solicitacao->adotante_email) ?></td>
                    <td><?= e(formatar_data_hora($solicitacao->data_solicitacao)) ?></td>
                    <?php $situacao = e(rotulo(STATUS_SOLICITACAO, $solicitacao->status)); ?>
                    <td><?= $solicitacao->status === 'PENDENTE' ? "<strong>{$situacao}</strong>" : $situacao ?></td>
                    <td><?= e($solicitacao->avaliador_nome) ?></td>
                    <td><?= e(formatar_data_hora($solicitacao->data_decisao)) ?></td>
                    <td><?= e(mb_strimwidth((string) $solicitacao->justificativa, 0, 80, '…')) ?></td>
                    <td>
                        <a href="avaliar/solicitacao.php?id=<?= e($solicitacao->id_solicitacao) ?>">
                            <?= $solicitacao->status === 'PENDENTE' ? 'Analisar' : 'Ver' ?>
                        </a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php if (!$solicitacoes) { ?>
        <p>Nenhuma solicitação encontrada.</p>
        <?php } ?>
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
