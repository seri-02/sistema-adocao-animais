<?php
    // Navegação da área administrativa (barra superior no mobile + sidebar).
    // Espera, definidos pela página ANTES do include:
    //   $usuario_logado      retorno de exigir_perfis(PERFIS_EQUIPE)
    //   $pagina_admin_atual  'painel' | 'solicitacoes' | 'animais' | 'voluntarios' | 'usuarios'
    // Só apresenta links: a autorização continua nas guardas de cada rota (lib/auth.php).
    require_once __DIR__ . '/../../lib/auth.php';
    require_once __DIR__ . '/../../lib/escape.php';
    require_once __DIR__ . '/../../lib/usuario.php';

    $pagina_admin_atual = $pagina_admin_atual ?? '';

    // [rótulo, arquivo a partir da raiz do projeto, ícone, somente ADMIN]
    $itens_admin = [
        'painel' => ['Visão geral', 'admin/painel.php', 'inicio', false],
        'solicitacoes' => ['Solicitações', 'admin/solicitacoes.php', 'solicitacoes', false],
        'animais' => ['Animais', 'admin/tabelas/tabela_gatos.php', 'animais', false],
        'voluntarios' => ['Voluntários', 'admin/tabelas/tabela_voluntario.php', 'voluntarios', false],
        'usuarios' => ['Usuários', 'admin/tabelas/tabela_usuario.php', 'usuarios', true],
    ];

    // Ícones decorativos (o texto do link é que informa)
    $icones_admin = [
        'inicio' => '<path d="M3 11l9-7 9 7"/><path d="M5 10v10h14V10"/><path d="M10 20v-6h4v6"/>',
        'solicitacoes' => '<rect x="5" y="4" width="14" height="17" rx="2"/><path d="M9 4h6v3H9z"/><path d="M9 12h6M9 16h4"/>',
        'animais' => '<circle cx="6" cy="10" r="2"/><circle cx="10" cy="6" r="2"/><circle cx="14" cy="6" r="2"/><circle cx="18" cy="10" r="2"/><path d="M12 12c-3 0-5 3-5 5.5S9 20 12 20s5-.5 5-2.5S15 12 12 12z"/>',
        'voluntarios' => '<path d="M12 20s-7-4.5-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.5-7 10-7 10z"/>',
        'usuarios' => '<circle cx="9" cy="8" r="3"/><path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6"/><circle cx="17" cy="9" r="2.5"/><path d="M16 14.2c2.9.4 5 2.7 5 5.8"/>',
        'site' => '<path d="M14 4h6v6"/><path d="M20 4l-9 9"/><path d="M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5"/>',
        'sair' => '<path d="M15 4h4a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1h-4"/><path d="M10 17l-5-5 5-5"/><path d="M5 12h11"/>',
    ];
    $icone_admin = fn(string $nome): string =>
        '<svg class="icone" viewBox="0 0 24 24" aria-hidden="true" focusable="false">' . $icones_admin[$nome] . '</svg>';

    $perfil_usuario = (string) ($usuario_logado['perfil'] ?? '');
    $rotulo_perfil = PERFIS[$perfil_usuario] ?? $perfil_usuario;
?>
<div class="admin-barra">
    <span class="admin-marca-curta">Patas &amp; Lares</span>
    <button type="button" class="admin-menu-botao" id="admin-menu-botao" aria-expanded="false" aria-controls="admin-sidebar">
        <svg class="icone" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
        Menu
    </button>
</div>

<aside class="admin-sidebar" id="admin-sidebar" aria-label="Área administrativa">
    <div class="admin-marca">
        <a href="<?= e(url_projeto('admin/painel.php')) ?>">Patas &amp; Lares</a>
        <span>Área administrativa</span>
    </div>

    <div class="admin-usuario">
        <span class="admin-usuario-nome"><?= e($usuario_logado['nome'] ?? '') ?></span>
        <span class="badge-perfil badge-perfil-<?= e(strtolower($perfil_usuario)) ?>"><?= e($rotulo_perfil) ?></span>
    </div>

    <nav class="admin-nav" aria-label="Navegação administrativa">
        <ul>
            <?php foreach ($itens_admin as $chave => [$rotulo, $arquivo, $icone, $somente_admin]) { ?>
            <?php if ($somente_admin && !eh_admin()) { continue; } ?>
            <li>
                <a href="<?= e(url_projeto($arquivo)) ?>"<?= $chave === $pagina_admin_atual ? ' aria-current="page"' : '' ?>>
                    <?= $icone_admin($icone) ?>
                    <?= e($rotulo) ?>
                </a>
            </li>
            <?php } ?>
        </ul>
    </nav>

    <div class="admin-sidebar-rodape">
        <a href="<?= e(url_projeto('index.php')) ?>"><?= $icone_admin('site') ?>Ver site</a>
        <a href="<?= e(url_projeto('function/sair.php')) ?>"><?= $icone_admin('sair') ?>Sair</a>
    </div>
</aside>
