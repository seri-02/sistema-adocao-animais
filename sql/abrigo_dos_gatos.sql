-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 07-Dez-2022 às 12:19
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
  `fk_id_gato` varchar(50) NOT NULL,
  `nome_adotante` varchar(50) NOT NULL,
  `email_adotante` varchar(100) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  PRIMARY KEY (`id_adocao`),
  KEY `fk_id_gato` (`fk_id_gato`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `adocoes`
--

INSERT INTO `adocoes` (`id_adocao`, `fk_id_gato`, `nome_adotante`, `email_adotante`, `telefone`) VALUES
(32, '64', 'Maria', 'maria@gmail.com', '15997001407');

-- --------------------------------------------------------

--
-- Estrutura da tabela `gato`
--

DROP TABLE IF EXISTS `gato`;
CREATE TABLE IF NOT EXISTS `gato` (
  `id_gato` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  `descricao` text NOT NULL,
  `adotado` bit(1) NOT NULL,
  `img` varchar(255) NOT NULL,
  PRIMARY KEY (`id_gato`)
) ENGINE=MyISAM AUTO_INCREMENT=79 DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `gato`
--

INSERT INTO `gato` (`id_gato`, `nome`, `descricao`, `adotado`, `img`) VALUES
(64, 'Prince', 'Macho, 4 anos, castrado e negativo para Fiv e Felv', b'0', 'Prince'),
(76, 'Juliette', 'FÃªmea, 3 anos, castrada e negativa para Fiv e Felv', b'1', 'Juliette'),
(60, 'Vado', 'Macho, 2 anos, castrado e negativo para Fiv e Felv', b'1', 'Vado'),
(61, 'Ulisses', 'Macho, 3 anos, castrado, deficiente fÃ­sico e Fiv+', b'1', 'Ulisses'),
(59, 'Snow', 'Macho, 1 ano, castrado e negativo para Fiv e Felv', b'1', 'Snow'),
(58, 'Lucis', 'Macho, 3 anos, castrada, negativo para Fiv e Felv', b'1', 'Lucis');

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuario`
--

DROP TABLE IF EXISTS `usuario`;
CREATE TABLE IF NOT EXISTS `usuario` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) DEFAULT NULL,
  `senha` varchar(50) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_user`)
) ENGINE=MyISAM AUTO_INCREMENT=104 DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `usuario`
--

INSERT INTO `usuario` (`id_user`, `email`, `senha`, `name`) VALUES
(103, 'carlos@gmail.com', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'Carlos'),
(102, 'abrigo@gmail.com', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'Abrigo dos Gatos');

-- --------------------------------------------------------

--
-- Estrutura da tabela `voluntario`
--

DROP TABLE IF EXISTS `voluntario`;
CREATE TABLE IF NOT EXISTS `voluntario` (
  `id_voluntario` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `idade` int(11) DEFAULT NULL,
  `dia_disponivel` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_voluntario`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `voluntario`
--

INSERT INTO `voluntario` (`id_voluntario`, `nome`, `email`, `idade`, `dia_disponivel`) VALUES
(3, 'JoÃ£o', 'joao@gmail.com', 18, 'Segunda, Quarta e Sexta'),
(4, 'Luiza', 'luiza@gmail.com', 17, 'Domingo e terÃ§a'),
(5, 'Carlos', 'carlos@gmail.com', 20, 'Quarta, quinta e sexta');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
