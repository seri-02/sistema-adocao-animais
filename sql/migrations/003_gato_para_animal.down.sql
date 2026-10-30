-- =============================================================================
-- 003_gato_para_animal (DOWN)
-- =============================================================================
-- Volta `animal` para a tabela legada `gato` (estrutura posterior à 002).
-- Executar somente depois do DOWN da 004 (a FK de adocoes referencia animal).
--
-- DOWN deliberadamente CONSERVADOR: serve para rollback técnico logo após a
-- migração. Só converte se CADA registro ainda for exatamente representável no
-- modelo legado; caso contrário aborta ANTES de qualquer DDL, sem descartar nem
-- inventar dados. Depois que o sistema produziu dados do novo domínio, o
-- rollback seguro é restaurar o backup (ver sql/README.md).
--
-- Pré-checks (valem com qualquer sql_mode). Aborta se:
--   1. a 004 ainda estiver aplicada;
--   2. algum nome tiver mais de 50 caracteres (o legado truncaria);
--   3. alguma espécie for diferente de GATO;
--   4. algum status for diferente de DISPONIVEL/ADOTADO (únicos com equivalente
--      em `adotado`);
--   5. houver dado estruturado novo: sexo informado, nascimento, porte,
--      castrado, vacinado, condição de saúde ou comportamento;
--   6. existir registro fora dos 6 migrados com sua imagem original
--      (animal novo, imagem trocada, PNG etc.): não há nome legado seguro.
-- Com os pré-checks satisfeitos, nada relevante é perdido: os metadados
-- criado_em/atualizado_em são descartados e descricao NULL volta como ''.
-- =============================================================================

SET NAMES utf8mb4;

-- 1. Ordem das migrações
SET @sql_precheck = IF((SELECT COUNT(*) FROM `schema_migrations` WHERE `versao` = '004_adocoes_fk_animal') = 0, 'DO 0',
    'SELECT * FROM `ABORTADO__reverta_a_004_antes_da_003`');
PREPARE stmt_precheck FROM @sql_precheck;
EXECUTE stmt_precheck;
DEALLOCATE PREPARE stmt_precheck;

-- 2. Nome cabe no varchar(50) legado
SET @sql_precheck = IF((SELECT COUNT(*) FROM `animal` WHERE CHAR_LENGTH(`nome`) > 50) = 0, 'DO 0',
    'SELECT * FROM `ABORTADO__animal_com_nome_maior_que_50_caracteres`');
PREPARE stmt_precheck FROM @sql_precheck;
EXECUTE stmt_precheck;
DEALLOCATE PREPARE stmt_precheck;

-- 3. Espécie
SET @sql_precheck = IF((SELECT COUNT(*) FROM `animal` WHERE `especie` <> 'GATO') = 0, 'DO 0',
    'SELECT * FROM `ABORTADO__animal_de_especie_diferente_de_gato`');
PREPARE stmt_precheck FROM @sql_precheck;
EXECUTE stmt_precheck;
DEALLOCATE PREPARE stmt_precheck;

-- 4. Status
SET @sql_precheck = IF((SELECT COUNT(*) FROM `animal` WHERE `status` NOT IN ('DISPONIVEL', 'ADOTADO')) = 0, 'DO 0',
    'SELECT * FROM `ABORTADO__animal_com_status_sem_equivalente_legado`');
PREPARE stmt_precheck FROM @sql_precheck;
EXECUTE stmt_precheck;
DEALLOCATE PREPARE stmt_precheck;

-- 5. Dados estruturados que o legado descartaria
SET @sql_precheck = IF((
    SELECT COUNT(*) FROM `animal`
     WHERE `sexo` <> 'NAO_INFORMADO'
        OR `data_nascimento_estimada` IS NOT NULL
        OR `porte` IS NOT NULL
        OR `castrado` IS NOT NULL
        OR `vacinado` IS NOT NULL
        OR TRIM(IFNULL(`condicao_saude`, '')) <> ''
        OR TRIM(IFNULL(`comportamento`, '')) <> ''
    ) = 0, 'DO 0',
    'SELECT * FROM `ABORTADO__animal_com_dados_estruturados_novos`');
PREPARE stmt_precheck FROM @sql_precheck;
EXECUTE stmt_precheck;
DEALLOCATE PREPARE stmt_precheck;

-- 6. Somente os 6 registros migrados, cada um com sua imagem original
SET @sql_precheck = IF((
    SELECT COUNT(*) FROM `animal`
     WHERE (`id_animal`, IFNULL(`imagem`, '')) NOT IN ((58, 'lucis.jpg'), (59, 'snow.jpg'), (60, 'vado.jpg'),
                                                     (61, 'ulisses.jpg'), (64, 'prince.jpg'), (76, 'juliette.jpg'))
    ) = 0, 'DO 0',
    'SELECT * FROM `ABORTADO__animal_novo_ou_imagem_sem_equivalente_legado`');
PREPARE stmt_precheck FROM @sql_precheck;
EXECUTE stmt_precheck;
DEALLOCATE PREPARE stmt_precheck;

-- -----------------------------------------------------------------------------
-- Conversão (os pré-checks garantem que é exata)
-- -----------------------------------------------------------------------------
ALTER TABLE `animal`
    DROP KEY `idx_animal_status`,
    ADD `adotado` bit(1) NOT NULL DEFAULT b'1' AFTER `descricao`,
    ADD `img` varchar(255) NOT NULL DEFAULT '' AFTER `adotado`;

UPDATE `animal` SET `adotado` = IF(`status` = 'DISPONIVEL', 1, 0);

UPDATE `animal` SET `img` = 'Lucis'    WHERE `id_animal` = 58 AND `imagem` = 'lucis.jpg';
UPDATE `animal` SET `img` = 'Snow'     WHERE `id_animal` = 59 AND `imagem` = 'snow.jpg';
UPDATE `animal` SET `img` = 'Vado'     WHERE `id_animal` = 60 AND `imagem` = 'vado.jpg';
UPDATE `animal` SET `img` = 'Ulisses'  WHERE `id_animal` = 61 AND `imagem` = 'ulisses.jpg';
UPDATE `animal` SET `img` = 'Prince'   WHERE `id_animal` = 64 AND `imagem` = 'prince.jpg';
UPDATE `animal` SET `img` = 'Juliette' WHERE `id_animal` = 76 AND `imagem` = 'juliette.jpg';

UPDATE `animal` SET `descricao` = '' WHERE `descricao` IS NULL;

ALTER TABLE `animal`
    ALTER `adotado` DROP DEFAULT,
    ALTER `img` DROP DEFAULT,
    DROP COLUMN `especie`,
    DROP COLUMN `sexo`,
    DROP COLUMN `data_nascimento_estimada`,
    DROP COLUMN `porte`,
    DROP COLUMN `castrado`,
    DROP COLUMN `vacinado`,
    DROP COLUMN `condicao_saude`,
    DROP COLUMN `comportamento`,
    DROP COLUMN `imagem`,
    DROP COLUMN `status`,
    DROP COLUMN `criado_em`,
    DROP COLUMN `atualizado_em`,
    MODIFY `descricao` text NOT NULL,
    MODIFY `nome` varchar(50) NOT NULL,
    CHANGE `id_animal` `id_gato` int(11) NOT NULL AUTO_INCREMENT;

RENAME TABLE `animal` TO `gato`;

DELETE FROM `schema_migrations` WHERE `versao` = '003_gato_para_animal';
