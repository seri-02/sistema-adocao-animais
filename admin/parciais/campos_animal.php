<?php
    // Campos do formulário de animal, compartilhados pelo cadastro e pela edição.
    // Espera $animal (objeto da tabela animal, ou null no cadastro); lib/animal.php e lib/escape.php já carregados.
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
?>
            <div class="campo">
                <input type="text" name="nome" id="nome" maxlength="80" required value="<?= e($valor('nome')) ?>">
                <span>Nome</span>
            </div>

            <div class="campo">
                <select name="especie" id="especie" required>
                    <option value="" disabled hidden<?= $valor('especie') === null ? ' selected' : '' ?>></option>
                    <?php foreach (ESPECIES as $opcao => $texto) { ?>
                        <option value="<?= e($opcao) ?>"<?= $selecionado($valor('especie'), $opcao) ?>><?= e($texto) ?></option>
                    <?php } ?>
                </select>
                <span>Espécie</span>
            </div>

            <div class="campo">
                <select name="sexo" id="sexo" required>
                    <?php foreach (SEXOS as $opcao => $texto) { ?>
                        <option value="<?= e($opcao) ?>"<?= $selecionado($valor('sexo') ?? 'NAO_INFORMADO', $opcao) ?>><?= e($texto) ?></option>
                    <?php } ?>
                </select>
                <span>Sexo</span>
            </div>

            <div class="campo">
                <input type="date" name="data_nascimento_estimada" id="data_nascimento_estimada" max="<?= e(date('Y-m-d')) ?>" value="<?= e($valor('data_nascimento_estimada')) ?>">
                <span>Data de nascimento estimada</span>
            </div>

            <div class="campo">
                <select name="porte" id="porte">
                    <option value=""<?= $valor('porte') === null ? ' selected' : '' ?>>Não informado</option>
                    <?php foreach (PORTES as $opcao => $texto) { ?>
                        <option value="<?= e($opcao) ?>"<?= $selecionado($valor('porte'), $opcao) ?>><?= e($texto) ?></option>
                    <?php } ?>
                </select>
                <span>Porte</span>
            </div>

            <?php foreach (['castrado' => 'Castrado', 'vacinado' => 'Vacinado'] as $campo => $titulo) { ?>
                <div class="campo">
                    <select name="<?= e($campo) ?>" id="<?= e($campo) ?>">
                        <?php foreach ($sim_nao as $opcao => $texto) { ?>
                            <option value="<?= e($opcao) ?>"<?= $selecionado($valor($campo) ?? '', $opcao) ?>><?= e($texto) ?></option>
                        <?php } ?>
                    </select>
                    <span><?= e($titulo) ?></span>
                </div>
            <?php } ?>

            <div class="campo">
                <textarea name="condicao_saude" id="condicao_saude" cols="30" rows="3"><?= e($valor('condicao_saude')) ?></textarea>
                <span>Condição de saúde</span>
            </div>

            <div class="campo">
                <textarea name="comportamento" id="comportamento" cols="30" rows="3"><?= e($valor('comportamento')) ?></textarea>
                <span>Comportamento</span>
            </div>

            <div class="campo">
                <textarea name="descricao" id="descricao" cols="30" rows="6"><?= e($valor('descricao')) ?></textarea>
                <span>Descrição</span>
            </div>

            <?php if ($animal !== null) { ?>
                <div class="campo">
                    <?php if (in_array($animal->status, STATUS_ADMINISTRAVEIS, true)) { ?>
                        <select name="status" id="status" required>
                            <?php foreach (STATUS_ADMINISTRAVEIS as $opcao) { ?>
                                <option value="<?= e($opcao) ?>"<?= $selecionado($animal->status, $opcao) ?>><?= e(STATUS_ANIMAL[$opcao]) ?></option>
                            <?php } ?>
                        </select>
                        <span>Disponibilidade</span>
                    <?php } else { ?>
                        <p>Situação: <strong><?= e(rotulo(STATUS_ANIMAL, $animal->status)) ?></strong> (definida pelo fluxo de adoção; não pode ser alterada aqui)</p>
                    <?php } ?>
                </div>
            <?php } ?>

            <div class="campo">
                <?php if ($animal !== null && $animal->imagem) { ?>
                    <p>Imagem atual:</p>
                    <img src="../../image/<?= e($animal->imagem) ?>" alt="<?= e($animal->nome) ?>" height="120">
                    <p>Envie um arquivo apenas para substituí-la.</p>
                <?php } ?>
                <input type="file" name="imagem" id="imagem" accept="image/jpeg,image/png,image/gif"<?= $animal === null ? ' required' : '' ?>>
            </div>
