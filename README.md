# Como executar o projeto localmente

## Uso normal do projeto

### 1. Iniciar o banco de dados

Abra o **XAMPP Control Panel** e inicie:

- MySQL
- Apache — necessário principalmente caso queira acessar o phpMyAdmin.

> Para executar o projeto com `php -S`, o Apache não é obrigatório. O MySQL é.

### 2. Abrir a pasta raiz do projeto

A pasta correta é aquela onde estão arquivos como:

```text
composer.json
composer.lock
index.php
.gitignore
admin/
function/
lib/
```

Abra um PowerShell, Terminal ou Git Bash nessa pasta.

### 3. Atualizar o projeto

Caso esteja trabalhando com o GitHub, confirme que está na `main`:

```bash
git switch main
```

Depois atualize:

```bash
git pull
```

### 4. Iniciar o servidor PHP

Na raiz do projeto:

```bash
php -S localhost:8000
```

O terminal deverá permanecer aberto enquanto estiver usando o sistema.

### 5. Abrir o sistema

No navegador:

```text
http://localhost:8000
```

Pronto. O projeto estará executando localmente.

---

## Quando usar `composer install`

Não é necessário executar toda vez que abrir o projeto.

Use:

```bash
composer install
```

quando:

- estiver configurando o projeto pela primeira vez;
- baixar/clonar o projeto novamente;
- houver alteração no `composer.lock`;
- a pasta `vendor/` não existir.

A pasta `vendor/` não fica no GitHub e é reconstruída pelo Composer.

---

## Banco de dados

O projeto utiliza:

```text
Banco: abrigo_dos_gatos
Host: localhost
Usuário: root
Senha: vazia
```

A configuração fica em:

```text
lib/conn.php
```

O banco só precisa ser importado uma vez.

Caso precise recriá-lo:

1. Inicie Apache e MySQL no XAMPP.
2. Acesse:

```text
http://localhost/phpmyadmin
```

3. Crie/selecione o banco:

```text
abrigo_dos_gatos
```

4. Importe:

```text
sql/abrigo_dos_gatos.sql
```

---

## Resumo para o dia a dia

```text
1. Abrir XAMPP
2. Iniciar MySQL
3. Abrir terminal na raiz do projeto
4. git switch main
5. git pull
6. php -S localhost:8000
7. Abrir http://localhost:8000
```

Para encerrar o servidor PHP, volte ao terminal onde ele está executando e pressione:

```text
Ctrl + C
```
