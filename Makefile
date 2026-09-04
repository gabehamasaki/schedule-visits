.DEFAULT_GOAL := help
.PHONY: help install up down logs migrate fresh test test-backend test-frontend lint lint-backend lint-frontend fmt stan build check

BACKEND  := backend
FRONTEND := frontend

help: ## Lista os alvos disponíveis
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-16s\033[0m %s\n", $$1, $$2}'

## --- Dependências e ambiente ---

install: ## Instala as dependências dos dois lados
	cd $(BACKEND) && composer install
	cd $(FRONTEND) && npm install

up: ## Sobe API, frontend e banco
	docker compose up -d --build

down: ## Derruba os containers
	docker compose down

logs: ## Acompanha os logs dos containers
	docker compose logs -f

migrate: ## Migra, semeia e materializa a agenda
	docker compose exec api php bin/migrate.php

fresh: ## Recria o banco do zero
	docker compose exec api php bin/migrate.php --drop

## --- Testes ---

test: test-backend test-frontend ## Roda a bateria completa de testes

test-backend: ## PHPUnit
	cd $(BACKEND) && vendor/bin/phpunit

test-frontend: ## Vitest
	cd $(FRONTEND) && npm run test

## --- Análise estática e estilo ---

lint: lint-backend lint-frontend ## Estilo e lint dos dois lados

lint-backend: ## PHP-CS-Fixer em modo verificação
	cd $(BACKEND) && vendor/bin/php-cs-fixer fix --dry-run --diff

lint-frontend: ## oxlint
	cd $(FRONTEND) && npm run lint

fmt: ## Aplica o PHP-CS-Fixer
	cd $(BACKEND) && vendor/bin/php-cs-fixer fix

stan: ## PHPStan nível 8
	cd $(BACKEND) && vendor/bin/phpstan analyse --no-progress

build: ## Compila o frontend (inclui tsc -b)
	cd $(FRONTEND) && npm run build

check: lint stan test build ## O que a CI roda
