<?php
    require_once __DIR__ . '/../../lib/auth.php';
    $usuario_logado = exigir_perfis([PERFIL_ADMIN]);

    require_once __DIR__ . '/../../lib/conn.php';
    require_once __DIR__ . '/../../lib/escape.php';
    require_once __DIR__ . '/../../lib/usuario.php';

    $id_alvo = (int) ($_GET['id'] ?? 0);
    $id_ator = (int) $usuario_logado['id_usuario'];
    $sucesso = false;

    if ($id_alvo === $id_ator) {
        $mensagem = 'Você não pode excluir a própria conta.';
    } else {
        try {
            $conn->beginTransaction();
            [$linhas, $admins_ativos] = travar_usuarios_para_alteracao($conn, $id_alvo, $id_ator);
            $alvo = $linhas[$id_alvo] ?? null;

            if ($alvo === null) {
                $mensagem = 'Usuário não encontrado.';
            } elseif (!eh_admin_ativo($linhas[$id_ator] ?? null)) {
                $mensagem = 'Acesso negado.';
            } elseif (eh_admin_ativo($alvo) && $admins_ativos <= 1) {
                $mensagem = 'O sistema precisa de pelo menos um administrador ativo.';
            } else {
                $stmt = $conn->prepare('DELETE FROM usuario WHERE id_usuario = :id_usuario');
                $stmt->bindValue(':id_usuario', $id_alvo, PDO::PARAM_INT);
                $stmt->execute();
                $sucesso = true;
                $mensagem = 'Usuário deletado com sucesso!';
            }

            $sucesso ? $conn->commit() : $conn->rollBack();
        } catch (PDOException $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            $sucesso = false;
            // 1451: o usuário é referenciado por solicitacao_adocao (FK ON DELETE RESTRICT)
            if ((int) ($e->errorInfo[1] ?? 0) === 1451) {
                $mensagem = 'Este usuário possui histórico de solicitações e não pode ser excluído.';
            } else {
                error_log('Falha ao excluir usuário: ' . $e->getMessage());
                $mensagem = 'Não foi possível excluir o usuário.';
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
    <meta http-equiv="refresh" content="2; url=../../admin/tabelas/tabela_usuario.php">
    <title>Abrigo dos Gatos</title>
</head>
<body id="adm">
    <header>
        <span>
            <a href="../index.php">Abrigo dos gatos</a> 
        </span>
    </header>
    
    <main>
        <div class="alert <?= $sucesso ? 'success' : 'danger' ?>">
                    <i class="<?= $sucesso ? 'far fa-check-circle' : 'fas fa-times' ?>"></i>
                    <p class="lead"><?= e($mensagem) ?></p>
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