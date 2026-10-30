-- =============================================================================
-- 001_innodb_utf8mb4 (UP)
-- =============================================================================
-- Converte as 4 tabelas legadas para InnoDB (FK, transações, locks de linha) e
-- padroniza o charset em utf8mb4 / utf8mb4_unicode_ci (MariaDB 10.4 e MySQL 5.7/8).
-- Também cria a tabela de controle schema_migrations.
--
-- A conversão utf8 -> utf8mb4 preserva os bytes de todo texto existente.
-- Ela NÃO corrige a dupla codificação legada: isso é feito pela 002.
--
-- Pré-requisito: backup validado (ver sql/README.md).
-- Atenção: DDL faz commit implícito. Se falhar no meio, confira o estado das
-- tabelas antes de tentar novamente; em último caso, restaure o backup.
-- =============================================================================

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `schema_migrations` (
  `versao` varchar(50) NOT NULL,
  `aplicada_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`versao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Padrão do banco (vale para tabelas criadas pelas próximas migrações)
ALTER DATABASE CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `gato` ENGINE=InnoDB, CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- O CONVERT promove TEXT para MEDIUMTEXT; mantém o tipo original
ALTER TABLE `gato` MODIFY `descricao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;

ALTER TABLE `adocoes` ENGINE=InnoDB, CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `usuario` ENGINE=InnoDB, CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `voluntario` ENGINE=InnoDB, CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Registrado somente se todos os comandos acima tiverem sucesso
-- (o cliente mysql interrompe o script no primeiro erro).
INSERT INTO `schema_migrations` (`versao`) VALUES ('001_innodb_utf8mb4');
