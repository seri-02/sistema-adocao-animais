<?php
    // Dados da conta (nome, e-mail, telefone), comuns ao cadastro e à edição de usuário.
    // O bloco de acesso (perfil, situação, senha) difere entre as duas telas e fica em cada página.
    // Espera $usuario (objeto da tabela usuario, sem senha, ou null no cadastro) e lib/escape.php carregado.
    if (!function_exists('e')) {
        exit;
    }

    $valor = fn(string $campo) => $usuario->$campo ?? null;
?>
                <section aria-labelledby="bloco-conta">
                    <h2 id="bloco-conta">Informações da conta</h2>
                    <div class="usu-grade">
                        <div class="usu-campo usu-campo-largo">
                            <label for="nome">Nome</label>
                            <input type="text" name="nome" id="nome" maxlength="100" autocomplete="off" required value="<?= e($valor('nome')) ?>">
                        </div>

                        <div class="usu-campo">
                            <label for="email">E-mail</label>
                            <input type="email" name="email" id="email" maxlength="150" autocomplete="off" required value="<?= e($valor('email')) ?>">
                        </div>

                        <div class="usu-campo">
                            <label for="telefone">Telefone <span class="usu-opcional">(opcional)</span></label>
                            <input type="tel" name="telefone" id="telefone" maxlength="20" autocomplete="off" value="<?= e($valor('telefone')) ?>">
                        </div>
                    </div>
                </section>
