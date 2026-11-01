# Banco de dados: baseline e migrations

O schema do Patas & Lares é reproduzido por **baseline + migrations aplicadas em ordem**. Não há framework de
migração: são arquivos SQL executados no cliente `mysql` e registrados na tabela `schema_migrations`.

```text
sql/
├── baseline/000_schema_legado.sql   ← estrutura do banco do sistema anterior, sem dados
├── migrations/NNN_nome.up.sql       ← aplica a mudança
├── migrations/NNN_nome.down.sql     ← desfaz a mudança
└── legacy/README.md                 ← nota sobre o dump de 2022, removido da árvore
```

A baseline representa o banco do sistema anterior (tabelas MyISAM `gato`, `adocoes`, `usuario` e
`voluntario`). As migrations 001 a 008 levam esse ponto de partida até o modelo atual. O dump do projeto
original não deve ser usado para instalar nem para recriar o banco (ver [`legacy/README.md`](legacy/README.md)).

## Modelo final

Depois da migration 008, o banco tem exatamente estas tabelas, todas InnoDB com `utf8mb4_unicode_ci`:

| Tabela | Papel |
|---|---|
| `usuario` | Contas com perfil `ADOTANTE`, `FUNCIONARIO` ou `ADMIN` |
| `animal` | Animais de qualquer espécie, com `status` `DISPONIVEL`, `EM_PROCESSO`, `ADOTADO` ou `INDISPONIVEL` |
| `solicitacao_adocao` | Solicitações de adoção, com `status` `PENDENTE`, `APROVADA`, `REPROVADA` ou `CANCELADA` |
| `voluntario` | Cadastros de voluntários, sem vínculo com `usuario` |
| `schema_migrations` | Controle técnico das migrations aplicadas |

Chaves estrangeiras (todas `ON DELETE RESTRICT` / `ON UPDATE RESTRICT`):

```text
solicitacao_adocao.id_animal    → animal.id_animal
solicitacao_adocao.id_adotante  → usuario.id_usuario
solicitacao_adocao.id_avaliador → usuario.id_usuario   (opcional)
```

As tabelas legadas `gato` e `adocoes` **não existem mais**. `gato` foi renomeada para `animal` pela 003, e
`adocoes` foi removida pela 008.

## Migrations

| Ordem | Versão | O que faz |
|---|---|---|
| 1 | `001_innodb_utf8mb4` | Cria `schema_migrations`; converte `gato`, `adocoes`, `usuario` e `voluntario` para InnoDB e `utf8mb4_unicode_ci`; muda o padrão do banco para utf8mb4 |
| 2 | `002_reparar_encoding_legado` | Corrige os 4 textos com dupla codificação herdados do legado (ex.: `FÃªmea` → `Fêmea`). Só altera valores com os bytes exatos do defeito; em banco vazio não faz nada |
| 3 | `003_gato_para_animal` | Renomeia `gato` para `animal` preservando IDs; adiciona espécie, sexo, nascimento estimado, porte, castrado, vacinado, saúde, comportamento, `status`, `criado_em` e `atualizado_em`; converte `adotado` em `status` e `img` no nome real do arquivo (`imagem`) |
| 4 | `004_adocoes_fk_animal` | Na tabela legada `adocoes`, `fk_id_gato` (varchar) vira `id_animal` (int) com FK para `animal` (`ON DELETE/UPDATE RESTRICT`) |
| 5 | `005_usuario_perfis_autenticacao` | `usuario` ganha `id_usuario`, `nome`, `telefone`, `perfil` (`ADOTANTE`/`FUNCIONARIO`/`ADMIN`), `ativo`, timestamps, `senha` varchar(255) e `UNIQUE(email)`. Contas existentes viram ADMIN ativas; hashes SHA-1 legados são preservados e substituídos por `password_hash` no próximo login |
| 6 | `006_solicitacao_adocao` | Cria `solicitacao_adocao` (`id_animal`, `id_adotante`, `status` `PENDENTE`/`APROVADA`/`REPROVADA`, `id_avaliador`, `data_solicitacao`, `data_decisao`, `justificativa`) com FKs para `animal` e `usuario` (`ON DELETE/UPDATE RESTRICT`). Não migra os registros de `adocoes` |
| 7 | `007_cancelamento_solicitacao` | Acrescenta `CANCELADA` ao ENUM `solicitacao_adocao.status` (mantém `NOT NULL DEFAULT 'PENDENTE'`). Nenhuma coluna nova |
| 8 | `008_remover_adocoes_legadas` | Remove a tabela legada `adocoes` (adoções anônimas do sistema anterior, fora do fluxo atual). Os dados não são migrados para outra tabela: ficam apenas no backup pré-008. O status dos animais não é alterado. Aborta antes do `DROP` se a 007 não estiver aplicada, se `adocoes` não existir ou se algum registro apontar para animal que não esteja `ADOTADO` |

