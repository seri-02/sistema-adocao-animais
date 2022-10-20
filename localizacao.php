<!DOCTYPE html>
<html lang="pt-br">
<head>
    <link rel="stylesheet" href="css/style.css">

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
<body>
    <header class="localizacao-header">
        <span id="span-logo">
            <a href="index.php" id="logo">Abrigo dos Gatos</a>
        </span>
        <nav id="nav">
            <button id="btn-mobile"><img src="img/menu.svg" alt=""></button>
            <ul id="menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="seja_voluntario.php">Seja voluntário</a></li>
                <li><a href="adocoes.php">Adoções</a></li>
            </ul>
        </nav>
    </header> 

    <main class="conteudo-titulo">
        <span id="titulo">Localização</span>
        <div class="conteudo-localizacao">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3662.909458514996!2d-47.86517398505356!3d-233552945847855!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94c5d91e95230b29%3A0xbe294eb61ccc2c13!2sR%20Onze%20de%20Agosto%2C%20Tatu%C3%AD%20-%20SP!5e0!3m2!1spt-BR!2sbr!4v1660135287473!5m2!1spt-BR!2sbr" width="600" height="450"style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </main>
    
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
</html>