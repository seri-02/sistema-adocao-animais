<?php
    require_once 'lib/auth.php';
    require_once 'lib/conn.php';
    require_once 'lib/escape.php';
    require_once 'lib/animal.php';

    iniciar_sessao();
    if (isset($_GET['id'])) {
        $_SESSION['id_animal'] = (int) $_GET['id'];
    }
    $id = (int) ($_SESSION['id_animal'] ?? 0);

    $stmt = $conn->prepare("SELECT * FROM animal WHERE id_animal = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $animal = $stmt->fetch(PDO::FETCH_OBJ) ?: null;

    $usuario_menu = usuario_logado_opcional();

    // Texto livre: NULL ou só espaços contam como não informado
    $texto_informado = fn(?string $valor): ?string => $valor !== null && trim($valor) !== '' ? $valor : null;

    if ($animal !== null) {
        $sexo = rotulo(SEXOS, $animal->sexo);
        $idade = calcular_idade($animal->data_nascimento_estimada);
        $idade_informada = $idade !== 'Idade não informada';
        $porte = rotulo(PORTES, $animal->porte);

        // Resumo rápido: valores ausentes aparecem de forma discreta
        $resumo = [
            [rotulo(ESPECIES, $animal->especie), false],
            [$sexo !== NAO_INFORMADO ? $sexo : 'Sexo não informado', $sexo === NAO_INFORMADO],
            [$idade, !$idade_informada],
            [$porte !== NAO_INFORMADO ? 'Porte ' . mb_strtolower($porte) : 'Porte não informado', $porte === NAO_INFORMADO],
        ];

        $informacoes = [
            'Espécie' => rotulo(ESPECIES, $animal->especie),
            'Sexo' => $sexo,
            'Idade' => $idade_informada ? $idade : 'Não informada',
            'Porte' => $porte,
        ];

        $saude = [
            'Castrado' => rotulo_sim_nao($animal->castrado),
            'Vacinado' => rotulo_sim_nao($animal->vacinado),
        ];
        $condicao_saude = $texto_informado($animal->condicao_saude);
        $comportamento = $texto_informado($animal->comportamento);
        $descricao = $texto_informado($animal->descricao);

        // Foto só quando o arquivo existe; caso contrário a área mostra "Foto não disponível"
        $tem_foto = $animal->imagem !== null && $animal->imagem !== ''
            && is_file(PASTA_IMAGENS . '/' . basename($animal->imagem));

        // Orientação exibida no lugar da ação quando o animal não está disponível
        $mensagens_status = [
            'EM_PROCESSO' => 'Este animal já possui uma solicitação de adoção em análise.',
            'ADOTADO' => 'Este animal já foi adotado e encontrou um novo lar.',
            'INDISPONIVEL' => 'Este animal não está disponível para adoção no momento.',
        ];
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $animal !== null ? e($animal->nome) : 'Animal não encontrado' ?> - Patas &amp; Lares</title>
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23C86B4A'%3E%3Cellipse cx='12' cy='16' rx='5' ry='4.2'/%3E%3Ccircle cx='5.5' cy='10' r='2.2'/%3E%3Ccircle cx='9.5' cy='5.8' r='2.2'/%3E%3Ccircle cx='14.5' cy='5.8' r='2.2'/%3E%3Ccircle cx='18.5' cy='10' r='2.2'/%3E%3C/svg%3E">
    <link rel="stylesheet" href="css/detalhe-animal.css">
    <!-- Sem JavaScript o menu mobile permanece aberto; com JavaScript ele vira recolhível -->
    <script>document.documentElement.classList.add('js')</script>
    <!-- Mesmo menu recolhível da Home (depende só dos ids btn-mobile e nav) -->
    <script src="js/home.js" defer></script>
</head>
<body class="detalhe-animal">
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
                    <?php $mostrar_area_equipe = true; require __DIR__ . '/parciais/menu_conta.php'; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main id="conteudo" class="container">
        <a class="voltar" href="adocoes.php"><span aria-hidden="true">←</span> Voltar para animais</a>

        <?php if ($animal === null) { ?>
        <section class="cartao nao-encontrado">
            <h1>Animal não encontrado</h1>
            <p>O animal procurado não existe ou foi removido.</p>
            <a class="botao botao-secundario" href="adocoes.php">Ver animais disponíveis</a>
        </section>
        <?php } else { ?>
        <section class="principal">
            <div class="foto">
                <?php if ($tem_foto) { ?>
                <img src="image/<?= e($animal->imagem) ?>" alt="Foto de <?= e($animal->nome) ?>">
                <?php } else { ?>
                <span class="sem-foto">Foto não disponível</span>
                <?php } ?>
            </div>

            <div class="principal-texto">
                <h1><?= e($animal->nome) ?></h1>
                <p class="badge-status badge-<?= e(strtolower($animal->status)) ?>"><?= e(rotulo(STATUS_ANIMAL, $animal->status)) ?></p>

                <ul class="resumo">
                    <?php foreach ($resumo as [$texto, $ausente]) { ?>
                    <li<?= $ausente ? ' class="ausente"' : '' ?>><?= e($texto) ?></li>
                    <?php } ?>
                </ul>

                <?php // Equipe (ADMIN/FUNCIONARIO) com animal disponível: nenhuma ação nesta tela ?>
                <?php if ($animal->status !== 'DISPONIVEL' || $usuario_menu === null || $usuario_menu['perfil'] === PERFIL_ADOTANTE) { ?>
                <div class="acao">
                    <?php if ($animal->status === 'DISPONIVEL') { ?>
                        <?php if ($usuario_menu === null) { ?>
                        <p>Entre na sua conta para solicitar a adoção deste animal.</p>
                        <div class="acao-botoes">
                            <a class="botao botao-primario" href="login.php">Entrar</a>
                            <a class="botao botao-secundario" href="cadastro.php">Criar conta</a>
                        </div>
                        <?php } elseif ($usuario_menu['perfil'] === PERFIL_ADOTANTE) { ?>
                        <!-- Os dados do adotante vêm da conta autenticada: só o animal é enviado -->
                        <form action="function/func_solicitar_adocao.php" method="post" id="form-adotante">
                            <input type="hidden" name="id_animal" value="<?= e($animal->id_animal) ?>">
                            <button type="submit" class="botao botao-primario">Solicitar adoção</button>
                        </form>
                        <p class="acao-nota">A solicitação será analisada pela equipe. Você acompanha o andamento em Minhas solicitações.</p>
                        <?php } ?>
                    <?php } elseif (isset($mensagens_status[$animal->status])) { ?>
                    <p><?= e($mensagens_status[$animal->status]) ?></p>
                    <?php } ?>
                </div>
                <?php } ?>
            </div>
        </section>

        <section class="secao" aria-labelledby="titulo-informacoes">
            <h2 id="titulo-informacoes">Informações principais</h2>
            <dl class="dados">
                <?php foreach ($informacoes as $rotulo => $valor) { ?>
                <div class="dado">
                    <dt><?= e($rotulo) ?></dt>
                    <dd<?= in_array($valor, [NAO_INFORMADO, 'Não informada'], true) ? ' class="ausente"' : '' ?>><?= e($valor) ?></dd>
                </div>
                <?php } ?>
            </dl>
        </section>

        <section class="secao" aria-labelledby="titulo-saude">
            <h2 id="titulo-saude">Saúde e cuidados</h2>
            <dl class="dados">
                <?php foreach ($saude as $rotulo => $valor) { ?>
                <div class="dado">
                    <dt><?= e($rotulo) ?></dt>
                    <dd<?= $valor === NAO_INFORMADO ? ' class="ausente"' : '' ?>><?= e($valor) ?></dd>
                </div>
                <?php } ?>
                <div class="dado dado-largo">
                    <dt>Condição de saúde</dt>
                    <dd class="texto-livre<?= $condicao_saude === null ? ' ausente' : '' ?>"><?= e($condicao_saude ?? NAO_INFORMADO) ?></dd>
                </div>
            </dl>
        </section>

        <section class="secao" aria-labelledby="titulo-comportamento">
            <h2 id="titulo-comportamento">Comportamento</h2>
            <p class="cartao texto-livre<?= $comportamento === null ? ' ausente' : '' ?>"><?= e($comportamento ?? NAO_INFORMADO) ?></p>
        </section>

        <section class="secao" aria-labelledby="titulo-sobre">
            <h2 id="titulo-sobre">Sobre <?= e($animal->nome) ?></h2>
            <p class="cartao texto-livre<?= $descricao === null ? ' ausente' : '' ?>"><?= e($descricao ?? 'Nenhuma descrição informada.') ?></p>
        </section>
        <?php } ?>
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
