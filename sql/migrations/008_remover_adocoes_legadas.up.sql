-- =============================================================================
-- 008_remover_adocoes_legadas (UP)
-- =============================================================================
-- Remove a tabela legada `adocoes` (adoções imediatas do projeto antigo, anônimas,
-- sem vínculo com `usuario`, sem data e sem participação no fluxo atual, que usa
-- `solicitacao_adocao`). Seus dados NÃO são migrados para outra tabela: ficam
-- preservados apenas no backup feito antes desta migração.
--
-- O status dos animais NÃO é alterado. Animais adotados no legado continuam
-- ADOTADO em `animal` e seguem protegidos contra exclusão pela regra de status da
-- aplicação (function/delete/gato.php), que precisa estar em vigor ANTES desta
-- migração: até aqui a proteção deles era só a FK fk_adocoes_animal.
--
-- Aborta ANTES do DROP se:
--   - a 007 não estiver aplicada ou a 008 já estiver aplicada;
--   - a tabela `adocoes` não existir;
--   - alguma linha de `adocoes` apontar para animal que NÃO esteja ADOTADO
--     (nada é corrigido automaticamente: o DROP apagaria a única evidência).
--
-- Pré-requisito: 001..007 aplicadas; backup pré-008 validado.
-- =============================================================================

SET NAMES utf8mb4;

SET @sql_precheck = IF((SELECT COUNT(*) FROM `schema_migrations` WHERE `versao` = '007_cancelamento_solicitacao') = 1, 'DO 0',
    'SELECT * FROM `ABORTADO__aplique_a_migracao_007_antes_da_008`');
PREPARE stmt_precheck FROM @sql_precheck;
EXECUTE stmt_precheck;
DEALLOCATE PREPARE stmt_precheck;

SET @sql_precheck = IF((SELECT COUNT(*) FROM `schema_migrations` WHERE `versao` = '008_remover_adocoes_legadas') = 0, 'DO 0',
    'SELECT * FROM `ABORTADO__migracao_008_ja_aplicada`');
PREPARE stmt_precheck FROM @sql_precheck;
EXECUTE stmt_precheck;
DEALLOCATE PREPARE stmt_precheck;

SET @sql_precheck = IF((SELECT COUNT(*) FROM information_schema.TABLES
                         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'adocoes') = 1, 'DO 0',
    'SELECT * FROM `ABORTADO__tabela_adocoes_nao_existe`');
PREPARE stmt_precheck FROM @sql_precheck;
EXECUTE stmt_precheck;
DEALLOCATE PREPARE stmt_precheck;

-- Executado só depois de confirmar que a tabela existe (senão a própria consulta falharia)
SET @sql_precheck = 'SELECT IF(COUNT(*) = 0, ''DO 0'', ''SELECT * FROM `ABORTADO__adocao_legada_com_animal_nao_adotado`'') INTO @sql_precheck_status
                       FROM `adocoes` d
                       LEFT JOIN `animal` a ON a.`id_animal` = d.`id_animal`
                      WHERE a.`id_animal` IS NULL OR a.`status` <> ''ADOTADO''';
PREPARE stmt_precheck FROM @sql_precheck;
EXECUTE stmt_precheck;
DEALLOCATE PREPARE stmt_precheck;
PREPARE stmt_precheck FROM @sql_precheck_status;
EXECUTE stmt_precheck;
DEALLOCATE PREPARE stmt_precheck;

-- A FK fk_adocoes_animal está em `adocoes` e é removida junto com a tabela
DROP TABLE `adocoes`;

INSERT INTO `schema_migrations` (`versao`) VALUES ('008_remover_adocoes_legadas');
