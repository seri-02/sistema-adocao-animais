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
    <title>Abrigo dos Gatos - Benefícios da castração</title>
</head>
<body id="benecifios-body">
<header class="beneficios-header">
        <span id="span-logo">
            <a href="index.php" id="logo">Abrigo dos Gatos</a>
        </span>
        <nav id="nav">
            <button id="btn-mobile"><img src="img/menu.svg" alt=""></button>
            <ul id="menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="seja_voluntario.php">Seja voluntário</a></li>
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
        <span id="titulo">Benefícios da castração</span>
    </div>

    <main class="conteudo">
        <div class="row">
            <div class="conteudo-numero">
                <div>
                    <i class="fa-solid fa-1"></i>
                    <h3>Previne doenças</h3>
                </div>
                <p>Em fêmeas, o procedimento reduz as chances de câncer de mama entre 40% a 60%, e quanto mais cedo, melhor. Já em machos, reduz os problemas de próstata e evita-se o câncer de testículo, que pode ser fatal. Também diminiu acidentes, briga e transmissão de doenças infecto-contogiosas, como por exemplo o vírus da FIV (Aids felina).</p>
            </div>
    
            <div class="conteudo-numero">
                <div>
                    <i class="fa-solid fa-2"></i>
                    <h3>Controle de natalidade felina</h3>
                </div>
                <p>Através da castração, é possível ter um controle populacional, evitando ninhadas indesejadas e diminuindo a quantidade de animais abandonados. Não é difícil vermos filhotes abandonados em sacolas, e balde de lixo e caçambas, fazendo com que o número de gatos em situação de rua aumente cada vez mais, situação que poderia ser evitada com a castração.</p>
            </div>
        </div>

        <div class="row">
            <div class="conteudo-numero">
                <div>
                    <i class="fa-solid fa-3"></i>
                    <h3>Mais dóceis</h3>
                </div>
                <p>Gatos não castrados constumam ser mais agressivos, isso ocorre por conta do seu instito, de ir em defesa do seu território, e também pelos hormônios, que levam a brigas para conquistar fêmeas durante sua necessidade de acasalamento. Estudos comprovam que a castração diminui em até 94% as fugas dos nossos amigos peludos, já que eles perdem o impulso dos hormônios e a necessidade de ir a procura de outros gatos para procriar.</p>
            </div>
    
            <div class="conteudo-numero">
                <div>
                    <i class="fa-solid fa-4"></i>
                    <h3>Expectativa de vida</h3>   
                </div>
                <p>A expectativa de vida de gatos machos e fêmeas castrados é o dobro em relação aos não castrados e isso acontece por conta dos gatos castrados ficarem menos expostos á doenças e riscos.</p>
            </div>
        </div>
    </main>

    <div id="load" class="load"></div>
    
    <div class="conteudo-atualizacoes">
        <h3>Atualizações do Abrigo</h3>
        <p>Acompanhe-nos!</p>
        <div id="atualizacoes">
            <span class="conteudo-atualizacoes-numero">
                <p class="numero">
                    <?php
                        require_once 'lib/conn.php';
                        $sql = "SELECT COUNT(id_gato) AS qtd_no_abrigo FROM gato WHERE adotado = 0";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute();
                        $results = $stmt->fetch(PDO::FETCH_ASSOC);
                        echo $results['qtd_no_abrigo'];
                    ?> 
                </p>
                <p>Adotados</p>
            </span>
            <span id="borda"></span>
            <span class="conteudo-atualizacoes-numero">     
                <p class="numero">
                    <?php
                        require_once 'lib/conn.php';
                        $sql = "SELECT COUNT(id_gato) AS qtd_no_abrigo FROM gato WHERE adotado = 1";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute();
                        $results = $stmt->fetch(PDO::FETCH_ASSOC);
                        echo $results['qtd_no_abrigo'];
                    ?> 
                </p>
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