<?php
    require_once __DIR__ . '/../../lib/auth.php';
    require_once __DIR__ . '/../../lib/escape.php';
    $usuario_logado = exigir_perfis(PERFIS_EQUIPE);

    require_once __DIR__ . '/../../lib/conn.php';
    require_once __DIR__ . '/../../lib/animal.php';
    require_once __DIR__ . '/../../lib/solicitacao.php';

    $pagina_admin_atual = 'solicitacoes';

    $id = filter_var($_GET['id'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $solicitacao = $id === false ? null : buscar_solicitacao_equipe($conn, $id);
    if ($solicitacao === null) {
        http_response_code(404);
    }

    $mensagem = $_SESSION['mensagem_analise'] ?? null;
    unset($_SESSION['mensagem_analise']);

    // Tom do selo de cada situação (o texto do selo é que informa)
    $tons_status = ['PENDENTE' => 'atencao', 'APROVADA' => 'positivo', 'REPROVADA' => 'negativo', 'CANCELADA' => 'neutro'];

    if ($solicitacao === null) {
        $titulo = 'Solicitação não encontrada';
    } else {
        $titulo = $solicitacao->status === 'PENDENTE' ? 'Análise da solicitação' : 'Detalhes da solicitação';
        $justificativa = trim((string) $solicitacao->justificativa);
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titulo) ?> - Área administrativa - Patas &amp; Lares</title>
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23C86B4A'%3E%3Cellipse cx='12' cy='16' rx='5' ry='4.2'/%3E%3Ccircle cx='5.5' cy='10' r='2.2'/%3E%3Ccircle cx='9.5' cy='5.8' r='2.2'/%3E%3Ccircle cx='14.5' cy='5.8' r='2.2'/%3E%3Ccircle cx='18.5' cy='10' r='2.2'/%3E%3C/svg%3E">
    <link rel="stylesheet" href="../../css/admin-base.css">
    <link rel="stylesheet" href="../../css/admin-solicitacoes.css">
    <!-- Sem JavaScript a navegação fica sempre visível; com JavaScript vira menu recolhível no mobile -->
    <script>document.documentElement.classList.add('js')</script>
    <script src="../../js/admin.js" defer></script>
</head>
<body class="admin">
    <a class="pular-conteudo" href="#conteudo">Pular para o conteúdo</a>

    <div class="admin-layout">
        <?php require __DIR__ . '/../parciais/sidebar.php'; ?>

        <main id="conteudo" class="admin-conteudo">
            <a class="sol-voltar" href="../solicitacoes.php">
                <svg class="icone" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M15 6l-6 6 6 6"/></svg>
                Voltar para solicitações
            </a>

            <?php if ($mensagem !== null) { ?>
            <div class="alerta-sucesso" role="status">
                <svg class="icone" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="9"/><path d="M8 12.5l2.5 2.5L16 9.5"/></svg>
                <p><?= e($mensagem) ?></p>
            </div>
            <?php } ?>

            <?php if ($solicitacao === null) { ?>
            <header class="admin-cabecalho">
                <h1>Solicitação não encontrada</h1>
                <p>A solicitação informada não existe ou o endereço está incorreto. Volte para a lista e escolha uma solicitação.</p>
            </header>
            <?php } else { ?>
            <header class="admin-cabecalho">
                <div class="sol-cabecalho-linha">
                    <h1><?= e($titulo) ?></h1>
                    <span class="badge badge-<?= e($tons_status[$solicitacao->status] ?? 'neutro') ?>"><?= e(rotulo(STATUS_SOLICITACAO, $solicitacao->status)) ?></span>
                </div>
                <p>
                    <?= $solicitacao->status === 'PENDENTE'
                        ? 'Confira os dados do animal e do adotante antes de registrar a decisão.'
                        : 'Esta solicitação já foi encerrada e pode apenas ser consultada.' ?>
                </p>
            </header>

            <div class="sol-detalhe">
                <section class="cartao sol-cartao" aria-labelledby="titulo-animal">
                    <h2 id="titulo-animal">Animal</h2>
                    <?php if ($solicitacao->animal_imagem) { ?>
                    <img class="sol-foto" src="../../image/<?= e($solicitacao->animal_imagem) ?>" alt="Foto de <?= e($solicitacao->animal_nome) ?>">
                    <?php } else { ?>
                    <div class="sol-foto sol-foto-vazia" aria-hidden="true">
                        <svg class="icone" viewBox="0 0 24 24" focusable="false"><circle cx="6" cy="10" r="2"/><circle cx="10" cy="6" r="2"/><circle cx="14" cy="6" r="2"/><circle cx="18" cy="10" r="2"/><path d="M12 12c-3 0-5 3-5 5.5S9 20 12 20s5-.5 5-2.5S15 12 12 12z"/></svg>
                    </div>
                    <?php } ?>
                    <dl class="sol-campos">
                        <div><dt>Nome</dt><dd><?= e($solicitacao->animal_nome) ?></dd></div>
                        <div><dt>Espécie</dt><dd><?= e(rotulo(ESPECIES, $solicitacao->animal_especie)) ?></dd></div>
                        <div><dt>Sexo</dt><dd><?= e(rotulo(SEXOS, $solicitacao->animal_sexo)) ?></dd></div>
                        <div><dt>Idade</dt><dd><?= e(calcular_idade($solicitacao->animal_nascimento)) ?></dd></div>
                        <div><dt>Situação atual</dt><dd><?= e(rotulo(STATUS_ANIMAL, $solicitacao->animal_status)) ?></dd></div>
                    </dl>
                </section>

                <div class="sol-coluna">
                    <section class="cartao sol-cartao" aria-labelledby="titulo-adotante">
                        <h2 id="titulo-adotante">Adotante</h2>
                        <dl class="sol-campos">
                            <div><dt>Nome</dt><dd><?= e($solicitacao->adotante_nome) ?></dd></div>
                            <div><dt>E-mail</dt><dd><?= e($solicitacao->adotante_email) ?></dd></div>
                            <div><dt>Telefone</dt><dd><?= e($solicitacao->adotante_telefone ?? NAO_INFORMADO) ?></dd></div>
                        </dl>
                    </section>

                    <section class="cartao sol-cartao" aria-labelledby="titulo-solicitacao">
                        <h2 id="titulo-solicitacao">Solicitação</h2>
                        <dl class="sol-campos">
                            <div><dt>Situação</dt><dd><?= e(rotulo(STATUS_SOLICITACAO, $solicitacao->status)) ?></dd></div>
                            <div><dt>Data da solicitação</dt><dd><?= e(formatar_data_hora($solicitacao->data_solicitacao)) ?></dd></div>
                            <?php if ($solicitacao->status === 'CANCELADA') { ?>
                            <div><dt>Encerramento</dt><dd>Cancelada pelo adotante</dd></div>
                            <div><dt>Data do cancelamento</dt><dd><?= e(formatar_data_hora($solicitacao->data_decisao)) ?></dd></div>
                            <?php } elseif ($solicitacao->status !== 'PENDENTE') { ?>
                            <div><dt>Avaliador</dt><dd><?= e($solicitacao->avaliador_nome ?? NAO_INFORMADO) ?></dd></div>
                            <div><dt>Data da decisão</dt><dd><?= e(formatar_data_hora($solicitacao->data_decisao)) ?></dd></div>
                            <?php if ($justificativa !== '') { ?>
                            <div><dt>Justificativa</dt><dd><?= nl2br(e($justificativa)) ?></dd></div>
                            <?php } elseif ($solicitacao->status === 'REPROVADA') { ?>
                            <div><dt>Justificativa</dt><dd><?= e(NAO_INFORMADO) ?></dd></div>
                            <?php } ?>
                            <?php } ?>
                        </dl>
                    </section>

                    <?php if ($solicitacao->status === 'PENDENTE' && (int) $solicitacao->id_adotante === (int) $usuario_logado['id_usuario']) { ?>
                    <p class="cartao sol-aviso">Você não pode analisar sua própria solicitação de adoção.</p>
                    <?php } elseif ($solicitacao->status === 'PENDENTE') { ?>
                    <section class="cartao sol-cartao sol-decisao" aria-labelledby="titulo-decisao">
                        <h2 id="titulo-decisao">Decisão</h2>
                        <!-- Só a decisão e a justificativa são enviadas: avaliador, status e datas são definidos no servidor -->
                        <form action="../../function/update/func_analisar_solicitacao.php" method="post">
                            <input type="hidden" name="id_solicitacao" value="<?= e($solicitacao->id_solicitacao) ?>">
                            <label for="justificativa">Justificativa</label>
                            <p class="sol-ajuda" id="justificativa-ajuda">Obrigatória para reprovar e opcional ao aprovar. Até <?= e(JUSTIFICATIVA_TAMANHO_MAXIMO) ?> caracteres.</p>
                            <textarea name="justificativa" id="justificativa" rows="5" maxlength="<?= e(JUSTIFICATIVA_TAMANHO_MAXIMO) ?>" aria-describedby="justificativa-ajuda"></textarea>
                            <div class="sol-botoes">
                                <button class="botao botao-aprovar" type="submit" name="decisao" value="APROVAR">Aprovar</button>
                                <button class="botao botao-reprovar" type="submit" name="decisao" value="REPROVAR">Reprovar</button>
                            </div>
                        </form>
                    </section>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>
        </main>
    </div>
</body>
</html>
