<?php
    require_once __DIR__ . '/../../lib/auth.php';
    require_once __DIR__ . '/../../lib/escape.php';
    require_once __DIR__ . '/../../lib/usuario.php';
    $usuario_logado = exigir_perfis([PERFIL_ADMIN]);

    require_once __DIR__ . '/../../lib/conn.php';

    $pagina_admin_atual = 'usuarios';

    // A senha (hash) nunca é lida para a tela
    $sql = "SELECT id_usuario, nome, email, telefone, perfil, ativo FROM usuario ORDER BY id_usuario ASC";
    $stmt = $conn->query($sql);
    $usuarios = $stmt->fetchAll(PDO::FETCH_OBJ);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuários - Área administrativa - Patas &amp; Lares</title>
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23C86B4A'%3E%3Cellipse cx='12' cy='16' rx='5' ry='4.2'/%3E%3Ccircle cx='5.5' cy='10' r='2.2'/%3E%3Ccircle cx='9.5' cy='5.8' r='2.2'/%3E%3Ccircle cx='14.5' cy='5.8' r='2.2'/%3E%3Ccircle cx='18.5' cy='10' r='2.2'/%3E%3C/svg%3E">
    <link rel="stylesheet" href="../../css/admin-base.css">
    <link rel="stylesheet" href="../../css/admin-usuarios.css">
    <!-- Sem JavaScript a navegação fica sempre visível; com JavaScript vira menu recolhível no mobile -->
    <script>document.documentElement.classList.add('js')</script>
    <script src="../../js/admin.js" defer></script>
</head>
<body class="admin">
    <a class="pular-conteudo" href="#conteudo">Pular para o conteúdo</a>

    <div class="admin-layout">
        <?php require __DIR__ . '/../parciais/sidebar.php'; ?>

        <main id="conteudo" class="admin-conteudo">
            <header class="admin-cabecalho usu-cabecalho">
                <div>
                    <h1>Usuários</h1>
                    <p>Gerencie as contas, perfis e acessos ao sistema.</p>
                </div>
                <a class="botao botao-primario" href="../usuario.php">
                    <svg class="icone" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 5v14M5 12h14"/></svg>
                    Cadastrar usuário
                </a>
            </header>

            <?php if (!$usuarios) { ?>
            <div class="cartao usu-vazio">
                <p class="usu-vazio-titulo">Nenhum usuário encontrado</p>
                <a class="botao botao-primario" href="../usuario.php">Cadastrar usuário</a>
            </div>
            <?php } else { ?>
            <section aria-labelledby="titulo-lista">
                <h2 id="titulo-lista" class="visualmente-oculto">Usuários cadastrados</h2>
                <ul class="usu-lista">
                    <?php foreach ($usuarios as $usuario) { ?>
                    <?php
                        $propria_conta = (int) $usuario->id_usuario === (int) $usuario_logado['id_usuario'];
                        $ativo = (int) $usuario->ativo === 1;
                        $telefone = trim((string) $usuario->telefone);
                    ?>
                    <li class="cartao usu-item<?= $ativo ? '' : ' usu-item-inativo' ?>">
                        <div class="usu-info">
                            <div class="usu-titulo-linha">
                                <h3 class="usu-nome"><?= e($usuario->nome) ?></h3>
                                <?php if ($propria_conta) { ?>
                                <span class="usu-voce">Sua conta</span>
                                <?php } ?>
                            </div>
                            <p class="usu-selos">
                                <span class="badge badge-perfil-<?= e(strtolower($usuario->perfil)) ?>"><?= e(PERFIS[$usuario->perfil] ?? $usuario->perfil) ?></span>
                                <span class="badge <?= $ativo ? 'badge-positivo' : 'badge-neutro' ?>"><?= $ativo ? 'Ativo' : 'Inativo' ?></span>
                            </p>
                            <dl class="usu-dados">
                                <div>
                                    <dt>E-mail</dt>
                                    <dd><?= e($usuario->email) ?></dd>
                                </div>
                                <div>
                                    <dt>Telefone</dt>
                                    <dd><?= $telefone === '' ? 'Não informado' : e($telefone) ?></dd>
                                </div>
                            </dl>
                        </div>

                        <div class="usu-acoes-item">
                            <a class="botao botao-secundario" href="../editar/usuario.php?id=<?= e($usuario->id_usuario) ?>">
                                <svg class="icone" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 20h4L19 9l-4-4L4 16v4z"/><path d="M13.5 6.5l4 4"/></svg>
                                Editar<span class="visualmente-oculto"> <?= e($usuario->nome) ?></span>
                            </a>
                            <!-- A própria conta nunca pode ser excluída (regra também aplicada no servidor);
                                 último ADMIN ativo e histórico de solicitações são verificados pelo endpoint -->
                            <?php if (!$propria_conta) { ?>
                            <a class="botao botao-excluir" href="../../function/delete/usuario.php?id=<?= e($usuario->id_usuario) ?>">
                                <svg class="icone" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 7h16"/><path d="M10 11v6M14 11v6"/><path d="M6 7l1 13h10l1-13"/><path d="M9 7V4h6v3"/></svg>
                                Excluir<span class="visualmente-oculto"> <?= e($usuario->nome) ?></span>
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
