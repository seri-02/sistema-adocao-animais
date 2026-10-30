<?php
    // Carrega a configuração local (config/config.php), que fica fora do Git.
    if (!is_file(__DIR__ . '/../config/config.php')) {
        if (!headers_sent()) {
            http_response_code(500);
        }
        exit('Configuração local ausente: copie config/config.example.php para config/config.php.');
    }

    require_once __DIR__ . '/../config/config.php';
