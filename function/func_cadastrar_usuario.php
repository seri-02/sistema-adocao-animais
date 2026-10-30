<?php
  require_once __DIR__ . '/../lib/auth.php';
  $usuario_logado = exigir_perfis([PERFIL_ADMIN]);

  require_once __DIR__ . '/../lib/conn.php';
  require_once __DIR__ . '/../lib/escape.php';
  require_once __DIR__ . '/../lib/usuario.php';

  // Cadastro administrativo: somente FUNCIONARIO ou ADMIN (adotante se cadastra pelo site, F2b)
  [$dados, $erros] = ler_formulario_usuario($_POST, PERFIS_CADASTRO_ADMIN, true);

  if (!$erros) {
      try {
          $sql = "INSERT INTO usuario (nome, email, senha, telefone, perfil, ativo)
                  VALUES (:nome, :email, :senha, :telefone, :perfil, 1)";
          $insert = $conn->prepare($sql);
          $insert->bindValue(':nome', $dados['nome']);
          $insert->bindValue(':email', $dados['email']);
          $insert->bindValue(':senha', password_hash($dados['senha'], PASSWORD_DEFAULT));
          $insert->bindValue(':telefone', $dados['telefone'], $dados['telefone'] === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
          $insert->bindValue(':perfil', $dados['perfil']);
          $insert->execute();
      } catch (PDOException $e) {
          if (eh_email_duplicado($e)) {
              $erros[] = MSG_EMAIL_DUPLICADO;
          } else {
              error_log('Falha ao cadastrar usuário: ' . $e->getMessage());
              $erros[] = 'Não foi possível cadastrar o usuário. Verifique os dados informados.';
          }
      }
  }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <link rel="stylesheet" href="../css/style.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" integrity="sha512-1sCRPdkRXhBV2PBLUdRb4tMg1w2YPf37qatUFeS7zlBy7jJI8Lf4VHwWfZZfpXtYSLy85pkm9GaYVYMfw5BC1A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="icon" href="">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if (!$erros) { ?>
    <meta http-equiv="refresh" content="2; url=../admin/usuario.php">
    <?php } ?>
    <title>Abrigo dos Gatos</title>
</head>
<body id="adm">
    <header>
        <span>
            <a href="../index.php">Abrigo dos gatos</a>
        </span>
    </header>

    <main>
        <?php if (!$erros) { ?>
        <div class="alert success">
            <i class="far fa-check-circle"></i>
            <p class="lead">Usuário cadastrado com sucesso!</p>
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