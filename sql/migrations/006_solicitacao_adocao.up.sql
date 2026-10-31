-- =============================================================================
-- 006_solicitacao_adocao (UP)
-- =============================================================================
-- Cria `solicitacao_adocao`, base do novo workflow de adoção:
--   ADOTANTE solicita -> PENDENTE (animal vai para EM_PROCESSO)
--   equipe decide (F3b) -> APROVADA / REPROVADA
--
-- id_avaliador, data_decisao e justificativa ficam NULL até a decisão (F3b).
-- Todas as FKs são ON DELETE/UPDATE RESTRICT: o histórico de solicitações nunca
-- desaparece por exclusão de animal ou de usuário (nada de CASCADE).
--
-- A regra "no máximo uma solicitação PENDENTE por animal" é garantida pela
-- aplicação (transação + SELECT ... FOR UPDATE no animal + status DISPONIVEL),
-- não por constraint.
--
-- A tabela legada `adocoes` NÃO é migrada: seus registros são anônimos, sem
-- vínculo confiável com `usuario`. Ela permanece apenas como histórico legado.
--
-- Pré-requisito: 001..005 aplicadas; backup validado.
-- =============================================================================

SET NAMES utf8mb4;

SET @sql_precheck = IF((SELECT COUNT(*) FROM `schema_migrations` WHERE `versao` = '005_usuario_perfis_autenticacao') = 1, 'DO 0',
    'SELECT * FROM `ABORTADO__aplique_a_migracao_005_antes_da_006`');
PREPARE stmt_precheck FROM @sql_precheck;
EXECUTE stmt_precheck;
DEALLOCATE PREPARE stmt_precheck;

-- Índices:
--   (id_animal, status)        FK + "este animal tem solicitação PENDENTE?"
--   (id_adotante, data)        FK + "Minhas solicitações" em ordem de data
--   (status, data)             fila de solicitações por status (análise na F3b)
--   (id_avaliador)             FK
CREATE TABLE `solicitacao_adocao` (
    `id_solicitacao` int(11) NOT NULL AUTO_INCREMENT,
    `id_animal` int(11) NOT NULL,
    `id_adotante` int(11) NOT NULL,
    `status` enum('PENDENTE','APROVADA','REPROVADA') NOT NULL DEFAULT 'PENDENTE',
    `id_avaliador` int(11) NULL DEFAULT NULL,
    `data_solicitacao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `data_decisao` datetime NULL DEFAULT NULL,
    `justificativa` text NULL DEFAULT NULL,
    PRIMARY KEY (`id_solicitacao`),
    KEY `idx_solicitacao_animal_status` (`id_animal`, `status`),
    KEY `idx_solicitacao_adotante_data` (`id_adotante`, `data_solicitacao`),
    KEY `idx_solicitacao_status_data` (`status`, `data_solicitacao`),
    KEY `idx_solicitacao_avaliador` (`id_avaliador`),
    CONSTRAINT `fk_solicitacao_animal` FOREIGN KEY (`id_animal`)
        REFERENCES `animal` (`id_animal`)
        ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT `fk_solicitacao_adotante` FOREIGN KEY (`id_adotante`)
        REFERENCES `usuario` (`id_usuario`)
        ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT `fk_solicitacao_avaliador` FOREIGN KEY (`id_avaliador`)
        REFERENCES `usuario` (`id_usuario`)
        ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `schema_migrations` (`versao`) VALUES ('006_solicitacao_adocao');
