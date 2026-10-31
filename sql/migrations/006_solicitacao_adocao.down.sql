-- =============================================================================
-- 006_solicitacao_adocao (DOWN)
-- =============================================================================
-- Remove `solicitacao_adocao`.
--
-- DOWN deliberadamente CONSERVADOR: só serve para rollback técnico logo após a
-- migração, com a tabela VAZIA. Aborta ANTES do DROP TABLE se:
--   - alguma migração posterior à 006 ainda estiver aplicada;
--   - existir QUALQUER solicitação (histórico nunca é descartado em silêncio).
-- Depois que a aplicação registrar solicitações, o rollback seguro é restaurar
-- o backup pré-F3a.
-- =============================================================================

SET NAMES utf8mb4;

SET @sql_precheck = IF((SELECT COUNT(*) FROM `schema_migrations` WHERE `versao` > '006_solicitacao_adocao') = 0, 'DO 0',
    'SELECT * FROM `ABORTADO__reverta_as_migracoes_posteriores_a_006`');
PREPARE stmt_precheck FROM @sql_precheck;
EXECUTE stmt_precheck;
DEALLOCATE PREPARE stmt_precheck;

SET @sql_precheck = IF((SELECT COUNT(*) FROM `solicitacao_adocao`) = 0, 'DO 0',
    'SELECT * FROM `ABORTADO__solicitacao_adocao_tem_registros_restaure_backup`');
PREPARE stmt_precheck FROM @sql_precheck;
EXECUTE stmt_precheck;
DEALLOCATE PREPARE stmt_precheck;

DROP TABLE `solicitacao_adocao`;

DELETE FROM `schema_migrations` WHERE `versao` = '006_solicitacao_adocao';