Cada `up` registra sua versão em `schema_migrations` **como último comando**, e cada `down` remove o
registro. Para saber o que já foi aplicado:

```sql
SELECT * FROM schema_migrations ORDER BY versao;
```

### Regras do fluxo de adoção no banco

- O ADOTANTE autenticado solicita um animal `DISPONIVEL`. Numa transação, o animal é travado
  (`SELECT ... FOR UPDATE`), a solicitação nasce `PENDENTE` e o animal vai para `EM_PROCESSO`. Por isso um
  animal tem no máximo **uma** solicitação `PENDENTE`. Essa garantia é da aplicação, não de uma constraint.
- `id_avaliador`, `data_decisao` e `justificativa` ficam `NULL` até a solicitação ser encerrada.
- **Decisão da equipe:** `APROVADA` leva o animal a `ADOTADO`; `REPROVADA` devolve o animal a `DISPONIVEL`.
  Ambas exigem a solicitação `PENDENTE` e o animal `EM_PROCESSO`, e registram `id_avaliador`.
- **Cancelamento (007):** só o próprio ADOTANTE ativo cancela, e só uma solicitação `PENDENTE`. Numa
  transação, a solicitação vira `CANCELADA` e o animal volta de `EM_PROCESSO` para `DISPONIVEL`. Cancelar e
  decidir travam a mesma linha de `solicitacao_adocao` (`FOR UPDATE`), então só uma das duas ações encontra
  `PENDENTE`.
- **Convenção de `data_decisao`:** é o momento em que a solicitação foi **encerrada**. Em
  `APROVADA`/`REPROVADA`, é a data da decisão da equipe (com `id_avaliador`). Em `CANCELADA`, é a data do
  cancelamento pelo adotante (`id_avaliador` e `justificativa` ficam `NULL`).
- Animal ou usuário com solicitações não pode ser excluído (FK `RESTRICT`). A aplicação também recusa
  excluir animais `ADOTADO` ou `EM_PROCESSO`. O histórico nunca é apagado em cascata.

## Como executar

Os comandos abaixo funcionam no PowerShell e no Git Bash, a partir da raiz do projeto. No XAMPP, o cliente
fica em `C:/xampp/mysql/bin/`.

**Sempre use `--abort-source-on-error`.** Sem ele, o comando `source` continua depois de um erro, e a
migration seria registrada como aplicada mesmo tendo falhado.

```bash
C:/xampp/mysql/bin/mysql.exe -uroot --default-character-set=utf8mb4 --abort-source-on-error abrigo_dos_gatos -e "source sql/migrations/001_innodb_utf8mb4.up.sql"
```

Pelo phpMyAdmin também funciona: selecione o banco → **Importar** → escolha o arquivo `.sql`. O phpMyAdmin
interrompe a importação no primeiro erro.

### Instalação nova (banco vazio)

Procedimento recomendado para desenvolvimento:

