<?php
    // Abre uma solicitação de adoção. Somente ADOTANTE autenticado e ativo:
    // anônimo vai para o login; ADMIN/FUNCIONARIO recebem 403.
    // Do POST só é lido id_animal. Adotante vem da sessão validada no banco e o status
    // nasce sempre PENDENTE: id_adotante, status, id_avaliador etc. enviados são ignorados.
    require_once __DIR__ . '/../lib/auth.php';
    $usuario_logado = exigir_perfis([PERFIL_ADOTANTE], 'login.php');

    require_once __DIR__ . '/../lib/conn.php';
    require_once __DIR__ . '/../lib/escape.php';
    require_once __DIR__ . '/../lib/solicitacao.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ../adocoes.php');
        exit;
    }

    $id_animal = filter_var($_POST['id_animal'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

    $resultado = $id_animal === false
        ? SOLICITACAO_ANIMAL_INEXISTENTE
        : criar_solicitacao_adocao($conn, $id_animal, (int) $usuario_logado['id_usuario']);

    if ($resultado === SOLICITACAO_CRIADA) {
        $_SESSION['mensagem_solicitacao'] = 'Solicitação de adoção enviada com sucesso.';
        header('Location: ../minhas_solicitacoes.php', true, 303);
        exit;
    }

    $recusas = [
        SOLICITACAO_ANIMAL_INEXISTENTE => [404, 'Animal não encontrado.'],
        SOLICITACAO_ANIMAL_INDISPONIVEL => [409, 'Este animal não está mais disponível para solicitação.'],
    ];
    [$codigo, $mensagem] = $recusas[$resultado] ?? [500, 'Não foi possível enviar a solicitação. Tente novamente.'];
    http_response_code($codigo);
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
    <title>Solicitação de adoção - Patas &amp; Lares</title>
</head>
<body id="adm">
    <header>
        <span>
            <a href="../index.php">Patas &amp; Lares</a>
        </span>
    </header>

    <main>
        <div class="alert danger">
            <i class="fas fa-times"></i>
            <p class="lead"><?= e($mensagem) ?></p>
            <p><a href="../adocoes.php">Voltar ao catálogo</a></p>
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
