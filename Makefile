# ═══════════════════════════════════════════════════════════════
# Makefile — ServiceSaaS (Docker Commands)
# ═══════════════════════════════════════════════════════════════

# ── Carregar variáveis do .env ────────────────────────────
ifneq (,$(wildcard .env))
    include .env
    export
endif

# ── Configurações ─────────────────────────────────────────
DOCKER_COMPOSE = docker-compose
PHP_CONTAINER = flex_frontend_php
API_CONTAINER = flex_api_node
MYSQL_CONTAINER = flex_mysql

.PHONY: help up down build logs php api mysql restart clean test

help: ## 📖 Lista todos os comandos disponíveis
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | \
		awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

# ── Docker Compose Lifecycle ──────────────────────────────

up: ## 🚀 Sobe todos os containers (modo detached)
	$(DOCKER_COMPOSE) up -d

down: ## ⏹️ Derruba todos os containers
	$(DOCKER_COMPOSE) down

build: ## 🔨 Reconstrói as imagens Docker
	$(DOCKER_COMPOSE) build --no-cache

restart: down up ## 🔄 Reinicia todos os containers

ps: ## 📋 Lista os containers em execução
	$(DOCKER_COMPOSE) ps

# ── Logs ─────────────────────────────────────────────────

logs: ## 📜 Logs de todos os serviços
	$(DOCKER_COMPOSE) logs -f

logs-php: ## 📜 Logs apenas do PHP
	$(DOCKER_COMPOSE) logs -f php

logs-api: ## 📜 Logs apenas da API
	$(DOCKER_COMPOSE) logs -f api

logs-mysql: ## 📜 Logs apenas do MySQL
	$(DOCKER_COMPOSE) logs -f mysql

# ── Container Access ─────────────────────────────────────

php: ## 🐘 Acessa o container PHP
	$(DOCKER_COMPOSE) exec php sh

api: ## 🟢 Acessa o container Node.js
	$(DOCKER_COMPOSE) exec api sh

mysql: ## 🐬 Acessa o MySQL CLI
	$(DOCKER_COMPOSE) exec mysql mysql -u root -p

phpmyadmin: ## 📊 Abre phpMyAdmin (http://localhost:8081)
	@echo "Abrindo http://localhost:8081..."
	@start http://localhost:8081

# ── Development ──────────────────────────────────────────

dev: up logs ## 🛠️ Sobe containers e acompanha logs

setup: build up ## 🏗️ Build e sobe os containers do zero

npm-install: ## 📦 Instala dependências Node.js
	$(DOCKER_COMPOSE) exec api npm install

npm-dev: ## 🔄 Watch mode da API (nodemon)
	$(DOCKER_COMPOSE) exec api npm run dev

# ── Database ─────────────────────────────────────────────

migrate: ## 🗄️ Executa migrations no MySQL
	$(DOCKER_COMPOSE) exec -T mysql mysql -u root -p$(MYSQL_ROOT_PASSWORD) $(MYSQL_DATABASE) < scripts/init.sql

seed: ## 🌱 Insere dados de teste
	$(DOCKER_COMPOSE) exec -T mysql mysql -u root -p$(MYSQL_ROOT_PASSWORD) $(MYSQL_DATABASE) < scripts/seed.sql

db-reset: ## 🔄 Recria o banco de dados do zero
	$(DOCKER_COMPOSE) stop mysql
	$(DOCKER_COMPOSE) rm -f mysql
	$(DOCKER_COMPOSE) up -d mysql

db-shell: ## 🐚 Abre shell interativo do MySQL
	$(DOCKER_COMPOSE) exec mysql mysql -u root -p$(MYSQL_ROOT_PASSWORD) $(MYSQL_DATABASE)

# ── Health Check ─────────────────────────────────────────

health-api: ## 💓 Verifica saúde da API
	curl -s http://localhost:8080/health | python -m json.tool

health-all: ## 💓 Verifica todos os serviços
	@echo "=== Nginx ===" && curl -s -o /dev/null -w "%{http_code}" http://localhost:8080/health
	@echo ""
	@echo "=== API ===" && curl -s http://localhost:8080/api/v1/health | python -m json.tool || echo "API not ready"
	@echo "=== MySQL ===" && docker exec $(MYSQL_CONTAINER) mysqladmin ping -u root -p$(MYSQL_ROOT_PASSWORD) --silent

# ── Cleanup ──────────────────────────────────────────────

clean: ## 🧹 Remove containers, volumes e imagens não utilizados
	$(DOCKER_COMPOSE) down -v
	docker system prune -f

prune: ## 🧹 Limpeza profunda (cuidado: remove volumes)
	$(DOCKER_COMPOSE) down -v --rmi all
	docker system prune -af --volumes
