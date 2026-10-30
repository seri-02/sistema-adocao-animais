-- =============================================================================
-- BASELINE 000 — schema legado (estado anterior à F0b)
-- =============================================================================
-- Extraído do banco local real `abrigo_dos_gatos` (MariaDB 10.4.32) em 2026-10-29,
-- antes de qualquer migração. Somente ESTRUTURA: não contém dados, e-mails,
-- senhas nem registros pessoais.
--
-- Representa fielmente o legado, inclusive suas limitações conhecidas
-- (MyISAM sem FK/transação, charset utf8 de 3 bytes, adocoes.fk_id_gato varchar).
-- Essas limitações são tratadas pelas migrações em sql/migrations/.
--
-- Os contadores AUTO_INCREMENT=N das tabelas foram omitidos de propósito:
-- em uma instalação vazia a numeração começa em 1. Para reproduzir os dados e
-- a numeração reais, restaure um backup (ver sql/README.md).
--
-- Uso: banco vazio  →  este arquivo  →  migrações em ordem.
-- Este arquivo NÃO apaga tabelas: falha se alguma já existir.
-- =============================================================================

SET NAMES utf8mb4;

CREATE TABLE `adocoes` (
  `id_adocao` int(11) NOT NULL AUTO_INCREMENT,
  `fk_id_gato` varchar(50) NOT NULL,
  `nome_adotante` varchar(50) NOT NULL,
  `email_adotante` varchar(100) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  PRIMARY KEY (`id_adocao`),
  KEY `fk_id_gato` (`fk_id_gato`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `gato` (
  `id_gato` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  `descricao` text NOT NULL,
  `adotado` bit(1) NOT NULL,
  `img` varchar(255) NOT NULL,
  PRIMARY KEY (`id_gato`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `usuario` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) DEFAULT NULL,
  `senha` varchar(50) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `voluntario` (
  `id_voluntario` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `idade` int(11) DEFAULT NULL,
  `dia_disponivel` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_voluntario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
