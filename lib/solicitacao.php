<?php
    // Solicitação de adoção (F3a): o ADOTANTE solicita e a solicitação nasce PENDENTE.
    // A decisão (APROVADA/REPROVADA) pela equipe pertence à F3b.

    const STATUS_SOLICITACAO = [
        'PENDENTE' => 'Pendente',
        'APROVADA' => 'Aprovada',
        'REPROVADA' => 'Reprovada',
    ];

    const SOLICITACAO_CRIADA = 'criada';
    const SOLICITACAO_ANIMAL_INEXISTENTE = 'animal_inexistente';
    const SOLICITACAO_ANIMAL_INDISPONIVEL = 'animal_indisponivel';
    const SOLICITACAO_FALHA = 'falha';

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
            'SELECT s.status, s.data_solicitacao, s.data_decisao, s.justificativa,
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

    function formatar_data_hora(?string $data): string
    {
        $data_hora = $data ? DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $data) : false;
        return $data_hora ? $data_hora->format('d/m/Y H:i') : '';
    }
