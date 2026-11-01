<?php
    // Modelo da configuração local. Este arquivo é versionado: use apenas valores fictícios.
    // Para usar: copie para config/config.php (ignorado pelo Git) e preencha com os dados do seu ambiente.
    // NUNCA coloque senhas reais aqui.

    // Banco de dados (padrão do XAMPP)
    const DB_HOST = 'localhost';
    const DB_NAME = 'abrigo_dos_gatos';
    const DB_USER = 'root';
    const DB_PASS = '';

    // SMTP do formulário de contato
    const SMTP_HOST = 'smtp.gmail.com';
    const SMTP_PORT = 465;
    const SMTP_SECURE = 'ssl';                   // 'ssl' (SMTPS, porta 465) ou 'tls' (STARTTLS, porta 587)
    const SMTP_USER = 'usuario@example.com';
    const SMTP_PASS = 'SUA_SENHA_DE_APP';        // Gmail: senha de app, nunca a senha da conta

    // Gmail: normalmente a própria conta autenticada no SMTP (SMTP_USER), salvo quando existir alias autorizado
    const MAIL_FROM = 'usuario@example.com';
    const MAIL_FROM_NAME = 'Patas & Lares';
    const MAIL_TO = 'destinatario@example.com';  // quem recebe as mensagens do formulário
