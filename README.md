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

## Proximas frentes

- Isolamento real por empresa.
- Cadastro/onboarding de barbearia.
- Validacao de disponibilidade e conflito de agenda.
- Autorizacao por empresa e papel de usuario.
- Testes de fluxo de agendamento ponta a ponta.
