<?php
    // Itens de conta da navegação pública (páginas da raiz do site).
    // Espera $usuario_menu = usuario_logado_opcional() calculado ANTES de qualquer saída HTML.
    // $estilo_menu = 'botoes' para cabeçalhos simples; padrão: itens <li> do menu principal.
    require_once __DIR__ . '/../lib/escape.php';

    $usuario_menu = $usuario_menu ?? null;
    if ($usuario_menu === null) {
        $itens_conta = ['login.php' => 'Entrar', 'cadastro.php' => 'Cadastrar'];
    } elseif ($usuario_menu['perfil'] === 'ADOTANTE') {
        $itens_conta = [
            'minha_conta.php' => 'Minha conta',
            'minhas_solicitacoes.php' => 'Minhas solicitações',
            'function/sair.php' => 'Sair',
        ];
    } else {
        $itens_conta = ['admin/painel.php' => 'Painel', 'function/sair.php' => 'Sair'];
    }

    foreach ($itens_conta as $href => $texto) {
        if (($estilo_menu ?? 'lista') === 'botoes') {
            echo '<a href="', e($href), '"><button class="sair">', e($texto), '</button></a>', "\n";
        } else {
            echo '<li><a href="', e($href), '">', e($texto), '</a></li>', "\n";
        }
    }
