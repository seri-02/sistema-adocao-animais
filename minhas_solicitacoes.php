<?php
    require_once __DIR__ . '/lib/auth.php';
    require_once __DIR__ . '/lib/escape.php';
    // Somente ADOTANTE; perfil e situação são conferidos no banco a cada acesso
    $usuario_logado = exigir_perfis([PERFIL_ADOTANTE], 'login.php');

    require_once __DIR__ . '/lib/conn.php';
    require_once __DIR__ . '/lib/animal.php';
    require_once __DIR__ . '/lib/solicitacao.php';

    // O adotante consultado é SEMPRE o da sessão validada; parâmetros da URL são ignorados
    $solicitacoes = listar_solicitacoes_do_adotante($conn, (int) $usuario_logado['id_usuario']);

    $mensagem = $_SESSION['mensagem_solicitacao'] ?? null;
    unset($_SESSION['mensagem_solicitacao']);

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
    <title>Abrigo dos Gatos - Minhas solicitações</title>
</head>
<body id="adm">
    <header class="header-adm">
        <span>
            <a href="index.php">Abrigo dos gatos</a>
        </span>
        <?php require __DIR__ . '/parciais/menu_conta.php'; ?>
    </header>

    <main>
        <span>Minhas solicitações</span>

        <?php if ($mensagem !== null) { ?>
        <div class="alert success">
            <i class="far fa-check-circle"></i>
            <p class="lead"><?= e($mensagem) ?></p>
        </div>
        <?php } ?>

        <?php if (!$solicitacoes) { ?>
        <p>Você ainda não fez nenhuma solicitação de adoção.</p>
        <p><a href="adocoes.php">Ver animais disponíveis</a></p>
        <?php } else { ?>
        <table>
            <thead>
                <tr>
                    <th>Animal</th>
                    <th>Espécie</th>
                    <th>Data da solicitação</th>
                    <th>Situação</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($solicitacoes as $solicitacao) { ?>
                <tr>
                    <td>
                        <?php if ($solicitacao->imagem) { ?>
                        <img src="image/<?= e($solicitacao->imagem) ?>" alt="<?= e($solicitacao->nome) ?>" width="120">
                        <?php } ?>
                        <p><?= e($solicitacao->nome) ?></p>
                    </td>
                    <td><?= e(rotulo(ESPECIES, $solicitacao->especie)) ?></td>
                    <td><?= e(formatar_data_hora($solicitacao->data_solicitacao)) ?></td>
                    <td>
                        <p><?= e(rotulo(STATUS_SOLICITACAO, $solicitacao->status)) ?></p>
                        <?php if ($solicitacao->status !== 'PENDENTE') { ?>
                            <?php if ($solicitacao->data_decisao !== null) { ?>
                            <p><?= $solicitacao->status === 'CANCELADA' ? 'Cancelada em' : 'Decisão em' ?> <?= e(formatar_data_hora($solicitacao->data_decisao)) ?></p>
                            <?php } ?>
                            <?php if ($solicitacao->justificativa !== null && $solicitacao->justificativa !== '') { ?>
                            <p><?= $solicitacao->status === 'REPROVADA' ? 'Motivo:' : 'Observação:' ?> <?= nl2br(e($solicitacao->justificativa)) ?></p>
                            <?php } ?>
                        <?php } ?>
                    </td>
                    <td>
                        <?php if ($solicitacao->status === 'PENDENTE') { ?>
                        <!-- Só o id da solicitação é enviado: o dono e o animal são conferidos no servidor -->
                        <form action="function/update/func_cancelar_solicitacao.php" method="post"
                              onsubmit="return confirm('Tem certeza de que deseja cancelar esta solicitação?');">
                            <input type="hidden" name="id_solicitacao" value="<?= e($solicitacao->id_solicitacao) ?>">
                            <button type="submit" class="sair">Cancelar solicitação</button>
                        </form>
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
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
