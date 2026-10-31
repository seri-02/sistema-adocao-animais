<?php
    require_once __DIR__ . '/lib/auth.php';
    require_once __DIR__ . '/lib/usuario.php';
    require_once __DIR__ . '/lib/escape.php';
    // Com sessão válida não se cria uma segunda conta
    redirecionar_se_autenticado();

    $usuario_menu = null;
    $item_conta_atual = 'cadastro.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar conta - Patas &amp; Lares</title>
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23C86B4A'%3E%3Cellipse cx='12' cy='16' rx='5' ry='4.2'/%3E%3Ccircle cx='5.5' cy='10' r='2.2'/%3E%3Ccircle cx='9.5' cy='5.8' r='2.2'/%3E%3Ccircle cx='14.5' cy='5.8' r='2.2'/%3E%3Ccircle cx='18.5' cy='10' r='2.2'/%3E%3C/svg%3E">
    <link rel="stylesheet" href="css/auth.css">
    <!-- Sem JavaScript o menu mobile permanece aberto; com JavaScript ele vira recolhível -->
    <script>document.documentElement.classList.add('js')</script>
    <!-- Mesmo menu recolhível da Home (depende só dos ids btn-mobile e nav) -->
    <script src="js/home.js" defer></script>
    <script src="js/auth.js" defer></script>
</head>
<body class="autenticacao">
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
        <section class="auth-card" aria-labelledby="titulo-auth">
            <h1 id="titulo-auth">Criar sua conta</h1>
            <p class="auth-intro">Cadastre-se para solicitar a adoção de um animal e acompanhar o andamento pela sua conta.</p>

            <form class="auth-form" action="function/func_cadastrar_adotante.php" method="post">
                <div class="campo">
                    <label for="nome">Nome</label>
                    <input type="text" name="nome" id="nome" maxlength="100" autocomplete="name" required>
                </div>
                <div class="campo">
                    <label for="email">E-mail</label>
                    <input type="email" name="email" id="email" maxlength="150" autocomplete="email" required>
                </div>
                <div class="campo">
                    <label for="telefone">Telefone</label>
                    <input type="tel" name="telefone" id="telefone" maxlength="20" autocomplete="tel" aria-describedby="ajuda-telefone">
                    <p class="campo-ajuda" id="ajuda-telefone">Opcional</p>
                </div>
                <div class="campo">
                    <label for="senha">Senha</label>
                    <div class="campo-senha">
                        <input type="password" name="senha" id="senha" minlength="<?= e(SENHA_TAMANHO_MINIMO) ?>" autocomplete="new-password" aria-describedby="ajuda-senha" required>
                        <button type="button" class="mostrar-senha" aria-controls="senha" aria-label="Mostrar senha" data-rotulo="senha" hidden>
                            <svg class="icone-olho-fechado" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/><path d="M3 3l18 18"/></svg>
                            <svg class="icone-olho-aberto" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                    <p class="campo-ajuda" id="ajuda-senha">Mínimo de <?= e(SENHA_TAMANHO_MINIMO) ?> caracteres</p>
                </div>
                <div class="campo">
                    <label for="confirmacao_senha">Confirmar senha</label>
                    <div class="campo-senha">
                        <input type="password" name="confirmacao_senha" id="confirmacao_senha" minlength="<?= e(SENHA_TAMANHO_MINIMO) ?>" autocomplete="new-password" required>
                        <button type="button" class="mostrar-senha" aria-controls="confirmacao_senha" aria-label="Mostrar confirmação da senha" data-rotulo="confirmação da senha" hidden>
                            <svg class="icone-olho-fechado" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/><path d="M3 3l18 18"/></svg>
                            <svg class="icone-olho-aberto" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>
                <button type="submit" class="botao botao-primario">Criar conta</button>
            </form>

            <p class="auth-alternativa">Já possui uma conta? <a href="login.php">Entrar</a></p>
        </section>
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
