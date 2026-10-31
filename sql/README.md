# Banco de dados: baseline e migrações

O schema é reproduzido por **baseline + migrações em ordem**. Não existe framework de migração:
são arquivos SQL executados no cliente `mysql` e registrados na tabela `schema_migrations`.

```text
sql/
├── baseline/000_schema_legado.sql   ← estrutura do banco legado (antes da F0b), sem dados
├── migrations/NNN_nome.up.sql       ← aplica a mudança
├── migrations/NNN_nome.down.sql     ← desfaz a mudança
└── legacy/README.md                 ← nota sobre o dump de 2022, removido da árvore
```

> O dump do projeto original foi removido da árvore atual (ver [`legacy/README.md`](legacy/README.md))
> e não deve ser usado para instalar nem para recriar o banco.

## Migrações

| Ordem | Versão | O que faz |
|---|---|---|
| 1 | `001_innodb_utf8mb4` | Cria `schema_migrations`; converte `gato`, `adocoes`, `usuario` e `voluntario` para InnoDB e `utf8mb4_unicode_ci`; muda o padrão do banco para utf8mb4 |
| 2 | `002_reparar_encoding_legado` | Corrige os 4 textos com dupla codificação herdados do legado (ex.: `FÃªmea` → `Fêmea`). Só altera valores com os bytes exatos do defeito; em banco vazio não faz nada |
| 3 | `003_gato_para_animal` | Renomeia `gato` para `animal` preservando IDs; adiciona espécie, sexo, nascimento estimado, porte, castrado, vacinado, saúde, comportamento, `status`, `criado_em`, `atualizado_em`; converte `adotado` em `status` e `img` no nome real do arquivo (`imagem`) |
| 4 | `004_adocoes_fk_animal` | `adocoes.fk_id_gato` (varchar) vira `id_animal` (int) com FK para `animal` (`ON DELETE/UPDATE RESTRICT`) |
| 5 | `005_usuario_perfis_autenticacao` | `usuario` ganha `id_usuario`, `nome`, `telefone`, `perfil` (ADOTANTE/FUNCIONARIO/ADMIN), `ativo`, timestamps, `senha` varchar(255) e `UNIQUE(email)`; contas existentes viram ADMIN ativas; hashes SHA-1 preservados (modernizados no próximo login) |
| 6 | `006_solicitacao_adocao` | Cria `solicitacao_adocao` (`id_animal`, `id_adotante`, `status` PENDENTE/APROVADA/REPROVADA, `id_avaliador`, `data_solicitacao`, `data_decisao`, `justificativa`) com FKs para `animal` e `usuario` (`ON DELETE/UPDATE RESTRICT`). Não migra `adocoes` |

### Solicitação de adoção e `adocoes` legada

- O fluxo atual de adoção é a **solicitação**: o ADOTANTE autenticado solicita um animal `DISPONIVEL`; numa
  transação, o animal é travado (`SELECT ... FOR UPDATE`), a solicitação nasce `PENDENTE` e o animal vai para
  `EM_PROCESSO`. Por isso um animal tem no máximo **uma** solicitação `PENDENTE` (garantia da aplicação, não de
  constraint). `id_avaliador`, `data_decisao` e `justificativa` ficam `NULL` até a decisão da equipe.
- A tabela `adocoes` é **somente histórico legado** (adoções anônimas anteriores ao workflow, sem vínculo com
  `usuario`): não recebe novos registros e **não** foi convertida em solicitações.
- Animal ou usuário com solicitações não pode ser excluído (FK `RESTRICT`); o histórico nunca é apagado em cascata.

Cada `up` registra sua versão em `schema_migrations` **como último comando**. Cada `down` remove o registro.
Para saber o que já foi aplicado:

```sql
SELECT * FROM schema_migrations ORDER BY versao;
```

## Como executar

