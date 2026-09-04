# Agendamento de visitas

Agendamento de visitas a veículos de uma revenda. API em PHP sem framework web, frontend em React.

O cliente escolhe um veículo, vê os dias e horários daquele veículo, informa nome, e-mail e telefone, e recebe a confirmação. Horário já reservado aparece desabilitado. Horário que já passou não aparece.

## Stack

| Camada | O que usei |
|---|---|
| Backend | PHP 8.5, FastRoute, PHP-DI, PDO (PostgreSQL), phpdotenv |
| Frontend | Vite, React 19, TypeScript, MUI, Tanstack Query, axios, React Router |
| Banco | PostgreSQL 15 |
| Infra | Docker Compose - Apache para a API, nginx para o frontend, Postgres |
| Qualidade | PHPUnit, PHPStan nível 8, PHP-CS-Fixer, Vitest, oxlint |

## Estrutura

```
backend/          API PHP (Domain / Application / Infrastructure)
  bin/            migrate.php e generate-slots.php
  config/         dependencies.php (container) e schedule.php (grade e timezone)
  database/       migrations e seeders em SQL
  public/         front controller
  requests/       coleções .http para exercitar a API
  src/
  tests/
frontend/         Vite + React
  docker/         Dockerfile multi-stage e nginx.conf
  src/api/        cliente HTTP tipado
  src/hooks/      hooks do Tanstack Query
  src/components/
  src/pages/
docker-compose.yaml
```

## Rodando

```bash
cp backend/.env.example backend/.env
docker compose up -d --build
docker compose exec api php bin/migrate.php
```

O `migrate.php` cria o banco se não existir, aplica as migrations, roda os seeders e materializa a agenda dos próximos dias. Com `--drop` ele derruba as tabelas antes, para recomeçar do zero.

| Serviço | URL |
|---|---|
| Frontend | http://localhost:3000 |
| API | http://localhost:8080/api/v1 |
| PostgreSQL | localhost:5432 |

O frontend chama a API por caminho relativo (`/api/v1`) e o nginx faz o proxy para o container `api`. Uma origem só: sem CORS e sem a porta do backend embutida no bundle.

### Sem Docker

```bash
# backend
cd backend && composer install
php bin/migrate.php
php -S localhost:8080 -t public

# frontend, em outro terminal
cd frontend && npm install
npm run dev
```

O `vite.config.ts` faz proxy de `/api` para `localhost:8080`, o mesmo que o nginx faz no Docker - assim o código não muda entre os dois modos.

## Variáveis de ambiente

`backend/.env`, a partir do `.env.example`:

