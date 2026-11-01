<?php
    // Decisão da equipe (ADMIN/FUNCIONARIO) sobre uma solicitação de adoção. Somente POST.
    // Do POST só são lidos id_solicitacao, decisao (APROVAR/REPROVAR) e justificativa:
    // status, id_avaliador, id_adotante, id_animal e data_decisao enviados são ignorados.
    // O avaliador é sempre o usuário autenticado (perfil e situação conferidos no banco).
    require_once __DIR__ . '/../../lib/auth.php';
    $usuario_logado = exigir_perfis(PERFIS_EQUIPE);

    require_once __DIR__ . '/../../lib/conn.php';
    require_once __DIR__ . '/../../lib/escape.php';
    require_once __DIR__ . '/../../lib/solicitacao.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ../../admin/solicitacoes.php');
        exit;
    }

    $id_solicitacao = filter_var($_POST['id_solicitacao'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    [$decisao, $justificativa, $erros] = ler_formulario_decisao($_POST);

    if ($id_solicitacao === false) {
        $resultado = DECISAO_SOLICITACAO_INEXISTENTE;
    } elseif ($erros) {
        $resultado = null;
    } else {
        $resultado = decidir_solicitacao_adocao($conn, $id_solicitacao, $decisao, $justificativa,
                                                (int) $usuario_logado['id_usuario']);
    }

    if ($resultado === DECISAO_REGISTRADA) {
        $_SESSION['mensagem_analise'] = $decisao === 'APROVAR'
            ? 'Solicitação aprovada. O animal foi marcado como adotado.'
            : 'Solicitação reprovada. O animal voltou a ficar disponível.';
        header('Location: ../../admin/avaliar/solicitacao.php?id=' . $id_solicitacao, true, 303);
        exit;
    }

    $recusas = [
        DECISAO_SOLICITACAO_INEXISTENTE => [404, 'Solicitação não encontrada.'],
        DECISAO_JA_DECIDIDA => [409, 'Esta solicitação já foi encerrada (aprovada, reprovada ou cancelada) e não pode ser alterada.'],
        DECISAO_ADOTANTE_INVALIDO => [409, 'Não é possível aprovar esta solicitação porque o adotante não está mais ativo como ADOTANTE. Reprove a solicitação para liberar o animal.'],
        DECISAO_INCONSISTENTE => [409, 'Não foi possível registrar a decisão: a situação do animal não corresponde à solicitação. Nenhuma alteração foi feita.'],
        DECISAO_AVALIADOR_SEM_PERMISSAO => [403, 'Você não tem permissão para analisar solicitações.'],
        DECISAO_AUTOAVALIACAO => [403, 'Você não pode analisar sua própria solicitação de adoção.'],
    ];
    if ($resultado === null) {
        [$codigo, $mensagens] = [400, $erros];
    } else {
        [$codigo, $mensagem] = $recusas[$resultado] ?? [500, 'Não foi possível registrar a decisão. Tente novamente.'];
        $mensagens = [$mensagem];
    }
    http_response_code($codigo);
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
    <title>Solicitações de adoção - Patas &amp; Lares</title>
</head>
<body id="adm">
    <header>
        <span>
            <a href="../../index.php">Patas &amp; Lares</a>
        </span>
    </header>

    <main>
        <div class="alert danger">
            <i class="fas fa-times"></i>
            <?php foreach ($mensagens as $mensagem) { ?>
                <p class="lead"><?= e($mensagem) ?></p>
            <?php } ?>
            <?php if ($id_solicitacao !== false) { ?>
            <p><a href="../../admin/avaliar/solicitacao.php?id=<?= e($id_solicitacao) ?>">Voltar à solicitação</a></p>
            <?php } ?>
            <p><a href="../../admin/solicitacoes.php">Voltar às solicitações</a></p>
        </div>
    </main>

    <footer>
        <div id="baixo">
			<div class="centro">
				<span class="credito">&copy; 2026 Patas &amp; Lares · Projeto acadêmico</span>
			</div>
		</div>
    </footer>
</body>
</html>