Os comandos abaixo funcionam no PowerShell e no Git Bash, a partir da raiz do projeto
(no XAMPP o cliente fica em `C:/xampp/mysql/bin/`).

**Sempre use `--abort-source-on-error`.** Sem ele, o comando `source` continua depois de um erro
e a migração seria registrada como aplicada mesmo tendo falhado.

```bash
C:/xampp/mysql/bin/mysql.exe -uroot --default-character-set=utf8mb4 --abort-source-on-error abrigo_dos_gatos -e "source sql/migrations/001_innodb_utf8mb4.up.sql"
```

Pelo phpMyAdmin também funciona: selecione o banco → **Importar** → escolha o arquivo `.sql`
(o phpMyAdmin interrompe a importação no primeiro erro).

### Instalação nova (banco vazio)

```bash
C:/xampp/mysql/bin/mysql.exe -uroot -e "CREATE DATABASE abrigo_dos_gatos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
C:/xampp/mysql/bin/mysql.exe -uroot --default-character-set=utf8mb4 --abort-source-on-error abrigo_dos_gatos -e "source sql/baseline/000_schema_legado.sql"
C:/xampp/mysql/bin/mysql.exe -uroot --default-character-set=utf8mb4 --abort-source-on-error abrigo_dos_gatos -e "source sql/migrations/001_innodb_utf8mb4.up.sql"
C:/xampp/mysql/bin/mysql.exe -uroot --default-character-set=utf8mb4 --abort-source-on-error abrigo_dos_gatos -e "source sql/migrations/002_reparar_encoding_legado.up.sql"
C:/xampp/mysql/bin/mysql.exe -uroot --default-character-set=utf8mb4 --abort-source-on-error abrigo_dos_gatos -e "source sql/migrations/003_gato_para_animal.up.sql"
C:/xampp/mysql/bin/mysql.exe -uroot --default-character-set=utf8mb4 --abort-source-on-error abrigo_dos_gatos -e "source sql/migrations/004_adocoes_fk_animal.up.sql"
C:/xampp/mysql/bin/mysql.exe -uroot --default-character-set=utf8mb4 --abort-source-on-error abrigo_dos_gatos -e "source sql/migrations/005_usuario_perfis_autenticacao.up.sql"
C:/xampp/mysql/bin/mysql.exe -uroot --default-character-set=utf8mb4 --abort-source-on-error abrigo_dos_gatos -e "source sql/migrations/006_solicitacao_adocao.up.sql"
```

#### Administrador local

A baseline não tem dados, então o banco nasce sem usuários e não existe credencial padrão.
Para criar o primeiro administrador **no seu ambiente local**:

1. Gere o hash da senha escolhida com `password_hash` (o mesmo algoritmo usado pela aplicação):

   ```bash
   php -r "echo password_hash('DEFINA_UMA_SENHA_LOCAL', PASSWORD_DEFAULT), PHP_EOL;"
   ```

2. Insira o administrador usando o hash gerado:

   ```sql
   INSERT INTO usuario (nome, email, senha, perfil, ativo)
   VALUES ('Administrador Local', 'SEU_EMAIL_LOCAL', 'COLE_AQUI_O_HASH_GERADO', 'ADMIN', 1);
   ```

- **Substitua `DEFINA_UMA_SENHA_LOCAL`, `SEU_EMAIL_LOCAL` e `COLE_AQUI_O_HASH_GERADO` antes de executar.**
  A senha precisa ter pelo menos 8 caracteres.
- Nunca use SHA-1 nem grave senha em texto puro: a coluna `senha` guarda só o hash do `password_hash`.
- Nenhuma senha real deve ser versionada. O comando do passo 1 fica no histórico do terminal: use uma senha
  apenas local ou limpe o histórico depois.
- Os demais usuários da equipe (FUNCIONARIO/ADMIN) são criados pela tela **Usuários** do painel.

### Banco existente (já em uso)

