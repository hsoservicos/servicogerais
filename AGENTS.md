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
- `auth/`, `tenants/`, `clients/`, `catalog/`, `proposals/`, `payments/`, `transactions/`, `leads/`, `public/`, `admin/`, `dashboard/`, `domestic/`, `data/`

Each module typically contains: `*.routes.js`, `*.controller.js`, `*.service.js`

Route prefix: `/api/v1/{module}`

Database via `mysql2/promise` pool in `config/database.js` — exposes `query(sql, params)` and `transaction(callback)` helpers. Prefer `query()` over `execute()`.

Global middleware stack is defined in `server.js`: helmet, cors, rate-limit, request-ID, structured JSON logger, error handler.

## Multi-tenant

All tables reference `tenant_id` foreign key. The `tenants` table is the root of the data model. Tenant isolation is application-level (no separate databases).

## Testing

- **Jest** + **supertest** available in `api-backend`
- Command: `npm test` (currently passes with `--passWithNoTests` — no tests written yet, `make npm-install` first)
- No PHP test infrastructure exists
- **PRIORIDADE #1 DO PLANO V2:** Escrever testes antes de qualquer nova feature

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
| `api-backend/modules/` | Domain modules (13 módulos) |
| `scripts/init.sql` | Full DB schema + seed data |
| `web-frontend/public/index.php` | Frontend router (query string based) |
| `web-frontend/templates/` | PHP view templates (24 páginas + 6 parciais) |
| `nginx/default.conf` | Reverse proxy: `/` → PHP, `/api/` → Node |
| **`docs/planning/PLANEJAMENTO_V2.md`** | **NOVO — Planejamento Estratégico V2 (pós-auditoria)** |
| **`docs/planning/EPICOS_V2.md`** | **NOVO — Épicos e Histórias V2** |
| **`docs/planning/SPRINT_PLAN_V2.md`** | **NOVO — Sprint Plan V2** |
| `docs/planning/PLANEJAMENTO_MODERNO_PROJETO.md` | Planejamento original (arquivado) |
| `_bmad-output/planning-artifacts/architecture/` | Architecture decisions |

## Codebase conventions

- Comments and identifiers in **Brazilian Portuguese**
- Structured JSON logging (not console.log strings) in API
- Error responses use snake_case error codes (e.g., `ERR_RATE_LIMIT`, `ERR_NOT_FOUND`)
- Health endpoint at both `/health` and `/api/v1/health`
- Mercado Pago integration in `config/mercadopago.js` — API runs in degraded mode if `MP_ACCESS_TOKEN` not set
- No migration framework — single `init.sql` executed at MySQL container init

## Project Status V2 (Jul 29, 2026) — Pós Auditoria Completa

### ✅ Completos (86 endpoints, 18 tabelas, 30 templates PHP)

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
| LGPD Exportação + Consentimento | ✅ | ✅ | E13 |
| Perfil + Busca por município | ✅ | ✅ | E14 |

### 🧪 Testes — 168 testes escritos e passando (E15)

**Sprints 1-3 concluídos** — 168 testes (22 suites) para todos os módulos exceto Payments.
Setup completo: Jest + Supertest + banco de teste isolado + fixtures + helpers.
Hardening parcial: JWT secret, CORS, Helmet, Rate Limit, Email service.

### ✅ Bugs Corrigidos na Auditoria

| Bug | Status | Correção |
|:----|:------:|:---------|
| `schedules.service.js` tenantFilter sem prefixo JOIN | ✅ FIXED | `replace(/\btenant_id\b/g, 'ss.tenant_id')` |
| `workers.service.js` AND ? com string SQL | ✅ FIXED | Interpolação direta no SQL |

### 🔴 Épicos Não Construídos (3)

| Épico | Descrição | Risco |
|:------|:----------|:------|
| **E10** 🕐 Ponto Eletrônico | GPS + foto + engine trabalhista | Passivo trabalhista |
| **E11** 📋 eSocial Doméstico | Admissão, DAE, FGTS | Passivo fiscal |
| **E12** 🚨 Incidentes & Emergência | SOS, CAT | Risco civil |

### 🟡 Gaps Abertos

| Gap | Épico | Ação |
|:----|:------|:-----|
| Payments sem testes | E15 | Escrever testes para payments module |
| Email service em modo log | E17 | Ativar SendGrid |
| JWT secret ainda com default | E17 | Gerar secret 64 caracteres |
| CI/CD sem runner de testes | E15 | Adicionar `npm test` ao workflow |
| Migration framework ausente | — | Criar `scripts/migrate.js` |
| Docker test override ausente | E15 | Criar `docker-compose.test.yml` |

### 📝 Próximos Sprints

| Sprint | Foco | Épicos |
|:-----:|:-----|:------:|
| 4 | Ponto eletrônico (GPS + foto) | E10.1, E10.2 |
| 5 | Engine trabalhista + eSocial | E10.3, E10.4, E11.1 |
| 6 | DAE + Incidentes | E11.2, E11.3, E12.1, E12.2 |
| 7 | CAT + Refatoração templates | E12.3, E16.1, E16.2 |
| 8 | Refatoração controllers + CI/CD | E16.3, migration framework, CI |
| — | Testes Payments | E15.3 (pendente) |
| — | Hardening final (Email, JWT, Tunnel) | E17.2, E17.4 |

### 🔴 Gaps Críticos Ainda Não Construídos

| Item | Épico | Risco |
|:-----|:-----:|:------|
| Ponto eletrônico (GPS+foto) — Art. 12 LC 150 | E10 | Passivo trabalhista |
| eSocial Doméstico (admissão, DAE, FGTS) | E11 | Passivo fiscal |
| Motor trabalhista (HE, noturno, 12x36) | E10 | Passivo salarial |
| Incidentes, SOS, CAT | E12 | Risco civil |

### 🟡 Melhorias Planejadas

| Item | Épico |
|:-----|:-----:|
| Hardening segurança (JWT, CORS, Email, Rate Limit) | E17 |
| Refatoração templates grandes (proposals.php 1056L, solicitar.php 979L) | E16 |
| Migration framework | — |
| CI/CD test runner | E15 |

## Documentos de Planejamento V2

| Documento | Descrição |
|:----------|:----------|
| `docs/planning/PLANEJAMENTO_V2.md` | Plano estratégico completo pós-auditoria |
| `docs/planning/EPICOS_V2.md` | Épicos e histórias detalhadas (17 épicos, 64 stories) |
| `docs/planning/SPRINT_PLAN_V2.md` | 8 sprints priorizados com durations e riscos |
| `docs/planning/PLANEJAMENTO_MODERNO_PROJETO.md` | Versão anterior (arquivada) |
