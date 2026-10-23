# Doce Gestão

Projeto didático de uma confeitaria em Laravel 13, com Blade, Docker Compose e MySQL.

## Executando com Docker

Requisitos: Docker Desktop/OrbStack em execução.

```bash
./docker/install.sh
./docker/start.sh
```

Acesse http://localhost:8000. Para acompanhar os logs, use `docker compose logs -f app`.

O script de instalação cria a chave da aplicação, sobe as migrations e insere dados de exemplo. O banco MySQL fica persistido no volume `mysql_data`.

### Windows (PowerShell)

Com o Docker Desktop iniciado, execute na pasta do projeto:

```powershell
.\docker\install.ps1
npm.cmd ci
npx.cmd cypress install
```

Para iniciar novamente:

```powershell
.\docker\start.ps1
```

O comando de instalação do binário do Cypress é explícito porque o projeto define `ignore-scripts=true` no `.npmrc`.

O instalador preserva uma chave existente e os dados cadastrados. Os dados de exemplo são inseridos sem duplicação. As dependências PHP ficam no volume `vendor_data`, para que a montagem da pasta do Windows não esconda o `vendor` da imagem. PHP e Composer locais são opcionais ao usar Docker.

| Serviço | Endereço | Banco |
| --- | --- | --- |
| Aplicação para uso normal | http://localhost:8000 | `confeitaria` |
| Aplicação para Cypress | http://localhost:8001 | `confeitaria_e2e` |
| MySQL no computador | `127.0.0.1:3307` | usuário/senha de desenvolvimento: `confeitaria` |

Para parar os containers preservando os dados: `docker compose --profile testing down`.

## Módulos para explorar

- **Produtos e estoque:** categoria em select, preço, estoque mínimo e alerta visual de estoque baixo.
- **Clientes:** dados de contato e endereço.
- **Pedidos/vendas:** selects de cliente e produto, status, validação de estoque e baixa automática na criação.
- **Fretes/entregas:** pedido, endereço, taxa, data prevista, rastreio e status.

Cada módulo possui listagem paginada, formulário de criação, edição e exclusão. As regras ficam nos controllers e as tabelas são definidas por migrations.

## Desenvolvimento local sem Docker

Com PHP e Composer instalados:

```bash
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

Para executar os testes:

```bash
php artisan test
```

## Exercícios de Cypress e PHPUnit

### Executar

```powershell
# PHPUnit: usa SQLite em memória, inclusive quando executado no container.
docker compose exec -T app php artisan test

# Cypress: os dois serviços da aplicação precisam estar iniciados.
docker compose --profile testing up -d
npm.cmd run cy:run

# Interface gráfica do Cypress (equivalente a npx cypress open com a configuração de testes).
npm.cmd run cy:open
```

`cypress.config.js` define `baseUrl: 'http://localhost:8000'`, conforme o exercício 1. Os scripts `cy:run` e `cy:open` substituem a URL por `http://localhost:8001` para utilizar o banco separado. Antes de cada arquivo de testes, a task `db:seed` executa `php artisan migrate:fresh --seed --force --no-interaction` via `docker compose exec -T app-e2e`. Ela confere a porta e o nome do banco antes de apagar **somente os dados de testes**. Não execute a suíte durante um cadastro manual na porta 8001.

Os servidores Docker usam `artisan serve --no-reload` para preservar as variáveis de ambiente do Compose, incluindo o banco separado. O `phpunit.xml` define tanto as variáveis `env` quanto `server`, para que o ambiente `testing` e o SQLite em memória tenham prioridade sobre as variáveis exportadas pelo container.

Os testes usam seletores `data-cy`, fixtures e comandos customizados. Para validar mensagens do Laravel em formulários vazios ou negativos, os cenários removem apenas durante o teste a barreira de validação HTML (`novalidate`). Para simular texto em campos numéricos, alteram temporariamente o tipo do input no navegador. As validações originais das telas continuam ativas no uso normal.

### Onde está cada exercício

