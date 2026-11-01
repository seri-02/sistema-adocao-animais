<?php
    // Campos do formulário de animal, compartilhados pelo cadastro e pela edição.
    // Espera $animal (objeto da tabela animal, ou null no cadastro); lib/animal.php e lib/escape.php já carregados.
    // Os name/value dos campos são o contrato com func_cadastrar_gato.php e func_editar_gato.php.
    if (!function_exists('rotulo') || !function_exists('e')) {
        exit;
    }

    $valor = function (string $campo) use ($animal) {
        return $animal->$campo ?? null;
    };
    $selecionado = function ($atual, $opcao): string {
        return (string) $atual === (string) $opcao ? ' selected' : '';
    };
    $sim_nao = ['' => 'Não informado', '1' => 'Sim', '0' => 'Não'];

    // Imagem atual só é exibida se o arquivo existir de fato em image/
    $imagem_atual = $animal !== null ? (string) $animal->imagem : '';
    $tem_imagem_atual = $imagem_atual !== '' && $imagem_atual === basename($imagem_atual)
        && is_file(PASTA_IMAGENS . '/' . $imagem_atual);
?>
            <section class="cartao ani-bloco" aria-labelledby="bloco-basico">
                <h2 id="bloco-basico">Informações básicas</h2>
                <div class="ani-grade">
                    <div class="ani-campo ani-campo-largo">
                        <label for="nome">Nome <span class="ani-obrigatorio">(obrigatório)</span></label>
                        <input type="text" name="nome" id="nome" maxlength="80" required value="<?= e($valor('nome')) ?>">
                    </div>

                    <div class="ani-campo">
                        <label for="especie">Espécie <span class="ani-obrigatorio">(obrigatório)</span></label>
                        <select name="especie" id="especie" required>
                            <option value="" disabled<?= $valor('especie') === null ? ' selected' : '' ?>>Selecione</option>
                            <?php foreach (ESPECIES as $opcao => $texto) { ?>
                            <option value="<?= e($opcao) ?>"<?= $selecionado($valor('especie'), $opcao) ?>><?= e($texto) ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="ani-campo">
                        <label for="sexo">Sexo</label>
                        <select name="sexo" id="sexo" required>
                            <?php foreach (SEXOS as $opcao => $texto) { ?>
                            <option value="<?= e($opcao) ?>"<?= $selecionado($valor('sexo') ?? 'NAO_INFORMADO', $opcao) ?>><?= e($texto) ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="ani-campo">
                        <label for="data_nascimento_estimada">Data de nascimento estimada</label>
                        <input type="date" name="data_nascimento_estimada" id="data_nascimento_estimada" max="<?= e(date('Y-m-d')) ?>" value="<?= e($valor('data_nascimento_estimada')) ?>" aria-describedby="ajuda-nascimento">
                        <p class="ani-ajuda" id="ajuda-nascimento">Opcional. A idade exibida no site é calculada a partir desta data.</p>
                    </div>

                    <div class="ani-campo">
                        <label for="porte">Porte</label>
                        <select name="porte" id="porte">
                            <option value=""<?= $valor('porte') === null ? ' selected' : '' ?>>Não informado</option>
                            <?php foreach (PORTES as $opcao => $texto) { ?>
                            <option value="<?= e($opcao) ?>"<?= $selecionado($valor('porte'), $opcao) ?>><?= e($texto) ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </section>

            <section class="cartao ani-bloco" aria-labelledby="bloco-saude">
                <h2 id="bloco-saude">Saúde e cuidados</h2>
                <div class="ani-grade">
                    <?php foreach (['castrado' => 'Castrado', 'vacinado' => 'Vacinado'] as $campo => $titulo) { ?>
                    <div class="ani-campo">
                        <label for="<?= e($campo) ?>"><?= e($titulo) ?></label>
                        <select name="<?= e($campo) ?>" id="<?= e($campo) ?>">
                            <?php foreach ($sim_nao as $opcao => $texto) { ?>
                            <option value="<?= e($opcao) ?>"<?= $selecionado($valor($campo) ?? '', $opcao) ?>><?= e($texto) ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <?php } ?>

                    <div class="ani-campo ani-campo-largo">
                        <label for="condicao_saude">Condição de saúde</label>
                        <textarea name="condicao_saude" id="condicao_saude" rows="3"><?= e($valor('condicao_saude')) ?></textarea>
                    </div>
                </div>
            </section>

            <section class="cartao ani-bloco" aria-labelledby="bloco-perfil">
                <h2 id="bloco-perfil">Perfil do animal</h2>
                <div class="ani-grade">
                    <div class="ani-campo ani-campo-largo">
                        <label for="comportamento">Comportamento</label>
                        <textarea name="comportamento" id="comportamento" rows="3"><?= e($valor('comportamento')) ?></textarea>
                    </div>

                    <div class="ani-campo ani-campo-largo">
                        <label for="descricao">Descrição</label>
                        <textarea name="descricao" id="descricao" rows="6"><?= e($valor('descricao')) ?></textarea>
                    </div>
                </div>
            </section>

            <section class="cartao ani-bloco" aria-labelledby="bloco-situacao">
                <h2 id="bloco-situacao">Situação e imagem</h2>
                <div class="ani-grade">
                    <div class="ani-campo ani-campo-largo">
                        <?php if ($animal === null) { ?>
                        <p class="ani-rotulo">Situação do animal</p>
                        <p class="ani-situacao-fixa">Todo animal é cadastrado como <strong><?= e(STATUS_ANIMAL['DISPONIVEL']) ?></strong>. A situação pode ser alterada depois, na edição.</p>
                        <?php } elseif (in_array($animal->status, STATUS_ADMINISTRAVEIS, true)) { ?>
                        <label for="status">Situação do animal</label>
                        <select name="status" id="status" required aria-describedby="ajuda-status">
                            <?php foreach (STATUS_ADMINISTRAVEIS as $opcao) { ?>
                            <option value="<?= e($opcao) ?>"<?= $selecionado($animal->status, $opcao) ?>><?= e(STATUS_ANIMAL[$opcao]) ?></option>
                            <?php } ?>
                        </select>
                        <p class="ani-ajuda" id="ajuda-status">Somente animais disponíveis aparecem no catálogo de adoção do site.</p>
                        <?php } else { ?>
                        <p class="ani-rotulo">Situação do animal</p>
                        <p class="ani-situacao-fixa"><strong><?= e(rotulo(STATUS_ANIMAL, $animal->status)) ?></strong>: definida pelo fluxo de adoção e não pode ser alterada aqui.</p>
                        <?php } ?>
                    </div>

                    <?php if ($animal !== null) { ?>
                    <div class="ani-campo ani-campo-largo">
                        <p class="ani-rotulo">Imagem atual</p>
                        <?php if ($tem_imagem_atual) { ?>
                        <img class="ani-preview" src="../../image/<?= e($imagem_atual) ?>" alt="Foto atual de <?= e($animal->nome) ?>">
                        <?php } else { ?>
                        <div class="ani-preview ani-sem-foto" aria-hidden="true">
                            <svg class="icone" viewBox="0 0 24 24" focusable="false"><circle cx="6" cy="10" r="2"/><circle cx="10" cy="6" r="2"/><circle cx="14" cy="6" r="2"/><circle cx="18" cy="10" r="2"/><path d="M12 12c-3 0-5 3-5 5.5S9 20 12 20s5-.5 5-2.5S15 12 12 12z"/></svg>
                        </div>
                        <p class="ani-ajuda">Nenhuma imagem disponível para este animal.</p>
                        <?php } ?>
                    </div>
                    <?php } ?>

                    <div class="ani-campo ani-campo-largo">
                        <label for="imagem"><?= $animal === null ? 'Imagem <span class="ani-obrigatorio">(obrigatório)</span>' : 'Nova imagem' ?></label>
                        <input type="file" name="imagem" id="imagem" accept="image/jpeg,image/png,image/gif" aria-describedby="ajuda-imagem"<?= $animal === null ? ' required' : '' ?>>
                        <p class="ani-ajuda" id="ajuda-imagem">
                            <?= $animal === null
                                ? 'Arquivo JPG, PNG ou GIF com uma foto nítida do animal.'
                                : 'Envie uma nova imagem apenas se quiser substituir a atual. Arquivo JPG, PNG ou GIF.' ?>
                        </p>
                    </div>
                </div>
            </section>
