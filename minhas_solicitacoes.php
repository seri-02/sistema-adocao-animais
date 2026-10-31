<?php
    require_once __DIR__ . '/lib/auth.php';
    require_once __DIR__ . '/lib/escape.php';
    // Somente ADOTANTE; perfil e situação são conferidos no banco a cada acesso
    $usuario_logado = exigir_perfis([PERFIL_ADOTANTE], 'login.php');

    require_once __DIR__ . '/lib/conn.php';
    require_once __DIR__ . '/lib/animal.php';
    require_once __DIR__ . '/lib/solicitacao.php';

    // O adotante consultado é SEMPRE o da sessão validada; parâmetros da URL são ignorados
    $solicitacoes = listar_solicitacoes_do_adotante($conn, (int) $usuario_logado['id_usuario']);

    $mensagem = $_SESSION['mensagem_solicitacao'] ?? null;
    unset($_SESSION['mensagem_solicitacao']);

    $usuario_menu = $usuario_logado;
    $item_conta_atual = 'minhas_solicitacoes.php';

    // Apresentação por situação: texto curto, rótulo da data de encerramento e da justificativa
    $mensagens_status = [
        'PENDENTE' => 'Sua solicitação está aguardando análise da equipe.',
        'APROVADA' => 'Sua solicitação foi aprovada.',
        'REPROVADA' => 'Sua solicitação foi reprovada.',
        'CANCELADA' => 'Você cancelou esta solicitação.',
    ];
    $rotulos_encerramento = ['APROVADA' => 'Aprovada em', 'REPROVADA' => 'Reprovada em', 'CANCELADA' => 'Cancelada em'];
    // Cancelamento não tem justificativa da equipe: só decisões mostram o texto
    $rotulos_justificativa = ['APROVADA' => 'Observação da equipe', 'REPROVADA' => 'Motivo'];

    // "31/10/2026 às 14:57" a partir do formato padrão d/m/Y H:i
    $data_hora = fn(?string $data): string => str_replace(' ', ' às ', formatar_data_hora($data));
    $tem_foto = fn(?string $imagem): bool => $imagem !== null && $imagem !== ''
        && is_file(PASTA_IMAGENS . '/' . basename($imagem));
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas solicitações - Patas &amp; Lares</title>
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23C86B4A'%3E%3Cellipse cx='12' cy='16' rx='5' ry='4.2'/%3E%3Ccircle cx='5.5' cy='10' r='2.2'/%3E%3Ccircle cx='9.5' cy='5.8' r='2.2'/%3E%3Ccircle cx='14.5' cy='5.8' r='2.2'/%3E%3Ccircle cx='18.5' cy='10' r='2.2'/%3E%3C/svg%3E">
    <link rel="stylesheet" href="css/minhas-solicitacoes.css">
    <!-- Sem JavaScript o menu mobile permanece aberto; com JavaScript ele vira recolhível -->
    <script>document.documentElement.classList.add('js')</script>
    <!-- Mesmo menu recolhível da Home (depende só dos ids btn-mobile e nav) -->
    <script src="js/home.js" defer></script>
