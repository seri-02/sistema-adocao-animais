<?php
    require_once __DIR__ . '/../../lib/auth.php';
    // Equipe (ADMIN/FUNCIONARIO). Animal ADOTADO/EM_PROCESSO ou com histórico não é excluído por nenhum perfil.
    $usuario_logado = exigir_perfis(PERFIS_EQUIPE);

    require_once __DIR__ . '/../../lib/conn.php';
    require_once __DIR__ . '/../../lib/escape.php';

    // Só inteiro positivo: ausente, vazio, texto, decimal, array ou overflow nunca viram outro id
    $id_recebido = $_GET['id'] ?? null;
    $id = is_string($id_recebido)
        ? filter_var($id_recebido, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])
        : false;
    $sucesso = false;
    $mensagem = 'Animal não encontrado.';

    // Status que nunca permitem exclusão, para qualquer perfil (sem bypass para ADMIN):
    // ADOTADO é estado final/histórico; EM_PROCESSO é adoção em andamento.
    // DISPONIVEL/INDISPONIVEL podem ser excluídos se não houver histórico: a FK de
    // solicitacao_adocao (ON DELETE RESTRICT) continua como segunda barreira.
    $bloqueios_por_status = [
        'ADOTADO' => 'Este animal está adotado e não pode ser excluído.',
        'EM_PROCESSO' => 'Este animal possui uma adoção em andamento e não pode ser excluído.',
    ];

    if ($id !== false) {
        try {
            $conn->beginTransaction();

            // Trava a linha: o status conferido é o mesmo no momento do DELETE
            $stmt = $conn->prepare('SELECT status FROM animal WHERE id_animal = :id_animal FOR UPDATE');
            $stmt->bindValue(':id_animal', $id, PDO::PARAM_INT);
            $stmt->execute();
            $status = $stmt->fetchColumn();

            if ($status === false) {
                $conn->rollBack();
            } elseif (isset($bloqueios_por_status[$status])) {
                $conn->rollBack();
                $mensagem = $bloqueios_por_status[$status];
            } else {
                $stmt = $conn->prepare('DELETE FROM animal WHERE id_animal = :id_animal');
                $stmt->bindValue(':id_animal', $id, PDO::PARAM_INT);
                $stmt->execute();
                $conn->commit();
                $sucesso = true;
                $mensagem = 'Animal excluído com sucesso!';
            }
        } catch (PDOException $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            // 1451: o animal é referenciado por solicitacao_adocao (FK ON DELETE RESTRICT)
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