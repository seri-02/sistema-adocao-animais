<?php
    require_once __DIR__ . '/../lib/auth.php';
    require_once __DIR__ . '/../lib/escape.php';
    $usuario_logado = exigir_perfis(PERFIS_EQUIPE);

    require_once __DIR__ . '/../lib/conn.php';
    require_once __DIR__ . '/../lib/animal.php';
    require_once __DIR__ . '/../lib/solicitacao.php';

    $pagina_admin_atual = 'solicitacoes';

    // Filtro simples por situação; valor desconhecido mostra todas
    $filtro = is_string($_GET['status'] ?? null) && array_key_exists($_GET['status'], STATUS_SOLICITACAO) ? $_GET['status'] : null;
    $solicitacoes = listar_solicitacoes_equipe($conn, $filtro);
    $pendentes = contar_solicitacoes_pendentes($conn);

    // Tom do selo de cada situação (o texto do selo é que informa)
    $tons_status = ['PENDENTE' => 'atencao', 'APROVADA' => 'positivo', 'REPROVADA' => 'negativo', 'CANCELADA' => 'neutro'];

    $filtros = ['' => 'Todas'] + STATUS_SOLICITACAO;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitações de adoção - Área administrativa - Patas &amp; Lares</title>
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23C86B4A'%3E%3Cellipse cx='12' cy='16' rx='5' ry='4.2'/%3E%3Ccircle cx='5.5' cy='10' r='2.2'/%3E%3Ccircle cx='9.5' cy='5.8' r='2.2'/%3E%3Ccircle cx='14.5' cy='5.8' r='2.2'/%3E%3Ccircle cx='18.5' cy='10' r='2.2'/%3E%3C/svg%3E">
    <link rel="stylesheet" href="../css/admin-base.css">
    <link rel="stylesheet" href="../css/admin-solicitacoes.css">
    <!-- Sem JavaScript a navegação fica sempre visível; com JavaScript vira menu recolhível no mobile -->
    <script>document.documentElement.classList.add('js')</script>
    <script src="../js/admin.js" defer></script>
</head>
<body class="admin">
    <a class="pular-conteudo" href="#conteudo">Pular para o conteúdo</a>

    <div class="admin-layout">
        <?php require __DIR__ . '/parciais/sidebar.php'; ?>

        <main id="conteudo" class="admin-conteudo">
            <header class="admin-cabecalho">
                <h1>Solicitações de adoção</h1>
                <p>Acompanhe os pedidos de adoção e analise os que aguardam uma decisão da equipe.</p>
                <?php if ($pendentes === 0) { ?>
                <p class="sol-pendentes sol-pendentes-zero">Nenhuma solicitação pendente</p>
                <?php } else { ?>
                <p class="sol-pendentes"><?= e($pendentes) ?> <?= $pendentes === 1 ? 'solicitação pendente' : 'solicitações pendentes' ?></p>
                <?php } ?>
            </header>

            <!-- O filtro é feito no servidor pelo parâmetro ?status= -->
            <nav aria-label="Filtrar solicitações por situação">
                <ul class="sol-filtros">
                    <?php foreach ($filtros as $valor => $rotulo) { ?>
                    <?php $atual = ($filtro ?? '') === $valor; ?>
                    <li>
                        <a href="solicitacoes.php<?= $valor === '' ? '' : '?status=' . e($valor) ?>"<?= $atual ? ' aria-current="page"' : '' ?>><?= e($rotulo) ?></a>
                    </li>
                    <?php } ?>
                </ul>
            </nav>

            <section aria-labelledby="titulo-lista">
                <h2 id="titulo-lista" class="visualmente-oculto">
                    <?= e($filtro === null ? 'Todas as solicitações' : 'Solicitações com situação ' . rotulo(STATUS_SOLICITACAO, $filtro)) ?>
                </h2>

                <?php if (!$solicitacoes) { ?>
                <div class="cartao sol-vazio">
                    <p class="sol-vazio-titulo">Nenhuma solicitação encontrada</p>
                    <p><?= $filtro === null ? 'Ainda não existem solicitações de adoção.' : 'Não existem solicitações com este status no momento.' ?></p>
                </div>
                <?php } else { ?>
                <ul class="sol-lista">
                    <?php foreach ($solicitacoes as $solicitacao) { ?>
                    <?php
                        $status = $solicitacao->status;
                        $justificativa = trim((string) $solicitacao->justificativa);
                    ?>
                    <li class="cartao sol-item<?= $status === 'PENDENTE' ? ' sol-item-pendente' : '' ?>">
                        <?php if ($solicitacao->animal_imagem) { ?>
                        <img class="sol-miniatura" src="../image/<?= e($solicitacao->animal_imagem) ?>" alt="" width="72" height="72" loading="lazy">
                        <?php } else { ?>
                        <span class="sol-miniatura sol-miniatura-vazia" aria-hidden="true">
                            <svg class="icone" viewBox="0 0 24 24" focusable="false"><circle cx="6" cy="10" r="2"/><circle cx="10" cy="6" r="2"/><circle cx="14" cy="6" r="2"/><circle cx="18" cy="10" r="2"/><path d="M12 12c-3 0-5 3-5 5.5S9 20 12 20s5-.5 5-2.5S15 12 12 12z"/></svg>
                        </span>
                        <?php } ?>

                        <div>
                            <h3 class="sol-animal"><?= e($solicitacao->animal_nome) ?></h3>
                            <p class="sol-adotante">
                                <span>Adotante:</span> <strong><?= e($solicitacao->adotante_nome) ?></strong>
                                <span>· <?= e($solicitacao->adotante_email) ?></span>
                            </p>
                            <dl class="sol-dados">
                                <div>
                                    <dt>Solicitada em</dt>
                                    <dd><?= e(formatar_data_hora($solicitacao->data_solicitacao)) ?></dd>
                                </div>
                                <?php if ($status === 'CANCELADA') { ?>
                                <div>
                                    <dt>Cancelada pelo adotante em</dt>
                                    <dd><?= e(formatar_data_hora($solicitacao->data_decisao)) ?></dd>
                                </div>
                                <?php } elseif ($status !== 'PENDENTE') { ?>
                                <div>
                                    <dt>Decidida em</dt>
                                    <dd><?= e(formatar_data_hora($solicitacao->data_decisao)) ?></dd>
                                </div>
                                <div>
                                    <dt>Avaliador</dt>
                                    <dd><?= e($solicitacao->avaliador_nome ?? NAO_INFORMADO) ?></dd>
                                </div>
                                <?php } ?>
                            </dl>
                            <?php if ($justificativa !== '') { ?>
                            <p class="sol-justificativa"><strong>Justificativa:</strong> <?= e(mb_strimwidth($justificativa, 0, 140, '…')) ?></p>
                            <?php } ?>
                        </div>

                        <div class="sol-lateral">
                            <span class="badge badge-<?= e($tons_status[$status] ?? 'neutro') ?>"><?= e(rotulo(STATUS_SOLICITACAO, $status)) ?></span>
                            <a class="botao <?= $status === 'PENDENTE' ? 'botao-primario' : 'botao-secundario' ?>" href="avaliar/solicitacao.php?id=<?= e($solicitacao->id_solicitacao) ?>">
                                <?= $status === 'PENDENTE' ? 'Analisar' : 'Ver' ?><span class="visualmente-oculto"> a solicitação de <?= e($solicitacao->animal_nome) ?></span>
                            </a>
                        </div>
                    </li>
                    <?php } ?>
                </ul>
                <?php } ?>
            </section>
        </main>
    </div>
</body>
</html>
