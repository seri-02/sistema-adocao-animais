-- =============================================================================
-- 007_cancelamento_solicitacao (UP)
-- =============================================================================
-- Acrescenta CANCELADA a `solicitacao_adocao.status`:
--   ADOTANTE desiste de uma solicitação PENDENTE -> CANCELADA
--   (o animal volta de EM_PROCESSO para DISPONIVEL na mesma transação da aplicação).
--
-- Só o ENUM muda; NOT NULL e DEFAULT 'PENDENTE' são preservados. O novo valor entra
-- no FIM da lista: os valores existentes mantêm a mesma posição interna.
--
-- Convenção de `data_decisao` (sem coluna nova):
--   APROVADA / REPROVADA -> data da decisão da equipe (id_avaliador preenchido)
--   CANCELADA            -> data em que o próprio adotante cancelou
--                           (id_avaliador e justificativa ficam NULL)
--
-- Pré-requisito: 001..006 aplicadas; backup validado.
-- =============================================================================

SET NAMES utf8mb4;

SET @sql_precheck = IF((SELECT COUNT(*) FROM `schema_migrations` WHERE `versao` = '006_solicitacao_adocao') = 1, 'DO 0',
    'SELECT * FROM `ABORTADO__aplique_a_migracao_006_antes_da_007`');
PREPARE stmt_precheck FROM @sql_precheck;
EXECUTE stmt_precheck;
DEALLOCATE PREPARE stmt_precheck;

SET @sql_precheck = IF((SELECT COUNT(*) FROM `schema_migrations` WHERE `versao` = '007_cancelamento_solicitacao') = 0, 'DO 0',
    'SELECT * FROM `ABORTADO__migracao_007_ja_aplicada`');
PREPARE stmt_precheck FROM @sql_precheck;
EXECUTE stmt_precheck;
DEALLOCATE PREPARE stmt_precheck;

ALTER TABLE `solicitacao_adocao`
    MODIFY `status` enum('PENDENTE','APROVADA','REPROVADA','CANCELADA') NOT NULL DEFAULT 'PENDENTE';

INSERT INTO `schema_migrations` (`versao`) VALUES ('007_cancelamento_solicitacao');
