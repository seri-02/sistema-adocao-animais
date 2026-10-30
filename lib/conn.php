<?php
    require_once __DIR__ . '/config.php';

    try {
        $conn = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS);

        // Modo estrito só nesta conexão: dados inválidos geram erro em vez de serem
        // truncados/convertidos em silêncio. Mantém os demais modos do servidor e não
        // depende da configuração global (XAMPP/MariaDB costuma vir sem STRICT_TRANS_TABLES).
        $conn->exec("SET SESSION sql_mode = IF(FIND_IN_SET('STRICT_TRANS_TABLES', @@SESSION.sql_mode),
                                               @@SESSION.sql_mode,
                                               CONCAT_WS(',', 'STRICT_TRANS_TABLES', NULLIF(@@SESSION.sql_mode, '')))");

        $modos = explode(',', (string) $conn->query('SELECT @@SESSION.sql_mode')->fetchColumn());
        if (!in_array('STRICT_TRANS_TABLES', $modos, true)) {
            throw new RuntimeException('STRICT_TRANS_TABLES não foi ativado na sessão.');
        }
    } catch (PDOException | RuntimeException $e) {
        // Detalhes só no log do servidor; a tela nunca mostra host, usuário ou senha.
        error_log('Falha na conexão com o banco: ' . $e->getMessage());
        if (!headers_sent()) {
            http_response_code(500);
        }
        exit('Não foi possível conectar ao banco de dados.');
    }
