<?php
    // Fluxo LEGADO de adoção imediata (temporário até o workflow de solicitação da F3)
    require_once __DIR__ . '/../lib/auth.php';
    require_once __DIR__ . '/../lib/conn.php';
    require_once __DIR__ . '/../lib/escape.php';

    iniciar_sessao();
    if (isset($_GET['id'])) {
        $_SESSION['id_animal'] = (int) $_GET['id'];
    }
    $id_animal = (int) ($_SESSION['id_animal'] ?? 0);

    $nome_adotante = trim((string) ($_POST['nome_adotante'] ?? ''));
    $email_adotante = trim((string) ($_POST['email_adotante'] ?? ''));
    $telefone = trim((string) ($_POST['telefone'] ?? ''));

    $sucesso = false;
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $id_animal <= 0) {
        $mensagem = 'Escolha um animal no catálogo para adotar.';
    } elseif ($nome_adotante === '' || mb_strlen($nome_adotante) > 50
        || !filter_var($email_adotante, FILTER_VALIDATE_EMAIL) || mb_strlen($email_adotante) > 100
        || $telefone === '' || mb_strlen($telefone) > 20) {
        $mensagem = 'Preencha nome, e-mail e telefone válidos.';
    } else {
        try {
            $conn->beginTransaction();

            // A sessão só indica o animal escolhido: a disponibilidade é conferida aqui, com a linha travada
            $stmt = $conn->prepare('SELECT status FROM animal WHERE id_animal = :id_animal FOR UPDATE');
            $stmt->bindValue(':id_animal', $id_animal, PDO::PARAM_INT);
            $stmt->execute();
            $status = $stmt->fetchColumn();

            if ($status === false) {
                $conn->rollBack();
                $mensagem = 'Animal não encontrado.';
            } elseif ($status !== 'DISPONIVEL') {
                $conn->rollBack();
                $mensagem = 'Este animal não está disponível para adoção.';
            } else {
                $stmt = $conn->prepare('INSERT INTO adocoes (id_animal, nome_adotante, email_adotante, telefone)
                                        VALUES (:id_animal, :nome_adotante, :email_adotante, :telefone)');
                $stmt->bindValue(':id_animal', $id_animal, PDO::PARAM_INT);
                $stmt->bindValue(':nome_adotante', $nome_adotante);
                $stmt->bindValue(':email_adotante', $email_adotante);
                $stmt->bindValue(':telefone', $telefone);
                $stmt->execute();

                $stmt = $conn->prepare("UPDATE animal SET status = 'ADOTADO' WHERE id_animal = :id_animal");
                $stmt->bindValue(':id_animal', $id_animal, PDO::PARAM_INT);
                $stmt->execute();

                $conn->commit();
                $sucesso = true;
                $mensagem = 'Animal adotado com sucesso!';
            }
        } catch (Throwable $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            error_log('Falha na adoção: ' . $e->getMessage());
            $mensagem = 'Não foi possível registrar a adoção. Tente novamente.';
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
    <meta http-equiv="refresh" content="5; url=../adocoes.php">
    <title>Abrigo dos Gatos</title>
</head>
<body id="adm">
    <header>
        <span>
            <a href="index.php">Abrigo dos gatos</a> 
        </span>
    </header>
    
    <main>
        <?php if ($sucesso) { ?>
        <div class="alert success">
            <img src="../img/animation_200_l9is3tof.gif" alt="">
            <i class="far fa-check-circle"></i>
            <p class="lead"><?= e($mensagem) ?></p>
        </div>
        <?php } else { ?>
        <div class="alert danger">
            <i class="fas fa-times"></i>
            <p class="lead"><?= e($mensagem) ?></p>
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