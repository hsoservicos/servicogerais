# AGENTS.md — ServiceSaaS (Serviços Flex)

## Stack
- **Frontend**: PHP 8.2 (plain PHP, no framework) — templates in `web-frontend/templates/`
- **API**: Node.js 20 + Express.js — entrypoint `api-backend/server.js`
- **DB**: MySQL 8.0 — schema in `scripts/init.sql` (auto-executed on first container start)
- **Proxy**: Nginx 1.25 Alpine (`nginx/default.conf`)
- **All services** run via Docker Compose

## Docker development workflow

```bash
make setup          # build + up (first time)
make up             # start containers
make down           # stop containers
make logs           # tail all logs
make logs-api       # tail only API logs
make php            # shell into PHP container
make api            # shell into Node container
make mysql          # MySQL CLI
make migrate        # run init.sql manually
make seed           # run seed.sql
make db-reset       # destroy and recreate MySQL volume
make npm-install    # npm install inside API container
make npm-dev        # nodemon watch mode inside API container
make health-all     # ping all services
```

Source code is mounted as volumes — edit locally, changes reflected inside containers (nodemon for API hot-reload, PHP reads fresh on each request).

## API architecture

Modular by domain under `api-backend/modules/`:
- `auth/`, `tenants/`, `clients/`, `catalog/`, `proposals/`, `payments/`, `transactions/`, `leads/`, `public/`, `admin/`, `dashboard/`, `domestic/`, `data/`, `incidents/`

Each module typically contains: `*.routes.js`, `*.controller.js`, `*.service.js`

Route prefix: `/api/v1/{module}`

Database via `mysql2/promise` pool in `config/database.js` — exposes `query(sql, params)` and `transaction(callback)` helpers. Prefer `query()` over `execute()`.

Global middleware stack is defined in `server.js`: helmet, cors, rate-limit, request-ID, structured JSON logger, error handler.

## Multi-tenant

All tables reference `tenant_id` foreign key. The `tenants` table is the root of the data model. Tenant isolation is application-level (no separate databases).

## Testing

- **Jest** + **supertest** available in `api-backend`
- Command: `npm test` (192 testes, 25 suites, 100% passando)
- No PHP test infrastructure exists

## Linting

- ESLint configured in `api-backend` (no config file found — uses ESLint defaults for `.js`)
- Run: `make npm-install` then `npm run lint`

## Key files

| Path | Purpose |
|------|---------|
| `docker-compose.yml` | All 5 services (nginx, php, api, pma, mysql) |
| `Makefile` | All common commands |
| `.env.example` | Required env vars template |
| `api-backend/server.js` | API entry point, route registry |
| `api-backend/modules/` | Domain modules (14 módulos) |
| `scripts/init.sql` | Full DB schema + seed data |
| `web-frontend/public/index.php` | Frontend router (query string based) |
| `web-frontend/templates/` | PHP view templates (24 páginas + 16 parciais) |
| `nginx/default.conf` | Reverse proxy: `/` → PHP, `/api/` → Node |

## Codebase conventions

- Comments and identifiers in **Brazilian Portuguese**
- Structured JSON logging (not console.log strings) in API
- Error responses use snake_case error codes (e.g., `ERR_RATE_LIMIT`, `ERR_NOT_FOUND`)
- Health endpoint at both `/health` and `/api/v1/health`
- Mercado Pago integration in `config/mercadopago.js` — API runs in degraded mode if `MP_ACCESS_TOKEN` not set
- No migration framework — single `init.sql` executed at MySQL container init

## Project Status V2 (Jul 30, 2026) — Pós Auditoria Completa

### ✅ Completos (93 endpoints, 19 tabelas, 40 templates PHP)

| Módulo | API | Frontend | Épico |
|--------|:---:|:--------:|:-----:|
| Autenticação (register/login/me/forgot/reset) | ✅ | ✅ | E1 |
| Tenants (perfil + endereço) | ✅ | ✅ | E1/E14 |
| Clientes (CRUD + soft-delete) | ✅ | ✅ | E2 |
| Catálogo (categorias + serviços) | ✅ | ✅ | E2 |
| Propostas (CRUD + itens + PDF + WhatsApp + aprovação pública) | ✅ | ✅ | E3 |
| Dashboard (KPIs + gráfico Chart.js + follow-up) | ✅ | ✅ | E4 |
| Pagamentos MP (preference + webhook + estorno) | ✅ | 🔶 Parcial | E5 |
| Transações (histórico financeiro) | ✅ | ✅ | E4 |
| Leads (captura + wizard 3 etapas + gestão) | ✅ | ✅ | E6 |
| Público (landing page + busca + upload) | ✅ | ✅ | E6 |
| Admin (dashboard + tenants + planos + relatórios CSV + auditoria) | ✅ | ✅ | E7 |
| Workers (CRUD + CBO + 9 categorias LC 150) | ✅ | ✅ | E8 |
| Agendamentos (CRUD + trava frequência) | ✅ | ✅ | E9 |
| Cálculo CLT (INSS/FGTS/13º/férias) | ✅ | ✅ | E9 |
| Acordos CLT (domestic_agreements + transição) | ✅ | ✅ | E9 |
| LGPD Completo (export + consent + deleção + docs + crypto) | ✅ | ✅ | E13 |
| Perfil + Busca por município | ✅ | ✅ | E14 |
| Incidentes (reporte + SOS + CAT) | ✅ | ✅ | E12 |

### 🧪 Testes — 192 testes escritos e passando (E15)

**25 suites, 192 testes** — todos os módulos cobertos.
Setup: Jest + Supertest + banco de teste isolado + fixtures + helpers + E2E.
CI/CD: GitHub Actions (lint → test → build → deploy).
Cobertura configurada, Docker test override.

### ✅ Bugs Corrigidos na Auditoria

| Bug | Status | Correção |
|:----|:------:|:---------|
| `schedules.service.js` tenantFilter sem prefixo JOIN | ✅ FIXED | `replace(/\btenant_id\b/g, 'ss.tenant_id')` |
| `workers.service.js` AND ? com string SQL | ✅ FIXED | Interpolação direta no SQL |

### 🟡 Gaps Abertos

| Gap | Épico | Ação |
|:----|:------|:-----|
| Email service em modo log | E17 | Ativar SendGrid |
| JWT secret ainda com default | E17 | Gerar secret 64 caracteres |
| MP_ACCESS_TOKEN não configurado | E5 | Obter credenciais de produção |
| Cloudflare Tunnel não ativo | E17 | Configurar domínio + tunnel |
| Migration framework ausente | — | Criar `scripts/migrate.js` |

### 📝 Próximos Sprints

| Sprint | Foco | Épicos |
|:-----:|:-----|:------:|
| 1 | Refatoração controllers + workers.php | E16.3 |
| 2 | Migration framework + CI/CD final | — |
| 3 | Hardening (JWT, Email, Cloudflare) | E17 |

### 🟡 Melhorias Planejadas

| Item | Épico |
|:-----|:-----:|
| Hardening segurança (JWT, Email, Cloudflare) | E17 |
| Refatoração controllers grandes | E16 |
| Migration framework | — |

## Documentos de Planejamento V2

| Documento | Descrição |
|:----------|:----------|
| `docs/planning/PLANEJAMENTO_V2.md` | Plano estratégico completo pós-auditoria |
| `docs/planning/EPICOS_V2.md` | Épicos e histórias detalhadas |
| `docs/planning/SPRINT_PLAN_V2.md` | Sprint plan priorizado |
| `docs/planning/AUDITORIA_FINAL_V2.md` | Auditoria final consolidada |
