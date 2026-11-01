<?php
    require_once __DIR__ . '/lib/auth.php';
    require_once __DIR__ . '/lib/escape.php';
    require_once __DIR__ . '/lib/voluntario.php';

    // Página pública: visitante, adotante e equipe podem se cadastrar como voluntário.
    // O voluntariado é independente da conta de usuário (nada é preenchido a partir da sessão).
    $usuario_menu = usuario_logado_opcional();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seja voluntário - Patas &amp; Lares</title>
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23C86B4A'%3E%3Cellipse cx='12' cy='16' rx='5' ry='4.2'/%3E%3Ccircle cx='5.5' cy='10' r='2.2'/%3E%3Ccircle cx='9.5' cy='5.8' r='2.2'/%3E%3Ccircle cx='14.5' cy='5.8' r='2.2'/%3E%3Ccircle cx='18.5' cy='10' r='2.2'/%3E%3C/svg%3E">
    <link rel="stylesheet" href="css/voluntariado.css">
    <!-- Sem JavaScript o menu mobile permanece aberto; com JavaScript ele vira recolhível -->
    <script>document.documentElement.classList.add('js')</script>
    <!-- Mesmo menu recolhível da Home (depende só dos ids btn-mobile e nav) -->
    <script src="js/home.js" defer></script>
</head>
<body class="voluntariado">
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
                    <li><a href="seja_voluntario.php" aria-current="page">Voluntariado</a></li>
                </ul>
                <ul class="nav-conta">
                    <?php require __DIR__ . '/parciais/menu_conta.php'; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main id="conteudo" class="container">
        <section class="vol-card" aria-labelledby="titulo-voluntariado">
            <h1 id="titulo-voluntariado">Seja voluntário</h1>
            <p class="vol-intro">Ajude o Patas &amp; Lares dedicando parte do seu tempo ao cuidado e ao apoio aos animais.</p>
            <p class="vol-explicacao">Preencha seus dados e informe os dias em que você tem disponibilidade. A equipe utilizará essas informações para organizar o apoio aos animais.</p>

            <!-- Regras de validação ficam no servidor (lib/voluntario.php); os atributos só ajudam o navegador -->
            <form class="vol-form" action="function/func_cadastro_voluntario.php" method="post">
                <div class="vol-grade">
                    <div class="campo campo-largo">
                        <label for="nome">Nome</label>
                        <input type="text" name="nome" id="nome" maxlength="<?= e(VOLUNTARIO_NOME_MAXIMO) ?>" autocomplete="name" required>
                    </div>
                    <div class="campo">
                        <label for="email">E-mail</label>
                        <input type="email" name="email" id="email" maxlength="<?= e(VOLUNTARIO_EMAIL_MAXIMO) ?>" autocomplete="email" required>
                    </div>
                    <div class="campo">
                        <label for="idade">Idade</label>
                        <input type="number" name="idade" id="idade" min="1" step="1" inputmode="numeric" required>
                    </div>
                    <div class="campo campo-largo">
                        <label for="dia_disponivel">Dias disponíveis</label>
                        <input type="text" name="dia_disponivel" id="dia_disponivel" maxlength="<?= e(VOLUNTARIO_DIA_MAXIMO) ?>" aria-describedby="ajuda-dias" required>
                        <p class="campo-ajuda" id="ajuda-dias">Ex.: Segunda, Quarta e Sexta</p>
                    </div>
                </div>
                <button type="submit" class="botao botao-primario">Quero ser voluntário</button>
            </form>

            <p class="vol-voltar"><a href="index.php">Voltar para o início</a></p>
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
