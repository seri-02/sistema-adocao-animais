<?php
    // Vocabulário e regras simples do domínio Animal, compartilhados pelas páginas.

    const ESPECIES = ['GATO' => 'Gato', 'CACHORRO' => 'Cachorro', 'OUTRO' => 'Outro'];
    const SEXOS = ['NAO_INFORMADO' => 'Não informado', 'MACHO' => 'Macho', 'FEMEA' => 'Fêmea'];
    const PORTES = ['PEQUENO' => 'Pequeno', 'MEDIO' => 'Médio', 'GRANDE' => 'Grande'];
    const STATUS_ANIMAL = [
        'DISPONIVEL' => 'Disponível',
        'EM_PROCESSO' => 'Em processo de adoção',
        'ADOTADO' => 'Adotado',
        'INDISPONIVEL' => 'Indisponível',
    ];
    // Únicos status que a tela administrativa define; os demais pertencem ao workflow de adoção
    const STATUS_ADMINISTRAVEIS = ['DISPONIVEL', 'INDISPONIVEL'];

    // Campos editáveis pelo formulário administrativo (cadastro e edição)
    const CAMPOS_FORMULARIO_ANIMAL = [
        'nome', 'especie', 'sexo', 'data_nascimento_estimada', 'porte', 'castrado',
        'vacinado', 'condicao_saude', 'comportamento', 'descricao',
    ];

    const NAO_INFORMADO = 'Não informado';
    const PASTA_IMAGENS = __DIR__ . '/../image';

    function rotulo(array $opcoes, ?string $valor): string
    {
        return $opcoes[$valor] ?? NAO_INFORMADO;
    }

    // NULL significa "não informado" e nunca é tratado como "Não"
    function rotulo_sim_nao($valor): string
    {
        if ($valor === null) {
            return NAO_INFORMADO;
        }
        return (int) $valor === 1 ? 'Sim' : 'Não';
    }

    // Idade calculada a partir da data estimada (nunca gravada no banco)
    function calcular_idade(?string $data_nascimento, ?DateTimeImmutable $hoje = null): string
    {
        $nascimento = $data_nascimento ? DateTimeImmutable::createFromFormat('!Y-m-d', $data_nascimento) : false;
        $hoje = $hoje ?? new DateTimeImmutable('today');

        // Data inexistente (ex.: 2026-02-30 ou 0000-00-00) não é "corrigida" silenciosamente
        if (!$nascimento || $nascimento->format('Y-m-d') !== $data_nascimento || $nascimento > $hoje) {
            return 'Idade não informada';
        }

        $diferenca = $nascimento->diff($hoje);
        if ($diferenca->y >= 1) {
            return $diferenca->y . ($diferenca->y === 1 ? ' ano' : ' anos');
        }
        if ($diferenca->m >= 1) {
            return $diferenca->m . ($diferenca->m === 1 ? ' mês' : ' meses');
        }
        return 'Menos de 1 mês';
    }

    function contar_animais_por_status(PDO $conn, string $status): int
    {
        $stmt = $conn->prepare('SELECT COUNT(*) FROM animal WHERE status = :status');
        $stmt->bindValue(':status', $status);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    // Lê e valida o formulário administrativo de animal.
    // Retorna [dados, erros]. Campos opcionais vazios viram NULL (não informado).
    function ler_formulario_animal(array $entrada): array
    {
        $dados = [];
        foreach (CAMPOS_FORMULARIO_ANIMAL as $campo) {
            $valor = trim((string) ($entrada[$campo] ?? ''));
            $dados[$campo] = $valor === '' ? null : $valor;
        }
        $dados['sexo'] = $dados['sexo'] ?? 'NAO_INFORMADO';

        $erros = [];
        if ($dados['nome'] === null) {
            $erros[] = 'Informe o nome do animal.';
        } elseif (mb_strlen($dados['nome']) > 80) {
            $erros[] = 'O nome deve ter no máximo 80 caracteres.';
        }
        if (!array_key_exists((string) $dados['especie'], ESPECIES)) {
            $erros[] = 'Selecione a espécie.';
        }
        if (!array_key_exists($dados['sexo'], SEXOS)) {
            $erros[] = 'Sexo inválido.';
        }
        if ($dados['porte'] !== null && !array_key_exists($dados['porte'], PORTES)) {
            $erros[] = 'Porte inválido.';
        }
        foreach (['castrado' => 'Castrado', 'vacinado' => 'Vacinado'] as $campo => $nome_campo) {
            if ($dados[$campo] === null) {
                continue;
            }
            if ($dados[$campo] !== '0' && $dados[$campo] !== '1') {
                $erros[] = "Valor inválido em {$nome_campo}.";
                continue;
            }
            $dados[$campo] = (int) $dados[$campo];
        }
        if ($dados['data_nascimento_estimada'] !== null) {
            $data = DateTimeImmutable::createFromFormat('!Y-m-d', $dados['data_nascimento_estimada']);
            if (!$data || $data->format('Y-m-d') !== $dados['data_nascimento_estimada']) {
                $erros[] = 'Data de nascimento estimada inválida.';
            } elseif ($data > new DateTimeImmutable('today')) {
                $erros[] = 'A data de nascimento estimada não pode estar no futuro.';
            }
        }
        foreach (['condicao_saude', 'comportamento', 'descricao'] as $campo) {
            // Limite da coluna TEXT: evita truncamento silencioso pelo banco
            if ($dados[$campo] !== null && strlen($dados[$campo]) > 65535) {
                $erros[] = 'Texto muito longo.';
            }
        }

        return [$dados, $erros];
    }

    // Vincula os campos do formulário respeitando NULL e inteiros
    function vincular_campos_animal(PDOStatement $stmt, array $dados): void
    {
        foreach (CAMPOS_FORMULARIO_ANIMAL as $campo) {
            $valor = $dados[$campo];
            $tipo = $valor === null ? PDO::PARAM_NULL : (is_int($valor) ? PDO::PARAM_INT : PDO::PARAM_STR);
            $stmt->bindValue(':' . $campo, $valor, $tipo);
        }
    }

    // Valida pelo CONTEÚDO (não pelo tipo informado pelo navegador), grava a imagem
    // e devolve o nome real do arquivo criado em image/, com extensão (ex.: "rex.png").
    function enviar_imagem_animal(array $arquivo, string $nome_base): string
    {
        if (($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || !is_uploaded_file($arquivo['tmp_name'])) {
            throw new RuntimeException('Arquivo de imagem não recebido.');
        }

        $info = @getimagesize($arquivo['tmp_name']);
        if (!$info || !in_array($info[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF], true)) {
            throw new RuntimeException('O arquivo enviado não é uma imagem JPG, PNG ou GIF.');
        }
        $arquivo['type'] = $info['mime'];

        require_once __DIR__ . '/../src/Uploader.php';
        require_once __DIR__ . '/../src/Image.php';

        $uploader = new CoffeeCode\Uploader\Image(PASTA_IMAGENS, '', false);
        return basename($uploader->upload($arquivo, $nome_base));
    }

    // Apaga o arquivo de imagem apenas se nenhum animal o referencia mais.
    function remover_imagem_sem_uso(PDO $conn, ?string $imagem): void
    {
        if ($imagem === null || $imagem === '' || $imagem !== basename($imagem)) {
            return;
        }

        $stmt = $conn->prepare('SELECT COUNT(*) FROM animal WHERE imagem = :imagem');
        $stmt->bindValue(':imagem', $imagem);
        $stmt->execute();
        if ((int) $stmt->fetchColumn() > 0) {
            return;
        }

        $caminho = PASTA_IMAGENS . '/' . $imagem;
        if (is_file($caminho)) {
            unlink($caminho);
        }
    }
