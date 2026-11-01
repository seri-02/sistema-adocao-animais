<?php
    // Campos do formulário de voluntário, compartilhados pelo cadastro e pela edição da equipe.
    // Espera $voluntario (objeto da tabela voluntario, ou null no cadastro); lib/voluntario.php e
    // lib/escape.php já carregados. Os name são o contrato com os endpoints; a validação
    // continua no servidor (ler_formulario_voluntario), os atributos abaixo só ajudam o navegador.
    if (!function_exists('ler_formulario_voluntario') || !function_exists('e')) {
        exit;
    }

    $valor = fn(string $campo) => $voluntario->$campo ?? null;
?>
            <div class="cartao vol-cartao">
                <section aria-labelledby="bloco-dados">
                    <h2 id="bloco-dados">Informações do voluntário</h2>
                    <div class="vol-grade">
                        <div class="vol-campo vol-campo-largo">
                            <label for="nome">Nome</label>
                            <input type="text" name="nome" id="nome" maxlength="<?= e(VOLUNTARIO_NOME_MAXIMO) ?>" autocomplete="off" required value="<?= e($valor('nome')) ?>">
                        </div>

                        <div class="vol-campo">
                            <label for="email">E-mail</label>
                            <input type="email" name="email" id="email" maxlength="<?= e(VOLUNTARIO_EMAIL_MAXIMO) ?>" autocomplete="off" required value="<?= e($valor('email')) ?>">
                        </div>

                        <div class="vol-campo">
                            <label for="idade">Idade</label>
                            <input type="number" name="idade" id="idade" min="1" step="1" inputmode="numeric" required value="<?= e($valor('idade')) ?>">
                        </div>
                    </div>
                </section>

                <section class="vol-subsecao" aria-labelledby="bloco-disponibilidade">
                    <h2 id="bloco-disponibilidade">Disponibilidade</h2>
                    <div class="vol-campo">
                        <label for="dia_disponivel">Dias disponíveis</label>
                        <input type="text" name="dia_disponivel" id="dia_disponivel" maxlength="<?= e(VOLUNTARIO_DIA_MAXIMO) ?>" required aria-describedby="ajuda-dias" value="<?= e($valor('dia_disponivel')) ?>">
                        <p class="vol-ajuda" id="ajuda-dias">Ex.: Segunda, Quarta e Sexta</p>
                    </div>
                </section>
            </div>
