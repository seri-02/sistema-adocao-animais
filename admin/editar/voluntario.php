<?php
    require_once __DIR__ . '/../../lib/auth.php';
    require_once __DIR__ . '/../../lib/escape.php';
    $usuario_logado = exigir_perfis(PERFIS_EQUIPE);

    require_once __DIR__ . '/../../lib/conn.php';
    require_once __DIR__ . '/../../lib/voluntario.php';

    $pagina_admin_atual = 'voluntarios';

    // Id inválido (vazio, texto, negativo, decimal, array) ou inexistente: sem formulário, sem warning
    $id = ler_id_voluntario($_GET['id'] ?? null);
    $voluntario = false;
    if ($id !== false) {
        $stmt = $conn->prepare('SELECT * FROM voluntario WHERE id_voluntario = :id_voluntario');
        $stmt->bindValue(':id_voluntario', $id, PDO::PARAM_INT);
        $stmt->execute();
        $voluntario = $stmt->fetch(PDO::FETCH_OBJ);
    }
    if (!$voluntario) {
        http_response_code(404);
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $voluntario ? 'Editar voluntário' : 'Voluntário não encontrado' ?> - Área administrativa - Patas &amp; Lares</title>
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23C86B4A'%3E%3Cellipse cx='12' cy='16' rx='5' ry='4.2'/%3E%3Ccircle cx='5.5' cy='10' r='2.2'/%3E%3Ccircle cx='9.5' cy='5.8' r='2.2'/%3E%3Ccircle cx='14.5' cy='5.8' r='2.2'/%3E%3Ccircle cx='18.5' cy='10' r='2.2'/%3E%3C/svg%3E">
    <link rel="stylesheet" href="../../css/admin-base.css">
    <link rel="stylesheet" href="../../css/admin-voluntarios.css">
    <!-- Sem JavaScript a navegação fica sempre visível; com JavaScript vira menu recolhível no mobile -->
    <script>document.documentElement.classList.add('js')</script>
    <script src="../../js/admin.js" defer></script>
</head>
<body class="admin">
    <a class="pular-conteudo" href="#conteudo">Pular para o conteúdo</a>

    <div class="admin-layout">
        <?php require __DIR__ . '/../parciais/sidebar.php'; ?>

        <main id="conteudo" class="admin-conteudo">
            <a class="admin-voltar" href="../tabelas/tabela_voluntario.php">
                <svg class="icone" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M15 6l-6 6 6 6"/></svg>
                Voltar para voluntários
            </a>

            <?php if (!$voluntario) { ?>
            <header class="admin-cabecalho">
                <h1>Voluntário não encontrado</h1>
                <p>O voluntário informado não existe ou o endereço está incorreto. Volte para a lista e escolha um voluntário.</p>
            </header>
            <?php } else { ?>
            <header class="admin-cabecalho">
                <h1>Editar voluntário</h1>
                <p>Atualize os dados de <strong><?= e($voluntario->nome ?? 'voluntário sem nome') ?></strong>.</p>
            </header>

            <form class="vol-form" action="../../function/update/func_editar_voluntario.php" method="post">
                <input type="hidden" id="id_voluntario" name="id_voluntario" value="<?= e($voluntario->id_voluntario) ?>">
                <?php require __DIR__ . '/../parciais/campos_voluntario.php'; ?>

                <div class="vol-acoes">
                    <button class="botao botao-primario" type="submit">Salvar alterações</button>
                    <a class="botao botao-secundario" href="../tabelas/tabela_voluntario.php">Cancelar</a>
                </div>
            </form>
            <?php } ?>
        </main>
    </div>
</body>
</html>
