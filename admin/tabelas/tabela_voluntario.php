<?php
    require_once __DIR__ . '/../../lib/auth.php';
    require_once __DIR__ . '/../../lib/escape.php';
    $usuario_logado = exigir_perfis(PERFIS_EQUIPE);

    require_once __DIR__ . '/../../lib/conn.php';

    $pagina_admin_atual = 'voluntarios';

    $sql = "SELECT * FROM voluntario ORDER BY id_voluntario ASC";
    $stmt = $conn->query($sql);
    $voluntarios = $stmt->fetchAll(PDO::FETCH_OBJ);

    // Exibição apenas: o valor gravado não muda. Colunas aceitam NULL em registros antigos.
    $texto_ou_padrao = fn($valor): string => $valor === null || trim((string) $valor) === '' ? 'Não informado' : (string) $valor;
    $idade = function ($valor): string {
        $anos = filter_var($valor, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        return $anos === false ? 'Idade não informada' : $anos . ($anos === 1 ? ' ano' : ' anos');
    };
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voluntários - Área administrativa - Patas &amp; Lares</title>
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
            <header class="admin-cabecalho vol-cabecalho">
                <div>
                    <h1>Voluntários</h1>
                    <p>Gerencie os voluntários cadastrados e suas disponibilidades.</p>
                </div>
                <a class="botao botao-primario" href="../voluntario.php">
                    <svg class="icone" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 5v14M5 12h14"/></svg>
                    Cadastrar voluntário
                </a>
            </header>

            <?php if (!$voluntarios) { ?>
            <div class="cartao vol-vazio">
                <p class="vol-vazio-titulo">Nenhum voluntário cadastrado</p>
                <p>Cadastre um voluntário ou aguarde novos cadastros enviados pelo site.</p>
                <a class="botao botao-primario" href="../voluntario.php">Cadastrar voluntário</a>
            </div>
            <?php } else { ?>
            <section aria-labelledby="titulo-lista">
                <h2 id="titulo-lista" class="visualmente-oculto">Voluntários cadastrados</h2>
                <ul class="vol-lista">
                    <?php foreach ($voluntarios as $voluntario) { ?>
                    <?php $nome = $texto_ou_padrao($voluntario->nome); ?>
                    <li class="cartao vol-item">
                        <div class="vol-info">
                            <h3 class="vol-nome"><?= e($nome) ?></h3>
                            <p class="vol-email"><?= e($texto_ou_padrao($voluntario->email)) ?></p>
                            <dl class="vol-dados">
                                <div>
                                    <dt>Idade</dt>
                                    <dd><?= e($idade($voluntario->idade)) ?></dd>
                                </div>
                                <div>
                                    <dt>Disponibilidade</dt>
                                    <dd><?= e($texto_ou_padrao($voluntario->dia_disponivel)) ?></dd>
                                </div>
                            </dl>
                        </div>

                        <div class="vol-acoes-item">
                            <a class="botao botao-secundario" href="../editar/voluntario.php?id=<?= e($voluntario->id_voluntario) ?>">
                                <svg class="icone" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 20h4L19 9l-4-4L4 16v4z"/><path d="M13.5 6.5l4 4"/></svg>
                                Editar<span class="visualmente-oculto"> <?= e($nome) ?></span>
                            </a>
                            <a class="botao botao-excluir" href="../../function/delete/voluntario.php?id=<?= e($voluntario->id_voluntario) ?>">
                                <svg class="icone" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 7h16"/><path d="M10 11v6M14 11v6"/><path d="M6 7l1 13h10l1-13"/><path d="M9 7V4h6v3"/></svg>
                                Excluir<span class="visualmente-oculto"> <?= e($nome) ?></span>
                            </a>
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
