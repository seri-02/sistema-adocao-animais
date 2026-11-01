<?php
    require_once __DIR__ . '/../../lib/auth.php';
    require_once __DIR__ . '/../../lib/escape.php';
    require_once __DIR__ . '/../../lib/animal.php';
    $usuario_logado = exigir_perfis(PERFIS_EQUIPE);

    require_once __DIR__ . '/../../lib/conn.php';

    $pagina_admin_atual = 'animais';

    $sql = "SELECT * FROM animal ORDER BY id_animal ASC";
    $stmt = $conn->query($sql);
    $animais = $stmt->fetchAll(PDO::FETCH_OBJ);

    // Tom do selo de cada situação (o texto do selo é que informa)
    $tons_status = ['DISPONIVEL' => 'positivo', 'EM_PROCESSO' => 'atencao', 'ADOTADO' => 'info', 'INDISPONIVEL' => 'neutro'];

    // Status cuja exclusão o servidor sempre recusa (function/delete/gato.php): a ação não é oferecida
    $status_sem_exclusao = ['ADOTADO', 'EM_PROCESSO'];

    // Miniatura só quando o arquivo existe em image/; senão, placeholder
    $tem_foto = fn(object $animal): bool => $animal->imagem !== null && $animal->imagem !== ''
        && $animal->imagem === basename($animal->imagem) && is_file(PASTA_IMAGENS . '/' . $animal->imagem);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Animais - Área administrativa - Patas &amp; Lares</title>
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
            <header class="admin-cabecalho ani-cabecalho">
                <div>
                    <h1>Animais</h1>
                    <p>Gerencie os animais cadastrados e acompanhe a situação de cada um.</p>
                </div>
                <a class="botao botao-primario" href="../gato.php">
                    <svg class="icone" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 5v14M5 12h14"/></svg>
                    Cadastrar animal
                </a>
            </header>

            <?php if (!$animais) { ?>
            <div class="cartao ani-vazio">
                <p class="ani-vazio-titulo">Nenhum animal cadastrado</p>
                <p>Cadastre um animal para começar a disponibilizá-lo no sistema.</p>
                <a class="botao botao-primario" href="../gato.php">Cadastrar animal</a>
            </div>
            <?php } else { ?>
            <section aria-labelledby="titulo-lista">
                <h2 id="titulo-lista" class="visualmente-oculto">Animais cadastrados</h2>
                <ul class="ani-lista">
                    <?php foreach ($animais as $animal) { ?>
                    <li class="cartao ani-item">
                        <?php if ($tem_foto($animal)) { ?>
                        <img class="ani-miniatura" src="../../image/<?= e($animal->imagem) ?>" alt="" width="96" height="72" loading="lazy">
                        <?php } else { ?>
                        <span class="ani-miniatura ani-sem-foto" aria-hidden="true">
                            <svg class="icone" viewBox="0 0 24 24" focusable="false"><circle cx="6" cy="10" r="2"/><circle cx="10" cy="6" r="2"/><circle cx="14" cy="6" r="2"/><circle cx="18" cy="10" r="2"/><path d="M12 12c-3 0-5 3-5 5.5S9 20 12 20s5-.5 5-2.5S15 12 12 12z"/></svg>
                        </span>
                        <?php } ?>

                        <div class="ani-info">
                            <div class="ani-titulo-linha">
                                <h3 class="ani-nome"><?= e($animal->nome) ?></h3>
                                <span class="badge badge-<?= e($tons_status[$animal->status] ?? 'neutro') ?>"><?= e(rotulo(STATUS_ANIMAL, $animal->status)) ?></span>
                            </div>
                            <dl class="ani-dados">
                                <div><dt>Espécie</dt><dd><?= e(rotulo(ESPECIES, $animal->especie)) ?></dd></div>
                                <div><dt>Sexo</dt><dd><?= e(rotulo(SEXOS, $animal->sexo)) ?></dd></div>
                                <div><dt>Idade</dt><dd><?= e(calcular_idade($animal->data_nascimento_estimada)) ?></dd></div>
                                <div><dt>Porte</dt><dd><?= e(rotulo(PORTES, $animal->porte)) ?></dd></div>
                            </dl>
                        </div>

                        <div class="ani-acoes-item">
                            <a class="botao botao-secundario" href="../editar/gato.php?id=<?= e($animal->id_animal) ?>">
                                <svg class="icone" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 20h4L19 9l-4-4L4 16v4z"/><path d="M13.5 6.5l4 4"/></svg>
                                Editar<span class="visualmente-oculto"> <?= e($animal->nome) ?></span>
                            </a>
                            <!-- Só esconde a ação: a regra de exclusão (status/histórico) continua no servidor -->
                            <?php if (!in_array($animal->status, $status_sem_exclusao, true)) { ?>
                            <a class="botao botao-excluir" href="../../function/delete/gato.php?id=<?= e($animal->id_animal) ?>">
                                <svg class="icone" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 7h16"/><path d="M10 11v6M14 11v6"/><path d="M6 7l1 13h10l1-13"/><path d="M9 7V4h6v3"/></svg>
                                Excluir<span class="visualmente-oculto"> <?= e($animal->nome) ?></span>
                            </a>
                            <?php } ?>
                        </div>
                    </li>
                    <?php } ?>
                </ul>
            </section>
            <?php } ?>
        </main>
    </div>
</body>
</html>
