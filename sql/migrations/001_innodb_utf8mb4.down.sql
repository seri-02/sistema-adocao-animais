-- =============================================================================
-- 001_innodb_utf8mb4 (DOWN)
-- =============================================================================
-- Volta engines e charsets ao estado da baseline 000:
--   gato, adocoes, usuario -> MyISAM ; voluntario -> InnoDB (já era)
--   tabelas -> utf8 / utf8_general_ci ; banco -> utf8 / utf8_swedish_ci
-- Remove o registro da 001 e, se não restar nenhuma migração registrada,
-- a tabela schema_migrations.
--
-- Executar somente depois do DOWN da 002 (ordem inversa). Se a 003 ainda estiver
-- aplicada, a tabela `gato` não existe e o pré-check falha antes de qualquer DDL.
--
-- utf8 (3 bytes) não comporta caracteres de 4 bytes (ex.: emoji). Sem modo
-- estrito, o MySQL/MariaDB os troca por '?' EM SILÊNCIO — e o XAMPP/MariaDB
-- local costuma vir sem STRICT_TRANS_TABLES. Por isso esta migração:
--   1. aborta ANTES de qualquer DDL se algum texto tiver caractere de 4 bytes
--      (o modo estrito sozinho recusaria só no ALTER da tabela afetada, depois
--      de as anteriores já terem sido convertidas: estado misto);
--   2. ativa STRICT_TRANS_TABLES apenas nesta sessão (segunda camada), sem
--      alterar @@GLOBAL.sql_mode, e restaura o modo anterior ao concluir.
-- Se houver esses caracteres, restaure o backup em vez de usar este DOWN.
-- =============================================================================

SET NAMES utf8mb4;

-- Modo estrito só nesta sessão (o modo anterior é restaurado no final)
SET @sql_mode_anterior = @@SESSION.sql_mode;
SET SESSION sql_mode = IF(FIND_IN_SET('STRICT_TRANS_TABLES', @@SESSION.sql_mode),
                          @@SESSION.sql_mode,
                          CONCAT_WS(',', 'STRICT_TRANS_TABLES', NULLIF(@@SESSION.sql_mode, '')));

-- Pré-check: nenhum texto com caractere de 4 bytes (em UTF-8, os bytes F0-F4
-- só aparecem como início desses caracteres; o HEX alinhado em pares de dígitos
-- localiza esses bytes de forma idêntica no MariaDB e no MySQL)
SET @textos_4_bytes = (
    SELECT COUNT(*) FROM (
        SELECT `nome` AS t FROM `gato` UNION ALL SELECT `descricao` FROM `gato` UNION ALL SELECT `img` FROM `gato`
        UNION ALL SELECT `fk_id_gato` FROM `adocoes` UNION ALL SELECT `nome_adotante` FROM `adocoes`
        UNION ALL SELECT `email_adotante` FROM `adocoes` UNION ALL SELECT `telefone` FROM `adocoes`
        UNION ALL SELECT `email` FROM `usuario` UNION ALL SELECT `senha` FROM `usuario` UNION ALL SELECT `name` FROM `usuario`
        UNION ALL SELECT `nome` FROM `voluntario` UNION ALL SELECT `email` FROM `voluntario`
        UNION ALL SELECT `dia_disponivel` FROM `voluntario`
    ) textos
    WHERE HEX(t) REGEXP '^([0-9A-F]{2})*F[0-4]'
);
SET @sql_precheck = IF(@textos_4_bytes = 0, 'DO 0',
    'SELECT * FROM `ABORTADO__texto_com_caractere_de_4_bytes_sem_equivalente_em_utf8`');
PREPARE stmt_precheck FROM @sql_precheck;
EXECUTE stmt_precheck;
DEALLOCATE PREPARE stmt_precheck;

ALTER TABLE `gato` ENGINE=MyISAM, CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `gato` MODIFY `descricao` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `adocoes` ENGINE=MyISAM, CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;

ALTER TABLE `usuario` ENGINE=MyISAM, CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;

ALTER TABLE `voluntario` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;

ALTER DATABASE CHARACTER SET utf8 COLLATE utf8_swedish_ci;

DELETE FROM `schema_migrations` WHERE `versao` = '001_innodb_utf8mb4';

-- A tabela de controle nasceu na 001: só é removida se estiver vazia.
SET @sql_controle = IF((SELECT COUNT(*) FROM `schema_migrations`) = 0, 'DROP TABLE `schema_migrations`', 'DO 0');
PREPARE stmt_controle FROM @sql_controle;
EXECUTE stmt_controle;
DEALLOCATE PREPARE stmt_controle;

SET SESSION sql_mode = @sql_mode_anterior;
