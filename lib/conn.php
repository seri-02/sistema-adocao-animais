<?php
    require_once __DIR__ . '/config.php';

    try {
        $conn = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS);
    } catch (PDOException $e) {
        // Detalhes só no log do servidor; a tela nunca mostra host, usuário ou senha.
        error_log('Falha na conexão com o banco: ' . $e->getMessage());
        if (!headers_sent()) {
            http_response_code(500);
        }
        exit('Não foi possível conectar ao banco de dados.');
    }
