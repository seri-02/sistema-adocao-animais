<?php
    require_once __DIR__ . '/../../lib/auth.php';
    require_once __DIR__ . '/../../lib/escape.php';
    require_once __DIR__ . '/../../lib/animal.php';
    $usuario_logado = exigir_perfis(PERFIS_EQUIPE);

    require_once __DIR__ . '/../../lib/conn.php';

    $pagina_admin_atual = 'animais';

    // Só inteiro positivo: ausente, vazio, texto, decimal ou array nunca viram outro id
    $id_recebido = $_GET['id'] ?? null;
    $id = is_string($id_recebido)
        ? filter_var($id_recebido, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])
        : false;

    $animal = null;
    if ($id !== false) {
        $sql = "SELECT * FROM animal WHERE id_animal = :id_animal";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id_animal", $id, PDO::PARAM_INT);
        $stmt->execute();
        $animal = $stmt->fetch(PDO::FETCH_OBJ) ?: null;
    }
    if ($animal === null) {
        http_response_code(404);
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $animal === null ? 'Animal não encontrado' : 'Editar animal' ?> - Área administrativa - Patas &amp; Lares</title>
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23C86B4A'%3E%3Cellipse cx='12' cy='16' rx='5' ry='4.2'/%3E%3Ccircle cx='5.5' cy='10' r='2.2'/%3E%3Ccircle cx='9.5' cy='5.8' r='2.2'/%3E%3Ccircle cx='14.5' cy='5.8' r='2.2'/%3E%3Ccircle cx='18.5' cy='10' r='2.2'/%3E%3C/svg%3E">
    <link rel="stylesheet" href="../../css/admin-base.css">
    <link rel="stylesheet" href="../../css/admin-animais.css">
    <!-- Sem JavaScript a navegação fica sempre visível; com JavaScript vira menu recolhível no mobile -->
    <script>document.documentElement.classList.add('js')</script>
    <script src="../../js/admin.js" defer></script>
</head>
<body class="admin">
    <a class="pular-conteudo" href="#conteudo">Pular para o conteúdo</a>

    <div class="admin-layout">
        <?php require __DIR__ . '/../parciais/sidebar.php'; ?>

        <main id="conteudo" class="admin-conteudo">
            <a class="admin-voltar" href="../tabelas/tabela_gatos.php">
                <svg class="icone" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M15 6l-6 6 6 6"/></svg>
                Voltar para animais
            </a>

            <?php if ($animal === null) { ?>
            <header class="admin-cabecalho">
                <h1>Animal não encontrado</h1>
                <p>O animal informado não existe ou o endereço está incorreto. Volte para a lista e escolha um animal.</p>
            </header>
            <?php } else { ?>
            <header class="admin-cabecalho">
                <h1>Editar animal</h1>
                <p>Atualize as informações de <strong><?= e($animal->nome) ?></strong>.</p>
            </header>

            <form class="ani-form" action="../../function/update/func_editar_gato.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id_animal" value="<?= e($animal->id_animal) ?>">
                <?php require __DIR__ . '/../parciais/campos_animal.php'; ?>

                <div class="ani-acoes">
                    <button class="botao botao-primario" type="submit">Salvar alterações</button>
                    <a class="botao botao-secundario" href="../tabelas/tabela_gatos.php">Cancelar</a>
                </div>
            </form>
            <?php } ?>
        </main>
    </div>
</body>
</html>
