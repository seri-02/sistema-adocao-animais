<?php
    require_once __DIR__ . '/../lib/auth.php';

    // Equipe já autenticada vai direto ao painel. Um adotante com sessão pública vê o formulário
    // normalmente: se entrar com uma conta da equipe, a sessão é substituída por inteiro no login.
    $usuario = usuario_logado_opcional();
    if ($usuario !== null && in_array($usuario['perfil'], PERFIS_EQUIPE, true)) {
        header('Location: painel.php');
        exit;
    }

    $erro = isset($_GET['erro']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso administrativo - Patas &amp; Lares</title>
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23C86B4A'%3E%3Cellipse cx='12' cy='16' rx='5' ry='4.2'/%3E%3Ccircle cx='5.5' cy='10' r='2.2'/%3E%3Ccircle cx='9.5' cy='5.8' r='2.2'/%3E%3Ccircle cx='14.5' cy='5.8' r='2.2'/%3E%3Ccircle cx='18.5' cy='10' r='2.2'/%3E%3C/svg%3E">
    <link rel="stylesheet" href="../css/admin-login.css">
    <!-- Mostrar/ocultar senha: mesmo script do login público (sem JavaScript o botão fica oculto) -->
    <script src="../js/auth.js" defer></script>
</head>
<body class="login-admin">
    <a class="pular-conteudo" href="#conteudo">Pular para o conteúdo</a>

    <main id="conteudo" class="login-pagina">
        <section class="login-cartao" aria-labelledby="titulo-login">
            <div class="login-marca">
                <svg class="login-pata" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><ellipse cx="12" cy="16" rx="5" ry="4.2"/><circle cx="5.5" cy="10" r="2.2"/><circle cx="9.5" cy="5.8" r="2.2"/><circle cx="14.5" cy="5.8" r="2.2"/><circle cx="18.5" cy="10" r="2.2"/></svg>
                <span>Patas &amp; Lares</span>
            </div>
            <p class="login-selo">Área administrativa</p>

            <h1 id="titulo-login">Acesso administrativo</h1>
            <p class="login-intro">Entre com sua conta da equipe.</p>

            <?php if ($erro) { ?>
            <!-- Mesma mensagem para e-mail inexistente, senha incorreta, conta inativa e perfil sem acesso -->
            <div class="login-erro" id="erro-login" role="alert">
                <span class="login-erro-icone" aria-hidden="true">!</span>
                <p>E-mail ou senha inválidos.</p>
            </div>
            <?php } ?>

            <form class="login-form" action="../function/logar.php" method="post"<?= $erro ? ' aria-describedby="erro-login"' : '' ?>>
                <div class="login-campo">
                    <label for="email">E-mail</label>
                    <input type="email" name="email" id="email" autocomplete="email" required>
                </div>
                <div class="login-campo">
                    <label for="senha">Senha</label>
                    <div class="campo-senha">
                        <input type="password" name="senha" id="senha" autocomplete="current-password" required>
                        <button type="button" class="mostrar-senha" aria-controls="senha" aria-label="Mostrar senha" data-rotulo="senha" hidden>
                            <svg class="icone-olho-fechado" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/><path d="M3 3l18 18"/></svg>
                            <svg class="icone-olho-aberto" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>
                <button type="submit" class="login-botao">Entrar</button>
            </form>

            <p class="login-aviso">Acesso exclusivo para a equipe. Adotantes entram pelo <a href="../login.php">login do site</a>.</p>
        </section>

        <a class="login-voltar" href="../index.php">
            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M15 6l-6 6 6 6"/></svg>
            Voltar para o site
        </a>
    </main>

    <footer class="login-rodape">
        <p>&copy; 2026 Patas &amp; Lares · Projeto acadêmico</p>
    </footer>
</body>
</html>
