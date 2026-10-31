<?php
    require_once __DIR__ . '/../../lib/auth.php';
    $usuario_logado = exigir_perfis([PERFIL_ADMIN]);

    require_once __DIR__ . '/../../lib/conn.php';
    require_once __DIR__ . '/../../lib/escape.php';

    // Só inteiro positivo: ausente, vazio, texto, decimal, array ou overflow nunca viram outro id
    $id_recebido = $_GET['id'] ?? null;
    $id = is_string($id_recebido)
        ? filter_var($id_recebido, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])
        : false;
    $sucesso = false;
    $mensagem = 'Animal não encontrado.';

    if ($id !== false) {
        try {
            $stmt = $conn->prepare("DELETE FROM animal WHERE id_animal = :id_animal");
            $stmt->bindValue(':id_animal', $id, PDO::PARAM_INT);
            $stmt->execute();
            $sucesso = $stmt->rowCount() > 0;
            $mensagem = $sucesso ? 'Animal excluído com sucesso!' : 'Animal não encontrado.';
        } catch (PDOException $e) {
            // 1451: o animal é referenciado por adocoes ou solicitacao_adocao (FK ON DELETE RESTRICT)
            if ((int) ($e->errorInfo[1] ?? 0) === 1451) {
                $mensagem = 'Este animal possui histórico de adoção ou solicitações e não pode ser excluído.';
            } else {
                error_log('Falha ao excluir animal: ' . $e->getMessage());
                $mensagem = 'Não foi possível excluir o animal.';
            }
        }
    }
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
    <meta http-equiv="refresh" content="2; url=../../admin/tabelas/tabela_gatos.php">
    <title>Animais - Patas &amp; Lares</title>
</head>
<body id="adm">
    <header>
        <span>
            <a href="../../index.php">Patas &amp; Lares</a>
        </span>
    </header>
    
    <main>
        <div class="alert <?= $sucesso ? 'success' : 'danger' ?>">
                    <i class="<?= $sucesso ? 'far fa-check-circle' : 'fas fa-times' ?>"></i>
                    <p class="lead"><?= e($mensagem) ?></p>
                    <p><a href="../../admin/tabelas/tabela_gatos.php">Voltar para animais</a></p>
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