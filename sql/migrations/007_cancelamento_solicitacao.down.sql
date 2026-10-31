-- =============================================================================
-- 007_cancelamento_solicitacao (DOWN)
-- =============================================================================
-- Remove CANCELADA de `solicitacao_adocao.status`.
--
-- DOWN deliberadamente CONSERVADOR: só serve para rollback técnico logo após a
-- migração, antes de qualquer cancelamento. Aborta ANTES do ALTER se:
--   - alguma migração posterior à 007 ainda estiver aplicada;
--   - existir QUALQUER solicitação CANCELADA. Cancelamentos NUNCA são convertidos
--     em PENDENTE, REPROVADA ou outro status: o histórico não é reescrito.
-- Depois que houver cancelamentos, o rollback seguro é restaurar o backup pré-007.
--
-- Segunda camada: STRICT_TRANS_TABLES apenas nesta sessão durante o ALTER (sem
-- modo estrito, um valor fora do ENUM viraria '' em silêncio); o modo anterior
-- é restaurado ao final.
-- =============================================================================

SET NAMES utf8mb4;

SET @sql_precheck = IF((SELECT COUNT(*) FROM `schema_migrations` WHERE `versao` > '007_cancelamento_solicitacao') = 0, 'DO 0',
    'SELECT * FROM `ABORTADO__reverta_as_migracoes_posteriores_a_007`');
PREPARE stmt_precheck FROM @sql_precheck;
EXECUTE stmt_precheck;
DEALLOCATE PREPARE stmt_precheck;

SET @sql_precheck = IF((SELECT COUNT(*) FROM `solicitacao_adocao` WHERE `status` = 'CANCELADA') = 0, 'DO 0',
    'SELECT * FROM `ABORTADO__existem_solicitacoes_canceladas_restaure_backup`');
PREPARE stmt_precheck FROM @sql_precheck;
EXECUTE stmt_precheck;
DEALLOCATE PREPARE stmt_precheck;

SET @sql_mode_anterior = @@SESSION.sql_mode;
SET SESSION sql_mode = IF(FIND_IN_SET('STRICT_TRANS_TABLES', @@SESSION.sql_mode),
                          @@SESSION.sql_mode,
                          CONCAT_WS(',', 'STRICT_TRANS_TABLES', NULLIF(@@SESSION.sql_mode, '')));

ALTER TABLE `solicitacao_adocao`
    MODIFY `status` enum('PENDENTE','APROVADA','REPROVADA') NOT NULL DEFAULT 'PENDENTE';

SET SESSION sql_mode = @sql_mode_anterior;

DELETE FROM `schema_migrations` WHERE `versao` = '007_cancelamento_solicitacao';