```bash
C:/xampp/mysql/bin/mysql.exe -uroot -e "CREATE DATABASE abrigo_dos_gatos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
C:/xampp/mysql/bin/mysql.exe -uroot --default-character-set=utf8mb4 --abort-source-on-error abrigo_dos_gatos -e "source sql/baseline/000_schema_legado.sql"
C:/xampp/mysql/bin/mysql.exe -uroot --default-character-set=utf8mb4 --abort-source-on-error abrigo_dos_gatos -e "source sql/migrations/001_innodb_utf8mb4.up.sql"
C:/xampp/mysql/bin/mysql.exe -uroot --default-character-set=utf8mb4 --abort-source-on-error abrigo_dos_gatos -e "source sql/migrations/002_reparar_encoding_legado.up.sql"
C:/xampp/mysql/bin/mysql.exe -uroot --default-character-set=utf8mb4 --abort-source-on-error abrigo_dos_gatos -e "source sql/migrations/003_gato_para_animal.up.sql"
C:/xampp/mysql/bin/mysql.exe -uroot --default-character-set=utf8mb4 --abort-source-on-error abrigo_dos_gatos -e "source sql/migrations/004_adocoes_fk_animal.up.sql"
C:/xampp/mysql/bin/mysql.exe -uroot --default-character-set=utf8mb4 --abort-source-on-error abrigo_dos_gatos -e "source sql/migrations/005_usuario_perfis_autenticacao.up.sql"
C:/xampp/mysql/bin/mysql.exe -uroot --default-character-set=utf8mb4 --abort-source-on-error abrigo_dos_gatos -e "source sql/migrations/006_solicitacao_adocao.up.sql"
C:/xampp/mysql/bin/mysql.exe -uroot --default-character-set=utf8mb4 --abort-source-on-error abrigo_dos_gatos -e "source sql/migrations/007_cancelamento_solicitacao.up.sql"
C:/xampp/mysql/bin/mysql.exe -uroot --default-character-set=utf8mb4 --abort-source-on-error abrigo_dos_gatos -e "source sql/migrations/008_remover_adocoes_legadas.up.sql"
```

As migrations 004 e 008 passam pela tabela `adocoes` porque a baseline reproduz o banco legado. Ao final, ela
não existe mais. Confira o resultado:

```bash
C:/xampp/mysql/bin/mysql.exe -uroot abrigo_dos_gatos -e "SHOW TABLES; SELECT versao FROM schema_migrations ORDER BY versao"
```

O esperado são as tabelas `animal`, `schema_migrations`, `solicitacao_adocao`, `usuario` e `voluntario`, e as
versões de `001_innodb_utf8mb4` a `008_remover_adocoes_legadas`.

#### Administrador local

A baseline não tem dados, então o banco nasce sem usuários e não existe credencial padrão. Para criar o
primeiro administrador **no seu ambiente local**:

1. Gere o hash da senha escolhida com `password_hash`, o mesmo algoritmo usado pela aplicação:

   ```bash
   php -r "echo password_hash('DEFINA_UMA_SENHA_LOCAL', PASSWORD_DEFAULT), PHP_EOL;"
   ```

2. Insira o administrador usando o hash gerado:

   ```sql
   INSERT INTO usuario (nome, email, senha, perfil, ativo)
   VALUES ('Administrador Local', 'SEU_EMAIL_LOCAL', 'COLE_AQUI_O_HASH_GERADO', 'ADMIN', 1);
   ```

- **Substitua os valores de exemplo `DEFINA_UMA_SENHA_LOCAL`, `SEU_EMAIL_LOCAL` e `COLE_AQUI_O_HASH_GERADO`
  antes de executar.** A senha precisa ter pelo menos 8 caracteres.
- Nunca grave senha em texto puro nem use SHA-1: a coluna `senha` guarda só o hash do `password_hash`.
- Nenhuma senha real deve ser versionada. O comando do passo 1 fica no histórico do terminal: use uma senha
  apenas local ou limpe o histórico depois.
- Os demais usuários da equipe (FUNCIONARIO/ADMIN) são criados pela tela **Usuários** do painel. Os adotantes
  se cadastram pelo site.

### Banco existente (já em uso)

1. Faça e **valide** um backup (seção abaixo).
2. Veja em `schema_migrations` o que já foi aplicado. Se a tabela não existir, nenhuma migration foi aplicada.
3. Aplique, em ordem, apenas as migrations que faltam.

## Backup

Faça um backup antes de qualquer migration ou restauração. A pasta `backups/` é ignorada pelo Git: **dumps
contêm dados pessoais e nunca devem ser versionados**.

```bash
C:/xampp/mysql/bin/mysqldump.exe -uroot --default-character-set=utf8mb4 --single-transaction --routines --triggers --result-file=backups/backup_AAAA-MM-DD.sql abrigo_dos_gatos
```

Use `--result-file` em vez de `>`. No PowerShell 5.1, o `>` grava o arquivo em UTF-16, e o dump fica
inutilizável.

### Validação do backup

Restaure o backup em um banco **temporário** (nunca no principal) e compare com o banco de origem:

