<?php
    require_once __DIR__ . '/../../lib/auth.php';
    require_once __DIR__ . '/../../lib/escape.php';
    require_once __DIR__ . '/../../lib/usuario.php';
    $usuario_logado = exigir_perfis([PERFIL_ADMIN]);

    require_once __DIR__ . '/../../lib/conn.php';

    $pagina_admin_atual = 'usuarios';

    // Só inteiro positivo: ausente, vazio, texto, decimal, array ou overflow nunca viram outro id
    $id_recebido = $_GET['id'] ?? null;
    $id = is_string($id_recebido)
        ? filter_var($id_recebido, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])
        : false;

    $usuario = null;
    if ($id !== false) {
        // A senha (hash) nunca é lida para a tela
        $stmt = $conn->prepare("SELECT id_usuario, nome, email, telefone, perfil, ativo FROM usuario WHERE id_usuario = :id_usuario");
        $stmt->bindValue(':id_usuario', $id, PDO::PARAM_INT);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_OBJ) ?: null;
    }
    if ($usuario === null) {
        http_response_code(404);
    }

    // Na própria conta, perfil e situação não são editáveis (evita o administrador se bloquear)
    $propria_conta = $usuario !== null && (int) $usuario->id_usuario === (int) $usuario_logado['id_usuario'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $usuario === null ? 'Usuário não encontrado' : 'Editar usuário' ?> - Área administrativa - Patas &amp; Lares</title>
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
            <a class="admin-voltar" href="../tabelas/tabela_usuario.php">
                <svg class="icone" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M15 6l-6 6 6 6"/></svg>
                Voltar para usuários
            </a>

            <?php if ($usuario === null) { ?>
            <header class="admin-cabecalho">
                <h1>Usuário não encontrado</h1>
                <p>O usuário informado não existe ou o endereço está incorreto. Volte para a lista e escolha um usuário.</p>
            </header>
            <?php } else { ?>
            <header class="admin-cabecalho">
                <h1>Editar usuário</h1>
                <p>Atualize os dados e permissões de <strong><?= e($usuario->nome) ?></strong>.</p>
            </header>

            <form class="usu-form" action="../../function/update/func_editar_usuario.php" method="post">
                <input type="hidden" name="id_usuario" value="<?= e($usuario->id_usuario) ?>">
                <div class="cartao usu-cartao">
                    <?php require __DIR__ . '/../parciais/campos_usuario.php'; ?>

                    <section class="usu-subsecao" aria-labelledby="bloco-acesso">
                        <h2 id="bloco-acesso">Acesso</h2>
                        <div class="usu-grade">
                            <?php if ($propria_conta) { ?>
                            <div class="usu-campo usu-campo-largo">
                                <p class="usu-fixo">
                                    <span class="badge badge-perfil-<?= e(strtolower($usuario->perfil)) ?>"><?= e(PERFIS[$usuario->perfil] ?? $usuario->perfil) ?></span>
                                    <span class="badge badge-positivo">Ativo</span>
                                </p>
                                <p class="usu-ajuda">Esta é a sua conta: não é possível alterar o próprio perfil nem desativar a própria conta.</p>
                            </div>
                            <?php } else { ?>
                            <div class="usu-campo">
                                <label for="perfil">Perfil</label>
                                <select name="perfil" id="perfil" required>
                                    <?php foreach (PERFIS as $perfil => $rotulo) { ?>
                                    <option value="<?= e($perfil) ?>"<?= $usuario->perfil === $perfil ? ' selected' : '' ?>><?= e($rotulo) ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="usu-campo">
                                <label for="ativo">Situação</label>
                                <select name="ativo" id="ativo" required aria-describedby="ajuda-situacao">
                                    <option value="1"<?= (int) $usuario->ativo === 1 ? ' selected' : '' ?>>Ativo</option>
                                    <option value="0"<?= (int) $usuario->ativo === 0 ? ' selected' : '' ?>>Inativo</option>
                                </select>
                                <p class="usu-ajuda" id="ajuda-situacao">Contas inativas não conseguem entrar no sistema.</p>
                            </div>
                            <?php } ?>

                            <div class="usu-campo usu-campo-largo">
                                <label for="senha">Nova senha</label>
                                <input type="password" name="senha" id="senha" minlength="<?= e(SENHA_TAMANHO_MINIMO) ?>" autocomplete="new-password" aria-describedby="ajuda-senha">
                                <p class="usu-ajuda" id="ajuda-senha">Preencha somente se quiser definir uma nova senha (mínimo de <?= e(SENHA_TAMANHO_MINIMO) ?> caracteres). Em branco, a senha atual é mantida.</p>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="usu-acoes">
                    <button class="botao botao-primario" type="submit">Salvar alterações</button>
                    <a class="botao botao-secundario" href="../tabelas/tabela_usuario.php">Cancelar</a>
                </div>
            </form>
            <?php } ?>
        </main>
    </div>
</body>
</html>
