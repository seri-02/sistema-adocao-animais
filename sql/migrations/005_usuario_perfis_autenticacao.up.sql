-- =============================================================================
-- 005_usuario_perfis_autenticacao (UP)
-- =============================================================================
-- Evolui `usuario` (contas administrativas legadas) para suportar perfis:
--   id_user -> id_usuario ; name -> nome varchar(100) ; email varchar(150) NOT NULL
--   senha varchar(255) NOT NULL (comporta password_hash) ; + telefone, perfil,
--   ativo, criado_em, atualizado_em ; + UNIQUE(email)
--
-- Todos os registros existentes viram perfil = 'ADMIN', ativo = 1 (eram as
-- contas administrativas; ninguém é transformado em FUNCIONARIO).
-- IDs, nome, e-mail, hash de senha (SHA-1 legado, byte a byte) e AUTO_INCREMENT
-- são preservados. A senha NÃO é convertida aqui: é impossível sem o texto puro.
-- A aplicação refaz o hash com password_hash no próximo login válido.
-- criado_em dos registros legados recebe o momento da migração.
--
-- Pré-checks antes de qualquer DDL (valem com qualquer sql_mode): aborta se
-- houver e-mail duplicado (mesma collation do índice UNIQUE), e-mail/nome/senha
-- NULL ou vazios. Nada é corrigido automaticamente.
-- Pré-requisito: 001..004 aplicadas; backup validado.
-- =============================================================================

SET NAMES utf8mb4;

-- E-mail duplicado (comparação igual à do índice: utf8mb4_unicode_ci, sem
-- diferenciar maiúsculas nem espaços finais)
SET @sql_precheck = IF((
    SELECT COUNT(*) FROM (
        SELECT `email` FROM `usuario` WHERE `email` IS NOT NULL
         GROUP BY `email` COLLATE utf8mb4_unicode_ci HAVING COUNT(*) > 1
    ) duplicados
    ) = 0, 'DO 0',
    'SELECT * FROM `ABORTADO__usuario_com_email_duplicado`');
PREPARE stmt_precheck FROM @sql_precheck;
EXECUTE stmt_precheck;
DEALLOCATE PREPARE stmt_precheck;

SET @sql_precheck = IF((SELECT COUNT(*) FROM `usuario` WHERE TRIM(IFNULL(`email`, '')) = '') = 0, 'DO 0',
    'SELECT * FROM `ABORTADO__usuario_com_email_nulo_ou_vazio`');
PREPARE stmt_precheck FROM @sql_precheck;
EXECUTE stmt_precheck;
DEALLOCATE PREPARE stmt_precheck;

SET @sql_precheck = IF((SELECT COUNT(*) FROM `usuario` WHERE TRIM(IFNULL(`name`, '')) = '') = 0, 'DO 0',
    'SELECT * FROM `ABORTADO__usuario_com_nome_nulo_ou_vazio`');
PREPARE stmt_precheck FROM @sql_precheck;
EXECUTE stmt_precheck;
DEALLOCATE PREPARE stmt_precheck;

SET @sql_precheck = IF((SELECT COUNT(*) FROM `usuario` WHERE IFNULL(`senha`, '') = '') = 0, 'DO 0',
    'SELECT * FROM `ABORTADO__usuario_com_senha_nula_ou_vazia`');
PREPARE stmt_precheck FROM @sql_precheck;
EXECUTE stmt_precheck;
DEALLOCATE PREPARE stmt_precheck;

-- -----------------------------------------------------------------------------
-- Estrutura
-- -----------------------------------------------------------------------------
ALTER TABLE `usuario`
    CHANGE `id_user` `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
    CHANGE `name` `nome` varchar(100) NOT NULL AFTER `id_usuario`,
    MODIFY `email` varchar(150) NOT NULL AFTER `nome`,
    MODIFY `senha` varchar(255) NOT NULL AFTER `email`,
    ADD `telefone` varchar(20) NULL DEFAULT NULL AFTER `senha`,
    ADD `perfil` enum('ADOTANTE','FUNCIONARIO','ADMIN') NOT NULL DEFAULT 'ADOTANTE' AFTER `telefone`,
    ADD `ativo` tinyint(1) NOT NULL DEFAULT 1 AFTER `perfil`,
    ADD `criado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `ativo`,
    ADD UNIQUE KEY `uk_usuario_email` (`email`);

-- Contas legadas eram administrativas: preserva o acesso
UPDATE `usuario` SET `perfil` = 'ADMIN', `ativo` = 1;

-- Criada só agora para que o UPDATE acima não conte como "edição"
ALTER TABLE `usuario`
    ADD `atualizado_em` datetime NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER `criado_em`;

INSERT INTO `schema_migrations` (`versao`) VALUES ('005_usuario_perfis_autenticacao');
