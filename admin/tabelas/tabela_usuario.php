<?php
    require_once __DIR__ . '/../../lib/auth.php';
    require_once __DIR__ . '/../../lib/escape.php';
    require_once __DIR__ . '/../../lib/usuario.php';
    $usuario_logado = exigir_perfis([PERFIL_ADMIN]);
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
    <title>Abrigo dos Gatos</title>
</head>
<body id="adm">
    <header class="header-adm">
        <span>
            <a href="../../index.php">Abrigo dos gatos</a> 
        </span>
        <a href="../painel.php"><button class="sair"><i class="fa-solid fa-chevron-left"></i> Voltar</button></a>
    </header>

        <div class="tabela">
            <h3>Lista de Usuários</h3>
            <table>
                <thead>
                    <tr>
                        <th class="opcao">#</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>Perfil</th>
                        <th>Situação</th>
                        <th>Editar</th>
                        <th>Excluir</th>
                    </tr>
                </thead>
                
                <tbody>
                    <?php
                        require_once '../../lib/conn.php';
                        $sql = "SELECT id_usuario, nome, email, telefone, perfil, ativo FROM usuario ORDER BY id_usuario ASC";
                        $stmt = $conn->query($sql);
                        $usuarios = $stmt->fetchAll(PDO::FETCH_OBJ);
                        foreach ($usuarios as $usuario) {
                            ?>
                        <tr>
                            <td class="id"><?= e($usuario->id_usuario) ?></td>
                            <td><?= e($usuario->nome) ?></td>
                            <td><?= e($usuario->email) ?></td>
                            <td><?= e($usuario->telefone ?? '') ?></td>
                            <td><?= e(PERFIS[$usuario->perfil] ?? $usuario->perfil) ?></td>
                            <td><?= (int) $usuario->ativo === 1 ? 'Ativo' : 'Inativo' ?></td>
                            <td><a href="../editar/usuario.php?id=<?= e($usuario->id_usuario) ?>"><i class="fa-solid fa-user-pen"></i></a></td>
                            <?php if ((int) $usuario->id_usuario !== (int) $usuario_logado['id_usuario']) { ?>
                            <td><a href="../../function/delete/usuario.php?id=<?= e($usuario->id_usuario) ?>"><i class="fa-solid fa-trash"></i></a></td>
                            <?php } else { ?>
                            <td></td>
                            <?php } ?>
                        </tr>   
                    <?php
                        }
                    ?>
                </tbody>
            </table>
        </div>

    <footer id="footer-adm">
        <div id="baixo">
			<div class="centro">
				<span class="credito">&copy; Abrigo dos gatos |</span>
				<span>Design by <a href="#">ANA LAURA </a>2022</span>
			</div>
		</div>
    </footer>
</body>
</html>