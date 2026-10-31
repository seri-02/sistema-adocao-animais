<?php
    require_once __DIR__ . '/lib/auth.php';
    require_once __DIR__ . '/lib/escape.php';
    $usuario_menu = usuario_logado_opcional();

    require_once __DIR__ . '/lib/conn.php';
    require_once __DIR__ . '/lib/animal.php';
    $sqlBuscaAnimais = "SELECT * FROM animal WHERE status = 'DISPONIVEL' ORDER BY id_animal";
    $buscaAnimais = $conn->query($sqlBuscaAnimais);
    $animais = $buscaAnimais->fetchAll(PDO::FETCH_OBJ);

    // A contagem é a da própria consulta, então sempre bate com os cards exibidos
    $total = count($animais);
    $texto_total = $total . ($total === 1 ? ' animal disponível' : ' animais disponíveis');

    // Dados rápidos do card: só valores conhecidos na linha principal;
    // os ausentes viram uma nota única ("Sexo, idade e porte não informados")
    $cards = [];
    foreach ($animais as $animal) {
        $sexo = rotulo(SEXOS, $animal->sexo);
        $idade = calcular_idade($animal->data_nascimento_estimada);
        $porte = rotulo(PORTES, $animal->porte);

        $dados = [rotulo(ESPECIES, $animal->especie)];
        $ausentes = [];
        if ($sexo !== NAO_INFORMADO) {
            $dados[] = $sexo;
        } else {
            $ausentes['sexo'] = 'Sexo não informado';
        }
        if ($idade !== 'Idade não informada') {
            $dados[] = $idade;
        } else {
            $ausentes['idade'] = $idade;
        }
        if ($porte === NAO_INFORMADO) {
            $ausentes['porte'] = 'Porte não informado';
        }

        if (count($ausentes) > 1) {
            $nomes = array_keys($ausentes);
            $ultimo = array_pop($nomes);
            $nota = ucfirst(implode(', ', $nomes) . ' e ' . $ultimo) . ' não informados';
        } else {
            $nota = $ausentes ? reset($ausentes) : null;
        }

        $cards[] = [
            'animal' => $animal,
            'dados' => implode(' · ', $dados),
            'porte' => $porte === NAO_INFORMADO ? null : 'Porte ' . mb_strtolower($porte),
            'nota' => $nota,
        ];
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Animais para adoção - Patas &amp; Lares</title>
    <meta name="description" content="Conheça os animais disponíveis para adoção.">
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23C86B4A'%3E%3Cellipse cx='12' cy='16' rx='5' ry='4.2'/%3E%3Ccircle cx='5.5' cy='10' r='2.2'/%3E%3Ccircle cx='9.5' cy='5.8' r='2.2'/%3E%3Ccircle cx='14.5' cy='5.8' r='2.2'/%3E%3Ccircle cx='18.5' cy='10' r='2.2'/%3E%3C/svg%3E">
    <link rel="stylesheet" href="css/catalogo.css">
    <!-- Sem JavaScript o menu mobile permanece aberto; com JavaScript ele vira recolhível -->
    <script>document.documentElement.classList.add('js')</script>
    <!-- Mesmo menu recolhível da Home (depende só dos ids btn-mobile e nav) -->
    <script src="js/home.js" defer></script>
</head>
<body class="catalogo">
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
                    <li><a href="adocoes.php" aria-current="page">Animais</a></li>
                    <li><a href="index.php#como-adotar">Como adotar</a></li>
                    <li><a href="seja_voluntario.php">Voluntariado</a></li>
                </ul>
                <ul class="nav-conta">
                    <?php require __DIR__ . '/parciais/menu_conta.php'; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main id="conteudo">
        <section class="catalogo-topo" aria-labelledby="titulo-catalogo">
            <div class="container">
                <h1 id="titulo-catalogo">Animais para adoção</h1>
                <p class="catalogo-descricao">Conheça os animais disponíveis e encontre um novo companheiro.</p>
                <p class="catalogo-total"><?= e($texto_total) ?></p>
            </div>
        </section>

        <section class="catalogo-lista" aria-label="Lista de animais disponíveis">
            <div class="container">
                <?php if ($cards) { ?>
                <ul class="grade-animais">
                    <?php foreach ($cards as $card) { $animal = $card['animal']; ?>
                    <li>
                        <article class="card-animal">
                            <div class="card-foto">
                                <?php if ($animal->imagem) { ?>
                                <img src="image/<?= e($animal->imagem) ?>" alt="Foto de <?= e($animal->nome) ?>" loading="lazy">
                                <?php } else { ?>
                                <span class="card-sem-foto">Foto não disponível</span>
                                <?php } ?>
                                <span class="badge-status"><?= e(rotulo(STATUS_ANIMAL, $animal->status)) ?></span>
                            </div>
                            <div class="card-corpo">
                                <h2 class="card-nome"><?= e($animal->nome) ?></h2>
                                <p class="card-dados"><?= e($card['dados']) ?></p>
                                <?php if ($card['porte'] !== null) { ?>
                                <p class="card-dados"><?= e($card['porte']) ?></p>
                                <?php } ?>
                                <?php if ($card['nota'] !== null) { ?>
                                <p class="card-nota"><?= e($card['nota']) ?></p>
                                <?php } ?>
                                <a class="botao botao-primario" href="gato.php?id=<?= e($animal->id_animal) ?>">
                                    Ver detalhes<span class="sr-only"> de <?= e($animal->nome) ?></span>
                                </a>
                            </div>
                        </article>
                    </li>
                    <?php } ?>
                </ul>
                <?php } else { ?>
                <div class="catalogo-vazio">
                    <h2>Nenhum animal disponível no momento</h2>
                    <p>Novos animais podem ficar disponíveis para adoção em breve.</p>
                    <a class="botao botao-secundario" href="index.php">Voltar ao início</a>
                </div>
                <?php } ?>
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
