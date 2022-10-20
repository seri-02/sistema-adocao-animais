<?php
    require_once 'lib/conn.php';
    $sqlBuscaGato = 'SELECT * FROM gato WHERE adotado = 1';
    $buscaGato = $conn->query($sqlBuscaGato);
    $gatos = $buscaGato->fetchAll(PDO::FETCH_OBJ);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<link rel="stylesheet" href="css/style.css">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" integrity="sha512-1sCRPdkRXhBV2PBLUdRb4tMg1w2YPf37qatUFeS7zlBy7jJI8Lf4VHwWfZZfpXtYSLy85pkm9GaYVYMfw5BC1A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<script src="js/jquery.js"></script>

<link rel="icon" href="">
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Abrigo dos Gatos</title>
</head>
<body>
    <header class="adote-header">
        <span id="span-logo">
            <a href="index.php" id="logo">Abrigo dos Gatos</a>
        </span>
        <nav id="nav">
            <button id="btn-mobile"><img src="img/menu.svg" alt=""></button>
            <ul id="menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="adocoes.php">Adoções</a></li>
                <li><a href="localizacao.php">Localização</a></li>                
            </ul>
        </nav>
        <ul class="social">
            <li>
                <a href="https://www.facebook.com/gabrielaxavier.mendescoito" target="_blank"><i class="fab fa-facebook-f" aria-hidden="true"></i><div>Facebook</div></a>
            </li>
            <li>
                <a href="https://api.whatsapp.com/send?phone=55996335800" target="_blank"><i class="fab fa-whatsapp" aria-hidden="true"></i><div>Whatsapp</div></a>
            </li>
            <li>
                <a href="https://www.instagram.com/abrigo.dosgatos/" target="_blank"><i class="fab fa-instagram" aria-hidden="true"></i><div>Instagram</div></a>
            </li>
        </ul>
    </header> 

    <div class="conteudo-titulo">
        <span id="titulo">Adote</span>
    </div>

    <main class="conteudo">
        <div class="row-card">
            <?php
                require_once 'lib/conn.php';
                $sql = "SELECT * FROM gato";
                $stmt = $conn->query($sql);
                foreach($gatos as $gato) {
            ?>
                <a href="gato.php?id=<?= $gato->id_gato ?>">
                    <div class="card">
                        <img src="image/<?= $gato->img ?>">
                        <h3><?= $gato->nome ?></h1>
                        <h5><?= $gato->descricao ?></h5>
                    </div>
                </a>
            <?php
                }
            ?>
        </div>
    </main>

    <div class="conteudo-atualizacoes">
        <h3>Atualizações do Abrigo</h3>
        <p>Acompanhe-nos!</p>
        <div id="atualizacoes">
            <span class="conteudo-atualizacoes-numero">
                <p class="numero">11</p>
                <p>Adotados</p>
            </span>
            <span id="borda"></span>
            <span class="conteudo-atualizacoes-numero">
                <p class="numero">40</p>
                <p>no abrigo</p>
            </span>
        </div>
    </div>

    <button class="back-to-top" href="#"><i class=" fa-solid fa-chevron-up"></i></button>

    <footer>
        <div id="conteudo-footer">
            <div class="caixa esquerda">
                <h3>Sobre nós</h3>
                <div class="conteudos-footer">
                    <p>Nosso Abrigo é particular, não temos nenhum vínculo com o município. Nosso abrigo é só de gatos. Nosso trabalho é 100% realizado por uma equipe de voluntários</p>                    
                    <div class="social-footer">
                        <a href="https://www.facebook.com/gabrielaxavier.mendescoito" target="_blank"><span class="fab fa-facebook-f"></span></a>
                        <a  href="https://api.whatsapp.com/send?phone=55996335800" target="_blank"><span class="fab fa-whatsapp"></span></a>
                        <a href="https://www.instagram.com/abrigo.dosgatos/" target="_blank"><span class="fab fa-instagram"></span></a>
                    </div>
                </div>
            </div>

            <div class="caixa centro">
                <h3>Endereço</h3>
                <div class="conteudos-footer">
                    <div id="rua">
                        <span class="fas fa-solid fa-location-dot"></span>
                        <span class="texto">Santa Rita, Tatuí - SP</span>
                    </div>

                    <div id="telefone">
                        <span class="fas fas fa-phone-alt"></span>
						<span class="texto">+55 015 99633-5800</span>
                    </div>
                    
                    <div id="email">
                        <span class="fas fa-envelope"></span>
						<span class="texto">abrigodosgatos@gmail.com</span>
                    </div>
                </div>
            </div>

            <div class="caixa direita">
				<h3>Contato</h3>
				<div class="conteudos-footer">
					<form action="function/enviar.php" method="post">
						<div class="email campo">
                            <input type="email" id="email" name="email" required>
							<span class="texto">Email</span>
						</div>
						<div class="mensagem campo">
                            <textarea rows="2" cols="25" id="msg" name="msg" required></textarea>
							<span class="texto">Mensagem</span>
						</div>
						<div class="btn">
							<button type="submit">Enviar</button>
						</div>
					</form>
				</div>
			</div>
        </div>

        <div id="baixo">
			<div class="centro">
				<span class="credito">&copy; Abrigo dos gatos |</span>
				<span>Design by <a href="#">ANA LAURA </a>2022</span>
			</div>
		</div>
    </footer>
</body>
<script src="js/btn_menu.js"></script>
<script src="js/maquina_escrever.js"></script>
<script src="js/back_to_top.js"></script>
</html>