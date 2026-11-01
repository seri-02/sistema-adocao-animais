<?php
    require_once __DIR__ . '/../lib/auth.php';
    require_once __DIR__ . '/../lib/escape.php';
    require_once __DIR__ . '/../lib/animal.php';
    $usuario_logado = exigir_perfis(PERFIS_EQUIPE);

    $pagina_admin_atual = 'animais';
    $animal = null;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar animal - Área administrativa - Patas &amp; Lares</title>
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23C86B4A'%3E%3Cellipse cx='12' cy='16' rx='5' ry='4.2'/%3E%3Ccircle cx='5.5' cy='10' r='2.2'/%3E%3Ccircle cx='9.5' cy='5.8' r='2.2'/%3E%3Ccircle cx='14.5' cy='5.8' r='2.2'/%3E%3Ccircle cx='18.5' cy='10' r='2.2'/%3E%3C/svg%3E">
    <link rel="stylesheet" href="../css/admin-base.css">
    <link rel="stylesheet" href="../css/admin-animais.css">
    <!-- Sem JavaScript a navegação fica sempre visível; com JavaScript vira menu recolhível no mobile -->
    <script>document.documentElement.classList.add('js')</script>
    <script src="../js/admin.js" defer></script>
</head>
<body class="admin">
    <a class="pular-conteudo" href="#conteudo">Pular para o conteúdo</a>

    <div class="admin-layout">
        <?php require __DIR__ . '/parciais/sidebar.php'; ?>

        <main id="conteudo" class="admin-conteudo">
            <a class="admin-voltar" href="tabelas/tabela_gatos.php">
                <svg class="icone" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M15 6l-6 6 6 6"/></svg>
                Voltar para animais
            </a>

            <header class="admin-cabecalho">
                <h1>Cadastrar animal</h1>
                <p>Preencha as informações utilizadas para apresentar e acompanhar o animal no sistema.</p>
            </header>

            <form class="ani-form" action="../function/func_cadastrar_gato.php" method="post" enctype="multipart/form-data">
                <?php require __DIR__ . '/parciais/campos_animal.php'; ?>

                <div class="ani-acoes">
                    <button class="botao botao-primario" type="submit">Cadastrar animal</button>
                    <a class="botao botao-secundario" href="tabelas/tabela_gatos.php">Cancelar</a>
                </div>
            </form>
        </main>
    </div>
</body>
</html>