```bash
C:/xampp/mysql/bin/mysql.exe -uroot -e "CREATE DATABASE abrigo_restore_teste CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
C:/xampp/mysql/bin/mysql.exe -uroot --default-character-set=utf8mb4 --abort-source-on-error abrigo_restore_teste -e "source backups/backup_AAAA-MM-DD.sql"
C:/xampp/mysql/bin/mysql.exe -uroot abrigo_restore_teste -e "SHOW TABLES; SELECT COUNT(*) FROM usuario; SELECT COUNT(*) FROM animal; SELECT COUNT(*) FROM solicitacao_adocao; SELECT COUNT(*) FROM voluntario; SELECT versao FROM schema_migrations ORDER BY versao"
C:/xampp/mysql/bin/mysql.exe -uroot -e "CHECKSUM TABLE abrigo_dos_gatos.usuario, abrigo_dos_gatos.animal, abrigo_dos_gatos.solicitacao_adocao, abrigo_dos_gatos.voluntario, abrigo_dos_gatos.schema_migrations; CHECKSUM TABLE abrigo_restore_teste.usuario, abrigo_restore_teste.animal, abrigo_restore_teste.solicitacao_adocao, abrigo_restore_teste.voluntario, abrigo_restore_teste.schema_migrations"
C:/xampp/mysql/bin/mysql.exe -uroot -e "DROP DATABASE abrigo_restore_teste"
```

O backup é válido quando as tabelas, as contagens e os `CHECKSUM TABLE` coincidem com os do banco de origem.

Os comandos acima valem para backups do modelo final. Backups mais antigos refletem o schema da época: antes da
008 incluem a tabela `adocoes`; antes da 006 não têm `solicitacao_adocao`; antes da 003 a tabela `animal` ainda
se chama `gato`.

## Restauração

Para voltar o banco principal ao estado de um backup já validado:

1. Faça um backup do estado atual, mesmo que ele vá ser descartado.
2. Recrie o banco vazio e restaure o dump nele. Assim nenhuma tabela criada depois do backup permanece:

   ```bash
   C:/xampp/mysql/bin/mysql.exe -uroot -e "DROP DATABASE abrigo_dos_gatos; CREATE DATABASE abrigo_dos_gatos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
   C:/xampp/mysql/bin/mysql.exe -uroot --default-character-set=utf8mb4 --abort-source-on-error abrigo_dos_gatos -e "source backups/backup_AAAA-MM-DD.sql"
   ```

3. Confira `SHOW TABLES` e `schema_migrations`, e aplique as migrations posteriores ao backup, se houver.

> O `DROP DATABASE` apaga todo o banco atual. Execute-o apenas depois do passo 1 e com o nome correto do banco.

## Rollback

Execute os `down` **em ordem inversa**, do mais recente para o mais antigo:

```bash
C:/xampp/mysql/bin/mysql.exe -uroot --default-character-set=utf8mb4 --abort-source-on-error abrigo_dos_gatos -e "source sql/migrations/008_remover_adocoes_legadas.down.sql"
C:/xampp/mysql/bin/mysql.exe -uroot --default-character-set=utf8mb4 --abort-source-on-error abrigo_dos_gatos -e "source sql/migrations/007_cancelamento_solicitacao.down.sql"
C:/xampp/mysql/bin/mysql.exe -uroot --default-character-set=utf8mb4 --abort-source-on-error abrigo_dos_gatos -e "source sql/migrations/006_solicitacao_adocao.down.sql"
C:/xampp/mysql/bin/mysql.exe -uroot --default-character-set=utf8mb4 --abort-source-on-error abrigo_dos_gatos -e "source sql/migrations/005_usuario_perfis_autenticacao.down.sql"
C:/xampp/mysql/bin/mysql.exe -uroot --default-character-set=utf8mb4 --abort-source-on-error abrigo_dos_gatos -e "source sql/migrations/004_adocoes_fk_animal.down.sql"
C:/xampp/mysql/bin/mysql.exe -uroot --default-character-set=utf8mb4 --abort-source-on-error abrigo_dos_gatos -e "source sql/migrations/003_gato_para_animal.down.sql"
C:/xampp/mysql/bin/mysql.exe -uroot --default-character-set=utf8mb4 --abort-source-on-error abrigo_dos_gatos -e "source sql/migrations/002_reparar_encoding_legado.down.sql"
C:/xampp/mysql/bin/mysql.exe -uroot --default-character-set=utf8mb4 --abort-source-on-error abrigo_dos_gatos -e "source sql/migrations/001_innodb_utf8mb4.down.sql"
```

