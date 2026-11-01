# Patas & Lares

Sistema de gerenciamento de adoção de animais, desenvolvido como projeto acadêmico do Trabalho de Graduação.

A aplicação centraliza o cadastro dos animais e acompanha o processo de adoção, desde a solicitação do
adotante até a decisão da equipe. A instituição "Patas & Lares" é fictícia e serve apenas para demonstrar
o funcionamento do sistema.

## Demonstração online

**<https://pataselares.alwaysdata.net>**

É uma instância acadêmica e demonstrativa, com animais e solicitações de exemplo preparados para a
apresentação do TG. Não é um serviço de adoção real. Este README não traz credenciais de acesso.

## Status

O sistema está **finalizado para a apresentação acadêmica** e foi validado por um *smoke test* final
(fluxos públicos, conta do adotante, Área da equipe, permissões por perfil e responsividade).

O projeto evoluiu a partir de um sistema anterior, voltado só para gatos. Nesta versão foram refeitos o
modelo de dados, a autenticação por perfis, o fluxo de adoção e a interface.

---

## Tecnologias

| Camada | Tecnologia |
|---|---|
| Back-end | PHP 8.2, com PDO para acesso ao banco |
| Banco de dados | MariaDB / MySQL (InnoDB, `utf8mb4`) |
| Front-end | HTML, CSS e JavaScript (sem framework) |
| Dependências | Composer: [PHPMailer](https://github.com/PHPMailer/PHPMailer) 6.6 e [CoffeeCode Uploader](https://github.com/robsonvleite/uploader) 1.0.11 |
| Versionamento | Git / GitHub |
| Ambiente local | XAMPP (PHP 8.2 e MariaDB 10.4) |
| Ambiente publicado | alwaysdata (PHP 8.2 e MariaDB) |

- **PHPMailer** envia as mensagens do formulário de contato.
- **CoffeeCode Uploader** faz o upload das fotos dos animais. A aplicação carrega os arquivos do pacote a
  partir de [`src/`](src/).

---

## Funcionalidades

### Área pública

- **Home**: apresentação do projeto, indicadores com a quantidade de animais por status e o passo a passo da
  adoção.
- **Catálogo de animais**: lista os animais disponíveis para adoção (gatos, cachorros e outras espécies).
- **Detalhes do animal**: espécie, sexo, idade calculada a partir da data de nascimento estimada, porte,
  castração, vacinação, saúde, comportamento e descrição.
- **Cadastro de adotante**: criação de conta com nome, e-mail, telefone e senha.
- **Login de adotante**: acesso à conta pelo site.
- **Cadastro público de voluntário**: formulário com nome, e-mail, idade e dias disponíveis.
- **Área da equipe**: link no cabeçalho do site e na tela de login que leva ao login administrativo, sem
  precisar conhecer uma URL manual.

### Conta do adotante

- **Minha conta**: dados do adotante e atalhos para as solicitações.
- **Editar conta**: alteração de nome, e-mail, telefone e senha. A troca de senha exige a senha atual.
- **Solicitar adoção**: disponível na página de detalhes de um animal disponível.
- **Minhas solicitações**: status, datas e justificativa da decisão de cada solicitação.
- **Cancelar solicitação**: enquanto a solicitação estiver pendente.

### Área da equipe

- **Painel**: indicadores de solicitações pendentes, animais disponíveis, voluntários e, para ADMIN,
  usuários ativos.
- **Solicitações de adoção**: lista com as pendentes primeiro, análise e decisão (aprovar ou reprovar).
- **Animais**: cadastro, edição, foto (JPG, PNG ou GIF) e exclusão. Animais adotados ou em processo de
  adoção não podem ser excluídos.
- **Voluntários**: consulta, cadastro, edição e exclusão.
- **Usuários** (somente ADMIN): lista de todas as contas, cadastro de contas da equipe, edição de dados,
  perfil e situação (ativa/inativa) e exclusão. O sistema sempre mantém pelo menos um administrador ativo.

---

## Perfis de acesso

Existem dois contextos de autenticação, com telas separadas:

| Contexto | Tela | Perfis aceitos | Destino após o login |
|---|---|---|---|
| Login público | `login.php` | ADOTANTE | Minha conta |
| Área da equipe | `admin/admin.php` | ADMIN, FUNCIONARIO | Painel administrativo |

Uma credencial válida que pertence ao outro contexto é recusada com a mesma mensagem genérica de erro.

| Permissão | Visitante | ADOTANTE | FUNCIONARIO | ADMIN |
|---|:-:|:-:|:-:|:-:|
| Navegar no site, ver catálogo e detalhes | ✓ | ✓ | ✓ | ✓ |
| Criar conta de adotante e cadastrar-se como voluntário | ✓ | – | – | – |
| Acessar a própria conta e editar os próprios dados | – | ✓ | – | – |
| Solicitar adoção, ver e cancelar as próprias solicitações | – | ✓ | – | – |
| Acessar o painel administrativo | – | – | ✓ | ✓ |
| Gerenciar solicitações, animais e voluntários | – | – | ✓ | ✓ |
| Gerenciar usuários | – | – | – | ✓ |

A autorização sempre consulta o estado **atual** do usuário no banco. Uma conta desativada ou com o perfil
alterado perde o acesso na próxima requisição.

---

## Fluxo de adoção

```text
Animal DISPONIVEL
   └─ adotante solicita ──► solicitação PENDENTE  +  animal EM_PROCESSO
                              ├─ equipe aprova ────► solicitação APROVADA   +  animal ADOTADO
                              ├─ equipe reprova ───► solicitação REPROVADA  +  animal DISPONIVEL
                              └─ adotante cancela ─► solicitação CANCELADA  +  animal DISPONIVEL
```

- A solicitação só pode ser criada para um animal `DISPONIVEL`. Ao ser criada, o animal sai do catálogo.
- A reprovação exige justificativa; na aprovação ela é opcional. Cada decisão registra quem decidiu e quando.
- A aprovação só é aceita se a conta do adotante continuar ativa com perfil ADOTANTE. Ninguém decide uma
  solicitação feita por si mesmo.
- O adotante só cancela uma solicitação própria que ainda esteja `PENDENTE`.
- Solicitações encerradas (aprovadas, reprovadas ou canceladas) são histórico: não são refeitas nem excluídas.
- Criar, decidir e cancelar são feitos em **transação**, com bloqueio das linhas envolvidas
  (`SELECT ... FOR UPDATE`). Assim, duas ações simultâneas sobre o mesmo animal ou a mesma solicitação não
  deixam o banco inconsistente: só a primeira encontra o estado esperado.
- Na tela de animais, a equipe só alterna entre `DISPONIVEL` e `INDISPONIVEL`. Os status `EM_PROCESSO` e
  `ADOTADO` são definidos exclusivamente pelo fluxo de adoção.

---

## Modelo de dados

| Tabela | Conteúdo |
|---|---|
| `usuario` | Contas do sistema: nome, e-mail (único), hash da senha, telefone, `perfil` (`ADOTANTE`, `FUNCIONARIO`, `ADMIN`), `ativo` e datas de criação/atualização |
| `animal` | Animais de qualquer espécie |
| `solicitacao_adocao` | Solicitações de adoção e suas decisões |
| `voluntario` | Cadastros de voluntários: nome, e-mail, idade e dias disponíveis |
| `schema_migrations` | Tabela técnica que registra as migrations aplicadas |

### `animal`

| Coluna | Descrição |
|---|---|
| `id_animal` | Identificador |
| `nome` | Nome do animal |
| `especie` | `GATO`, `CACHORRO` ou `OUTRO` |
| `sexo` | `MACHO`, `FEMEA` ou `NAO_INFORMADO` |
| `data_nascimento_estimada` | Base para o cálculo da idade exibida |
| `porte` | `PEQUENO`, `MEDIO` ou `GRANDE` (opcional) |
| `castrado`, `vacinado` | Sim/Não; vazio significa "não informado" |
| `condicao_saude`, `comportamento`, `descricao` | Textos livres |
| `imagem` | Nome do arquivo da foto em `image/` |
| `status` | `DISPONIVEL`, `EM_PROCESSO`, `ADOTADO` ou `INDISPONIVEL` |
| `criado_em`, `atualizado_em` | Datas de criação e atualização |

### `solicitacao_adocao`

| Coluna | Descrição |
|---|---|
| `id_solicitacao` | Identificador |
| `id_animal` | Animal solicitado |
| `id_adotante` | Usuário ADOTANTE que solicitou |
| `status` | `PENDENTE`, `APROVADA`, `REPROVADA` ou `CANCELADA` |
| `id_avaliador` | Membro da equipe que decidiu (vazio enquanto pendente ou quando cancelada) |
| `data_solicitacao` | Momento da solicitação |
| `data_decisao` | Momento em que a solicitação foi encerrada (decisão ou cancelamento) |
| `justificativa` | Observação da decisão |

### Relacionamentos

```text
usuario 1 ── N solicitacao_adocao   (como adotante: id_adotante)
usuario 1 ── N solicitacao_adocao   (como avaliador: id_avaliador, opcional)
animal  1 ── N solicitacao_adocao   (id_animal)
voluntario                          (independente, sem vínculo com usuario)
```

As três chaves estrangeiras usam `ON DELETE RESTRICT` / `ON UPDATE RESTRICT`. Animais e usuários com
solicitações não podem ser excluídos, e o histórico nunca é apagado em cascata.

> **Nomes legados de arquivos.** Alguns arquivos PHP mantêm nomes do sistema anterior (por exemplo
> `gato.php`, `adocoes.php`, `admin/tabelas/tabela_gatos.php`). São apenas nomes de arquivo: o modelo atual
> usa a tabela `animal`, que atende qualquer espécie. As tabelas legadas `gato` e `adocoes` não existem mais
> (ver [`sql/README.md`](sql/README.md)).

---

## Segurança

Medidas implementadas:

- **Banco**: acesso por PDO com *prepared statements* e modo `STRICT_TRANS_TABLES` em cada conexão.
  Chaves estrangeiras, transações e regras de status protegem a consistência dos dados.
- **Senhas**: armazenadas com `password_hash`. Hashes SHA-1 herdados do sistema anterior são aceitos apenas
  para verificação e substituídos por `password_hash` no primeiro login válido.
- **Autenticação**: sessões PHP com o ID de sessão regenerado após o login e cookie de sessão `HttpOnly` e
  `SameSite=Lax`. As mensagens de erro de login são genéricas.
- **Autorização**: controle de acesso por perfil em todas as páginas e ações protegidas.
- **Entrada**: validação dos dados recebidos, recusa de campos enviados como array/estrutura malformada e
  validação do conteúdo real das imagens enviadas.
- **Saída**: *escaping* de todo conteúdo dinâmico exibido em HTML.
- **Configuração**: credenciais apenas em `config/config.php`, arquivo local fora do Git. Erros de banco são
  registrados no log do servidor e não aparecem na tela.

---

## Estrutura do projeto

```text
admin/       Área da equipe: login administrativo, painel, solicitações, animais, voluntários e usuários
config/      Configuração local (config.php, fora do Git) e o modelo versionado config.example.php
css/         Folhas de estilo, uma por área/página
function/    Endpoints que processam formulários (cadastros, login, solicitações, edição e exclusão)
image/       Fotos dos animais cadastrados
img/         Imagens fixas do site
js/          Scripts de interface (menu, formulários, painel)
lib/         Regras e funções compartilhadas: conexão, autenticação, animal, solicitação, usuário, voluntário
parciais/    Trechos de página reutilizados (menu de conta)
sql/         Baseline do banco e migrations (ver sql/README.md)
src/         Classes de upload de imagem (CoffeeCode Uploader)
vendor/      Dependências instaladas pelo Composer (fora do Git)
backups/     Dumps locais do banco (fora do Git)
```

As páginas públicas ficam na raiz (`index.php`, `adocoes.php`, `gato.php`, `cadastro.php`, `login.php`,
`minha_conta.php`, `editar_conta.php`, `minhas_solicitacoes.php` e `seja_voluntario.php`).

---

## Execução local

### Requisitos

- **XAMPP** com PHP 8.2 e MariaDB/MySQL (testado com PHP 8.2.12 e MariaDB 10.4.32).
- **Composer**.
- **Git**.

O Apache do XAMPP é opcional: a aplicação pode rodar com o servidor embutido do PHP. Ele só é necessário se
você quiser usar o phpMyAdmin.

### 1. Clonar e instalar as dependências

```bash
git clone https://github.com/seri-02/sistema-adocao-animais.git
cd sistema-adocao-animais
composer install
```

A pasta `vendor/` não é versionada. Execute `composer install` novamente sempre que o `composer.lock` mudar.

### 2. Configurar

Copie o modelo de configuração:

```bash
cp config/config.example.php config/config.php
```

(no PowerShell: `Copy-Item config/config.example.php config/config.php`)

Edite `config/config.php`:

- **Banco** (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`): no XAMPP padrão, `localhost`, `abrigo_dos_gatos`,
  `root` e senha vazia.
- **SMTP** (`SMTP_*`, `MAIL_FROM`, `MAIL_FROM_NAME`, `MAIL_TO`): opcional, usado apenas pelo formulário de
  contato. Sem SMTP o sistema funciona normalmente; só o envio de contato informa que a mensagem não pôde ser
  entregue.

> `config/config.php` é **local e ignorado pelo Git**. O `config.example.php` é versionado e deve conter
> somente valores fictícios.

### 3. Criar o banco de dados

Com o MySQL iniciado no XAMPP, crie o banco aplicando a **baseline e as migrations 001 a 008, em ordem**.
Esse é o procedimento recomendado para desenvolvimento. O passo a passo completo, com os comandos, está em
[`sql/README.md`](sql/README.md#instalação-nova-banco-vazio).

- A instalação nova cria o banco vazio, **sem usuários e sem credencial padrão**. Crie o primeiro
  administrador seguindo [Administrador local](sql/README.md#administrador-local). Os demais usuários da
  equipe são criados pelo painel, e os adotantes se cadastram pelo site.
- Restaurar um dump só se aplica a quem já possui um backup do próprio ambiente. Dumps contêm dados pessoais
  e não são distribuídos com o repositório.

### 4. Executar

Na raiz do projeto:

```bash
php -S localhost:8000
```

Acesse `http://localhost:8000`. Para encerrar, pressione `Ctrl + C` no terminal do servidor.

Ao atualizar o projeto (`git pull`), verifique se há migrations novas em `sql/migrations/`. Se houver, faça
backup e aplique-as antes de usar o sistema.

---

## Deploy / Demonstração

A versão pública está hospedada na **alwaysdata**, com PHP 8.2 e MariaDB:

**<https://pataselares.alwaysdata.net>**

A aplicação foi publicada a partir da branch `main` deste repositório. Credenciais de banco, de acesso ao
servidor e chaves de deploy ficam apenas no ambiente de hospedagem e não fazem parte do repositório.

---

## Limitações e melhorias futuras

- Proteção CSRF completa nos formulários.
- Limitação de tentativas de login (*rate limiting*).
- Ampliação dos testes automatizados.
- Automação adicional do processo de deploy.

---

## Créditos

Projeto desenvolvido por **Jonathas** e **Gloria** como Trabalho de Graduação.
