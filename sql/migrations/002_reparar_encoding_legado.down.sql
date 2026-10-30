-- =============================================================================
-- 002_reparar_encoding_legado (DOWN)
-- =============================================================================
-- Devolve os 4 valores exatamente aos bytes com defeito anteriores à 002.
-- Só altera um valor se ele ainda estiver como a 002 deixou (PK + HEX):
-- registros editados depois da 002 não são tocados.
-- =============================================================================

SET NAMES utf8mb4;

-- Restaura o encoding legado da descricao no gato legado id 61
UPDATE `gato`
   SET `descricao` = CONVERT(UNHEX('4D6163686F2C203320616E6F732C20636173747261646F2C20646566696369656E74652066C383C2AD7369636F2065204669762B') USING utf8mb4)
 WHERE `id_gato` = 61
   AND HEX(`descricao`) = '4D6163686F2C203320616E6F732C20636173747261646F2C20646566696369656E74652066C3AD7369636F2065204669762B';

-- Restaura o encoding legado da descricao no gato legado id 76
UPDATE `gato`
   SET `descricao` = CONVERT(UNHEX('46C383C2AA6D65612C203320616E6F732C2063617374726164612065206E6567617469766120706172612046697620652046656C76') USING utf8mb4)
 WHERE `id_gato` = 76
   AND HEX(`descricao`) = '46C3AA6D65612C203320616E6F732C2063617374726164612065206E6567617469766120706172612046697620652046656C76';

-- Restaura o encoding legado do campo nome no voluntario id 3
UPDATE `voluntario`
   SET `nome` = CONVERT(UNHEX('4A6FC383C2A36F') USING utf8mb4)
 WHERE `id_voluntario` = 3
   AND HEX(`nome`) = '4A6FC3A36F';

-- Restaura o encoding legado do campo dia_disponivel no voluntario id 4
UPDATE `voluntario`
   SET `dia_disponivel` = CONVERT(UNHEX('446F6D696E676F206520746572C383C2A761') USING utf8mb4)
 WHERE `id_voluntario` = 4
   AND HEX(`dia_disponivel`) = '446F6D696E676F206520746572C3A761';

DELETE FROM `schema_migrations` WHERE `versao` = '002_reparar_encoding_legado';
