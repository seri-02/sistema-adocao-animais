<?php
    require_once __DIR__ . '/lib/auth.php';
    require_once __DIR__ . '/lib/escape.php';
    $usuario_menu = usuario_logado_opcional();

    require_once __DIR__ . '/lib/conn.php';
    require_once __DIR__ . '/lib/animal.php';

    // Indicadores da Home: contagens reais do banco por status do animal
    $indicadores = [];
    foreach (['DISPONIVEL' => 'Disponíveis', 'EM_PROCESSO' => 'Em processo de adoção', 'ADOTADO' => 'Adotados'] as $status => $rotulo) {
        $indicadores[$rotulo] = contar_animais_por_status($conn, $status);
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patas &amp; Lares - Adoção de animais</title>
    <meta name="description" content="Sistema demonstrativo para gerenciamento de adoção de animais.">
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23C86B4A'%3E%3Cellipse cx='12' cy='16' rx='5' ry='4.2'/%3E%3Ccircle cx='5.5' cy='10' r='2.2'/%3E%3Ccircle cx='9.5' cy='5.8' r='2.2'/%3E%3Ccircle cx='14.5' cy='5.8' r='2.2'/%3E%3Ccircle cx='18.5' cy='10' r='2.2'/%3E%3C/svg%3E">
    <link rel="stylesheet" href="css/home.css">
    <!-- Sem JavaScript o menu mobile permanece aberto; com JavaScript ele vira recolhível -->
    <script>document.documentElement.classList.add('js')</script>
    <script src="js/home.js" defer></script>
</head>
<body class="home">
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
                    <li><a href="index.php" aria-current="page">Início</a></li>
                    <li><a href="adocoes.php">Animais</a></li>
                    <li><a href="#como-adotar">Como adotar</a></li>
                    <li><a href="seja_voluntario.php">Voluntariado</a></li>
                </ul>
                <ul class="nav-conta">
                    <?php require __DIR__ . '/parciais/menu_conta.php'; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main id="conteudo">
        <section class="hero">
            <div class="container hero-grade">
                <div class="hero-texto">
                    <h1>Encontre um novo companheiro</h1>
                    <p class="hero-descricao">Conheça os animais disponíveis e acompanhe todo o processo de adoção de forma simples e segura.</p>
                    <div class="acoes">
                        <a class="botao botao-primario" href="adocoes.php">Ver animais disponíveis</a>
                        <a class="botao botao-secundario" href="#como-adotar">Como funciona</a>
                    </div>
                </div>
                <div class="hero-imagem">
                    <img src="img/gatos.jpg" width="1000" height="667"
                         alt="Dois gatos deitados lado a lado olhando para a câmera: um preto e um tigrado com peito branco">
                </div>
            </div>
        </section>

        <section class="indicadores" aria-labelledby="titulo-indicadores">
            <div class="container">
                <h2 id="titulo-indicadores" class="titulo-pequeno">Situação atual dos animais</h2>
                <ul class="indicadores-lista">
                    <?php foreach ($indicadores as $rotulo => $total) { ?>
                    <li class="indicador">
                        <span class="indicador-numero"><?= e($total) ?></span>
                        <span class="indicador-rotulo"><?= e($rotulo) ?></span>
                    </li>
                    <?php } ?>
                </ul>
            </div>
        </section>

        <section id="como-adotar" class="secao secao-clara" aria-labelledby="titulo-como-adotar">
            <div class="container">
                <h2 id="titulo-como-adotar">Como funciona a adoção</h2>
                <ol class="passos">
                    <li class="cartao passo">
                        <span class="passo-numero" aria-hidden="true">1</span>
                        <h3>Encontre um animal</h3>
                        <p>Conheça os animais disponíveis e veja suas principais características.</p>
                    </li>
                    <li class="cartao passo">
                        <span class="passo-numero" aria-hidden="true">2</span>
                        <h3>Crie sua conta</h3>
                        <p>Cadastre-se para iniciar uma solicitação de adoção.</p>
                    </li>
                    <li class="cartao passo">
                        <span class="passo-numero" aria-hidden="true">3</span>
                        <h3>Envie sua solicitação</h3>
                        <p>Escolha o animal e envie seu interesse para análise.</p>
                    </li>
                    <li class="cartao passo">
                        <span class="passo-numero" aria-hidden="true">4</span>
                        <h3>Acompanhe</h3>
                        <p>Consulte pela sua conta se a solicitação está pendente, aprovada ou reprovada.</p>
                    </li>
                </ol>
            </div>
        </section>

        <section id="como-ajudar" class="secao" aria-labelledby="titulo-como-ajudar">
            <div class="container">
                <h2 id="titulo-como-ajudar">Como você pode ajudar</h2>
                <div class="ajuda">
                    <article class="cartao ajuda-cartao">
                        <h3>Adote</h3>
                        <p>Dê a um animal a oportunidade de encontrar um novo lar.</p>
                        <a class="botao botao-primario" href="adocoes.php">Ver animais</a>
                    </article>
                    <article class="cartao ajuda-cartao">
                        <h3>Seja voluntário</h3>
                        <p>Contribua com seu tempo apoiando atividades relacionadas ao cuidado dos animais.</p>
                        <a class="botao botao-secundario" href="seja_voluntario.php">Quero ser voluntário</a>
                    </article>
                    <article class="cartao ajuda-cartao">
                        <h3>Apoie a adoção responsável</h3>
                        <p>Adotar é um compromisso para toda a vida do animal. Antes de solicitar, avalie sua rotina, seu espaço e os cuidados de que ele vai precisar.</p>
                    </article>
                </div>
            </div>
        </section>

        <section id="sobre-projeto" class="secao secao-clara" aria-labelledby="titulo-sobre-projeto">
            <div class="container sobre">
                <h2 id="titulo-sobre-projeto">Sobre o projeto</h2>
                <p>O Patas &amp; Lares é uma instituição fictícia, utilizada para demonstrar o funcionamento do sistema desenvolvido neste Trabalho de Graduação.</p>
                <p>A aplicação foi criada para apoiar o gerenciamento de animais e do processo de adoção, centralizando as informações e facilitando o acompanhamento das solicitações.</p>
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
