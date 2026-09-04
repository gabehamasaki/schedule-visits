# TODO

## Backend (API)

- [x] Camadas do projeto (Domain / Application / Infrastructure)
- [x] Entidades Vehicle e Appointment
- [x] Repositórios (interface + implementação PDO)
- [x] Use cases principais (listar veículos, horários disponíveis, agendar visita)
- [x] DTOs de entrada/saída
- [x] Injeção de dependência (PHP-DI)
- [x] Controllers Vehicle, Appointment e Availability
- [x] Registrar rotas em `routes/web.php` (vehicles, appointments e availability)
- [x] Validação de input (email, telefone, campos obrigatórios) — `ScheduleVisitDTO::validate()`
- [x] Padronizar resposta de sucesso/erro — `Response::success/created/error`
- [x] Status HTTP corretos por tipo de erro (400/404/409) — via hierarquia de `DomainException`

## Frontend

- [x] Setup do projeto (Vite + React + TS + MUI + Tanstack Query)
- [x] Cliente da API tipado (`src/api`) + hooks do Tanstack Query (`src/hooks`)
- [x] Tema MUI com a paleta da marca, logo de `public/` e rotas do cliente
- [x] Tela inicial com a listagem dos veículos
- [x] Tela com detalhes do veículo — `VehicleCard` reusado na listagem e no agendamento
- [x] Seleção de dia e horário disponível — chips paginados, reservados e vencidos desabilitados
- [x] Loading/erro na busca de horários — skeletons, `ErrorState` e botão de tentar novamente
- [x] Formulário com dados do cliente — validação local espelhando o DTO e erros por campo vindos da API
- [x] Tela de confirmação do agendamento — dados vindos da resposta do POST
- [ ] Code splitting das páginas (bundle único passou de 500 kB)

## Banco de dados

- [x] Migrations (vehicles, appointments, vehicle_availability_slots)
- [x] Seed inicial
- [x] Trocar as imagens do seed (fotos reais de cada modelo via Wikimedia Commons, licença CC BY-SA/domínio público) e aumentar a quantidade de veículos (2 -> 8)

## Testes e qualidade

- [x] Testes unitários (entidades, use cases, repositórios)
- [x] PHPStan nível 8
- [x] Lint do frontend (oxlint, via template do Vite)
- [ ] Teste de integração batendo na API de verdade
- [ ] Testes do frontend (componentes de seleção de dia/hora e fluxo de agendamento)

## Docker

- [x] docker-compose com API + Postgres
- [x] Subir o frontend também no compose — build multi-stage, nginx servindo o bundle e fazendo proxy de `/api`
- [x] Healthcheck do container web
- [x] Separar o repositório em `backend/` e `frontend/`

## Documentação

- [ ] README com setup e decisões do projeto
- [ ] Lista de endpoints da API
- [ ] Seção "fora do escopo" (feriados e exceções de calendário, horários por loja)

## Melhorias

- [x] Evitar agendamento duplicado no mesmo horário — unique index em `(vehicle_id, appointment_date, appointment_time)` + `PdoAppointmentRepository` traduzindo a violação em `ConflictException`
- [x] Exceptions próprias em vez de Exception genérica — `DomainException` e subclasses em todo o `src/`
- [x] Validar email/telefone de forma mais robusta — `filter_var(FILTER_VALIDATE_EMAIL)` + regex de dígitos no `ScheduleVisitDTO`
- [x] Horários de atendimento configuráveis em vez de array hardcoded — value object `BusinessHours` montado a partir de `config/schedule.php` + variáveis de ambiente
- [x] Separar horário fora do expediente (400) de horário já reservado (409) — `ScheduleVisitUseCase` distingue slot inexistente de slot ocupado
- [x] Agenda persistida em `vehicle_availability_slots` — grade por dia/veículo, disponibilidade via anti-join com `appointments`
- [x] Bloquear agendamento em data passada — `ClockInterface` injetado, validado no `ScheduleVisitUseCase`
- [x] Timezone explícito na comparação de data/hora — `APP_TIMEZONE` no `SystemClock` e no `date_default_timezone_set`
- [x] Seeder de veículos idempotente — unique index em `(brand, model, version)` + `ON CONFLICT DO NOTHING`
- [x] Grade do dia com sinalizador `available` — reservado volta desabilitado, vencido não volta

## Por último

- [ ] CI rodando testes e phpstan
