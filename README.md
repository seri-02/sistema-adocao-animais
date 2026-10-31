# Como executar o projeto localmente

## Requisitos

- **XAMPP** com PHP 8.x e MySQL/MariaDB (testado com PHP 8.2.12 e MariaDB 10.4.32).
- **Composer**, para instalar as dependências PHP (`vendor/`).
- **MySQL é obrigatório.** O Apache é **opcional** para a aplicação quando ela roda com `php -S`;
  ele só é necessário se você quiser usar o phpMyAdmin.

A pasta raiz do projeto é aquela onde estão arquivos como:

```text
composer.json
index.php
admin/
config/
function/
lib/
sql/
```

Abra um PowerShell, Terminal ou Git Bash nessa pasta.

---

## Primeira configuração

Faça uma vez, ao clonar o projeto (ou quando algum item mudar).

### 1. Dependências

```bash
composer install
```

Repita quando o `composer.lock` mudar ou se a pasta `vendor/` não existir.
A pasta `vendor/` não fica no GitHub e é reconstruída pelo Composer.

### 2. Configuração local

Copie o modelo:

```text
config/config.example.php  →  config/config.php
```

```bash
cp config/config.example.php config/config.php
```

(no PowerShell: `Copy-Item config/config.example.php config/config.php`)

Edite `config/config.php` com os dados do seu ambiente:

- **Banco** (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`): no XAMPP padrão é `localhost`, `abrigo_dos_gatos`, `root` e senha vazia.
- **SMTP do formulário de contato** (`SMTP_*`, `MAIL_FROM`, `MAIL_FROM_NAME`, `MAIL_TO`): para Gmail use uma
  *senha de app*, nunca a senha da conta. Sem SMTP configurado o site funciona normalmente; apenas o formulário
  de contato responde que a mensagem não pôde ser entregue.

> **Segredos nunca vão para o Git.** `config/config.php` e a pasta `backups/` estão no `.gitignore`.
> O `config.example.php` é versionado e deve conter **somente valores fictícios**.

### 3. Banco de dados

Com o MySQL iniciado no XAMPP, crie o banco pela **baseline + migrações**, conforme
[`sql/README.md`](sql/README.md):

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

- A baseline (`sql/baseline/`) contém apenas a estrutura; os dados reais ficam em backups locais, fora do Git.
- **Instalação nova não tem administrador nem credencial padrão.** Crie o primeiro administrador com valores
  definidos por você (senha gerada com `password_hash`), seguindo
  [Administrador local](sql/README.md#administrador-local). Os demais usuários são criados pelo painel.
- **Banco já existente:** faça um backup e aplique somente as migrações que ainda não estão em
  `schema_migrations` (passo a passo em [`sql/README.md`](sql/README.md)).
- O dump do projeto original foi removido da árvore e não deve ser usado para instalação
  (ver [`sql/legacy/README.md`](sql/legacy/README.md)).

### Fluxo de adoção

A adoção começa por uma **solicitação**: o adotante entra na conta, abre um animal **Disponível** e clica em
**Solicitar adoção**. A solicitação fica **Pendente**, o animal passa a **Em processo de adoção** (sai do catálogo)
e o adotante acompanha o pedido em **Minhas solicitações**.

A equipe (ADMIN ou FUNCIONARIO) analisa em **Painel → Solicitações de adoção** (pendentes primeiro):

- **Aprovar** → solicitação **Aprovada** e animal **Adotado**. A observação é opcional. Só é possível se a conta
  do adotante continuar ativa com perfil ADOTANTE.
- **Reprovar** → solicitação **Reprovada** (justificativa obrigatória) e animal volta a **Disponível**, podendo
  receber nova solicitação.

Cada decisão registra quem decidiu, quando e a justificativa. Decisão concluída é histórica: não pode ser
refeita nem excluída. O adotante vê o resultado, a data e a justificativa em **Minhas solicitações**.

A antiga adoção imediata (formulário com nome/e-mail/telefone) foi desativada: a tabela `adocoes` guarda só o
histórico legado e não recebe novos registros.

---

## Uso no dia a dia

### 1. Iniciar o banco de dados

Abra o **XAMPP Control Panel** e inicie o **MySQL**
(o Apache só é necessário se for usar o phpMyAdmin).

### 2. Atualizar o projeto

```bash
git switch main
git pull
```

Se o `git pull` trouxer novas migrações em `sql/migrations/`, faça backup e aplique-as antes de usar o sistema.

### 3. Iniciar o servidor PHP

Na raiz do projeto:

```bash
php -S localhost:8000
```

O terminal deverá permanecer aberto enquanto estiver usando o sistema.

### 4. Abrir o sistema

```text
http://localhost:8000
```

Para encerrar o servidor PHP, volte ao terminal onde ele está executando e pressione `Ctrl + C`.

---

## Resumo para o dia a dia

```text
1. Abrir XAMPP e iniciar o MySQL
2. Abrir terminal na raiz do projeto
3. git switch main
4. git pull  (aplicar migrações novas, se houver)
5. php -S localhost:8000
6. Abrir http://localhost:8000
```