| Variável | Padrão | Para quê |
|---|---|---|
| `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | - | conexão PDO |
| `APP_TIMEZONE` | `America/Sao_Paulo` | fuso de referência para "hoje" e "agora" |
| `SCHEDULE_FIRST_SLOT` | `09:00` | primeiro horário de visita |
| `SCHEDULE_LAST_SLOT` | `18:00` | último horário de visita - é um início, não o fechamento |
| `SCHEDULE_SLOT_MINUTES` | `60` | duração de cada slot |
| `SCHEDULE_DAYS_AHEAD` | `14` | quantos dias de agenda o gerador materializa |

`frontend/.env`: `VITE_API_BASE_URL`, padrão `/api/v1`.

## API

Base `/api/v1`. Toda resposta usa o mesmo envelope.

```json
{ "status": "success", "data": { } }
{ "status": "error", "message": "Validation failed.", "errors": { "time": "..." } }
```

### `GET /vehicles`

```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "brand": "Porsche",
      "model": "911",
      "version": "3.0 CARRERA 6 CILINDROS",
      "price": 850000,
      "location": "São Paulo - SP",
      "imageUrl": "https://..."
    }
  ]
}
```

### `GET /vehicles/{id}`

Um veículo. `404` se não existir.

### `GET /vehicles/{id}/availability`

Agenda do veículo. Sem parâmetro devolve o horizonte inteiro; com `?date=YYYY-MM-DD`, só aquele dia, na mesma forma.

```json
{
  "status": "success",
  "data": {
    "vehicleId": 1,
    "days": [
      {
        "date": "2026-09-05",
        "slots": [
          { "time": "09:00", "available": false },
          { "time": "10:00", "available": true }
        ]
      }
    ]
  }
}
```

`available: false` quer dizer reservado. Horário que já passou não entra na lista, então hoje a resposta começa no próximo horário. Dia sem nenhum slot livre continua na resposta, para o cliente mostrar o dia desabilitado em vez de sumir com ele.

Erros: `400` para data malformada, `404` para veículo inexistente.

### `POST /vehicles/{id}/appointments`

```json
{
  "name": "Gabriel Hamasaki",
  "email": "gabriel@example.com",
  "phone": "11987654321",
  "date": "2026-09-10",
  "time": "14:00"
}
```

`201` com o agendamento criado. Os erros são separados de propósito:

| Situação | Status | Corpo |
|---|---|---|
| Campo inválido (nome, e-mail, telefone, formato de data/hora) | `400` | `errors` com a mensagem por campo |
| Data e hora no passado | `400` | `errors.date` |
| Horário fora da agenda daquele dia | `400` | `errors.time` |
| Veículo inexistente | `404` | `message` |
| Horário já reservado | `409` | `message` |

As coleções em `backend/requests/*.http` cobrem cada um desses casos.

## Modelo de dados

```
vehicles                        appointments                       vehicle_availability_slots
--------                        ------------                       --------------------------
id                              id                                 id
brand    ─┐                     vehicle_id  ──> vehicles.id        vehicle_id ──> vehicles.id
model     ├ UNIQUE              customer_name                      slot_date  ─┐
version  ─┘                     customer_email                     slot_time  ─┴ UNIQUE com vehicle_id
price                           customer_phone
location                        appointment_date ─┐
image_url                       appointment_time ─┴ UNIQUE com vehicle_id
```

Por que cada índice está lá:

- `ux_appointments_vehicle_date_time` - impede agendamento duplicado no banco, não só na aplicação. É o que segura duas requisições simultâneas para o mesmo slot; o repositório traduz a violação em `409`.
- `idx_availability_slots_vehicle_date` - atende o filtro por veículo e faixa de datas da consulta de disponibilidade.
- `ux_vehicles_brand_model_version` - dá ao seeder uma chave natural para o `ON CONFLICT DO NOTHING`, o que torna o `migrate` idempotente.

## Decisões

**Camadas com dependência apontando para dentro.** `Domain` não conhece ninguém, `Application` conhece o `Domain` por interfaces, `Infrastructure` implementa essas interfaces e é montada no container em `config/dependencies.php`. Trocar o PDO por outra coisa não encosta nos use cases.

**A agenda é dado, não fórmula.** Os horários oferecidos variam por dia: um dia pode ter 10h, 11h e 12h e o seguinte só 10h. Com a grade calculada na requisição, um dia só teria menos horários por estar reservado - a loja não conseguiria simplesmente abrir menos naquele dia. Por isso a oferta fica em `vehicle_availability_slots`, uma linha por veículo/data/hora.

**Persisti a oferta, não a disponibilidade.** Não existe coluna `is_available`. Seria dado derivado, com escrita dupla junto de `appointments` e corrida entre as duas. Disponível é calculado com `LEFT JOIN appointments ... WHERE a.id IS NULL`, e um horizonte inteiro de dias custa uma query - não uma por dia.

**`BusinessHours` é a política, a tabela é a materialização.** O value object monta a grade a partir do `config/schedule.php` (primeiro horário, último, duração) e o `bin/generate-slots.php` grava isso na tabela. Com `ON CONFLICT DO NOTHING` o comando é idempotente: rodar de novo estende o horizonte sem duplicar. Mudar horário de atendimento é configuração, não deploy.

**Tempo é injetado.** `ClockInterface`, com `SystemClock` em produção e `FrozenClock` nos testes. Sem isso, "não agendar no passado" viraria teste frágil, quebrando conforme o dia em que roda. O timezone é explícito no `APP_TIMEZONE` porque o container roda em UTC, e por três horas "hoje" em São Paulo é outro dia.

**Cada erro tem o seu status.** A hierarquia de `DomainException` carrega o código HTTP: campo inválido é `400`, veículo inexistente é `404`, corrida por um slot é `409`. Horário fora da agenda e horário tomado começaram como a mesma exceção; separar os dois foi o que deixou o frontend mostrar erro por campo em um caso e um aviso geral no outro.

**No frontend, a API é a autoridade.** O formulário valida o óbvio localmente, espelhando o `ScheduleVisitDTO::validate()`, mas quando o servidor recusa é a mensagem dele que aparece, no campo certo. Em `409` a disponibilidade é buscada de novo, para a grade refletir quem chegou primeiro.

**Reservado desabilita, vencido desaparece.** São informações diferentes. "Existe e alguém pegou" ajuda a decidir; "já passou" só ocupa espaço e empurra as opções reais para fora da janela do carrossel.

## Qualidade

```bash
# backend
cd backend
vendor/bin/phpunit tests
vendor/bin/phpstan analyse        # nível 8
vendor/bin/php-cs-fixer fix

# frontend
cd frontend
npm test                          # Vitest + Testing Library
npm run test:coverage
npm run build                     # inclui tsc -b
npm run lint
```

No backend os testes são unitários, com `PDO`/`PDOStatement` mockados, focados nos use cases e no que os repositórios fazem com as linhas que recebem.

No frontend eles cobrem os formatadores de data e preço, o comportamento dos seletores de dia e hora (reservado desabilita, janela pagina) e o fluxo de agendamento inteiro com a camada de API mockada - da escolha do horário até a confirmação, incluindo o `409` de quando alguém reserva primeiro.
