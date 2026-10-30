-- =============================================================================
-- 004_adocoes_fk_animal (UP)
-- =============================================================================
-- Adapta a tabela TEMPORÁRIA `adocoes` (fluxo legado de adoção, a ser substituído
-- pela solicitação de adoção na F3) ao domínio `animal`:
--   fk_id_gato varchar(50)  ->  id_animal int(11) NOT NULL
--   + FK física adocoes.id_animal -> animal.id_animal
--     ON DELETE RESTRICT ON UPDATE RESTRICT
-- Animal com histórico de adoção não pode ser excluído; órfãos deixam de ser possíveis.
-- Nenhuma outra coluna ou regra é adicionada a `adocoes`.
--
-- Pré-requisito: 003 aplicada; backup validado.
-- =============================================================================

SET NAMES utf8mb4;

-- -----------------------------------------------------------------------------
-- Pré-check de órfãos (antes de qualquer DDL). Não corrige nada: se existir
-- adoção com referência não numérica ou sem animal correspondente, aborta.
-- -----------------------------------------------------------------------------
SET @adocoes_orfas = (
    SELECT COUNT(*)
      FROM `adocoes` a
      LEFT JOIN `animal` n ON a.`fk_id_gato` REGEXP '^[0-9]+$' AND n.`id_animal` = CAST(a.`fk_id_gato` AS UNSIGNED)
     WHERE n.`id_animal` IS NULL
);
SET @sql_precheck = IF(@adocoes_orfas = 0, 'DO 0',
    'SELECT * FROM `ABORTADO__adocoes_com_referencia_orfa`');
PREPARE stmt_precheck FROM @sql_precheck;
EXECUTE stmt_precheck;
DEALLOCATE PREPARE stmt_precheck;

ALTER TABLE `adocoes`
    DROP KEY `fk_id_gato`,
    CHANGE `fk_id_gato` `id_animal` int(11) NOT NULL;

ALTER TABLE `adocoes`
    ADD KEY `idx_adocoes_animal` (`id_animal`),
    ADD CONSTRAINT `fk_adocoes_animal` FOREIGN KEY (`id_animal`)
        REFERENCES `animal` (`id_animal`)
        ON DELETE RESTRICT ON UPDATE RESTRICT;

INSERT INTO `schema_migrations` (`versao`) VALUES ('004_adocoes_fk_animal');
