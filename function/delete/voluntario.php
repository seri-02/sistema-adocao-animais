<?php
    require_once __DIR__ . '/../../lib/auth.php';
    // Equipe (ADMIN/FUNCIONARIO)
    $usuario_logado = exigir_perfis(PERFIS_EQUIPE);

    require_once __DIR__ . '/../../lib/conn.php';
    require_once __DIR__ . '/../../lib/escape.php';
    require_once __DIR__ . '/../../lib/voluntario.php';

    // Id inválido ou inexistente nunca é anunciado como exclusão bem-sucedida
    $id = ler_id_voluntario($_GET['id'] ?? null);
    $sucesso = false;
    $mensagem = 'Voluntário não encontrado.';

    if ($id !== false) {
        try {
            $stmt = $conn->prepare('DELETE FROM voluntario WHERE id_voluntario = :id_voluntario');
            $stmt->bindValue(':id_voluntario', $id, PDO::PARAM_INT);
            $stmt->execute();
            $sucesso = $stmt->rowCount() > 0;
            if ($sucesso) {
                $mensagem = 'Voluntário deletado com sucesso!';
            }
        } catch (PDOException $e) {
            error_log('Falha ao excluir voluntário: ' . $e->getMessage());
            $mensagem = 'Não foi possível excluir o voluntário.';
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
    <meta http-equiv="refresh" content="2; url=../../admin/tabelas/tabela_voluntario.php">
    <title>Voluntário - Patas &amp; Lares</title>
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
            <p><a href="../../admin/tabelas/tabela_voluntario.php">Voltar para voluntários</a></p>
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
