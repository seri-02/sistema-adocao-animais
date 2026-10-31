<?php
    // Regras do cadastro de voluntário: cadastro público (seja_voluntario.php) e edição
    // pela equipe usam a MESMA leitura/validação. Só os campos esperados são lidos;
    // qualquer outro campo enviado é ignorado (nunca usar extract() com dados do cliente).

    // Limites das colunas de `voluntario` (o schema não muda nesta fase)
    const VOLUNTARIO_NOME_MAXIMO = 50;
    const VOLUNTARIO_EMAIL_MAXIMO = 50;
    const VOLUNTARIO_DIA_MAXIMO = 255;
    const VOLUNTARIO_IDADE_MAXIMA = 2147483647; // limite de int(11); não é regra de negócio

    // Texto do formulário: só string é aceita (array ou ausente viram ''), sem espaços nas pontas
    function texto_do_formulario(array $entrada, string $campo): string
    {
        $valor = $entrada[$campo] ?? '';
        return is_string($valor) ? trim($valor) : '';
    }

    // Lê e valida nome, e-mail, idade e dia(s) disponível(is). Retorna [dados, erros].
    function ler_formulario_voluntario(array $entrada): array
    {
        $dados = [
            'nome' => texto_do_formulario($entrada, 'nome'),
            'email' => texto_do_formulario($entrada, 'email'),
            'idade' => texto_do_formulario($entrada, 'idade'),
            'dia_disponivel' => texto_do_formulario($entrada, 'dia_disponivel'),
        ];

        $erros = [];
        foreach ($dados as $valor) {
            if (!mb_check_encoding($valor, 'UTF-8')) {
                return [$dados, ['Os dados enviados contêm caracteres inválidos.']];
            }
        }

        if ($dados['nome'] === '' || mb_strlen($dados['nome']) > VOLUNTARIO_NOME_MAXIMO) {
            $erros[] = 'Informe o nome (até ' . VOLUNTARIO_NOME_MAXIMO . ' caracteres).';
        }
        if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($dados['email']) > VOLUNTARIO_EMAIL_MAXIMO) {
            $erros[] = 'Informe um e-mail válido (até ' . VOLUNTARIO_EMAIL_MAXIMO . ' caracteres).';
        }
        $idade = filter_var($dados['idade'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => VOLUNTARIO_IDADE_MAXIMA]]);
        if ($idade === false) {
            $erros[] = 'Informe a idade como um número inteiro positivo.';
        } else {
            $dados['idade'] = $idade;
        }
        if ($dados['dia_disponivel'] === '' || mb_strlen($dados['dia_disponivel']) > VOLUNTARIO_DIA_MAXIMO) {
            $erros[] = 'Informe os dias disponíveis (até ' . VOLUNTARIO_DIA_MAXIMO . ' caracteres).';
        }

        return [$dados, $erros];
    }

    // Grava um voluntário já validado por ler_formulario_voluntario() (cadastro público e da equipe).
    // Colunas sempre nomeadas; erros de banco sobem como PDOException para o endpoint tratar.
    function cadastrar_voluntario(PDO $conn, array $dados): void
    {
        $stmt = $conn->prepare(
            'INSERT INTO voluntario (nome, email, idade, dia_disponivel)
             VALUES (:nome, :email, :idade, :dia_disponivel)'
        );
        $stmt->bindValue(':nome', $dados['nome']);
        $stmt->bindValue(':email', $dados['email']);
        $stmt->bindValue(':idade', $dados['idade'], PDO::PARAM_INT);
        $stmt->bindValue(':dia_disponivel', $dados['dia_disponivel']);
        $stmt->execute();
    }

    // Id de voluntário vindo do cliente: inteiro positivo ou false (array/texto/vazio não geram warning)
    function ler_id_voluntario($valor)
    {
        return is_string($valor) || is_int($valor)
            ? filter_var($valor, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])
            : false;
    }
