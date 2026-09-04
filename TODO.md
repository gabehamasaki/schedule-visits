# TODO

## Backend (API)

- [x] Camadas do projeto (Domain / Application / Infrastructure)
- [x] Entidades Vehicle e Appointment
- [x] Repositórios (interface + implementação PDO)
- [x] Use cases principais (listar veículos, horários disponíveis, agendar visita)
- [x] DTOs de entrada/saída
- [x] Injeção de dependência (PHP-DI)
- [x] Controllers Vehicle, Appointment e Availability
- [x] Registrar rotas em `routes/web.php` (vehicles, appointments e available-hours)
- [x] Validação de input (email, telefone, campos obrigatórios) — `ScheduleVisitDTO::validate()`
- [x] Padronizar resposta de sucesso/erro — `Response::success/created/error`
- [x] Status HTTP corretos por tipo de erro (400/404/409) — via hierarquia de `DomainException`

## Frontend

- [ ] Setup do projeto (React + TS + MUI + Tanstack Query)
- [ ] Tela com detalhes do veículo
- [ ] Seleção de dia e horário disponível
- [ ] Formulário com dados do cliente
- [ ] Tela de confirmação do agendamento
- [ ] Loading/erro na busca de horários

## Banco de dados

- [x] Migrations (vehicles, appointments)
- [x] Seed inicial
- [x] Trocar as imagens do seed (fotos reais de cada modelo via Wikimedia Commons, licença CC BY-SA/domínio público) e aumentar a quantidade de veículos (2 -> 8)

## Testes e qualidade

- [x] Testes unitários (entidades, use cases, repositórios)
- [x] PHPStan nível 8
- [ ] Teste de integração batendo na API de verdade

## Docker

- [x] docker-compose com API + Postgres
- [ ] Subir o frontend também no compose

## Documentação

- [ ] README com setup e decisões do projeto
- [ ] Lista de endpoints da API

## Melhorias

- [x] Evitar agendamento duplicado no mesmo horário — unique index em `(vehicle_id, appointment_date, appointment_time)` + `PdoAppointmentRepository` traduzindo a violação em `ConflictException`
- [x] Exceptions próprias em vez de Exception genérica — `DomainException` e subclasses em todo o `src/`
- [x] Validar email/telefone de forma mais robusta — `filter_var(FILTER_VALIDATE_EMAIL)` + regex de dígitos no `ScheduleVisitDTO`
- [x] Horários de atendimento configuráveis em vez de array hardcoded — value object `BusinessHours` montado a partir de `config/schedule.php` + variáveis de ambiente
- [x] Separar horário fora do expediente (400) de horário já reservado (409) — `ScheduleVisitUseCase` valida contra `BusinessHours` antes de consultar disponibilidade
- [ ] Bloquear agendamento em data passada
- [ ] Fim de semana e feriados fora da grade de horários
- [ ] Timezone explícito na comparação de data/hora

## Por último

- [ ] CI rodando testes e phpstan
