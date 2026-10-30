-- =============================================================================
-- 001_innodb_utf8mb4 (DOWN)
-- =============================================================================
-- Volta engines e charsets ao estado da baseline 000:
--   gato, adocoes, usuario -> MyISAM ; voluntario -> InnoDB (já era)
--   tabelas -> utf8 / utf8_general_ci ; banco -> utf8 / utf8_swedish_ci
-- Remove o registro da 001 e, se não restar nenhuma migração registrada,
-- a tabela schema_migrations.
--
-- Executar somente depois do DOWN da 002 (ordem inversa).
--
-- Limitação: textos gravados depois da 001 com caracteres de 4 bytes
-- (ex.: emoji) não cabem em utf8 de 3 bytes. Nesse caso o ALTER falha
-- (modo estrito) e nada é perdido silenciosamente: restaure o backup.
-- =============================================================================

SET NAMES utf8mb4;

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
