-- =============================================================================
-- 002_reparar_encoding_legado (UP)
-- =============================================================================
-- Corrige a dupla codificação (mojibake) herdada da importação do dump de 2022:
-- texto UTF-8 que foi relido como latin1 e regravado (ex.: "ê" virou "Ãª").
--
-- Atualiza SOMENTE os 4 valores confirmados no levantamento de 2026-10-29
-- (varredura de todas as colunas de texto das 4 tabelas).
-- Cada UPDATE exige a PK E os bytes exatos do valor com defeito (HEX): se o
-- registro foi editado ou já está correto, nada acontece. Por isso é seguro
-- reexecutar e, em instalação vazia (baseline), não altera nada.
--
-- Valores em hexadecimal para não depender da codificação deste arquivo nem
-- do cliente. Comentários usam apenas identificadores técnicos (tabela, id, coluna).
--
-- Pré-requisitos: 001 aplicada; backup validado.
-- =============================================================================

SET NAMES utf8mb4;

-- Corrige encoding da descricao no gato legado id 61
UPDATE `gato`
   SET `descricao` = CONVERT(UNHEX('4D6163686F2C203320616E6F732C20636173747261646F2C20646566696369656E74652066C3AD7369636F2065204669762B') USING utf8mb4)
 WHERE `id_gato` = 61
   AND HEX(`descricao`) = '4D6163686F2C203320616E6F732C20636173747261646F2C20646566696369656E74652066C383C2AD7369636F2065204669762B';

-- Corrige encoding da descricao no gato legado id 76
UPDATE `gato`
   SET `descricao` = CONVERT(UNHEX('46C3AA6D65612C203320616E6F732C2063617374726164612065206E6567617469766120706172612046697620652046656C76') USING utf8mb4)
 WHERE `id_gato` = 76
   AND HEX(`descricao`) = '46C383C2AA6D65612C203320616E6F732C2063617374726164612065206E6567617469766120706172612046697620652046656C76';

-- Corrige encoding do campo nome no voluntario id 3
UPDATE `voluntario`
   SET `nome` = CONVERT(UNHEX('4A6FC3A36F') USING utf8mb4)
 WHERE `id_voluntario` = 3
   AND HEX(`nome`) = '4A6FC383C2A36F';

-- Corrige encoding do campo dia_disponivel no voluntario id 4
UPDATE `voluntario`
   SET `dia_disponivel` = CONVERT(UNHEX('446F6D696E676F206520746572C3A761') USING utf8mb4)
 WHERE `id_voluntario` = 4
   AND HEX(`dia_disponivel`) = '446F6D696E676F206520746572C383C2A761';

INSERT INTO `schema_migrations` (`versao`) VALUES ('002_reparar_encoding_legado');
