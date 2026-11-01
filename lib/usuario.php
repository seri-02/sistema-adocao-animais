<?php
    // Regras do cadastro/edição administrativa de usuários.
    require_once __DIR__ . '/auth.php';

    const PERFIS = [
        PERFIL_ADMIN => 'Administrador',
        PERFIL_FUNCIONARIO => 'Funcionário',
        PERFIL_ADOTANTE => 'Adotante',
    ];
    // O formulário administrativo cria só contas da equipe; adotantes se cadastram (F2b)
    const PERFIS_CADASTRO_ADMIN = [PERFIL_FUNCIONARIO, PERFIL_ADMIN];
    const SENHA_TAMANHO_MINIMO = 8;
    const MSG_EMAIL_DUPLICADO = 'Já existe um usuário cadastrado com este e-mail.';
    // Mesma situação, no vocabulário da área pública (cadastro e Minha conta)
    const MSG_EMAIL_CONTA_DUPLICADA = 'Já existe uma conta cadastrada com este e-mail.';
    // Campo esperado como texto chegou como array/estrutura (só ocorre em requisição forjada)
    const MSG_DADOS_INVALIDOS = 'Os dados enviados são inválidos.';

    // Valor textual de um campo enviado pelo cliente. Só string é aceita: ausente, array ou
    // qualquer outra estrutura vira '' (nunca é convertida implicitamente, o que geraria
    // "Array to string conversion" e deixaria o texto "Array" passar adiante).
    // Não apara espaços: quem chama decide (a senha, por exemplo, é usada como digitada).
    function valor_texto(array $entrada, string $campo): string
    {
        $valor = $entrada[$campo] ?? '';
        return is_string($valor) ? $valor : '';
    }

    // Algum dos campos foi ENVIADO, mas não como texto? Ausente continua com a semântica de
    // cada campo; presente e malformado é recusado, inclusive em campo opcional (telefone,
    // nova senha), para que nada malformado seja aceito como "vazio".
    function tem_campo_nao_textual(array $entrada, array $campos): bool
    {
        foreach ($campos as $campo) {
            if (array_key_exists($campo, $entrada) && !is_string($entrada[$campo])) {
                return true;
            }
        }
        return false;
    }

    // Lê e valida nome, e-mail, telefone, perfil e senha.
    // Senha vazia só é aceita quando $senha_obrigatoria = false (edição: mantém a atual).
    function ler_formulario_usuario(array $entrada, array $perfis_permitidos, bool $senha_obrigatoria): array
    {
        $dados = [
            'nome' => trim(valor_texto($entrada, 'nome')),
            'email' => trim(valor_texto($entrada, 'email')),
            'telefone' => trim(valor_texto($entrada, 'telefone')),
            'perfil' => valor_texto($entrada, 'perfil'),
            'senha' => valor_texto($entrada, 'senha'),
        ];
        $dados['telefone'] = $dados['telefone'] === '' ? null : $dados['telefone'];

        if (tem_campo_nao_textual($entrada, ['nome', 'email', 'telefone', 'perfil', 'senha'])) {
            return [$dados, [MSG_DADOS_INVALIDOS]];
        }

        $erros = [];
        if ($dados['nome'] === '' || mb_strlen($dados['nome']) > 100) {
            $erros[] = 'Informe o nome (até 100 caracteres).';
        }
        if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($dados['email']) > 150) {
            $erros[] = 'Informe um e-mail válido (até 150 caracteres).';
        }
        if ($dados['telefone'] !== null && mb_strlen($dados['telefone']) > 20) {
            $erros[] = 'O telefone deve ter no máximo 20 caracteres.';
        }
        if (!in_array($dados['perfil'], $perfis_permitidos, true)) {
            $erros[] = 'Perfil inválido.';
        }
        if ($dados['senha'] === '') {
            if ($senha_obrigatoria) {
                $erros[] = 'Informe a senha.';
            }
        } elseif (mb_strlen($dados['senha']) < SENHA_TAMANHO_MINIMO) {
            $erros[] = 'A senha deve ter pelo menos ' . SENHA_TAMANHO_MINIMO . ' caracteres.';
        }

        return [$dados, $erros];
    }

    // Dados da conta do adotante (cadastro público e Minha conta). Reaproveita as regras
    // de nome/e-mail/telefone/senha acima com o perfil fixado no servidor e acrescenta:
    // e-mail em minúsculas, caracteres permitidos no telefone e confirmação da senha.
    // $campo_senha: 'senha' no cadastro (obrigatória); null na edição (senha tratada à parte).
    function ler_formulario_adotante(array $entrada, ?string $campo_senha): array
    {
        $base = [
            'nome' => $entrada['nome'] ?? '',
            'email' => $entrada['email'] ?? '',
            'telefone' => $entrada['telefone'] ?? '',
            'perfil' => PERFIL_ADOTANTE,
            'senha' => $campo_senha !== null ? valor_texto($entrada, $campo_senha) : '',
        ];
        [$dados, $erros] = ler_formulario_usuario($base, [PERFIL_ADOTANTE], $campo_senha !== null);

        $dados['email'] = mb_strtolower($dados['email']);
        if ($dados['telefone'] !== null && !preg_match('/^[0-9 ()+\-]+$/', $dados['telefone'])) {
            $erros[] = 'O telefone pode conter apenas números, espaços, parênteses, "+" e "-".';
        }
        if ($campo_senha !== null && valor_texto($entrada, 'confirmacao_senha') !== $dados['senha']) {
            $erros[] = 'A confirmação da senha não confere.';
        }
        // Senha/confirmação malformadas (os demais campos já foram conferidos acima)
        $campos_senha = $campo_senha !== null ? [$campo_senha, 'confirmacao_senha'] : [];
        if (in_array(MSG_DADOS_INVALIDOS, $erros, true) || tem_campo_nao_textual($entrada, $campos_senha)) {
            $erros = [MSG_DADOS_INVALIDOS];
        }

        unset($dados['perfil']);
        return [$dados, $erros];
    }

    // E-mail já usado por outra conta (verificação amigável; a garantia final é o UNIQUE)
    function email_em_uso(PDO $conn, string $email, int $exceto_id = 0): bool
    {
        $stmt = $conn->prepare('SELECT COUNT(*) FROM usuario WHERE email = :email AND id_usuario <> :id');
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':id', $exceto_id, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn() > 0;
    }

    // Dentro de uma transação: trava o usuário alvo, o usuário que executa a ação e
    // os administradores ativos (na mesma ordem, evitando deadlock entre operações
    // concorrentes). Retorna [linhas por id, quantidade de ADMIN ativos].
    function travar_usuarios_para_alteracao(PDO $conn, int $id_alvo, int $id_ator): array
    {
        $stmt = $conn->prepare(
            "SELECT id_usuario, perfil, ativo FROM usuario
              WHERE id_usuario IN (:alvo, :ator) OR (perfil = 'ADMIN' AND ativo = 1)
              ORDER BY id_usuario FOR UPDATE"
        );
        $stmt->bindValue(':alvo', $id_alvo, PDO::PARAM_INT);
        $stmt->bindValue(':ator', $id_ator, PDO::PARAM_INT);
        $stmt->execute();

        $linhas = [];
        $admins_ativos = 0;
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $linha) {
            $linhas[(int) $linha['id_usuario']] = $linha;
            if ($linha['perfil'] === PERFIL_ADMIN && (int) $linha['ativo'] === 1) {
                $admins_ativos++;
            }
        }
        return [$linhas, $admins_ativos];
    }

    function eh_admin_ativo(?array $linha): bool
    {
        return $linha !== null && $linha['perfil'] === PERFIL_ADMIN && (int) $linha['ativo'] === 1;
    }

    function eh_email_duplicado(PDOException $e): bool
    {
        return (int) ($e->errorInfo[1] ?? 0) === 1062;
    }
