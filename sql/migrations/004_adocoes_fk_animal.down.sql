-- =============================================================================
-- 004_adocoes_fk_animal (DOWN)
-- =============================================================================
-- Remove a FK e devolve `adocoes` à referência legada fk_id_gato varchar(50),
-- com o índice original. Os valores (ids) são preservados como texto.
-- Executar ANTES do DOWN da 003.
-- =============================================================================

SET NAMES utf8mb4;

ALTER TABLE `adocoes`
    DROP FOREIGN KEY `fk_adocoes_animal`;

ALTER TABLE `adocoes`
    DROP KEY `idx_adocoes_animal`,
    CHANGE `id_animal` `fk_id_gato` varchar(50) NOT NULL,
    ADD KEY `fk_id_gato` (`fk_id_gato`);

DELETE FROM `schema_migrations` WHERE `versao` = '004_adocoes_fk_animal';
