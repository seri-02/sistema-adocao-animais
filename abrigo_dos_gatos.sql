SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `abrigo_dos_gatos` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci;
USE `abrigo_dos_gatos`;


DROP TABLE IF EXISTS `usuario`;
CREATE TABLE IF NOT EXISTS `usuario` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `senha` varchar(50) COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `name` varchar(50) COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  PRIMARY KEY (`id_user`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

INSERT INTO `usuario` (`id_user`, `email`, `senha`, `name`) VALUES
(1, 'analauraalmeidaventura@gmail.com', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 'Ana Laura de Almeida Ventura'),
(2, 'ana@teste.com', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 'Ana Laura de Almeida Ventura'),
(3, 'teste@gmail.com', '6643521711328a1e282daf5a5da43970eb11a089', 'Teste'),
(4, NULL, 'da39a3ee5e6b4b0d3255bfef95601890afd80709', NULL);


DROP TABLE IF EXISTS `voluntario`;
CREATE TABLE IF NOT EXISTS `voluntario` (
  `id_voluntario` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) COLLATE utf8_swedish_ci DEFAULT NULL,
  `email` varchar(50) COLLATE utf8_swedish_ci DEFAULT NULL,
  `idade` int(11) DEFAULT NULL,
  `dia_disponivel` varchar(50) COLLATE utf8_swedish_ci DEFAULT NULL,
  PRIMARY KEY (`id_voluntario`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;


INSERT INTO `voluntario` (`id_voluntario`, `nome`, `email`, `idade`, `dia_disponivel`) VALUES
(2, 'Ana Laura de Almeida Ventura ', 'ana@teste.com', 34, 'dsdsd');
COMMIT;