1. Faça e **valide** um backup (seção abaixo).
2. Veja em `schema_migrations` o que já foi aplicado (se a tabela não existir, nenhuma migração foi aplicada).
3. Aplique, em ordem, apenas as migrações que faltam.

## Backup

Antes de qualquer migração. A pasta `backups/` é ignorada pelo Git: **dumps contêm dados pessoais e nunca devem ser versionados**.

```bash
C:/xampp/mysql/bin/mysqldump.exe -uroot --default-character-set=utf8mb4 --single-transaction --routines --triggers --result-file=backups/antes_da_migracao_AAAA-MM-DD.sql abrigo_dos_gatos
```

Use `--result-file` em vez de `>`: no PowerShell 5.1 o `>` grava o arquivo em UTF-16 e o dump fica inutilizável.

**Valide o backup** restaurando-o em um banco temporário (nunca no principal) e conferindo tabelas e contagens:

```bash
C:/xampp/mysql/bin/mysql.exe -uroot -e "CREATE DATABASE abrigo_restore_teste"
C:/xampp/mysql/bin/mysql.exe -uroot --default-character-set=utf8mb4 --abort-source-on-error abrigo_restore_teste -e "source backups/antes_da_migracao_AAAA-MM-DD.sql"
C:/xampp/mysql/bin/mysql.exe -uroot abrigo_restore_teste -e "SELECT COUNT(*) FROM animal; SELECT COUNT(*) FROM adocoes; SELECT COUNT(*) FROM usuario; SELECT COUNT(*) FROM voluntario"
C:/xampp/mysql/bin/mysql.exe -uroot -e "DROP DATABASE abrigo_restore_teste"
```

(Em backups anteriores à migração 003, a tabela `animal` ainda se chama `gato`.)

## Rollback

Execute os `down` **em ordem inversa** (o mais recente primeiro):

```bash
C:/xampp/mysql/bin/mysql.exe -uroot --default-character-set=utf8mb4 --abort-source-on-error abrigo_dos_gatos -e "source sql/migrations/006_solicitacao_adocao.down.sql"
C:/xampp/mysql/bin/mysql.exe -uroot --default-character-set=utf8mb4 --abort-source-on-error abrigo_dos_gatos -e "source sql/migrations/005_usuario_perfis_autenticacao.down.sql"
C:/xampp/mysql/bin/mysql.exe -uroot --default-character-set=utf8mb4 --abort-source-on-error abrigo_dos_gatos -e "source sql/migrations/004_adocoes_fk_animal.down.sql"
C:/xampp/mysql/bin/mysql.exe -uroot --default-character-set=utf8mb4 --abort-source-on-error abrigo_dos_gatos -e "source sql/migrations/003_gato_para_animal.down.sql"
C:/xampp/mysql/bin/mysql.exe -uroot --default-character-set=utf8mb4 --abort-source-on-error abrigo_dos_gatos -e "source sql/migrations/002_reparar_encoding_legado.down.sql"
C:/xampp/mysql/bin/mysql.exe -uroot --default-character-set=utf8mb4 --abort-source-on-error abrigo_dos_gatos -e "source sql/migrations/001_innodb_utf8mb4.down.sql"
```

Limitações que devem ser conhecidas antes de depender do rollback:

- **DDL não é transacional no MySQL/MariaDB.** Cada `ALTER`/`CREATE` faz commit implícito: se uma migração
  falhar no meio, as instruções anteriores já foram gravadas. Confira o estado das tabelas antes de tentar de
  novo. O caminho seguro de recuperação é **restaurar o backup**.
- O `down` da 002 só devolve um valor ao estado antigo se ele ainda estiver exatamente como a 002 deixou;
  registros editados depois não são tocados.
- O `down` da 001 volta as tabelas para utf8 de 3 bytes, que não comporta caracteres de 4 bytes (ex.: emoji).
  Se algum texto tiver esse tipo de caractere, ele **aborta antes de qualquer alteração**; nesse caso restaure o
  backup. Como segunda camada, ativa `STRICT_TRANS_TABLES` na própria sessão (ver "SQL mode" abaixo).
