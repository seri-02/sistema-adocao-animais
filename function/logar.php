<?php
    // Login único do sistema: tela pública (login.php, origem=publico) e tela administrativa
    // (admin/admin.php). O perfil vem sempre do banco; a tela de origem só decide QUAIS perfis
    // podem entrar por ela e para onde volta em caso de falha:
    //   pública (origem=publico)        -> somente ADOTANTE
    //   administrativa (sem origem)     -> somente ADMIN e FUNCIONARIO
    //   origem com qualquer outro valor -> nenhum perfil (requisição forjada)
    require_once __DIR__ . '/../lib/auth.php';
    require_once __DIR__ . '/../lib/conn.php';
    require_once __DIR__ . '/../lib/usuario.php';

    // Só texto é aceito: array ou outra estrutura vira vazio (sem conversão implícita/warning)
    $email = trim(valor_texto($_POST, 'email'));
    $senha = valor_texto($_POST, 'senha');
    $origem_publica = valor_texto($_POST, 'origem') === 'publico';
    $origem_administrativa = !array_key_exists('origem', $_POST);
    $perfis_da_origem = $origem_publica ? [PERFIL_ADOTANTE] : ($origem_administrativa ? PERFIS_EQUIPE : []);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ' . ($origem_publica ? '../login.php' : '../admin/admin.php'));
        exit;
    }

    $usuario = null;
    if ($email !== '' && $senha !== '') {
        try {
            $usuario = autenticar($conn, $email, $senha);
        } catch (PDOException $e) {
            error_log('Falha no login: ' . $e->getMessage());
        }
    }

    // Credencial válida, mas perfil de outro portal: não chega a iniciar_sessao_autenticada()
    // (mesma resposta genérica de credencial inválida; a sessão existente não é tocada)
    if ($usuario !== null && !in_array($usuario['perfil'], $perfis_da_origem, true)) {
        $usuario = null;
    }

    if ($usuario !== null) {
        iniciar_sessao_autenticada($usuario);
        // O destino depende do perfil autenticado, nunca da tela de origem
        header('Location: ' . destino_pos_login($usuario['perfil']));
        exit;
    }

    // Falha: cada tela recebe a própria mensagem genérica (e-mail inexistente, senha incorreta,
    // conta inativa e perfil sem acesso respondem igual)
    header('Location: ' . ($origem_publica ? '../login.php?erro=1' : '../admin/admin.php?erro=1'), true, 303);
    exit;
