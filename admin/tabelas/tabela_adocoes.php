<?php
    session_start();
    if(!isset($_SESSION['id_user'])) {
        header('Location: ../../index.php');
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
    <title>Abrigo dos Gatos</title>
</head>
<body id="adm">
    <header class="header-adm">
        <span>
            <a href="../../index.php">Abrigo dos gatos</a> 
        </span>
        <a href="../painel.php"><button><i class="fa-solid fa-chevron-left"></i> Voltar</button></a>
    </header>

        <div class="tabela">
            <h3>Lista de Adoções</h3>
            <table>
                <thead>
                    <tr>
                        <th class="opcao">#</th>
                        <th>Nome gato</th>
                        <th>Nome Adotante</th>
                        <th>Telefone</th>  
                        <th>Excluir</th>
                    </tr>
                </thead>
                
                <tbody>
                    <?php
                        require_once '../../lib/conn.php';

                        $sql = "SELECT adocoes.id_adocao, gato.nome, adocoes.nome_adotante, adocoes.telefone FROM adocoes, gato WHERE adocoes.fk_id_gato = gato.id_gato ORDER BY adocoes.id_adocao ASC";     
                        $stmt = $conn->query($sql);
                        $gatos = $stmt->fetchAll(PDO::FETCH_OBJ);
                        foreach ($gatos as $gato) {
                            ?>
                        <tr>
                            <td class="id"><?= $gato->id_adocao ?></td>
                            <td><?= $gato->nome ?></td>
                            <td><?= $gato->nome_adotante ?></td>
                            <td><?= $gato->telefone ?></td>
                            <td><a href=""><i class="fa-solid fa-trash"></i></a></td>
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