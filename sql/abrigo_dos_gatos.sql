-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 20-Out-2022 às 17:05
-- Versão do servidor: 5.7.36
-- versão do PHP: 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `abrigo_dos_gatos`
--
CREATE DATABASE IF NOT EXISTS `abrigo_dos_gatos` DEFAULT CHARACTER SET utf8 COLLATE utf8_swedish_ci;
USE `abrigo_dos_gatos`;

-- --------------------------------------------------------

--
-- Estrutura da tabela `adocoes`
--

DROP TABLE IF EXISTS `adocoes`;
CREATE TABLE IF NOT EXISTS `adocoes` (
  `id_adocao` int(11) NOT NULL AUTO_INCREMENT,
  `fk_id_gato` varchar(50) COLLATE utf8_swedish_ci NOT NULL,
  `nome_adotante` varchar(50) COLLATE utf8_swedish_ci NOT NULL,
  `email_adotante` varchar(100) COLLATE utf8_swedish_ci NOT NULL,
  `telefone` varchar(20) COLLATE utf8_swedish_ci NOT NULL,
  PRIMARY KEY (`id_adocao`),
  KEY `fk_id_gato` (`fk_id_gato`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Extraindo dados da tabela `adocoes`
--

INSERT INTO `adocoes` (`id_adocao`, `fk_id_gato`, `nome_adotante`, `email_adotante`, `telefone`) VALUES
(9, '33', 'ana', 'ana@teste.com', '998776566'),
(8, '31', '', '', ''),
(7, '24', 'ana', 'ana@teste.com', '15998776566'),
(10, '32', 'ana', 'ana@teste.com', '998776566'),
(11, '36', 'ana', 'ana@teste.com', '15998776566'),
(12, '35', 'ana', 'ana@teste.com', '996335800'),
(13, '34', 'ana', 'ana@teste.com', '15998776566'),
(14, '37', 'ana', 'ana@teste.com', '998776566'),
(15, '38', 'ana', 'ana@teste.com', '15997001407'),
(16, '38', 'ana', 'ana@teste.com', '15997001407'),
(17, '38', 'ana', 'ana@teste.com', '15997001407'),
(18, '38', 'ana', 'ana@teste.com', '15997001407'),
(19, '38', 'ana', 'ana@teste.com', '15997001407'),
(20, '46', 'ana', 'ana@teste.com', '996335800'),
(21, '45', 'ana', 'ana@teste.com', '15998776566'),
(22, '48', 'ana', 'ana@teste.com', '15997001407');

-- --------------------------------------------------------

--
-- Estrutura da tabela `gato`
--

DROP TABLE IF EXISTS `gato`;
CREATE TABLE IF NOT EXISTS `gato` (
  `id_gato` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) COLLATE utf8_swedish_ci NOT NULL,
  `descricao` text COLLATE utf8_swedish_ci NOT NULL,
  `adotado` bit(1) NOT NULL,
  `img` varchar(255) COLLATE utf8_swedish_ci NOT NULL,
  PRIMARY KEY (`id_gato`)
) ENGINE=MyISAM AUTO_INCREMENT=56 DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Extraindo dados da tabela `gato`
--

INSERT INTO `gato` (`id_gato`, `nome`, `descricao`, `adotado`, `img`) VALUES
(42, 'Vado', '2 anos de idade, macho e negativo para Fiv e Felv', b'1', 'Vado'),
(43, 'Ulisses', 'Macho, 4 anos, Fiv+, Ã© um gato especial com mÃ¡ formaÃ§Ã£o nas patas traseiras, nÃ£o afeta a vida dele', b'1', 'Ulisses'),
(44, 'Juliette', 'FÃªmea, 3 anos, negativa para Fiv e Felv', b'1', 'Juliette'),
(45, 'Ariela', 'Uma gatinha doce, super carinhosa, resgatada em uma situaÃ§Ã£o deplorÃ¡vel.', b'1', 'Ariela'),
(54, 'Anita', 'FÃªmea, 3 anos, castrada, negativa para Fiv e Felv', b'1', 'Anita'),
(53, 'Lucis', 'Macho, 2 anos, castrado, negativo para Fiv e Felv', b'1', 'Lucis'),
(55, 'Zezinho', 'Macho, 5 anos, castrado, negativo para Fiv e Felv', b'1', 'Zezinho');

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuario`
--

DROP TABLE IF EXISTS `usuario`;
CREATE TABLE IF NOT EXISTS `usuario` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) COLLATE utf8_swedish_ci DEFAULT NULL,
  `senha` varchar(50) COLLATE utf8_swedish_ci DEFAULT NULL,
  `name` varchar(50) COLLATE utf8_swedish_ci DEFAULT NULL,
  PRIMARY KEY (`id_user`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Extraindo dados da tabela `usuario`
--

INSERT INTO `usuario` (`id_user`, `email`, `senha`, `name`) VALUES
(26, 'ana11@teste.com', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'Ana Laura de Almeida Ventura'),
(27, 'ana4@teste.com', 'f38cfe2e2facbcc742bad63f91ad55637300cb45', 'Ana');

-- --------------------------------------------------------

--
-- Estrutura da tabela `voluntario`
--

DROP TABLE IF EXISTS `voluntario`;
CREATE TABLE IF NOT EXISTS `voluntario` (
  `id_voluntario` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) COLLATE utf8_swedish_ci DEFAULT NULL,
  `email` varchar(50) COLLATE utf8_swedish_ci DEFAULT NULL,
  `idade` int(11) DEFAULT NULL,
  `dia_disponivel` varchar(50) COLLATE utf8_swedish_ci DEFAULT NULL,
  PRIMARY KEY (`id_voluntario`)
) ENGINE=InnoDB AUTO_INCREMENT=135 DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Extraindo dados da tabela `voluntario`
--

INSERT INTO `voluntario` (`id_voluntario`, `nome`, `email`, `idade`, `dia_disponivel`) VALUES
(134, 'ana', 'ana@teste', 10, 'segunda');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
