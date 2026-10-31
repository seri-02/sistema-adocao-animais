<?php
    require_once __DIR__ . '/../lib/auth.php';
    $usuario_logado = exigir_perfis(PERFIS_EQUIPE);

    require_once __DIR__ . '/../lib/conn.php';
    require_once __DIR__ . '/../lib/escape.php';
    require_once __DIR__ . '/../lib/animal.php';
    require_once __DIR__ . '/../lib/solicitacao.php';

    $pagina_admin_atual = 'painel';

    // Indicadores: contagens reais. Uma falha vira null ("—" na tela); o detalhe só vai para o log.
    $contar = function (callable $consulta): ?int {
        try {
            return $consulta();
        } catch (PDOException $e) {
            error_log('Falha ao carregar indicador do painel: ' . $e->getMessage());
            return null;
        }
    };

    $indicadores = [
        [
            'rotulo' => 'Solicitações pendentes',
            'valor' => $contar(fn() => contar_solicitacoes_pendentes($conn)),
            'link' => 'solicitacoes.php?status=PENDENTE',
            'acao' => 'Ver solicitações',
        ],
        [
            'rotulo' => 'Animais disponíveis',
            'valor' => $contar(fn() => contar_animais_por_status($conn, 'DISPONIVEL')),
            'link' => 'tabelas/tabela_gatos.php',
            'acao' => 'Ver animais',
        ],
        [
            'rotulo' => 'Voluntários cadastrados',
            'valor' => $contar(fn() => (int) $conn->query('SELECT COUNT(*) FROM voluntario')->fetchColumn()),
            'link' => 'tabelas/tabela_voluntario.php',
            'acao' => 'Ver voluntários',
        ],
    ];
    if (eh_admin()) {
        $indicadores[] = [
            'rotulo' => 'Usuários ativos',
            // ativo = 1: mesmo critério de lib/auth.php para conta ativa
            'valor' => $contar(fn() => (int) $conn->query('SELECT COUNT(*) FROM usuario WHERE ativo = 1')->fetchColumn()),
            'link' => 'tabelas/tabela_usuario.php',
            'acao' => 'Ver usuários',
        ];
    }

    $acoes_rapidas = [
        ['Cadastrar animal', 'gato.php'],
        ['Cadastrar voluntário', 'voluntario.php'],
    ];
    if (eh_admin()) {
        $acoes_rapidas[] = ['Cadastrar usuário', 'usuario.php'];
    }
    $acoes_rapidas[] = ['Ver solicitações', 'solicitacoes.php'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visão geral - Área administrativa - Patas &amp; Lares</title>
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23C86B4A'%3E%3Cellipse cx='12' cy='16' rx='5' ry='4.2'/%3E%3Ccircle cx='5.5' cy='10' r='2.2'/%3E%3Ccircle cx='9.5' cy='5.8' r='2.2'/%3E%3Ccircle cx='14.5' cy='5.8' r='2.2'/%3E%3Ccircle cx='18.5' cy='10' r='2.2'/%3E%3C/svg%3E">
    <link rel="stylesheet" href="../css/admin-base.css">
    <link rel="stylesheet" href="../css/admin-painel.css">
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
                <h1>Visão geral</h1>
                <p>Acompanhe as principais informações e acesse rapidamente as rotinas do Patas &amp; Lares.</p>
            </header>

            <section aria-labelledby="titulo-indicadores">
                <h2 id="titulo-indicadores" class="admin-secao-titulo">Indicadores</h2>
                <ul class="indicadores">
                    <?php foreach ($indicadores as $indicador) { ?>
                    <li class="cartao indicador">
                        <span class="indicador-rotulo"><?= e($indicador['rotulo']) ?></span>
                        <?php if ($indicador['valor'] === null) { ?>
                        <span class="indicador-valor indicador-indisponivel">—</span>
                        <span class="indicador-aviso">Não foi possível carregar agora.</span>
                        <?php } else { ?>
                        <span class="indicador-valor"><?= e($indicador['valor']) ?></span>
                        <?php } ?>
                        <a class="indicador-link" href="<?= e($indicador['link']) ?>"><?= e($indicador['acao']) ?></a>
                    </li>
                    <?php } ?>
                </ul>
            </section>

            <section aria-labelledby="titulo-acoes">
                <h2 id="titulo-acoes" class="admin-secao-titulo">Ações rápidas</h2>
                <ul class="acoes-rapidas">
                    <?php foreach ($acoes_rapidas as [$rotulo, $link]) { ?>
                    <li><a class="botao botao-secundario" href="<?= e($link) ?>"><?= e($rotulo) ?></a></li>
                    <?php } ?>
                </ul>
            </section>
        </main>
    </div>
</body>
</html>
