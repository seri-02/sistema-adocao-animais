<?php
    // Cancelamento de uma solicitação PENDENTE pelo próprio ADOTANTE. Somente POST.
    // Anônimo vai para o login; ADMIN/FUNCIONARIO recebem 403 (a equipe não cancela em
    // nome do adotante: ela reprova). Do POST só é lido id_solicitacao: id_adotante,
    // id_animal, status, id_avaliador, data_decisao e justificativa enviados são ignorados.
    // O adotante vem da sessão validada no banco e o animal, da própria solicitação.
    require_once __DIR__ . '/../../lib/auth.php';
    $usuario_logado = exigir_perfis([PERFIL_ADOTANTE], 'login.php');

    require_once __DIR__ . '/../../lib/conn.php';
    require_once __DIR__ . '/../../lib/escape.php';
    require_once __DIR__ . '/../../lib/solicitacao.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ../../minhas_solicitacoes.php');
        exit;
    }

    $id_solicitacao = filter_var($_POST['id_solicitacao'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

    $resultado = $id_solicitacao === false
        ? CANCELAMENTO_NAO_ENCONTRADA
        : cancelar_solicitacao_adocao($conn, $id_solicitacao, (int) $usuario_logado['id_usuario']);

    if ($resultado === CANCELAMENTO_REGISTRADO) {
        $_SESSION['mensagem_solicitacao'] = 'Solicitação cancelada. O animal voltou a ficar disponível para adoção.';
        header('Location: ../../minhas_solicitacoes.php', true, 303);
        exit;
    }

    $recusas = [
        CANCELAMENTO_NAO_ENCONTRADA => [404, 'Solicitação não encontrada.'],
        CANCELAMENTO_NAO_PENDENTE => [409, 'Esta solicitação não pode mais ser cancelada.'],
        CANCELAMENTO_ADOTANTE_INVALIDO => [403, 'Você não tem permissão para cancelar esta solicitação.'],
        CANCELAMENTO_INCONSISTENTE => [409, 'Não foi possível cancelar: a situação do animal não corresponde à solicitação. Nenhuma alteração foi feita.'],
    ];
    [$codigo, $mensagem] = $recusas[$resultado] ?? [500, 'Não foi possível cancelar a solicitação. Tente novamente.'];
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
    <title>Abrigo dos Gatos</title>
</head>
<body id="adm">
    <header>
        <span>
            <a href="../../index.php">Abrigo dos gatos</a>
        </span>
    </header>

    <main>
        <div class="alert danger">
            <i class="fas fa-times"></i>
            <p class="lead"><?= e($mensagem) ?></p>
            <p><a href="../../minhas_solicitacoes.php">Voltar às minhas solicitações</a></p>
        </div>
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
