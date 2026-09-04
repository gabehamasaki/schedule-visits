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
- [ ] Trocar as imagens do seed (as atuais são só placeholder e não carregam)

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

- [ ] Evitar agendamento duplicado no mesmo horário (constraint no banco)
- [x] Exceptions próprias em vez de Exception genérica — `DomainException` e subclasses em todo o `src/`
- [x] Validar email/telefone de forma mais robusta — `filter_var(FILTER_VALIDATE_EMAIL)` + regex de dígitos no `ScheduleVisitDTO`

## Por último

- [ ] CI rodando testes e phpstan
