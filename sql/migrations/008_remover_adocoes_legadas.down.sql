-- =============================================================================
-- 008_remover_adocoes_legadas (DOWN)
-- =============================================================================
-- Recria `adocoes` VAZIA, com a estrutura exata de antes da 008 (a deixada pela
-- 004), para que os DOWNs da 004, 003 e 001 continuem tecnicamente executáveis.
--
-- Os registros legados NÃO voltam por aqui: só podem ser restaurados a partir do
-- backup pré-008.
--
-- Aborta ANTES de qualquer DDL se alguma migração posterior à 008 estiver aplicada
-- ou se `adocoes` já existir (nada é sobrescrito).
-- =============================================================================

SET NAMES utf8mb4;

SET @sql_precheck = IF((SELECT COUNT(*) FROM `schema_migrations` WHERE `versao` > '008_remover_adocoes_legadas') = 0, 'DO 0',
    'SELECT * FROM `ABORTADO__reverta_as_migracoes_posteriores_a_008`');
PREPARE stmt_precheck FROM @sql_precheck;
EXECUTE stmt_precheck;
DEALLOCATE PREPARE stmt_precheck;

SET @sql_precheck = IF((SELECT COUNT(*) FROM information_schema.TABLES
                         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'adocoes') = 0, 'DO 0',
    'SELECT * FROM `ABORTADO__tabela_adocoes_ja_existe`');
PREPARE stmt_precheck FROM @sql_precheck;
EXECUTE stmt_precheck;
DEALLOCATE PREPARE stmt_precheck;

CREATE TABLE `adocoes` (
    `id_adocao` int(11) NOT NULL AUTO_INCREMENT,
    `id_animal` int(11) NOT NULL,
    `nome_adotante` varchar(50) NOT NULL,
    `email_adotante` varchar(100) NOT NULL,
    `telefone` varchar(20) NOT NULL,
    PRIMARY KEY (`id_adocao`),
    KEY `idx_adocoes_animal` (`id_animal`),
    CONSTRAINT `fk_adocoes_animal` FOREIGN KEY (`id_animal`)
        REFERENCES `animal` (`id_animal`)
        ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DELETE FROM `schema_migrations` WHERE `versao` = '008_remover_adocoes_legadas';
