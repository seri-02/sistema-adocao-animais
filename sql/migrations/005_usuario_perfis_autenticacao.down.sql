-- =============================================================================
-- 005_usuario_perfis_autenticacao (DOWN)
-- =============================================================================
-- Volta `usuario` ao formato legado (id_user, email/senha/name varchar(50)).
--
-- DOWN deliberadamente CONSERVADOR: serve para rollback técnico logo após a
-- migração. Aborta ANTES de qualquer DDL se algum dado não for representável no
-- legado; nada é descartado nem convertido:
--   - migração posterior à 005 ainda aplicada;
--   - nome, e-mail ou senha com mais de 50 caracteres (um hash de
--     password_hash já não cabe: depois que qualquer senha for modernizada no
--     login, este DOWN é recusado de propósito; hashes modernos NUNCA são
--     convertidos de volta para SHA-1);
--   - perfil diferente de ADMIN; telefone preenchido; ativo diferente de 1.
-- Nesses casos o rollback seguro é restaurar o backup pré-F2a.
-- criado_em e atualizado_em são metadados técnicos e são descartados.
-- =============================================================================

SET NAMES utf8mb4;

SET @sql_precheck = IF((SELECT COUNT(*) FROM `schema_migrations` WHERE `versao` > '005_usuario_perfis_autenticacao') = 0, 'DO 0',
    'SELECT * FROM `ABORTADO__reverta_as_migracoes_posteriores_a_005`');
PREPARE stmt_precheck FROM @sql_precheck;
EXECUTE stmt_precheck;
DEALLOCATE PREPARE stmt_precheck;

SET @sql_precheck = IF((
    SELECT COUNT(*) FROM `usuario`
     WHERE CHAR_LENGTH(`nome`) > 50 OR CHAR_LENGTH(`email`) > 50 OR CHAR_LENGTH(`senha`) > 50
    ) = 0, 'DO 0',
    'SELECT * FROM `ABORTADO__usuario_com_nome_email_ou_senha_maior_que_50`');
PREPARE stmt_precheck FROM @sql_precheck;
EXECUTE stmt_precheck;
DEALLOCATE PREPARE stmt_precheck;

SET @sql_precheck = IF((
    SELECT COUNT(*) FROM `usuario`
     WHERE `perfil` <> 'ADMIN' OR `ativo` <> 1 OR TRIM(IFNULL(`telefone`, '')) <> ''
    ) = 0, 'DO 0',
    'SELECT * FROM `ABORTADO__usuario_com_perfil_telefone_ou_ativo_nao_legado`');
PREPARE stmt_precheck FROM @sql_precheck;
EXECUTE stmt_precheck;
DEALLOCATE PREPARE stmt_precheck;

-- -----------------------------------------------------------------------------
-- Conversão (os pré-checks garantem que é exata)
-- -----------------------------------------------------------------------------
ALTER TABLE `usuario`
    DROP KEY `uk_usuario_email`,
    DROP COLUMN `telefone`,
    DROP COLUMN `perfil`,
    DROP COLUMN `ativo`,
    DROP COLUMN `criado_em`,
    DROP COLUMN `atualizado_em`,
    CHANGE `id_usuario` `id_user` int(11) NOT NULL AUTO_INCREMENT,
    MODIFY `email` varchar(50) DEFAULT NULL AFTER `id_user`,
    MODIFY `senha` varchar(50) DEFAULT NULL AFTER `email`,
    CHANGE `nome` `name` varchar(50) DEFAULT NULL AFTER `senha`;

DELETE FROM `schema_migrations` WHERE `versao` = '005_usuario_perfis_autenticacao';
