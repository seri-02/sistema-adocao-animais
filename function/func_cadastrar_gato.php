<?php
    require_once __DIR__ . '/../lib/auth.php';
    $usuario_logado = exigir_perfis(PERFIS_EQUIPE);

    require_once __DIR__ . '/../lib/conn.php';
    require_once __DIR__ . '/../lib/escape.php';
    require_once __DIR__ . '/../lib/animal.php';

    [$dados, $erros] = ler_formulario_animal($_POST);

    $imagem = null;
    if (!$erros) {
        try {
            $imagem = enviar_imagem_animal($_FILES['imagem'] ?? [], $dados['nome']);
        } catch (Throwable $e) {
            $erros[] = 'Imagem inválida: envie um arquivo JPG, PNG ou GIF.';
        }
    }

    if (!$erros) {
        try {
            $sql = "INSERT INTO animal (nome, especie, sexo, data_nascimento_estimada, porte, castrado, vacinado,
                                        condicao_saude, comportamento, descricao, imagem, status)
                    VALUES (:nome, :especie, :sexo, :data_nascimento_estimada, :porte, :castrado, :vacinado,
                            :condicao_saude, :comportamento, :descricao, :imagem, 'DISPONIVEL')";
            $stmt = $conn->prepare($sql);
            vincular_campos_animal($stmt, $dados);
            $stmt->bindValue(':imagem', $imagem);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log('Falha ao cadastrar animal: ' . $e->getMessage());
            remover_imagem_sem_uso($conn, $imagem);
            $erros[] = 'Não foi possível cadastrar o animal.';
        }
    }
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
    <?php if (!$erros) { ?>
    <meta http-equiv="refresh" content="2; url=../admin/gato.php">
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
            <p class="lead">Animal cadastrado com sucesso!</p>
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