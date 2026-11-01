<?php
    require_once __DIR__ . '/lib/auth.php';
    require_once __DIR__ . '/lib/escape.php';
    require_once __DIR__ . '/lib/usuario.php';
    $usuario_logado = exigir_perfis([PERFIL_ADOTANTE], 'login.php');

    require_once __DIR__ . '/lib/conn.php';
    // A senha (hash) nunca é lida para a tela
    $stmt = $conn->prepare('SELECT nome, email, telefone FROM usuario WHERE id_usuario = :id_usuario');
    $stmt->bindValue(':id_usuario', (int) $usuario_logado['id_usuario'], PDO::PARAM_INT);
    $stmt->execute();
    $conta = $stmt->fetch(PDO::FETCH_OBJ);

    $usuario_menu = $usuario_logado;
    // A edição faz parte da seção Minha conta
    $item_conta_atual = 'minha_conta.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar meus dados - Patas &amp; Lares</title>
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23C86B4A'%3E%3Cellipse cx='12' cy='16' rx='5' ry='4.2'/%3E%3Ccircle cx='5.5' cy='10' r='2.2'/%3E%3Ccircle cx='9.5' cy='5.8' r='2.2'/%3E%3Ccircle cx='14.5' cy='5.8' r='2.2'/%3E%3Ccircle cx='18.5' cy='10' r='2.2'/%3E%3C/svg%3E">
    <link rel="stylesheet" href="css/minha-conta.css">
    <!-- Sem JavaScript o menu mobile permanece aberto; com JavaScript ele vira recolhível -->
    <script>document.documentElement.classList.add('js')</script>
    <!-- Mesmo menu recolhível da Home (depende só dos ids btn-mobile e nav) -->
    <script src="js/home.js" defer></script>
    <script src="js/auth.js" defer></script>
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
                    <?php $mostrar_area_equipe = true; require __DIR__ . '/parciais/menu_conta.php'; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main id="conteudo" class="container">
        <div class="pagina-topo">
            <h1>Editar meus dados</h1>
            <p>Atualize suas informações pessoais ou altere sua senha.</p>
        </div>

        <form action="function/update/func_editar_conta.php" method="post">
            <section class="cartao" aria-labelledby="titulo-dados">
                <h2 id="titulo-dados">Dados pessoais</h2>
                <div class="campo">
                    <label for="nome">Nome</label>
                    <input type="text" name="nome" id="nome" maxlength="100" autocomplete="name" required value="<?= e($conta->nome) ?>">
                </div>
                <div class="campo">
                    <label for="email">E-mail</label>
                    <input type="email" name="email" id="email" maxlength="150" autocomplete="email" required value="<?= e($conta->email) ?>">
                </div>
                <div class="campo">
                    <label for="telefone">Telefone</label>
                    <input type="tel" name="telefone" id="telefone" maxlength="20" autocomplete="tel" aria-describedby="ajuda-telefone" value="<?= e($conta->telefone) ?>">
                    <p class="campo-ajuda" id="ajuda-telefone">Opcional</p>
                </div>
            </section>

            <section class="cartao" aria-labelledby="titulo-senha">
                <h2 id="titulo-senha">Alterar senha</h2>
                <p class="cartao-intro" id="ajuda-troca-senha">Preencha os campos abaixo somente se quiser alterar sua senha.</p>
                <div class="campo">
                    <label for="senha_atual">Senha atual</label>
                    <div class="campo-senha">
                        <input type="password" name="senha_atual" id="senha_atual" autocomplete="current-password" aria-describedby="ajuda-troca-senha">
                        <button type="button" class="mostrar-senha" aria-controls="senha_atual" aria-label="Mostrar senha atual" data-rotulo="senha atual" hidden>
                            <svg class="icone-olho-fechado" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/><path d="M3 3l18 18"/></svg>
                            <svg class="icone-olho-aberto" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>
                <div class="campo">
                    <label for="nova_senha">Nova senha</label>
                    <div class="campo-senha">
                        <input type="password" name="nova_senha" id="nova_senha" minlength="<?= e(SENHA_TAMANHO_MINIMO) ?>" autocomplete="new-password" aria-describedby="ajuda-nova-senha">
                        <button type="button" class="mostrar-senha" aria-controls="nova_senha" aria-label="Mostrar nova senha" data-rotulo="nova senha" hidden>
                            <svg class="icone-olho-fechado" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/><path d="M3 3l18 18"/></svg>
                            <svg class="icone-olho-aberto" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                    <p class="campo-ajuda" id="ajuda-nova-senha">Mínimo de <?= e(SENHA_TAMANHO_MINIMO) ?> caracteres</p>
                </div>
                <div class="campo">
                    <label for="confirmacao_nova_senha">Confirmar nova senha</label>
                    <div class="campo-senha">
                        <input type="password" name="confirmacao_nova_senha" id="confirmacao_nova_senha" minlength="<?= e(SENHA_TAMANHO_MINIMO) ?>" autocomplete="new-password">
                        <button type="button" class="mostrar-senha" aria-controls="confirmacao_nova_senha" aria-label="Mostrar confirmação da nova senha" data-rotulo="confirmação da nova senha" hidden>
                            <svg class="icone-olho-fechado" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/><path d="M3 3l18 18"/></svg>
                            <svg class="icone-olho-aberto" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>
            </section>

            <div class="acoes">
                <button type="submit" class="botao botao-primario">Salvar alterações</button>
                <a class="botao botao-secundario" href="minha_conta.php">Voltar para Minha conta</a>
            </div>
        </form>
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
