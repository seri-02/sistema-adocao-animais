<?php
    // Solicitação de adoção: o ADOTANTE solicita e a solicitação nasce PENDENTE (F3a);
    // a equipe (ADMIN/FUNCIONARIO) aprova ou reprova (F3b); enquanto PENDENTE, o próprio
    // adotante pode cancelar. Solicitação encerrada (APROVADA/REPROVADA/CANCELADA) é histórica.
    // data_decisao = momento do encerramento: decisão da equipe ou cancelamento pelo adotante.
    require_once __DIR__ . '/auth.php';

    const STATUS_SOLICITACAO = [
        'PENDENTE' => 'Pendente',
        'APROVADA' => 'Aprovada',
        'REPROVADA' => 'Reprovada',
        'CANCELADA' => 'Cancelada',
    ];

    const SOLICITACAO_CRIADA = 'criada';
    const SOLICITACAO_ANIMAL_INEXISTENTE = 'animal_inexistente';
    const SOLICITACAO_ANIMAL_INDISPONIVEL = 'animal_indisponivel';
    const SOLICITACAO_FALHA = 'falha';

    // Decisão enviada pela equipe => [status da solicitação, status do animal]
    const DECISOES = [
        'APROVAR' => ['APROVADA', 'ADOTADO'],
        'REPROVAR' => ['REPROVADA', 'DISPONIVEL'],
    ];
    const JUSTIFICATIVA_TAMANHO_MAXIMO = 2000;

    const DECISAO_REGISTRADA = 'registrada';
    const DECISAO_SOLICITACAO_INEXISTENTE = 'solicitacao_inexistente';
    const DECISAO_JA_DECIDIDA = 'ja_decidida';
    const DECISAO_ADOTANTE_INVALIDO = 'adotante_invalido';
    const DECISAO_AVALIADOR_SEM_PERMISSAO = 'avaliador_sem_permissao';
    const DECISAO_AUTOAVALIACAO = 'autoavaliacao';
    const DECISAO_INCONSISTENTE = 'inconsistente';
    const DECISAO_FALHA = 'falha';

    const CANCELAMENTO_REGISTRADO = 'registrado';
    // Inexistente OU de outro adotante: mesma resposta, para não revelar pedidos alheios
    const CANCELAMENTO_NAO_ENCONTRADA = 'nao_encontrada';
    const CANCELAMENTO_NAO_PENDENTE = 'nao_pendente';
    const CANCELAMENTO_ADOTANTE_INVALIDO = 'adotante_invalido';
    const CANCELAMENTO_INCONSISTENTE = 'inconsistente';
    const CANCELAMENTO_FALHA = 'falha';

    // Cria a solicitação PENDENTE e leva o animal para EM_PROCESSO, de forma atômica.
    // A linha do animal fica travada (FOR UPDATE) até o COMMIT: solicitações simultâneas
    // para o mesmo animal esperam e, ao continuar, já encontram EM_PROCESSO.
    // Assim um animal nunca tem mais de uma solicitação PENDENTE.
    // $id_adotante deve vir do usuário autenticado, nunca do navegador.
    function criar_solicitacao_adocao(PDO $conn, int $id_animal, int $id_adotante): string
    {
        try {
            $conn->beginTransaction();

            $stmt = $conn->prepare('SELECT id_animal, status FROM animal WHERE id_animal = :id_animal FOR UPDATE');
            $stmt->bindValue(':id_animal', $id_animal, PDO::PARAM_INT);
            $stmt->execute();
            $animal = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$animal) {
                $conn->rollBack();
                return SOLICITACAO_ANIMAL_INEXISTENTE;
            }
            if ($animal['status'] !== 'DISPONIVEL') {
                $conn->rollBack();
                return SOLICITACAO_ANIMAL_INDISPONIVEL;
            }

            $stmt = $conn->prepare(
                "INSERT INTO solicitacao_adocao (id_animal, id_adotante, status)
                 VALUES (:id_animal, :id_adotante, 'PENDENTE')"
            );
            $stmt->bindValue(':id_animal', $id_animal, PDO::PARAM_INT);
            $stmt->bindValue(':id_adotante', $id_adotante, PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $conn->prepare(
                "UPDATE animal SET status = 'EM_PROCESSO' WHERE id_animal = :id_animal AND status = 'DISPONIVEL'"
            );
            $stmt->bindValue(':id_animal', $id_animal, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException('Status do animal mudou durante a solicitação.');
            }

            $conn->commit();
            return SOLICITACAO_CRIADA;
        } catch (Throwable $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            error_log('Falha ao criar solicitação de adoção: ' . $e->getMessage());
            return SOLICITACAO_FALHA;
        }
    }

    // Solicitações de UM adotante, com os dados do animal para exibição
    function listar_solicitacoes_do_adotante(PDO $conn, int $id_adotante): array
    {
        $stmt = $conn->prepare(
            'SELECT s.id_solicitacao, s.id_animal, s.status, s.data_solicitacao, s.data_decisao, s.justificativa,
                    a.nome, a.especie, a.imagem
               FROM solicitacao_adocao s
               JOIN animal a ON a.id_animal = s.id_animal
              WHERE s.id_adotante = :id_adotante
              ORDER BY s.data_solicitacao DESC, s.id_solicitacao DESC'
        );
        $stmt->bindValue(':id_adotante', $id_adotante, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Lê e valida a decisão da equipe. Retorna [decisao, justificativa, erros].
    // Justificativa: obrigatória para reprovar; opcional (observação) ao aprovar; vazia vira NULL.
    function ler_formulario_decisao(array $entrada): array
    {
        $decisao = is_string($entrada['decisao'] ?? null) ? $entrada['decisao'] : '';
        $justificativa = trim(is_string($entrada['justificativa'] ?? null) ? $entrada['justificativa'] : '');
        $justificativa = $justificativa === '' ? null : $justificativa;

        $erros = [];
        if (!array_key_exists($decisao, DECISOES)) {
            $erros[] = 'Decisão inválida.';
        }
        if ($decisao === 'REPROVAR' && $justificativa === null) {
            $erros[] = 'Informe a justificativa da reprovação.';
        }
        if ($justificativa !== null && !mb_check_encoding($justificativa, 'UTF-8')) {
            $erros[] = 'A justificativa contém caracteres inválidos.';
        } elseif ($justificativa !== null && mb_strlen($justificativa) > JUSTIFICATIVA_TAMANHO_MAXIMO) {
            $erros[] = 'A justificativa deve ter no máximo ' . JUSTIFICATIVA_TAMANHO_MAXIMO . ' caracteres.';
        }

        return [$decisao, $justificativa, $erros];
    }

    // Registra a decisão da equipe numa única transação. Travas, sempre nesta ordem:
    //   avaliador (compartilhada) -> solicitação -> animal -> adotante (só ao aprovar).
    // - avaliador: precisa continuar ADMIN/FUNCIONARIO ativo no momento da decisão
    //   e não pode ser o próprio adotante da solicitação;
    // - solicitação: precisa estar PENDENTE (decisão concluída nunca é refeita);
    // - animal: precisa estar EM_PROCESSO; se não estiver, é inconsistência de domínio:
    //   nada é corrigido, a transação é desfeita e o caso fica registrado no log;
    // - adotante (aprovação): precisa continuar ADOTANTE ativo. Reprovar não exige isso,
    //   para que seja sempre possível encerrar o pedido e liberar o animal.
    // $id_avaliador deve vir do usuário autenticado, nunca do navegador.
    function decidir_solicitacao_adocao(PDO $conn, int $id_solicitacao, string $decisao,
                                        ?string $justificativa, int $id_avaliador): string
    {
        [$status_solicitacao, $status_animal] = DECISOES[$decisao];

        try {
            $conn->beginTransaction();

            $stmt = $conn->prepare('SELECT perfil, ativo FROM usuario WHERE id_usuario = :id LOCK IN SHARE MODE');
            $stmt->bindValue(':id', $id_avaliador, PDO::PARAM_INT);
            $stmt->execute();
            $avaliador = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$avaliador || (int) $avaliador['ativo'] !== 1 || !in_array($avaliador['perfil'], PERFIS_EQUIPE, true)) {
                $conn->rollBack();
                return DECISAO_AVALIADOR_SEM_PERMISSAO;
            }

            $stmt = $conn->prepare(
                'SELECT id_animal, id_adotante, status FROM solicitacao_adocao WHERE id_solicitacao = :id FOR UPDATE'
            );
            $stmt->bindValue(':id', $id_solicitacao, PDO::PARAM_INT);
            $stmt->execute();
            $solicitacao = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$solicitacao) {
                $conn->rollBack();
                return DECISAO_SOLICITACAO_INEXISTENTE;
            }
            // Ninguém decide a própria solicitação (ex.: adotante que depois virou FUNCIONARIO)
            if ((int) $solicitacao['id_adotante'] === $id_avaliador) {
                $conn->rollBack();
                return DECISAO_AUTOAVALIACAO;
            }
            if ($solicitacao['status'] !== 'PENDENTE') {
                $conn->rollBack();
                return DECISAO_JA_DECIDIDA;
            }

            $stmt = $conn->prepare('SELECT status FROM animal WHERE id_animal = :id FOR UPDATE');
            $stmt->bindValue(':id', (int) $solicitacao['id_animal'], PDO::PARAM_INT);
            $stmt->execute();
            $animal = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$animal || $animal['status'] !== 'EM_PROCESSO') {
                $conn->rollBack();
                error_log(sprintf(
                    'Inconsistência: solicitação %d PENDENTE com animal %d em status %s; decisão %s não aplicada.',
                    $id_solicitacao, (int) $solicitacao['id_animal'], $animal ? $animal['status'] : 'inexistente', $decisao
                ));
                return DECISAO_INCONSISTENTE;
            }

            if ($decisao === 'APROVAR') {
                $stmt = $conn->prepare('SELECT perfil, ativo FROM usuario WHERE id_usuario = :id FOR UPDATE');
                $stmt->bindValue(':id', (int) $solicitacao['id_adotante'], PDO::PARAM_INT);
                $stmt->execute();
                $adotante = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$adotante || $adotante['perfil'] !== PERFIL_ADOTANTE || (int) $adotante['ativo'] !== 1) {
                    $conn->rollBack();
                    return DECISAO_ADOTANTE_INVALIDO;
                }
            }

            $stmt = $conn->prepare(
                "UPDATE solicitacao_adocao
                    SET status = :status, id_avaliador = :id_avaliador, data_decisao = NOW(), justificativa = :justificativa
                  WHERE id_solicitacao = :id AND status = 'PENDENTE'"
            );
            $stmt->bindValue(':status', $status_solicitacao);
            $stmt->bindValue(':id_avaliador', $id_avaliador, PDO::PARAM_INT);
            $stmt->bindValue(':justificativa', $justificativa, $justificativa === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':id', $id_solicitacao, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException("Solicitação {$id_solicitacao} deixou de estar PENDENTE durante a decisão.");
            }

            $stmt = $conn->prepare("UPDATE animal SET status = :status WHERE id_animal = :id AND status = 'EM_PROCESSO'");
            $stmt->bindValue(':status', $status_animal);
            $stmt->bindValue(':id', (int) $solicitacao['id_animal'], PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException("Animal {$solicitacao['id_animal']} deixou de estar EM_PROCESSO durante a decisão.");
            }

            $conn->commit();
            return DECISAO_REGISTRADA;
        } catch (Throwable $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            error_log('Falha ao decidir solicitação de adoção: ' . $e->getMessage());
            return DECISAO_FALHA;
        }
    }

    // O próprio adotante cancela sua solicitação PENDENTE numa única transação:
    // solicitação -> CANCELADA (data_decisao = agora, sem avaliador nem justificativa)
    // e animal EM_PROCESSO -> DISPONIVEL. Travas, sempre nesta ordem:
    //   solicitação -> adotante (compartilhada) -> animal.
    // A solicitação é travada primeiro, como na decisão da equipe: cancelar e decidir a
    // mesma solicitação são serializados por essa linha e só um deles encontra PENDENTE.
    // - solicitação: precisa existir e pertencer a $id_adotante (senão, mesma resposta de
    //   inexistente) e estar PENDENTE (encerrada nunca é alterada);
    // - adotante: precisa continuar ADOTANTE ativo no momento do cancelamento;
    // - animal: precisa estar EM_PROCESSO; se não estiver, é inconsistência de domínio:
    //   nada é corrigido, a transação é desfeita e o caso fica registrado no log.
    // $id_adotante deve vir do usuário autenticado; o animal vem da própria solicitação.
    function cancelar_solicitacao_adocao(PDO $conn, int $id_solicitacao, int $id_adotante): string
    {
        try {
            $conn->beginTransaction();

            $stmt = $conn->prepare(
                'SELECT id_solicitacao, id_animal, id_adotante, status
                   FROM solicitacao_adocao WHERE id_solicitacao = :id FOR UPDATE'
            );
            $stmt->bindValue(':id', $id_solicitacao, PDO::PARAM_INT);
            $stmt->execute();
            $solicitacao = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$solicitacao || (int) $solicitacao['id_adotante'] !== $id_adotante) {
                $conn->rollBack();
                return CANCELAMENTO_NAO_ENCONTRADA;
            }
            if ($solicitacao['status'] !== 'PENDENTE') {
                $conn->rollBack();
                return CANCELAMENTO_NAO_PENDENTE;
            }

            $stmt = $conn->prepare('SELECT perfil, ativo FROM usuario WHERE id_usuario = :id LOCK IN SHARE MODE');
            $stmt->bindValue(':id', $id_adotante, PDO::PARAM_INT);
            $stmt->execute();
            $adotante = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$adotante || $adotante['perfil'] !== PERFIL_ADOTANTE || (int) $adotante['ativo'] !== 1) {
                $conn->rollBack();
                return CANCELAMENTO_ADOTANTE_INVALIDO;
            }

            $stmt = $conn->prepare('SELECT id_animal, status FROM animal WHERE id_animal = :id FOR UPDATE');
            $stmt->bindValue(':id', (int) $solicitacao['id_animal'], PDO::PARAM_INT);
            $stmt->execute();
            $animal = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$animal || $animal['status'] !== 'EM_PROCESSO') {
                $conn->rollBack();
                error_log(sprintf(
                    'Inconsistência: solicitação %d PENDENTE com animal %d em status %s; cancelamento não aplicado.',
                    $id_solicitacao, (int) $solicitacao['id_animal'], $animal ? $animal['status'] : 'inexistente'
                ));
                return CANCELAMENTO_INCONSISTENTE;
            }

            $stmt = $conn->prepare(
                "UPDATE solicitacao_adocao
                    SET status = 'CANCELADA', data_decisao = NOW(), id_avaliador = NULL, justificativa = NULL
                  WHERE id_solicitacao = :id AND id_adotante = :id_adotante AND status = 'PENDENTE'"
            );
            $stmt->bindValue(':id', $id_solicitacao, PDO::PARAM_INT);
            $stmt->bindValue(':id_adotante', $id_adotante, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException("Solicitação {$id_solicitacao} deixou de estar PENDENTE durante o cancelamento.");
            }

            $stmt = $conn->prepare("UPDATE animal SET status = 'DISPONIVEL' WHERE id_animal = :id AND status = 'EM_PROCESSO'");
            $stmt->bindValue(':id', (int) $solicitacao['id_animal'], PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException("Animal {$solicitacao['id_animal']} deixou de estar EM_PROCESSO durante o cancelamento.");
            }

            $conn->commit();
            return CANCELAMENTO_REGISTRADO;
        } catch (Throwable $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            error_log('Falha ao cancelar solicitação de adoção: ' . $e->getMessage());
            return CANCELAMENTO_FALHA;
        }
    }

    // Colunas exibidas à equipe: nunca senha/hash
    const SQL_SOLICITACAO_EQUIPE = "
        SELECT s.id_solicitacao, s.id_adotante, s.status, s.data_solicitacao, s.data_decisao, s.justificativa,
               a.nome AS animal_nome, a.imagem AS animal_imagem, a.especie AS animal_especie,
               a.sexo AS animal_sexo, a.data_nascimento_estimada AS animal_nascimento, a.status AS animal_status,
               ad.nome AS adotante_nome, ad.email AS adotante_email, ad.telefone AS adotante_telefone,
               av.nome AS avaliador_nome
          FROM solicitacao_adocao s
          JOIN animal a ON a.id_animal = s.id_animal
          JOIN usuario ad ON ad.id_usuario = s.id_adotante
          LEFT JOIN usuario av ON av.id_usuario = s.id_avaliador";

    // Listagem da equipe: PENDENTES primeiro (fila de trabalho, mais antigas antes), depois as concluídas
    function listar_solicitacoes_equipe(PDO $conn, ?string $status = null): array
    {
        $sql = SQL_SOLICITACAO_EQUIPE
             . ($status !== null ? ' WHERE s.status = :status' : '')
             . " ORDER BY s.status = 'PENDENTE' DESC,
                         CASE WHEN s.status = 'PENDENTE' THEN s.id_solicitacao END ASC,
                         s.data_decisao DESC, s.id_solicitacao DESC";
        $stmt = $conn->prepare($sql);
        if ($status !== null) {
            $stmt->bindValue(':status', $status);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    function buscar_solicitacao_equipe(PDO $conn, int $id_solicitacao): ?object
    {
        $stmt = $conn->prepare(SQL_SOLICITACAO_EQUIPE . ' WHERE s.id_solicitacao = :id');
        $stmt->bindValue(':id', $id_solicitacao, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ) ?: null;
    }

    function contar_solicitacoes_pendentes(PDO $conn): int
    {
        return (int) $conn->query("SELECT COUNT(*) FROM solicitacao_adocao WHERE status = 'PENDENTE'")->fetchColumn();
    }

    function formatar_data_hora(?string $data): string
    {
        $data_hora = $data ? DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $data) : false;
        return $data_hora ? $data_hora->format('d/m/Y H:i') : '';
    }
