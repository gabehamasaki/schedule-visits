# Checklist do projeto

## Backend (API REST)

- [x] Arquitetura em camadas (Domain / Application / Infrastructure)
- [x] Entidades `Vehicle` e `Appointment`
- [x] Interfaces de repositório (`VehicleRepositoryInterface`, `AppointmentRepositoryInterface`)
- [x] Repositórios PDO (`PdoVehicleRepository`, `PdoAppointmentRepository`)
- [x] Use Cases: `GetAllVehiclesUseCase`, `GetAvailableHoursUseCase`, `ScheduleVisitUseCase`
- [x] DTOs de entrada/saída (`ScheduleVisitDTO`, `GetAvailableHoursDTO`, `VehicleResponseDTO`, `AppointmentResponseDTO`, `AvailableHoursResponseDTO`)
- [x] `ApiResponseDTO` com nome de arquivo corrigido (era `JsonSerializable.php`, PSR-4 quebraria ao ser usado)
- [x] Injeção de dependência via PHP-DI (`config/dependencies.php`)
- [x] `PdoConnection` (singleton)
- [ ] Criar Controllers em `src/Infrastructure/Http/Controllers/` (hoje só tem `.gitkeep`):
  - [ ] `VehicleController` → listar veículos (`GetAllVehiclesUseCase`)
  - [ ] `AvailabilityController` → horários disponíveis por veículo/data (`GetAvailableHoursUseCase`)
  - [ ] `AppointmentController` → criar agendamento (`ScheduleVisitUseCase`)
- [ ] Registrar as rotas em `routes/web.php` (grupo `/api/v1` está vazio)
- [ ] Mapear request body → DTOs dentro dos controllers
- [ ] Adicionar validação de input (email, telefone, campos obrigatórios) — hoje os DTOs não validam nada
- [ ] Padronizar respostas usando `ApiResponseDTO`
- [ ] Tratar erros de negócio (ex: `Exception` do `ScheduleVisitUseCase`) com status HTTP adequados (400/404/409) em vez de cair só no catch genérico 500 do `index.php`

## Frontend

- [ ] Setup do projeto (React + TypeScript + MUI + Tanstack Query)
- [ ] Tela de agendamento: seção detalhes do veículo (imagem, marca, modelo, versão, preço, local)
- [ ] Seção de agendamento: listar dias disponíveis → ao selecionar, listar horários daquele dia
- [ ] Formulário nome/email/telefone após escolha de dia+hora
- [ ] Tela de confirmação pós-agendamento
- [ ] Integração com a API (depende dos endpoints acima existirem)

## Banco de dados

- [x] Migrations (`vehicles`, `appointments`)
- [x] Seeder de veículos
- [x] Script de migração customizado (`bin/migrate.php`)

## Qualidade

- [x] Suíte de testes unitários (entidades, use cases, repositórios) — 10 testes passando
- [x] PHPStan configurado em nível 8, sem erros

## Infra

- [x] `docker-compose.yaml` com serviços `api` (PHP/Apache) e `db` (Postgres)
- [x] `Dockerfile` do backend
- [x] `.env.example`
- [ ] Adicionar serviço `web` (frontend) no `docker-compose.yaml` (há um TODO deixado no próprio arquivo)
- [ ] Confirmar que o repositório remoto está **público** no GitHub

## Git

- [x] Repositório criado no GitHub com histórico de commits granular

## Documentação

- [ ] Criar `README.md`: como rodar (docker-compose), decisões arquiteturais, estrutura de pastas, como rodar testes/phpstan

## Nice-to-have (não bloqueante)

- [ ] Revisar regra de "horários disponíveis por dia" — hoje é janela fixa 09h–18h menos agendados; confirmar se bate com o exemplo do README (dia 2 só com 10h disponível)
- [ ] CI (GitHub Actions) rodando phpunit + phpstan
