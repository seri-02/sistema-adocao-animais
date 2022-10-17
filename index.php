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
    <header class="index-header">
        <span id="span-logo">
            <a href="index.php" id="logo">Abrigo dos Gatos</a>
        </span>
        <nav id="nav">
            <button id="btn-mobile"><img src="img/menu.svg" alt=""></button>
            <ul id="menu">
                <li><a href="seja_voluntario.php">Seja voluntário</a></li>
                <li><a href="adocoes.php">Adoções</a></li>
                <li><a href="localizacao.php">Localização</a></li>
                <li><a href="admin/admin.php">Área administrativa</a></li>
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
        <span id="titulo">Adote uma vida</span>
    </div>
    
    <main class="conteudo">
        <div class="conteudo-light">
            <h3>Adotar</h3>
            <div>
                <img src="img/gatinho.jpg" width="670" height="300">
                <p>É sobre ter uma amizade verdadeira e leal, que nos ame com pureza e livre de julgamentos, mesmo nos momentos mais difíceis. Adotar um animal é 
                adotar um amigo! Adotar é um gesto de amor! É dar a chance de um animalzinho ter uma família e a oportunidade de ser recompensado diariamente com muito amor!
                Comprar animais alimenta um mercado muito nocivo à vida dos animais. Adotar é salvar vidas!</p> 
            </div>
            <a href="adocoes.php"><button id="btn-adote">ADOTE</button></a>
        </div>

        <div id="fileira">
            <div class="conteudos-fileira">
                <h3>Benefícios da castração</h3>
                <p>Confira as vantagens de castrar o seu gato e veja como a castração é um ato de amor, previnindo doenças e diminuindo os riscos</p>
                <a href="beneficios_castracao.php"><button>Saiba mais</button></a>
            </div>
    
            <div class="conteudos-fileira">
                <h3 id="tamanho-h3-motivos">5 motivos para adotar um gato adulto</h3>
                <p>Nosso abrigo contem 50 gatos adultos, confira algumas vantagens de adotar um deles!</p>
                <a href="5_motivos.php"><button>Saiba mais</button></a> 
            </div>

            <div class="conteudos-fileira">
                <h3>Lugar de gato é dentro casa</h3>
                <p>Um gato doméstico vive em média 15 anos, um gato de rua vive 4 anos e um gato doméstico que foi abandonado, 3 meses.</p>
                <a href="gato_dentro_de_casa.php"><button>Saiba mais</button></a> 
            </div>

            <div class="conteudos-fileira" id="conheca-felv">
                <h3>Conheça a FelV</h3>
                <p>A FelV é a leucemia felina, ou seja, ela é uma doença exclusiva dos gatos. Saiba os cuidados</p>
                <a href="felv.php"><button>Saiba mais</button></a> 
            </div>
        </div>

        <div class="conteudo-light">
            <h3>Arrecadação de ração</h3>
            <div>
                <img src="img/gato_comendo.jpg" width="670" height="300" id="gato-comendo">
                <p> Com mais de 50 gatos, gastamos cerca de 25kg de ração por semana, totalizando um gasto de mil reais por mês, apenas com a ração, sem contar com as demais despesas! Sem a ajuda de vocês não conseguiremos, então bora ajudar?</p>  
            </div>
            <a href="doe.php"><button>DOE</button></a>
        </div>

    </main>
    
    <div class="fazemos-o-impossivel">
        <div class="conteudo-titulo">
            <span class="titulo">Fazemos o impossivel ser realidade</span>
        </div>
    </div>

    <main class="conteudo">
        <div class="conteudo-light">
            <p id="texto-fazemos-o-impossivel">Lutamos contra o abandono de gatos domésticos. Não medimos esforços para ajudar e dar suporte quando necessário.</p>
        </div>
    </main>

    <div class="causa">
        <div class="conteudo-titulo">
            <span class="titulo">Você também pode ajudar com essa causa</span>
        </div>
    </div>

    <main class="conteudo">
        <h3 class="titulo-h3">Como ?</h3>
        <div class="row">
            <div class="conteudo-numero">
                <div>
                    <i class="fa-solid fa-1"></i>
                    <h3>Doe suprimentos</h3>
                </div>
                    <p>Abrigos se beneficiam de doações, como ração, produtos de limpeza, brinquedos, entre outros. Isso torna possível que o abrigo utilize seu orçamento em outras áreas</p>
            </div>

            <div class="conteudo-numero">
                <div>
                    <i class="fa-solid fa-2"></i>
                    <h3>Doe dinheiro</h3>
                </div>
                    <p>Além de ajudar a manter as operaçoes diárias, seu dinheiro pode contribuir para a expansão do abrigo e assim, consequentemente, mais animais poderão ser ajudados.</p>
            </div>
        </div>

        <div class="row">
            <div class="conteudo-numero">
                <div>
                    <i class="fa-solid fa-3"></i>
                    <h3>Adote</h3>
                </div>
                    <p>Quando você adota, você ganha um amigo para a vida, salva uma e também, abre espaço no abrigo para que outro pet possa ser escolhido.</p>
            </div>

            <div class="conteudo-numero">
                <div>
                    <i class="fa-solid fa-4"></i>
                    <h3>Doe seus serviços</h3>
                </div>
                <p>Se você possui alguma habilidade ou especificação que ajude o abrigo, doe seu serviço</p>
            </div>
        </div>

        <div class="row">
            <div class="conteudo-numero">
                <div>
                    <i class="fa-solid fa-5"></i>
                    <h3>Ofereça um lar temporário</h3>
                </div>
                    <p>Muitos abrigos têm serviços e recursos limitados e, infelizmente, não conseguem atender todos os pets que necessitam de carinho e cuidados. Você pode adotar algum pet por um tempo, té que ele ganhe um lar definitivo onde será amado, ou até que ele pssa voltar ao abrigo para receber os cuidados de que precisa.</p>
            </div>

            <div class="conteudo-numero">
                <div>
                    <i class="fa-solid fa-6"></i>
                    <h3>Seja voluntário</h3>
                </div>
                    <p>As vezes o abrigo fica sobrecarregado pela quantidade de gatos, ser voluntário é poder ajudar a cuidar dos nossos gatinhos</p>
                    <a href="seja_voluntario.php"><button>Seja voluntário</button></a> 
            </div>
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
                    <a href="" id="saiba-mais-footer">Saiba mais</a>
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
                        <span class="texto">Rua Onze de agosto</span>
                    </div>

                    <div id="telefone">
                        <span class="fas fas fa-phone-alt"></span>
						<span class="texto">+55 61 9999-9999</span>
                    </div>
                    
                    <div id="email">
                        <span class="fas fa-envelope"></span>
						<span class="texto">exemplo@exemplo.com</span>
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