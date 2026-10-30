<?php
    require_once __DIR__ . '/../../lib/auth.php';
    exigir_login();

    require_once __DIR__ . '/../../lib/conn.php';
    require_once __DIR__ . '/../../lib/escape.php';
    require_once __DIR__ . '/../../lib/animal.php';

    $id_animal = (int) ($_POST['id_animal'] ?? 0);
    [$dados, $erros] = ler_formulario_animal($_POST);

    $stmt = $conn->prepare('SELECT id_animal, imagem, status FROM animal WHERE id_animal = :id_animal');
    $stmt->bindValue(':id_animal', $id_animal, PDO::PARAM_INT);
    $stmt->execute();
    $atual = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$atual) {
        $erros = ['Animal não encontrado.'];
    }

    // ADOTADO / EM_PROCESSO pertencem ao workflow: o formulário não altera esses status
    $status = $atual ? $atual->status : null;
    if ($atual && in_array($atual->status, STATUS_ADMINISTRAVEIS, true)) {
        $status = (string) ($_POST['status'] ?? $atual->status);
        if (!in_array($status, STATUS_ADMINISTRAVEIS, true)) {
            $erros[] = 'Disponibilidade inválida.';
        }
    }

    $nova_imagem = null;
    $arquivo = $_FILES['imagem'] ?? ['error' => UPLOAD_ERR_NO_FILE];
    if (!$erros && $arquivo['error'] !== UPLOAD_ERR_NO_FILE) {
        try {
            $nova_imagem = enviar_imagem_animal($arquivo, $dados['nome']);
        } catch (Throwable $e) {
            $erros[] = 'Imagem inválida: envie um arquivo JPG, PNG ou GIF. Nenhuma alteração foi salva.';
        }
    }

    if (!$erros) {
        try {
            $sql = "UPDATE animal
                       SET nome = :nome, especie = :especie, sexo = :sexo,
                           data_nascimento_estimada = :data_nascimento_estimada, porte = :porte,
                           castrado = :castrado, vacinado = :vacinado, condicao_saude = :condicao_saude,
                           comportamento = :comportamento, descricao = :descricao, imagem = :imagem,
                           status = CASE WHEN status IN ('DISPONIVEL', 'INDISPONIVEL') THEN :status ELSE status END
                     WHERE id_animal = :id_animal";
            $stmt = $conn->prepare($sql);
            vincular_campos_animal($stmt, $dados);
            $stmt->bindValue(':imagem', $nova_imagem ?? $atual->imagem);
            $stmt->bindValue(':status', $status);
            $stmt->bindValue(':id_animal', $id_animal, PDO::PARAM_INT);
            $stmt->execute();

            // Só depois do UPDATE confirmado a imagem antiga deixa de ser necessária
            if ($nova_imagem !== null && $nova_imagem !== $atual->imagem) {
                remover_imagem_sem_uso($conn, $atual->imagem);
            }
        } catch (PDOException $e) {
            error_log('Falha ao editar animal: ' . $e->getMessage());
            remover_imagem_sem_uso($conn, $nova_imagem);
            $erros[] = 'Não foi possível atualizar o animal. Nenhuma alteração foi salva.';
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
    <?php if (!$erros) { ?>
    <meta http-equiv="refresh" content="2; url=../../admin/tabelas/tabela_gatos.php">
    <?php } ?>
    <title>Abrigo dos Gatos</title>
</head>
<body id="adm">
    <header>
        <span>
            <a href="../index.php">Abrigo dos gatos</a> 
        </span>
    </header>
    
    <main>
        <?php if (!$erros) { ?>
        <div class="alert success">
            <i class="far fa-check-circle"></i>
            <p class="lead">Animal atualizado com sucesso!</p>
        </div>
        <?php } else { ?>
        <div class="alert danger">
            <i class="fas fa-times"></i>
            <?php foreach ($erros as $erro) { ?>
                <p class="lead"><?= e($erro) ?></p>
            <?php } ?>
            <p><a href="javascript:history.back()">Voltar ao formulário</a></p>
        </div>
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