- O `down` da 001 devolve o padrão do banco para `utf8` / `utf8_swedish_ci`, que era o valor do banco legado.
- O `down` da 003 é **deliberadamente conservador**: serve para rollback técnico **logo após** a migração.
  Ele só converte `animal` → `gato` se cada registro ainda for exatamente representável no legado e aborta,
  **antes de qualquer alteração**, se: a 004 ainda estiver aplicada; algum nome passar de 50 caracteres; existir
  espécie diferente de `GATO`; existir status diferente de `DISPONIVEL`/`ADOTADO`; houver sexo, nascimento,
  porte, castrado, vacinado, condição de saúde ou comportamento preenchidos; ou existir animal além dos 6
  migrados ou com imagem trocada. Nada é descartado nem inventado.
- **Depois que o sistema já produziu dados do novo domínio (animais novos, outras espécies, campos
  estruturados, novos status), o rollback seguro é restaurar o backup feito antes da migração**, não o `down`.
- As migrações 003 e 004 têm pré-checks que abortam **antes** de qualquer DDL: a 003 se existir registro de
  `gato` fora do mapeamento de imagens conferido; a 004 se existir adoção órfã. Nada é corrigido automaticamente.
- O `down` da 005 também é **conservador** e aborta antes de qualquer alteração se algum usuário não couber no
  formato legado: nome, e-mail ou senha com mais de 50 caracteres, perfil diferente de `ADMIN`, telefone
  preenchido ou conta inativa. Um hash de `password_hash` tem mais de 50 caracteres: **depois que qualquer senha
  for modernizada (o que acontece no primeiro login válido), o `down` da 005 é recusado de propósito** — hashes
  modernos nunca são convertidos de volta para SHA-1. Nesse caso, o rollback seguro é restaurar o backup pré-F2a.
- O `down` da 006 é **conservador**: só remove `solicitacao_adocao` se ela estiver **vazia** (rollback técnico logo
  após a migração). Se existir qualquer solicitação, aborta **antes do `DROP TABLE`** — histórico de solicitações
  nunca é descartado em silêncio. Depois que a aplicação registrar solicitações, o rollback seguro é restaurar o
  backup pré-F3a.
- Rollback de migração não desfaz dados criados pela aplicação depois dela: para isso, só o backup.

## SQL mode

- O MariaDB/MySQL do XAMPP local pode **não** ter `STRICT_TRANS_TABLES` no `sql_mode` global. Sem ele, valores
  inválidos são truncados ou convertidos em silêncio (texto longo cortado, ENUM inválido vira `''`, emoji vira `?`).
- **A aplicação ativa `STRICT_TRANS_TABLES` em cada conexão PDO** (`lib/conn.php`), mantendo os demais modos do
  servidor. Se não conseguir ativá-lo, a aplicação não conecta (mensagem genérica na tela, detalhe no log).
- **Migrações não passam por `lib/conn.php`.** Uma migração cuja segurança dependa de modo estrito deve ativá-lo
  na própria sessão e restaurar o modo anterior ao final (como o `down` da 001), ou, de preferência, usar
  pré-checks que abortem antes de qualquer DDL (como as migrações 001 `down`, 003 e 004).
- Não é necessário alterar o `sql_mode` global do servidor (`my.ini`/`my.cnf`) nem executar `SET GLOBAL`.

## Criando uma nova migração

- Próximo número da sequência, com `up` e `down` (`003_descricao.up.sql` / `003_descricao.down.sql`).
- Comece com `SET NAMES utf8mb4;`.
- No `up`, o `INSERT INTO schema_migrations` é o **último** comando; no `down`, remova o registro.
- Teste `up → down → up` em um banco temporário restaurado de um backup antes de aplicar no banco principal.
