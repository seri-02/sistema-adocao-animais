<?php
    // Controle de acesso da área administrativa.
    // Nesta fase existe apenas: autenticado ou não autenticado.

    function iniciar_sessao(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    function usuario_autenticado(): bool
    {
        iniciar_sessao();
        return isset($_SESSION['id_user']);
    }

    // Caminho relativo da página atual até a tela de login (admin/admin.php).
    // Relativo para funcionar tanto com `php -S` na raiz quanto em subpasta do Apache.
    function url_login(): string
    {
        $raiz = str_replace('\\', '/', (string) realpath(dirname(__DIR__)));
        $pasta = str_replace('\\', '/', (string) realpath(dirname($_SERVER['SCRIPT_FILENAME'])));

        if ($raiz === '' || strpos($pasta, $raiz) !== 0) {
            return '/admin/admin.php';
        }

        $relativo = trim(substr($pasta, strlen($raiz)), '/');
        $niveis = $relativo === '' ? 0 : count(explode('/', $relativo));

        return str_repeat('../', $niveis) . 'admin/admin.php';
    }

    // Deve ser chamada antes de qualquer saída HTML.
    // Encerra a execução para que nenhum conteúdo protegido seja enviado.
    function exigir_login(): void
    {
        if (!usuario_autenticado()) {
            header('Location: ' . url_login());
            exit;
        }
    }
