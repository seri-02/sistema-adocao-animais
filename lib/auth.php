<?php
    // Autenticação e autorização por perfil (ADMIN, FUNCIONARIO, ADOTANTE).
    // A autorização sempre usa o estado ATUAL do usuário no banco, não só o que
    // foi gravado na sessão no login: desativar ou trocar o perfil vale na hora.

    const PERFIL_ADMIN = 'ADMIN';
    const PERFIL_FUNCIONARIO = 'FUNCIONARIO';
    const PERFIL_ADOTANTE = 'ADOTANTE';
    // Quem acessa a área administrativa
    const PERFIS_EQUIPE = [PERFIL_ADMIN, PERFIL_FUNCIONARIO];

    // Hash bcrypt de um texto descartável: usado para gastar o mesmo tempo quando
    // o e-mail não existe (evita descobrir contas pelo tempo de resposta)
    const HASH_FICTICIO = '$2y$10$WBes09NF8IEot9p2qAnuhOrQKNd3lgvtBt5lFNNYML4C1NWnKkJR.';

    function iniciar_sessao(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            // Cookie de sessão inacessível a JavaScript e não enviado em POST de outro site.
            // Duração, path, domínio e secure continuam os do ambiente (secure fixo quebraria o
            // XAMPP local em http://).
            $parametros = session_get_cookie_params();
            session_set_cookie_params([
                'lifetime' => $parametros['lifetime'],
                'path' => $parametros['path'],
                'domain' => $parametros['domain'],
                'secure' => $parametros['secure'],
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            session_start();
        }
    }

    function conexao_auth(): PDO
    {
        global $conn;
        require_once __DIR__ . '/conn.php';
        return $conn;
    }

    // Usuário autenticado e ativo (com dados atuais do banco) ou null.
    // Se o registro sumiu ou foi desativado, a sessão é encerrada.
    function usuario_atual(): ?array
    {
        static $usuario = false;
        if ($usuario !== false) {
            return $usuario;
        }

        iniciar_sessao();
        $id = (int) ($_SESSION['id_usuario'] ?? 0);
        if ($id <= 0) {
            return $usuario = null;
        }

        $stmt = conexao_auth()->prepare(
            'SELECT id_usuario, nome, email, perfil, ativo FROM usuario WHERE id_usuario = :id'
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $registro = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$registro || (int) $registro['ativo'] !== 1) {
            encerrar_sessao();
            return $usuario = null;
        }

        unset($registro['ativo']);
        $_SESSION['nome'] = $registro['nome'];
        $_SESSION['email'] = $registro['email'];
        $_SESSION['perfil'] = $registro['perfil'];
        return $usuario = $registro;
    }

    function usuario_autenticado(): bool
    {
        return usuario_atual() !== null;
    }

    function tem_perfil(string ...$perfis): bool
    {
        $usuario = usuario_atual();
        return $usuario !== null && in_array($usuario['perfil'], $perfis, true);
    }

    function eh_admin(): bool
    {
        return tem_perfil(PERFIL_ADMIN);
    }

    // Caminho relativo da página atual até um arquivo a partir da raiz do projeto.
    // Relativo para funcionar tanto com `php -S` na raiz quanto em subpasta do Apache.
    function url_projeto(string $arquivo): string
    {
        $raiz = str_replace('\\', '/', (string) realpath(dirname(__DIR__)));
        $pasta = str_replace('\\', '/', (string) realpath(dirname($_SERVER['SCRIPT_FILENAME'])));

        if ($raiz === '' || strpos($pasta, $raiz) !== 0) {
            return '/' . $arquivo;
        }

        $relativo = trim(substr($pasta, strlen($raiz)), '/');
        $niveis = $relativo === '' ? 0 : count(explode('/', $relativo));

        return str_repeat('../', $niveis) . $arquivo;
    }

    function url_login(): string
    {
        return url_projeto('admin/admin.php');
    }

    // Exigem ser chamadas antes de qualquer saída HTML e encerram a execução
    // para que nenhum conteúdo protegido seja enviado.
    // $pagina_login: para onde vai quem não está autenticado (área administrativa por padrão).
    function exigir_login(string $pagina_login = 'admin/admin.php'): array
    {
        $usuario = usuario_atual();
        if ($usuario === null) {
            header('Location: ' . url_projeto($pagina_login));
            exit;
        }
        return $usuario;
    }

    function exigir_perfis(array $perfis, string $pagina_login = 'admin/admin.php'): array
    {
        $usuario = exigir_login($pagina_login);
        if (!in_array($usuario['perfil'], $perfis, true)) {
            negar_acesso();
        }
        return $usuario;
    }

    // Usuário logado para exibir no menu público, sem criar sessão para visitante anônimo
    function usuario_logado_opcional(): ?array
    {
        if (session_status() !== PHP_SESSION_ACTIVE && empty($_COOKIE[session_name()])) {
            return null;
        }
        return usuario_atual();
    }

    // Página inicial de cada perfil após o login
    function destino_pos_login(string $perfil): string
    {
        return url_projeto(in_array($perfil, PERFIS_EQUIPE, true) ? 'admin/painel.php' : 'minha_conta.php');
    }

    // Login e cadastro não fazem sentido com uma sessão válida: leva à área do perfil atual
    function redirecionar_se_autenticado(): void
    {
        $usuario = usuario_logado_opcional();
        if ($usuario !== null) {
            header('Location: ' . destino_pos_login($usuario['perfil']));
            exit;
        }
    }

    function negar_acesso(): void
    {
        http_response_code(403);
        echo '<!DOCTYPE html><html lang="pt-br"><head><meta charset="UTF-8"><title>Acesso negado</title></head>',
             '<body><h1>Acesso negado</h1><p>Você não tem permissão para acessar esta página.</p>',
             '<p><a href="', htmlspecialchars(url_projeto('index.php'), ENT_QUOTES, 'UTF-8'), '">Voltar ao início</a></p></body></html>';
        exit;
    }

    function eh_hash_legado(string $hash): bool
    {
        return preg_match('/^[0-9a-f]{40}$/i', $hash) === 1;
    }

    // Única verificação de senha do sistema (login e confirmação de senha atual).
    // Aceita o hash moderno e, temporariamente, o SHA-1 legado.
    function senha_confere(string $senha, string $hash): bool
    {
        return eh_hash_legado($hash)
            ? hash_equals(strtolower($hash), sha1($senha))   // SHA-1 legado: somente verificação, nunca gravação
            : password_verify($senha, $hash);
    }

    // Confere e-mail e senha. Aceita o hash moderno (password_hash) e, temporariamente,
    // o SHA-1 legado; neste caso refaz o hash com password_hash após o login válido.
    // Retorna o usuário (sem a senha) ou null — o motivo da falha nunca é exposto.
    function autenticar(PDO $conn, string $email, string $senha): ?array
    {
        $stmt = $conn->prepare(
            'SELECT id_usuario, nome, email, senha, perfil, ativo FROM usuario WHERE email = :email'
        );
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            password_verify($senha, HASH_FICTICIO);
            return null;
        }

        $hash = $usuario['senha'];
        if (!senha_confere($senha, $hash) || (int) $usuario['ativo'] !== 1) {
            return null;
        }

        if (eh_hash_legado($hash) || password_needs_rehash($hash, PASSWORD_DEFAULT)) {
            // Só troca se o hash ainda for o lido agora (evita sobrescrever alteração concorrente)
            $update = $conn->prepare('UPDATE usuario SET senha = :nova WHERE id_usuario = :id AND senha = :atual');
            $update->bindValue(':nova', password_hash($senha, PASSWORD_DEFAULT));
            $update->bindValue(':id', (int) $usuario['id_usuario'], PDO::PARAM_INT);
            $update->bindValue(':atual', $hash);
            $update->execute();
        }

        unset($usuario['senha'], $usuario['ativo']);
        return $usuario;
    }

    function iniciar_sessao_autenticada(array $usuario): void
    {
        iniciar_sessao();
        session_regenerate_id(true);
        $_SESSION = [
            'id_usuario' => (int) $usuario['id_usuario'],
            'nome' => $usuario['nome'],
            'email' => $usuario['email'],
            'perfil' => $usuario['perfil'],
        ];
    }

    function encerrar_sessao(): void
    {
        iniciar_sessao();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $parametros = session_get_cookie_params();
            setcookie(session_name(), '', [
                'expires' => time() - 42000,
                'path' => $parametros['path'],
                'domain' => $parametros['domain'],
                'secure' => $parametros['secure'],
                'httponly' => $parametros['httponly'],
                'samesite' => $parametros['samesite'],
            ]);
        }
        session_destroy();
    }
