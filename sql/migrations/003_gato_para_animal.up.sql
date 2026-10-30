-- =============================================================================
-- 003_gato_para_animal (UP)
-- =============================================================================
-- Transforma a tabela legada `gato` na tabela de domínio `animal`, preservando
-- todos os registros e os IDs (id_gato -> id_animal, mesmo AUTO_INCREMENT).
--
-- Mapeamento:
--   nome      -> nome (varchar 50 -> 80)
--   descricao -> descricao (agora opcional)
--   adotado   -> status: 1 = 'DISPONIVEL', 0 = 'ADOTADO' (semântica legada invertida)
--   img       -> imagem: nome REAL do arquivo, com extensão (mapeamento explícito
--                por id, conferido contra os arquivos de image/ em 2026-10-29)
--   (novo)    -> especie = 'GATO' para todos os registros legados
-- Os demais campos novos ficam NULL/padrão: nada é deduzido do texto da descricao.
-- criado_em dos registros legados recebe o momento da migração (a data original
-- de cadastro não existia no legado).
--
-- Pré-requisitos: 001 e 002 aplicadas; backup validado.
-- DDL faz commit implícito: por isso todas as verificações vêm ANTES do 1º ALTER.
-- =============================================================================

SET NAMES utf8mb4;

-- -----------------------------------------------------------------------------
-- Pré-check: todo registro existente precisa estar no mapeamento de imagens
-- conferido. Se houver algum fora dele, a migração é abortada aqui, sem alterar
-- nada (o SELECT abaixo falha de propósito: tabela inexistente com nome explicativo).
-- -----------------------------------------------------------------------------
SET @gatos_fora_do_mapeamento = (
    SELECT COUNT(*) FROM `gato`
     WHERE (`id_gato`, `img`) NOT IN ((58, 'Lucis'), (59, 'Snow'), (60, 'Vado'),
                                      (61, 'Ulisses'), (64, 'Prince'), (76, 'Juliette'))
);
SET @sql_precheck = IF(@gatos_fora_do_mapeamento = 0, 'DO 0',
    'SELECT * FROM `ABORTADO__gato_com_imagem_fora_do_mapeamento_conferido`');
PREPARE stmt_precheck FROM @sql_precheck;
EXECUTE stmt_precheck;
DEALLOCATE PREPARE stmt_precheck;

-- -----------------------------------------------------------------------------
-- Estrutura
-- -----------------------------------------------------------------------------
RENAME TABLE `gato` TO `animal`;

ALTER TABLE `animal`
    CHANGE `id_gato` `id_animal` int(11) NOT NULL AUTO_INCREMENT,
    MODIFY `nome` varchar(80) NOT NULL,
    ADD `especie` enum('GATO','CACHORRO','OUTRO') NOT NULL DEFAULT 'GATO' AFTER `nome`,
    ADD `sexo` enum('MACHO','FEMEA','NAO_INFORMADO') NOT NULL DEFAULT 'NAO_INFORMADO' AFTER `especie`,
    ADD `data_nascimento_estimada` date NULL DEFAULT NULL AFTER `sexo`,
    ADD `porte` enum('PEQUENO','MEDIO','GRANDE') NULL DEFAULT NULL AFTER `data_nascimento_estimada`,
    ADD `castrado` tinyint(1) NULL DEFAULT NULL AFTER `porte`,
    ADD `vacinado` tinyint(1) NULL DEFAULT NULL AFTER `castrado`,
    ADD `condicao_saude` text NULL DEFAULT NULL AFTER `vacinado`,
    ADD `comportamento` text NULL DEFAULT NULL AFTER `condicao_saude`,
    MODIFY `descricao` text NULL DEFAULT NULL AFTER `comportamento`,
    ADD `imagem` varchar(255) NULL DEFAULT NULL AFTER `descricao`,
    ADD `status` enum('DISPONIVEL','EM_PROCESSO','ADOTADO','INDISPONIVEL') NOT NULL DEFAULT 'DISPONIVEL' AFTER `imagem`,
    ADD `criado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `status`;

-- -----------------------------------------------------------------------------
-- Dados
-- -----------------------------------------------------------------------------
UPDATE `animal` SET `status` = IF(`adotado` = 1, 'DISPONIVEL', 'ADOTADO');

-- Imagens: nome real do arquivo (o pré-check garantiu que só existem estes registros)
UPDATE `animal` SET `imagem` = 'lucis.jpg'    WHERE `id_animal` = 58 AND `img` = 'Lucis';
UPDATE `animal` SET `imagem` = 'snow.jpg'     WHERE `id_animal` = 59 AND `img` = 'Snow';
UPDATE `animal` SET `imagem` = 'vado.jpg'     WHERE `id_animal` = 60 AND `img` = 'Vado';
UPDATE `animal` SET `imagem` = 'ulisses.jpg'  WHERE `id_animal` = 61 AND `img` = 'Ulisses';
UPDATE `animal` SET `imagem` = 'prince.jpg'   WHERE `id_animal` = 64 AND `img` = 'Prince';
UPDATE `animal` SET `imagem` = 'juliette.jpg' WHERE `id_animal` = 76 AND `img` = 'Juliette';

-- -----------------------------------------------------------------------------
-- Remove as colunas legadas e finaliza a estrutura
-- -----------------------------------------------------------------------------
ALTER TABLE `animal`
    DROP COLUMN `adotado`,
    DROP COLUMN `img`,
    -- especie sem DEFAULT explícito (o 'GATO' era só para os registros legados).
    -- Atenção: ENUM NOT NULL ainda assume implicitamente o 1º valor se a coluna
    -- for omitida no INSERT; por isso a aplicação sempre informa e valida a espécie.
    ALTER `especie` DROP DEFAULT,
    -- criada só agora para que as atualizações acima não contem como "edição"
    ADD `atualizado_em` datetime NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER `criado_em`,
    ADD KEY `idx_animal_status` (`status`);

INSERT INTO `schema_migrations` (`versao`) VALUES ('003_gato_para_animal');
