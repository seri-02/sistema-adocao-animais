<?php
    // Itens de conta da navegação pública (páginas da raiz do site).
    // Espera $usuario_menu = usuario_logado_opcional() calculado ANTES de qualquer saída HTML.
    // $estilo_menu = 'botoes' para cabeçalhos simples; padrão: itens <li> do menu principal.
    // $item_conta_atual (opcional) = href do item da página atual, marcado com aria-current="page".
    // $mostrar_area_equipe = true (opcional) no include do cabeçalho moderno: inclui "Área da equipe"
    // para visitante. Vale só para esse include (o rodapé e as páginas antigas não exibem o item).
    require_once __DIR__ . '/../lib/escape.php';

    $com_area_equipe = !empty($mostrar_area_equipe);
    unset($mostrar_area_equipe);

    $usuario_menu = $usuario_menu ?? null;
    if ($usuario_menu === null) {
        // Visitante: "Área da equipe" leva ao login administrativo (ADMIN/FUNCIONARIO), separado
        // do login público de adotante. Equipe logada já vê "Painel"; adotante não precisa dele.
        $itens_conta = $com_area_equipe ? ['admin/admin.php' => 'Área da equipe'] : [];
        $itens_conta += ['login.php' => 'Entrar', 'cadastro.php' => 'Cadastrar'];
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
            $atual = $href === ($item_conta_atual ?? null) ? ' aria-current="page"' : '';
            $classe = $href === 'admin/admin.php' ? ' class="item-equipe"' : '';
            echo '<li', $classe, '><a href="', e($href), '"', $atual, '>', e($texto), '</a></li>', "\n";
        }
    }
