<?php
    require_once __DIR__ . '/../../lib/auth.php';
    require_once __DIR__ . '/../../lib/escape.php';
    $usuario_logado = exigir_perfis(PERFIS_EQUIPE);
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
            <h3>Adoções legadas</h3>
            <p>Histórico anterior ao fluxo de solicitações. As adoções atuais estão em <a href="../solicitacoes.php">Solicitações de adoção</a>.</p>
            <table>
                <thead>
                    <tr>
                        <th class="opcao">#</th>
                        <th>Animal</th>
                        <th>Nome Adotante</th>
                        <th>Telefone</th>  
                    </tr>
                </thead>
                
                <tbody>
                    <?php
                        require_once '../../lib/conn.php';

                        $sql = "SELECT adocoes.id_adocao, animal.nome, adocoes.nome_adotante, adocoes.telefone
                                  FROM adocoes
                                  JOIN animal ON animal.id_animal = adocoes.id_animal
                                 ORDER BY adocoes.id_adocao ASC";
                        $stmt = $conn->query($sql);
                        $adocoes = $stmt->fetchAll(PDO::FETCH_OBJ);
                        foreach ($adocoes as $adocao) {
                            ?>
                        <tr>
                            <td class="id"><?= e($adocao->id_adocao) ?></td>
                            <td><?= e($adocao->nome) ?></td>
                            <td><?= e($adocao->nome_adotante) ?></td>
                            <td><?= e($adocao->telefone) ?></td>
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