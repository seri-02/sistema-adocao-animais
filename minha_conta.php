<?php
    require_once __DIR__ . '/lib/auth.php';
    require_once __DIR__ . '/lib/escape.php';
    // Somente ADOTANTE; perfil e situação são conferidos no banco a cada acesso
    $usuario_logado = exigir_perfis([PERFIL_ADOTANTE], 'login.php');

    require_once __DIR__ . '/lib/conn.php';
    $stmt = $conn->prepare('SELECT nome, email, telefone FROM usuario WHERE id_usuario = :id_usuario');
    $stmt->bindValue(':id_usuario', (int) $usuario_logado['id_usuario'], PDO::PARAM_INT);
    $stmt->execute();
    $conta = $stmt->fetch(PDO::FETCH_OBJ);

    $usuario_menu = $usuario_logado;
    $item_conta_atual = 'minha_conta.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha conta - Patas &amp; Lares</title>
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23C86B4A'%3E%3Cellipse cx='12' cy='16' rx='5' ry='4.2'/%3E%3Ccircle cx='5.5' cy='10' r='2.2'/%3E%3Ccircle cx='9.5' cy='5.8' r='2.2'/%3E%3Ccircle cx='14.5' cy='5.8' r='2.2'/%3E%3Ccircle cx='18.5' cy='10' r='2.2'/%3E%3C/svg%3E">
    <link rel="stylesheet" href="css/minha-conta.css">
    <!-- Sem JavaScript o menu mobile permanece aberto; com JavaScript ele vira recolhível -->
    <script>document.documentElement.classList.add('js')</script>
    <!-- Mesmo menu recolhível da Home (depende só dos ids btn-mobile e nav) -->
    <script src="js/home.js" defer></script>
</head>
<body class="minha-conta">
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
            <h1>Minha conta</h1>
            <p>Gerencie seus dados pessoais e acompanhe suas solicitações de adoção.</p>
        </div>

        <section class="cartao" aria-labelledby="titulo-dados">
            <h2 id="titulo-dados">Dados pessoais</h2>
            <dl class="dados-conta">
                <div>
                    <dt>Nome</dt>
                    <dd><?= e($conta->nome) ?></dd>
                </div>
                <div>
                    <dt>E-mail</dt>
                    <dd><?= e($conta->email) ?></dd>
                </div>
                <div>
                    <dt>Telefone</dt>
                    <?php if ($conta->telefone !== null && $conta->telefone !== '') { ?>
                    <dd><?= e($conta->telefone) ?></dd>
                    <?php } else { ?>
                    <dd class="ausente">Não informado</dd>
                    <?php } ?>
                </div>
            </dl>

            <div class="acoes">
                <a class="botao botao-primario" href="editar_conta.php">Editar meus dados</a>
                <a class="botao botao-secundario" href="minhas_solicitacoes.php">Minhas solicitações</a>
            </div>
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
