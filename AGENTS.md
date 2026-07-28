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
- `auth/`, `tenants/`, `clients/`, `catalog/`, `proposals/`, `payments/`, `transactions/`, `leads/`, `public/`, `admin/`, `dashboard/`, `domestic/`

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
| `api-backend/modules/` | Domain modules |
| `scripts/init.sql` | Full DB schema + seed data |
| `web-frontend/public/index.php` | Frontend router (query string based) |
| `web-frontend/templates/` | PHP view templates |
| `nginx/default.conf` | Reverse proxy: `/` → PHP, `/api/` → Node |
| `_bmad-output/planning-artifacts/architecture/` | Architecture decisions |
| `docs/planning/PLANEJAMENTO_MODERNO_PROJETO.md` | Main planning doc |

## Codebase conventions

- Comments and identifiers in **Brazilian Portuguese**
- Structured JSON logging (not console.log strings) in API
- Error responses use snake_case error codes (e.g., `ERR_RATE_LIMIT`, `ERR_NOT_FOUND`)
- Health endpoint at both `/health` and `/api/v1/health`
- Mercado Pago integration in `config/mercadopago.js` — API runs in degraded mode if `MP_ACCESS_TOKEN` not set
- No migration framework — single `init.sql` executed at MySQL container init

## Key audit findings (domestic compliance)

Full report: `docs/auditoria/AUDITORIA_COMPLIANCE_DOMESTICO.md`

**Current system only handles autonomous service proposals.** To support domestic employees (LC 150/2015), the following are **missing** and must be built:

| Module | Status | Risk if absent |
|--------|--------|----------------|
| Workers table with CBO codes + 9 domestic categories | ❌ Not modeled | Cannot onboard workers |
| Frequency-lock algorithm (max 2d/week for diaristas) | ❌ Not built | CLT descaracterization lawsuit |
| Electronic time tracking (GPS + photo) per Art. 12 LC 150 | ❌ Not built | Labor liability |
| eSocial Doméstico integration (admission, DAE, FGTS) | ❌ Not built | Tax liability |
| Labor calculation engine (overtime, night shift, 12×36) | ❌ Not built | Wage liability |
| Worker certification & background check | ❌ Not built | Safety risk |
| Incident/emergency reporting + insurance | ❌ Not built | Civil liability |
| LGPD data portability & erasure flows | 🟡 Partial | Fine up to 2% revenue |

**Schema conflicts found:** `migrations/002_create_transactions_table.sql` duplicates `transactions` table already in `init.sql` (different schema). Remove the migration file.

**No tests exist** (`__tests__/` directory absent, Jest runs `--passWithNoTests`).