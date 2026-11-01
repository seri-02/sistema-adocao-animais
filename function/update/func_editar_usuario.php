<?php
    require_once __DIR__ . '/../../lib/auth.php';
    $usuario_logado = exigir_perfis([PERFIL_ADMIN]);

    require_once __DIR__ . '/../../lib/conn.php';
    require_once __DIR__ . '/../../lib/escape.php';
    require_once __DIR__ . '/../../lib/usuario.php';

    // Só inteiro positivo: ausente, vazio, texto, decimal, array ou overflow nunca viram outro id
    $id_recebido = $_POST['id_usuario'] ?? null;
    $id_alvo = is_string($id_recebido)
        ? filter_var($id_recebido, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])
        : false;
    $id_ator = (int) $usuario_logado['id_usuario'];
    $propria_conta = $id_alvo === $id_ator;

    $erros = [];
    $entrada = $_POST;
    if ($propria_conta) {
        // Regra do servidor (não só do formulário): o administrador não remove o próprio acesso
        if ((isset($_POST['perfil']) && $_POST['perfil'] !== PERFIL_ADMIN)
            || (isset($_POST['ativo']) && valor_texto($_POST, 'ativo') !== '1')) {
            $erros[] = 'Você não pode alterar o próprio perfil nem desativar a própria conta.';
        }
        $entrada['perfil'] = PERFIL_ADMIN;
        $ativo = 1;
    } else {
        $ativo = valor_texto($_POST, 'ativo');
        if ($ativo !== '0' && $ativo !== '1') {
            $erros[] = 'Situação inválida.';
        }
        $ativo = (int) $ativo;
    }

    [$dados, $erros_formulario] = ler_formulario_usuario($entrada, array_keys(PERFIS), false);
    $erros = array_merge($erros, $erros_formulario);
    if ($id_alvo === false) {
        // Id malformado: nenhuma consulta nem UPDATE; mesma resposta de usuário inexistente
        $erros = ['Usuário não encontrado.'];
    }

    if (!$erros) {
        try {
            $conn->beginTransaction();
            [$linhas, $admins_ativos] = travar_usuarios_para_alteracao($conn, $id_alvo, $id_ator);
            $alvo = $linhas[$id_alvo] ?? null;
            $continua_admin_ativo = $dados['perfil'] === PERFIL_ADMIN && $ativo === 1;

            if ($alvo === null) {
                $erros[] = 'Usuário não encontrado.';
            } elseif (!eh_admin_ativo($linhas[$id_ator] ?? null)) {
                // Quem executa a ação perdeu o perfil de ADMIN enquanto a requisição esperava o lock
                $erros[] = 'Acesso negado.';
            } elseif (eh_admin_ativo($alvo) && !$continua_admin_ativo && $admins_ativos <= 1) {
                $erros[] = 'O sistema precisa de pelo menos um administrador ativo.';
            }

            if ($erros) {
                $conn->rollBack();
            } else {
                $sql = "UPDATE usuario
                           SET nome = :nome, email = :email, telefone = :telefone, perfil = :perfil, ativo = :ativo"
                     . ($dados['senha'] !== '' ? ', senha = :senha' : '')   // senha vazia: mantém o hash atual
                     . " WHERE id_usuario = :id_usuario";
                $stmt = $conn->prepare($sql);
                $stmt->bindValue(':nome', $dados['nome']);
                $stmt->bindValue(':email', $dados['email']);
                $stmt->bindValue(':telefone', $dados['telefone'], $dados['telefone'] === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
                $stmt->bindValue(':perfil', $dados['perfil']);
                $stmt->bindValue(':ativo', $ativo, PDO::PARAM_INT);
                if ($dados['senha'] !== '') {
                    $stmt->bindValue(':senha', password_hash($dados['senha'], PASSWORD_DEFAULT));
                }
                $stmt->bindValue(':id_usuario', $id_alvo, PDO::PARAM_INT);
                $stmt->execute();
                $conn->commit();
            }
        } catch (PDOException $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            if (eh_email_duplicado($e)) {
                $erros[] = MSG_EMAIL_DUPLICADO;
            } else {
                error_log('Falha ao editar usuário: ' . $e->getMessage());
                $erros[] = 'Não foi possível atualizar o usuário. Nenhuma alteração foi salva.';
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <link rel="stylesheet" href="../../css/style.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" integrity="sha512-1sCRPdkRXhBV2PBLUdRb4tMg1w2YPf37qatUFeS7zlBy7jJI8Lf4VHwWfZZfpXtYSLy85pkm9GaYVYMfw5BC1A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="icon" href="">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if (!$erros) { ?>
    <meta http-equiv="refresh" content="2; url=../../admin/tabelas/tabela_usuario.php">
    <?php } ?>
    <title>Abrigo dos Gatos</title>
</head>
<body id="adm">
    <header>
        <span>
            <a href="../../index.php">Abrigo dos gatos</a>
        </span>
    </header>

    <main>
        <?php if (!$erros) { ?>
        <div class="alert success">
            <i class="far fa-check-circle"></i>
            <p class="lead">Usuário atualizado com sucesso!</p>
        </div>
        <?php } else { ?>
        <div class="alert danger">
            <i class="fas fa-times"></i>
            <?php foreach ($erros as $erro) { ?>
                <p class="lead"><?= e($erro) ?></p>
            <?php } ?>
            <p><a href="javascript:history.back()">Voltar ao formulário</a></p>
        </div>
        <?php } ?>
    </main>

    <footer>
        <div id="baixo">
			<div class="centro">
				<span class="credito">&copy; Abrigo dos gatos |</span>
				<span>Design by <a href="#">ANA LAURA </a>2022</span>
			</div>
		</div>
    </footer>
</body>
</html>
