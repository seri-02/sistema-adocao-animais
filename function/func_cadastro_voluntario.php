<?php
    // Cadastro PÚBLICO de voluntário (seja_voluntario.php). Somente POST.
    // Só nome/email/idade/dia_disponivel são lidos e validados (lib/voluntario.php);
    // qualquer outro campo enviado é ignorado.
    require_once __DIR__ . '/../lib/conn.php';
    require_once __DIR__ . '/../lib/escape.php';
    require_once __DIR__ . '/../lib/voluntario.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ../seja_voluntario.php');
        exit;
    }

    [$dados, $erros] = ler_formulario_voluntario($_POST);

    if (!$erros) {
        try {
            cadastrar_voluntario($conn, $dados);
        } catch (PDOException $e) {
            error_log('Falha ao cadastrar voluntário: ' . $e->getMessage());
            $erros[] = 'Não foi possível concluir o cadastro. Verifique os dados informados.';
        }
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if (!$erros) { ?>
    <meta http-equiv="refresh" content="2; url=../seja_voluntario.php">
    <?php } ?>
    <title>Seja voluntário - Patas &amp; Lares</title>
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23C86B4A'%3E%3Cellipse cx='12' cy='16' rx='5' ry='4.2'/%3E%3Ccircle cx='5.5' cy='10' r='2.2'/%3E%3Ccircle cx='9.5' cy='5.8' r='2.2'/%3E%3Ccircle cx='14.5' cy='5.8' r='2.2'/%3E%3Ccircle cx='18.5' cy='10' r='2.2'/%3E%3C/svg%3E">
    <!-- Mesmo card da resposta pública de cadastro de adotante (auth.css); sem CSS próprio -->
    <link rel="stylesheet" href="../css/auth.css">
</head>
<body class="autenticacao">
    <!-- Resposta simples: só a marca, sem o menu completo (o endpoint não carrega estado de sessão para o menu) -->
    <header class="topo">
        <div class="container topo-barra">
            <a href="../index.php" class="marca">
                <svg class="marca-icone" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <ellipse cx="12" cy="16" rx="5" ry="4.2"/>
                    <circle cx="5.5" cy="10" r="2.2"/>
                    <circle cx="9.5" cy="5.8" r="2.2"/>
                    <circle cx="14.5" cy="5.8" r="2.2"/>
                    <circle cx="18.5" cy="10" r="2.2"/>
                </svg>
                Patas &amp; Lares
            </a>
        </div>
    </header>

    <main id="conteudo" class="container">
        <section class="auth-card" aria-labelledby="titulo-auth">
            <?php if (!$erros) { ?>
            <h1 id="titulo-auth">Voluntário cadastrado com sucesso!</h1>
            <p class="auth-intro">Obrigado por se oferecer para ajudar. Você será direcionado de volta para a página de voluntariado.</p>
            <a class="botao botao-primario" href="../seja_voluntario.php">Voltar para voluntariado</a>
            <?php } else { ?>
            <h1 id="titulo-auth">Não foi possível concluir seu cadastro</h1>
            <div class="alerta-erro" role="alert">
                <span class="alerta-icone" aria-hidden="true">!</span>
                <div>
                    <?php foreach ($erros as $erro) { ?>
                    <p><?= e($erro) ?></p>
                    <?php } ?>
                </div>
            </div>
            <a class="botao botao-secundario" href="../seja_voluntario.php">Voltar ao formulário</a>
            <?php } ?>
        </section>
    </main>

    <footer class="rodape rodape-simples">
        <div class="container rodape-base">
            <p>&copy; 2026 Patas &amp; Lares · Projeto acadêmico</p>
            <p>Desenvolvido por Jonathas e Gloria</p>
        </div>
    </footer>
</body>
</html>
