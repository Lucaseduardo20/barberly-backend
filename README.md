# Bigods Barber Backend

Backend em Laravel para o SaaS de barbearias Bigods. A API atende o painel administrativo da barbearia e o fluxo público de agendamento dos clientes.

## Stack

- PHP 8.2
- Laravel 11
- MySQL 8
- Redis
- JWT Auth
- Spatie Laravel Data

## Setup local

1. Instale as dependencias PHP:

```bash
composer install
```

2. Crie o arquivo de ambiente:

```bash
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
```

3. Configure o banco no `.env`. Para desenvolvimento com Docker, os valores esperados sao:

```env
DB_CONNECTION=mysql
DB_HOST=bigods-mysql
DB_PORT=3306
DB_DATABASE=bigods
DB_USERNAME=root
DB_PASSWORD=secret
```

4. Suba os containers:

```bash
docker compose up -d
```

5. Rode migrations e seeders somente quando fizer sentido para o banco que voce esta usando:

```bash
php artisan migrate --seed
```

Se o banco ja tem dados importantes, faca backup antes de rodar migrations novas.

## Testes

Os testes devem rodar em banco separado do banco real. A configuracao padrao do `phpunit.xml` usa SQLite em memoria para evitar alterar dados locais.

```bash
php artisan test
```

No ambiente local atual, o PHP precisa ter as extensoes `mbstring` e `pdo_sqlite` habilitadas para rodar a suite dessa forma.

Se for rodar testes dentro do Docker, force o banco de testes no comando para nao usar as variaveis MySQL do `docker-compose.yml`:

```bash
docker-compose exec -T -e DB_CONNECTION=sqlite -e DB_DATABASE=:memory: bigods-app php artisan test
```

## Rotas principais

- `POST /api/login`
- `POST /api/register`
- `POST /api/customer/register`
- `GET /api/customer/barbers`
- `POST /api/appointments`
- `GET /api/appointments`
- `POST /api/appointments/cancel`
- `POST /api/appointments/done`
- `GET /api/user/preview`
- `GET|POST|DELETE /api/user/available-schedules`

## Multiempresa inicial

A API ja tem uma primeira camada de isolamento por empresa:

- Rotas autenticadas usam a `company_id` do usuario logado.
- Rotas publicas podem receber `company_id` no payload/query string ou o header `X-Company-Id`.
- Enquanto o frontend publico ainda nao sabe resolver empresa por slug, dominio ou link unico, a API usa a primeira empresa cadastrada como fallback temporario.
- Clientes e servicos pertencem a uma empresa.
- Agendamentos publicos so aceitam cliente, barbeiro e servicos da mesma empresa.
- `POST /api/register` pode criar uma nova empresa quando recebe `company_name`, ou anexar o usuario a uma empresa existente quando recebe `company_id`.

Esse fallback existe apenas para manter compatibilidade com os frontends atuais. Antes de producao, o ideal e substituir isso por uma URL publica da barbearia, por exemplo `/barbearias/{slug}` ou subdominio.

## Agenda

O agendamento publico calcula preco e duracao pelos servicos cadastrados no backend. Os campos `amount` e `duration` podem continuar vindo do frontend por compatibilidade, mas nao sao fonte de verdade.

Para criar um agendamento, o barbeiro precisa ter disponibilidade cadastrada em `available_schedules` no dia e intervalo solicitado. A API rejeita:

- cliente, barbeiro ou servico de empresas diferentes;
- usuario selecionado que nao seja barbeiro;
- horario fora da disponibilidade;
- conflito com outro agendamento ativo;
- servico inexistente ou de outra empresa.

## Proximas frentes

- Cadastro/onboarding de barbearia.
- Substituir fallback de tenant por slug/subdominio publico.
- Refatorar controllers para services/repositories.
- Testes de fluxo de agendamento ponta a ponta.
