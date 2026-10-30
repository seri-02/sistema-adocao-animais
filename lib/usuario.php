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

    // Lê e valida nome, e-mail, telefone, perfil e senha.
    // Senha vazia só é aceita quando $senha_obrigatoria = false (edição: mantém a atual).
    function ler_formulario_usuario(array $entrada, array $perfis_permitidos, bool $senha_obrigatoria): array
    {
        $dados = [
            'nome' => trim((string) ($entrada['nome'] ?? '')),
            'email' => trim((string) ($entrada['email'] ?? '')),
            'telefone' => trim((string) ($entrada['telefone'] ?? '')),
            'perfil' => (string) ($entrada['perfil'] ?? ''),
            'senha' => (string) ($entrada['senha'] ?? ''),
        ];
        $dados['telefone'] = $dados['telefone'] === '' ? null : $dados['telefone'];

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
