<?php
    require_once __DIR__ . '/../lib/auth.php';

    encerrar_sessao();
    header('Location: ../index.php');
    exit;
