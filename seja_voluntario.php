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
<body id="seja-voluntario-body">
    <header class="seja-voluntario-header">
        <span id="span-logo">
            <a href="index.php" id="logo">Abrigo dos Gatos</a>
        </span>
        <nav id="nav">
            <button id="btn-mobile"><img src="img/menu.svg" alt=""></button>
            <ul id="menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="adocoes.php">Adoções</a></li>
                <li><a href="localizacao.php">Localização</a></li>
                <li><a href="denuncia.php">Denúncia</a></li>
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
        <span id="titulo">Seja voluntário</span>
    </div>

    <main class="conteudo">
        <h3 class="titulo-h3">REQUISITOS</h3>

        <div class="row">
            <div class="conteudo-numero">
                <div>
                    <i class="fa-solid fa-1"></i>
                    <h3>Ser responsável;</h3>
                </div>
            </div>
    
            <div class="conteudo-numero">
                <div>
                    <i class="fa-solid fa-2"></i>
                    <h3>Ter disponibilidade;</h3>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="conteudo-numero">
                <div>
                    <i class="fa-solid fa-3"></i>
                    <h3>Ter compromisso;</h3>
                </div>
            </div>
    
            <div class="conteudo-numero">
                <div>
                    <i class="fa-solid fa-4"></i>
                    <h3>Amar os animais</h3>
                </div>
            </div>
        </div>    
        
        <div id="form-seja-voluntario">
            <span>Cadastre-se</span>
            <form action="func_cadastro_voluntario.php" method="post" id="seja-voluntario">
                <div class="campo">
                    <label for="nome">Digite seu nome</label>
                    <input type="text" name="nome" id="nome">
                </div>
                <div class="campo">
                    <label for="email">Digite seu email</label>
                    <input type="email" name="email" id="email">
                </div>
                <div class="campo">
                    <label for="idade">Digite sua Idade</label>
                    <input type="number" name="idade" id="idade">
                </div>
                <div class="campo">
                    <label for="dia_disponivel">Digite os dias disponível</label>
                    <input type="text" name="dia_disponivel" id="dia_disponivel">
                </div>
                <button type="submit">
                    Enviar
                </button>
            </form>
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
                        <span class="fas fa-map-marker"></span>
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
                <h3>Área administrativa</h3>
				<div class="conteudos-footer">
				    <a href="admin/admin.php"><button>Área administrativa</button></a>	
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