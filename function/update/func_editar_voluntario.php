<?php
    // Edição de voluntário pela equipe (ADMIN/FUNCIONARIO). Somente POST.
    // Só id_voluntario, nome, email, idade e dia_disponivel são lidos; qualquer outro campo
    // enviado (inclusive algo como _SESSION[...]) é ignorado: nada do POST vira variável.
    require_once __DIR__ . '/../../lib/auth.php';
    $usuario_logado = exigir_perfis(PERFIS_EQUIPE);

    require_once __DIR__ . '/../../lib/conn.php';
    require_once __DIR__ . '/../../lib/escape.php';
    require_once __DIR__ . '/../../lib/voluntario.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ../../admin/tabelas/tabela_voluntario.php');
        exit;
    }

    $id_voluntario = ler_id_voluntario($_POST['id_voluntario'] ?? null);
    $erros = [];

    try {
        $existe = false;
        if ($id_voluntario !== false) {
            $stmt = $conn->prepare('SELECT COUNT(*) FROM voluntario WHERE id_voluntario = :id_voluntario');
            $stmt->bindValue(':id_voluntario', $id_voluntario, PDO::PARAM_INT);
            $stmt->execute();
            $existe = (int) $stmt->fetchColumn() === 1;
        }

        if (!$existe) {
            $erros[] = 'Voluntário não encontrado.';
        } else {
            [$dados, $erros] = ler_formulario_voluntario($_POST);
        }

        if (!$erros) {
            $stmt = $conn->prepare(
                'UPDATE voluntario
                    SET nome = :nome, email = :email, idade = :idade, dia_disponivel = :dia_disponivel
                  WHERE id_voluntario = :id_voluntario'
            );
            $stmt->bindValue(':nome', $dados['nome']);
            $stmt->bindValue(':email', $dados['email']);
            $stmt->bindValue(':idade', $dados['idade'], PDO::PARAM_INT);
            $stmt->bindValue(':dia_disponivel', $dados['dia_disponivel']);
            $stmt->bindValue(':id_voluntario', $id_voluntario, PDO::PARAM_INT);
            $stmt->execute();
        }
    } catch (PDOException $e) {
        error_log('Falha ao atualizar voluntário: ' . $e->getMessage());
        $erros = ['Não foi possível atualizar o voluntário. Nenhuma alteração foi salva.'];
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
    <meta http-equiv="refresh" content="2; url=../../admin/tabelas/tabela_voluntario.php">
    <?php } ?>
    <title>Voluntário - Patas &amp; Lares</title>
</head>
<body id="adm">
    <header>
        <span>
            <a href="../../index.php">Patas &amp; Lares</a>
        </span>
    </header>

    <main>
        <?php if (!$erros) { ?>
        <div class="alert success">
            <i class="far fa-check-circle"></i>
            <p class="lead">Voluntário atualizado com sucesso!</p>
            <p><a href="../../admin/tabelas/tabela_voluntario.php">Voltar para voluntários</a></p>
        </div>
        <?php } else { ?>
        <div class="alert danger">
            <i class="fas fa-times"></i>
            <?php foreach ($erros as $erro) { ?>
                <p class="lead"><?= e($erro) ?></p>
            <?php } ?>
            <p><a href="javascript:history.back()">Voltar ao formulário</a></p>
            <p><a href="../../admin/tabelas/tabela_voluntario.php">Voltar para voluntários</a></p>
        </div>
        <?php } ?>
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
