-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 10-Out-2022 às 14:59
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
  `fk_id_gato` int(11) NOT NULL,
  `nome_adotante` varchar(50) COLLATE utf8_swedish_ci NOT NULL,
  `email_adotante` varchar(100) COLLATE utf8_swedish_ci NOT NULL,
  `telefone` varchar(20) COLLATE utf8_swedish_ci NOT NULL,
  PRIMARY KEY (`id_adocao`),
  FOREIGN KEY(fk_id_gato) REFERENCES gato(id_gato)    
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Extraindo dados da tabela `adocoes`
--

INSERT INTO `adocoes` (`id_adocao`, `nome_adotante`, `email_adotante`, `telefone`) VALUES
(1, 'ana', 'ana@teste.com', '996335800');

-- --------------------------------------------------------

--
-- Estrutura da tabela `gato`
--

DROP TABLE IF EXISTS `gato`;
CREATE TABLE IF NOT EXISTS `gato` (
  `id_gato` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) COLLATE utf8_swedish_ci NOT NULL,
  `idade` int(11) NOT NULL,
  `sexo` varchar(20) COLLATE utf8_swedish_ci NOT NULL,
  `descricao` text COLLATE utf8_swedish_ci NOT NULL,
  `adotado` bit(1) NOT NULL,
  `img` varchar(255) COLLATE utf8_swedish_ci NOT NULL,
  PRIMARY KEY (`id_gato`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Extraindo dados da tabela `gato`
--

INSERT INTO `gato` (`id_gato`, `nome`, `idade`, `sexo`, `descricao`, `adotado`, `img`) VALUES
(14, 'Fofinho', 4, 'masculino', 'teste', b'1', 'Fofinho');

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
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Extraindo dados da tabela `usuario`
--

INSERT INTO `usuario` (`id_user`, `email`, `senha`, `name`) VALUES
(25, 'ana@teste.com', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 'Ana Laura de Almeida Ventura');

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
) ENGINE=InnoDB AUTO_INCREMENT=133 DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Extraindo dados da tabela `voluntario`
--

INSERT INTO `voluntario` (`id_voluntario`, `nome`, `email`, `idade`, `dia_disponivel`) VALUES
(73, 'Ana', 'ana@gmail.com', 15, 'domingo'),
(93, 'Ana', 'ana@gmail.com', 900, 'segunda'),
(104, 'Ana', 'ana@gmail.com', 900, 'segunda'),
(105, 'ss', 'sss@gmail.com', 12, 'ssss'),
(106, 'Ana Laura de Almeida Ventura', 'ana@teste.com', 34, 'sasasas'),
(107, 'Ana Laura de Almeida Ventura', 'ana@teste.com', 34, 'sasasas'),
(108, 'Ana Laura de Almeida Ventura', 'addd@gmail.com', 34, 'ajsna'),
(109, 'ssssss', 'aaaa@gmail.com', 12, 'ssss'),
(110, 'ssssss', 'aaaa@gmail.com', 700, 'ssss'),
(111, 'ssssss', 'aaaa@gmail.com.br', 700, 'ssss'),
(112, 'Teste', 'teste@gmail.com', 3, 'Segunda'),
(113, 'Teste', 'teste@gmail.com', 23, 'Sss'),
(114, 'Teste', 'teste4@gmail.com', 3, 'dd'),
(115, 'Teste', 'ana@teste.com', 45, 'Segunda'),
(116, 'akskkss', 'ana@teste.com', 3, 'Segunda'),
(117, 'akskkss', 'ana@teste.com', 12, 'Segunda'),
(118, 'akskkss', 'ana@teste.com', 12, 'Segunda'),
(119, 'Ana Laura Linda', 'analinda@gmail.com', 17, 'Segunda, TerÃ§a, Quarta, Quinta, Sexta'),
(120, 'Teste', 'aaa@gmail.com', 12, 'aaaaa'),
(121, 'Teste', 'ana@teste.com', 1, 'Segunda'),
(122, 'akskkss', 'ana@teste.com', 1, 'Segunda'),
(123, 'akskkss', 'ana@teste.com', 1, 'aaa'),
(124, 'akskkss', 'gabriel.flaise@gmail.com', 2, '1'),
(125, 'akskkss', 'ana@teste.com', 2, '1234'),
(126, 'akskkss', 'ana@teste.com', 3, '1'),
(127, NULL, 'ana12@teste.com', NULL, NULL),
(128, NULL, 'ana12@teste.com', NULL, NULL),
(129, 'aaaaaa', 'ana@teste.com', 1, '1'),
(130, 'Teste', 'ana12@teste.com', 2, '1'),
(131, 'Teste', 'ana@teste.com', 2, 'a'),
(132, 'Teste php', 'php@gmail.com', 10, 'Segunda, TerÃ§a, Quarta, Quinta, Sexta');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