</head>
<body class="minhas-solicitacoes">
    <a class="pular-conteudo" href="#conteudo">Pular para o conteúdo</a>

    <header class="topo">
        <div class="container topo-barra">
            <a href="index.php" class="marca">
                <svg class="marca-icone" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <ellipse cx="12" cy="16" rx="5" ry="4.2"/>
                    <circle cx="5.5" cy="10" r="2.2"/>
                    <circle cx="9.5" cy="5.8" r="2.2"/>
                    <circle cx="14.5" cy="5.8" r="2.2"/>
                    <circle cx="18.5" cy="10" r="2.2"/>
                </svg>
                Patas &amp; Lares
            </a>

            <button id="btn-mobile" class="menu-botao" type="button" aria-expanded="false" aria-controls="nav">
                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <path d="M4 7h16M4 12h16M4 17h16"/>
                </svg>
                Menu
            </button>

            <nav id="nav" class="navegacao" aria-label="Navegação principal">
                <ul id="menu" class="nav-links">
                    <li><a href="index.php">Início</a></li>
                    <li><a href="adocoes.php">Animais</a></li>
                    <li><a href="index.php#como-adotar">Como adotar</a></li>
                    <li><a href="seja_voluntario.php">Voluntariado</a></li>
                </ul>
                <ul class="nav-conta">
                    <?php require __DIR__ . '/parciais/menu_conta.php'; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main id="conteudo" class="container">
        <div class="pagina-topo">
            <div>
                <h1>Minhas solicitações</h1>
                <p>Acompanhe o andamento dos seus pedidos de adoção.</p>
            </div>
            <?php if ($solicitacoes) { ?>
            <a class="botao botao-secundario" href="adocoes.php">Ver animais disponíveis</a>
            <?php } ?>
        </div>

        <?php if ($mensagem !== null) { ?>
        <div class="alerta" role="status">
            <span class="alerta-icone" aria-hidden="true">✓</span>
            <p><?= e($mensagem) ?></p>
        </div>
        <?php } ?>

        <?php if (!$solicitacoes) { ?>
        <section class="vazio">
            <h2>Você ainda não possui solicitações de adoção.</h2>
            <p>Conheça os animais disponíveis e encontre um novo companheiro.</p>
            <a class="botao botao-primario" href="adocoes.php">Ver animais disponíveis</a>
        </section>
        <?php } else { ?>
        <ul class="lista-solicitacoes">
            <?php foreach ($solicitacoes as $solicitacao) {
                $status = $solicitacao->status;
                $link_animal = 'gato.php?id=' . $solicitacao->id_animal;
                $justificativa = $solicitacao->justificativa !== null && trim($solicitacao->justificativa) !== ''
                    ? $solicitacao->justificativa : null;
            ?>
            <li>
                <article class="solicitacao">
                    <div class="sol-foto">
                        <?php if ($tem_foto($solicitacao->imagem)) { ?>
                        <img src="image/<?= e($solicitacao->imagem) ?>" alt="Foto de <?= e($solicitacao->nome) ?>">
                        <?php } else { ?>
                        <span class="sem-foto">Foto não disponível</span>
                        <?php } ?>
                    </div>

                    <div class="sol-corpo">
                        <div class="sol-topo">
                            <div>
                                <h2 class="sol-nome"><a href="<?= e($link_animal) ?>"><?= e($solicitacao->nome) ?></a></h2>
                                <p class="sol-especie"><?= e(rotulo(ESPECIES, $solicitacao->especie)) ?></p>
                            </div>
                            <span class="badge badge-<?= e(strtolower($status)) ?>"><?= e(rotulo(STATUS_SOLICITACAO, $status)) ?></span>
                        </div>

                        <?php if (isset($mensagens_status[$status])) { ?>
                        <p class="sol-mensagem"><?= e($mensagens_status[$status]) ?></p>
                        <?php } ?>

                        <dl class="sol-datas">
                            <div>
                                <dt>Solicitado em</dt>
                                <dd><?= e($data_hora($solicitacao->data_solicitacao)) ?></dd>
                            </div>
                            <?php if (isset($rotulos_encerramento[$status]) && $solicitacao->data_decisao !== null) { ?>
                            <div>
                                <dt><?= e($rotulos_encerramento[$status]) ?></dt>
                                <dd><?= e($data_hora($solicitacao->data_decisao)) ?></dd>
                            </div>
                            <?php } ?>
                        </dl>

                        <?php if (isset($rotulos_justificativa[$status]) && $justificativa !== null) { ?>
                        <dl class="sol-texto">
                            <dt><?= e($rotulos_justificativa[$status]) ?></dt>
                            <dd class="texto-livre"><?= e($justificativa) ?></dd>
                        </dl>
                        <?php } ?>
                    </div>

                    <div class="sol-acoes">
                        <a class="botao botao-secundario" href="<?= e($link_animal) ?>">Ver animal<span class="sr-only"> <?= e($solicitacao->nome) ?></span></a>
                        <?php if ($status === 'PENDENTE') { ?>
                        <!-- Só o id da solicitação é enviado: o dono e o animal são conferidos no servidor -->
                        <form action="function/update/func_cancelar_solicitacao.php" method="post"
                              onsubmit="return confirm('Tem certeza de que deseja cancelar esta solicitação?');">
                            <input type="hidden" name="id_solicitacao" value="<?= e($solicitacao->id_solicitacao) ?>">
                            <button type="submit" class="botao-cancelar">Cancelar solicitação<span class="sr-only"> de <?= e($solicitacao->nome) ?></span></button>
                        </form>
                        <?php } ?>
                    </div>
                </article>
            </li>
            <?php } ?>
        </ul>
        <?php } ?>
    </main>

    <footer class="rodape">
        <div class="container rodape-grade">
            <div>
                <p class="rodape-marca">Patas &amp; Lares</p>
                <p>Sistema demonstrativo para gerenciamento de adoção de animais.</p>
                <p>Tatuí - SP</p>
            </div>
            <nav aria-label="Links do rodapé">
                <ul class="rodape-links">
                    <li><a href="adocoes.php">Animais</a></li>
                    <li><a href="seja_voluntario.php">Voluntariado</a></li>
                    <?php require __DIR__ . '/parciais/menu_conta.php'; ?>
                </ul>
            </nav>
        </div>
        <div class="container rodape-base">
            <p>&copy; 2026 Patas &amp; Lares · Projeto acadêmico</p>
            <p>Desenvolvido por Jonathas e Gloria</p>
        </div>
    </footer>
</body>
</html>