Os `down` existem para rollback técnico **logo após** a migration correspondente. **Depois que o sistema
produziu dados do novo modelo (animais de outras espécies, novos status, solicitações, senhas modernizadas), o
rollback seguro é restaurar o backup feito antes da migration**, não executar o `down`.

Limitações que devem ser conhecidas antes de depender do rollback:

- **DDL não é transacional no MySQL/MariaDB.** Cada `ALTER`/`CREATE`/`DROP` faz commit implícito: se uma
  migration falhar no meio, as instruções anteriores já foram gravadas. Confira o estado das tabelas antes de
  tentar de novo. O caminho seguro de recuperação é **restaurar o backup**.
- O `down` da 008 recria `adocoes` **vazia**, com a estrutura deixada pela 004, para que os `down` da 004, 003 e
  001 continuem executáveis. Os registros legados **não** voltam por ele, só pelo backup pré-008. Ele aborta
  antes de qualquer DDL se `adocoes` já existir ou se houver migration posterior aplicada.
- O `down` da 007 aborta **antes do `ALTER`** se existir qualquer solicitação `CANCELADA` (cancelamentos nunca
  são convertidos em outro status) ou se houver migration posterior aplicada.
- O `down` da 006 só remove `solicitacao_adocao` se ela estiver **vazia**. Se existir qualquer solicitação, ele
  aborta antes do `DROP TABLE`.
- O `down` da 005 aborta antes de qualquer alteração se algum usuário não couber no formato legado: nome,
  e-mail ou senha com mais de 50 caracteres, perfil diferente de `ADMIN`, telefone preenchido ou conta inativa.
  Um hash de `password_hash` tem mais de 50 caracteres, então, **depois que qualquer senha for modernizada, o
  `down` da 005 é recusado de propósito**. Hashes modernos nunca são convertidos de volta para SHA-1.
- O `down` da 003 só converte `animal` → `gato` se cada registro ainda for representável no legado. Ele aborta,
  **antes de qualquer alteração**, se:
  - a 004 ainda estiver aplicada;
  - algum nome passar de 50 caracteres;
  - existir espécie diferente de `GATO` ou status diferente de `DISPONIVEL`/`ADOTADO`;
  - houver campos estruturados preenchidos;
  - existir animal além dos 6 migrados ou com imagem trocada.
- As migrations 003, 004 e 008 têm pré-checks que abortam **antes** de qualquer DDL. Nada é corrigido
  automaticamente.
- O `down` da 002 só devolve um valor ao estado antigo se ele ainda estiver exatamente como a 002 deixou.
- O `down` da 001 volta as tabelas para utf8 de 3 bytes e devolve o padrão do banco para `utf8` /
  `utf8_swedish_ci`. Se algum texto tiver caractere de 4 bytes (ex.: emoji), ele aborta antes de qualquer
  alteração. Como segunda camada, ativa `STRICT_TRANS_TABLES` na própria sessão.
- Rollback de migration não desfaz dados criados pela aplicação depois dela. Para isso, só o backup.

## SQL mode

- O MariaDB/MySQL do XAMPP local pode **não** ter `STRICT_TRANS_TABLES` no `sql_mode` global. Sem ele, valores
  inválidos são truncados ou convertidos em silêncio: texto longo é cortado, ENUM inválido vira `''` e emoji
  vira `?`.
- **A aplicação ativa `STRICT_TRANS_TABLES` em cada conexão PDO** ([`lib/conn.php`](../lib/conn.php)),
  mantendo os demais modos do servidor. Se não conseguir ativá-lo, a aplicação não conecta e exibe uma
  mensagem genérica; o detalhe vai para o log.
- **As migrations não passam por `lib/conn.php`.** Uma migration cuja segurança dependa do modo estrito deve
  ativá-lo na própria sessão e restaurar o modo anterior ao final. De preferência, deve usar pré-checks que
  abortem antes de qualquer DDL.
- Não é necessário alterar o `sql_mode` global do servidor (`my.ini`/`my.cnf`) nem executar `SET GLOBAL`.

## Criando uma nova migration

- Use o próximo número da sequência, com `up` e `down` (ex.: `009_descricao.up.sql` /
  `009_descricao.down.sql`).
- Comece com `SET NAMES utf8mb4;`.
- No `up`, o `INSERT INTO schema_migrations` é o **último** comando; no `down`, remova o registro.
- Teste `up → down → up` em um banco temporário restaurado de um backup antes de aplicar no banco principal.