| Exercício | Implementação |
| --- | --- |
| 1 | Dependência Cypress, `cypress.config.js`, scripts `cy:open` e `cy:run` |
| 2–3 | `cypress/e2e/home.cy.js`: nome do sistema e quatro menus |
| 4–6 | `cypress/e2e/produtos.cy.js`: tabela, seed, cabeçalhos, formulário; `data-cy`, `describe/it` e `beforeEach` nas suítes |
| 7–11 | `produtos.cy.js`: cadastro, campos vazios, preço/estoque negativos e texto, edição e exclusão |
| 12 | `cypress/e2e/clientes.cy.js`: criar, editar e excluir com contato e endereço |
| 13 | `produtos.cy.js`: estoque abaixo/acima do mínimo e alerta visual |
| 14 | `cypress/fixtures/produto.json` e `cliente.json`, carregados com `cy.fixture()` |
| 15 | `cypress/support/commands.js`: `cy.criarProduto(dados)` e `cy.criarCliente(dados)` |
| 16 | `cypress/e2e/paginacao.cy.js`: 11 cadastros pelo comando, próxima e anterior |
| 17–18 | `cypress/e2e/pedidos-fretes.cy.js`: pedido, status, leitura do estoque antes/depois e rejeição sem estoque |
| 19 | `pedidos-fretes.cy.js`: frete com taxa, endereço, data, rastreio e status até entregue |
| 20 | `cypress.config.js` e `cypress/support/e2e.js`: reset do banco; `pedidos-fretes.cy.js`: `cy.intercept` do POST (302) e fluxo completo |
| 21 | `tests/Unit/FirstTest.php`: `assertTrue(true)`; falha intencional observada e depois restaurada |
| 22–23 | `tests/Feature/RoutesTest.php`: início e quatro listagens com status 200 |
| 24 | `tests/Feature/ProductTest.php`: `assertSee` para o produto criado |
| 25 | `RoutesTest.php`: rota inexistente com `assertNotFound()` |
| 26 | `ProductTest.php`: `RefreshDatabase`, factory e `assertDatabaseHas` |
| 27 | `tests/Feature/CustomerTest.php`: POST, redirecionamento e confirmação no banco |
| 28 | `ProductTest.php`: nome ausente e preço inválido com `assertSessionHasErrors`, entre outros casos |
| 29 | `ProductTest.php` e `CustomerTest.php`: atualização de preço e exclusão de cliente |
| 30 | `tests/Feature/OrderTest.php`: estoque 10 → 7 e pedido insuficiente sem persistência |

`tests/Feature/DeliveryTest.php` também valida a evolução do frete pelo backend. O exercício 21 foi executado com `assertFalse(true)`: o PHPUnit retornou `Failed asserting that true is false.`. O teste foi restaurado para `assertTrue(true)` e passou. As saídas locais estão em `test-results/exercicio-21-falha.txt` e `test-results/exercicio-21-corrigido.txt` (pasta ignorada pelo Git).

### Resultado da validação em 22/10/2026

- PHPUnit no Windows (PHP 8.5): **27 testes passaram, 107 assertions**.
- PHPUnit no Docker (PHP 8.4): **27 testes passaram, 107 assertions**. Saída em `test-results/phpunit.txt` e relatório JUnit em `test-results/phpunit.xml`.
- Cypress 16.2.0 com Electron 146: **21 testes passaram em 5 arquivos**, sem falhas ou testes pulados, em 2min04s. Saída completa em `test-results/cypress.txt`.
- `npm run build`, verificação de sintaxe dos scripts e Laravel Pint: concluídos com sucesso.
- Após as suítes, o banco principal manteve **3 produtos, 2 clientes, 0 pedidos e 0 entregas**. Os cadastros e resets do Cypress ficaram em `confeitaria_e2e`.

## Diagnóstico do Docker nesta máquina

O Docker Desktop 4.76.0 encerrava o backend com:

```text
starting services: initializing Inference manager:
... Docker/run/dockerInference: The file cannot be accessed by the system.
```

`docker version` mostrava apenas o cliente, sem acesso ao pipe `dockerDesktopLinuxEngine`. A consulta ao arquivo pelo Windows retornou erro 1920. O WSL 2 estava instalado, mas a distribuição interna do Docker ainda não havia sido inicializada.

A recuperação consistiu em parar o Desktop com `docker desktop stop --force`, preservar a pasta `%LOCALAPPDATA%\Docker\run` como `run.backup-20261022-2115`, criar uma pasta `run` vazia e iniciar o Desktop novamente. Depois disso, o motor respondeu com versão 29.5.2 e foi possível construir as imagens do projeto. Não houve reset de fábrica ou exclusão de volumes. O problema de sockets persistentes também está descrito no [relato no repositório do Docker](https://github.com/docker/desktop-feedback/issues/554).

Se o erro voltar, confira primeiro `%LOCALAPPDATA%\Docker\log\host\com.docker.backend.exe.log`. Não apague dados do WSL para corrigir esse erro de socket. A pasta de backup foi preservada fora do projeto.
