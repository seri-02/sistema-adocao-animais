<?php
    require_once __DIR__ . '/../lib/auth.php';
    require_once __DIR__ . '/../lib/escape.php';
    require_once __DIR__ . '/../lib/usuario.php';
    $usuario_logado = exigir_perfis([PERFIL_ADMIN]);

    $pagina_admin_atual = 'usuarios';
    $usuario = null;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar usuário - Área administrativa - Patas &amp; Lares</title>
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23C86B4A'%3E%3Cellipse cx='12' cy='16' rx='5' ry='4.2'/%3E%3Ccircle cx='5.5' cy='10' r='2.2'/%3E%3Ccircle cx='9.5' cy='5.8' r='2.2'/%3E%3Ccircle cx='14.5' cy='5.8' r='2.2'/%3E%3Ccircle cx='18.5' cy='10' r='2.2'/%3E%3C/svg%3E">
    <link rel="stylesheet" href="../css/admin-base.css">
    <link rel="stylesheet" href="../css/admin-usuarios.css">
    <!-- Sem JavaScript a navegação fica sempre visível; com JavaScript vira menu recolhível no mobile -->
    <script>document.documentElement.classList.add('js')</script>
    <script src="../js/admin.js" defer></script>
</head>
<body class="admin">
    <a class="pular-conteudo" href="#conteudo">Pular para o conteúdo</a>

    <div class="admin-layout">
        <?php require __DIR__ . '/parciais/sidebar.php'; ?>

        <main id="conteudo" class="admin-conteudo">
            <a class="admin-voltar" href="tabelas/tabela_usuario.php">
                <svg class="icone" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M15 6l-6 6 6 6"/></svg>
                Voltar para usuários
            </a>

            <header class="admin-cabecalho">
                <h1>Cadastrar usuário da equipe</h1>
                <p>Crie uma conta para um funcionário ou administrador.</p>
            </header>

            <form class="usu-form" action="../function/func_cadastrar_usuario.php" method="post">
                <div class="cartao usu-cartao">
                    <?php require __DIR__ . '/parciais/campos_usuario.php'; ?>

                    <section class="usu-subsecao" aria-labelledby="bloco-acesso">
                        <h2 id="bloco-acesso">Acesso</h2>
                        <div class="usu-grade">
                            <div class="usu-campo">
                                <label for="perfil">Perfil</label>
                                <!-- Só perfis da equipe: adotantes se cadastram pelo site -->
                                <select name="perfil" id="perfil" required>
                                    <?php foreach (PERFIS_CADASTRO_ADMIN as $perfil) { ?>
                                    <option value="<?= e($perfil) ?>"><?= e(PERFIS[$perfil]) ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="usu-campo">
                                <label for="senha">Senha</label>
                                <input type="password" name="senha" id="senha" minlength="<?= e(SENHA_TAMANHO_MINIMO) ?>" autocomplete="new-password" required aria-describedby="ajuda-senha">
                                <p class="usu-ajuda" id="ajuda-senha">Mínimo de <?= e(SENHA_TAMANHO_MINIMO) ?> caracteres.</p>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="usu-acoes">
                    <button class="botao botao-primario" type="submit">Cadastrar usuário</button>
                    <a class="botao botao-secundario" href="tabelas/tabela_usuario.php">Cancelar</a>
                </div>
            </form>
        </main>
    </div>
</body>
</html>
