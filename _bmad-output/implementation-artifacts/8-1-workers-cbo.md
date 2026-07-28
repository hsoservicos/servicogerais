---
baseline_commit: HEAD
story_key: "8-1-workers-cbo"
epic: "E8"
---

# Story 8.1 — Modelagem da Tabela Workers + CBO

**Épico:** 8 — Workers e Cadastro de Trabalhadores Domésticos
**Story:** Como prestador (tenant), quero cadastrar trabalhadores domésticos com CPF, CBO e categoria LC 150 para começar a operar contratações.

---

## Acceptance Criteria

- [ ] AC1: Worker é persistido na tabela `workers` com status `active = TRUE` e `background_check_status = 'PENDING'`
- [ ] AC2: CPF duplicado retorna erro `ERR_DUPLICATE_ENTRY` (HTTP 409)
- [ ] AC3: Categoria inválida (fora do ENUM de 9 categorias) retorna erro `ERR_VALIDATION` (HTTP 422)
- [ ] AC4: CBO code vazio ou formato inválido retorna erro `ERR_VALIDATION` (HTTP 422)
- [ ] AC5: Tenant isolation — workers de um tenant não são visíveis por outro tenant
- [ ] AC6: Exclusão lógica (active=FALSE), não DELETE físico
- [ ] AC7: Busca por nome, CPF e categoria funcionando com paginação

---

## Tasks / Subtasks

### [ ] Task 1: Migration SQL — Criar tabelas `workers`, `worker_certifications`, `service_schedules`

- [ ] Subtask 1.1: Criar `scripts/migrations/003_create_workers.sql` com tabela `workers` (id, tenant_id, name, email, cpf UNIQUE, rg, cbo_code, worker_category ENUM com 9 categorias, phone, whatsapp, pix_key, address JSON, avatar_url, background_check_status ENUM, background_check_date, background_check_provider, active BOOLEAN, created_at, updated_at)
- [ ] Subtask 1.2: Adicionar tabela `worker_certifications` (id, worker_id FK, certification_type ENUM, title, issuer, issue_date, expiry_date, document_url, verified BOOLEAN, created_at)
- [ ] Subtask 1.3: Adicionar tabela `service_schedules` (id, tenant_id, worker_id, client_id, service_category, regime ENUM, scheduled_date, start_time, end_time, status, hourly_rate, total_amount, transport_voucher, notes, timestamps)

### [ ] Task 2: Módulo API `domestic/` — CRUD Workers

- [ ] Subtask 2.1: Criar `api-backend/modules/domestic/` com `workers.routes.js`, `workers.controller.js`, `workers.service.js`
- [ ] Subtask 2.2: Implementar `POST /api/v1/workers` — criar worker com validação de CPF, CBO e categoria
- [ ] Subtask 2.3: Implementar `GET /api/v1/workers` — listar workers com paginação, busca (nome, CPF, categoria)
- [ ] Subtask 2.4: Implementar `GET /api/v1/workers/:id` — detalhes do worker + certificações
- [ ] Subtask 2.5: Implementar `PUT /api/v1/workers/:id` — atualizar worker
- [ ] Subtask 2.6: Implementar `DELETE /api/v1/workers/:id` — exclusão lógica (active=FALSE)
- [ ] Subtask 2.7: Registrar rotas no `server.js`

### [ ] Task 3: Frontend PHP — Template `workers.php`

- [ ] Subtask 3.1: Criar `web-frontend/templates/workers.php` com listagem, busca, paginação
- [ ] Subtask 3.2: Modal de criação/edição de worker com campos: nome, CPF, CBO, categoria, telefone, email, PIX
- [ ] Subtask 3.3: Adicionar rota `workers` no `web-frontend/public/index.php`
- [ ] Subtask 3.4: Adicionar link no sidebar para Workers

### [ ] Task 4: Testes

- [ ] Subtask 4.1: Teste unitário — validação de CPF duplicado
- [ ] Subtask 4.2: Teste unitário — validação de categoria inválida
- [ ] Subtask 4.3: Teste de integração — CRUD workers completo
- [ ] Subtask 4.4: Teste de integração — tenant isolation (worker do tenant A não visível no tenant B)

---

## Dev Notes

### Architecture Context

- **Arquitetura:** AD-9 (Workers as distinct entity), AD-2 (Multi-tenancy via `req.tenantFilter`), AD-8 (Logical deletes)
- **Padrão do projeto:** Modular por domínio em `api-backend/modules/`. Cada módulo tem `.routes.js`, `.controller.js`, `.service.js`
- **Banco:** `mysql2/promise` pool via `config/database.js` — usar `query(sql, params)` para SELECT/INSERT/UPDATE/DELETE e `transaction(callback)` para operações atômicas
- **Auth:** JWT via middleware `authenticate` + `injectTenant` — `req.tenantFilter` já anexado pela cadeia de middlewares
- **Frontend:** PHP sem framework, query string routing (`?page=workers`), Tailwind CSS CDN, vanilla JS com `fetch()` para API calls

### Worker Categories (ENUM)

```
EMPREGADO_DOMESTICO_GERAL
DIARISTA
BABA
CUIDADOR_IDOSOS
COZINHEIRO
MOTORISTA
JARDINEIRO
CASEIRO
GOVERNANTA
```

### Existing Patterns to Follow

- `api-backend/modules/clients/clients.controller.js` — CRUD com tenantFilter, busca, exclusão lógica
- `web-frontend/templates/clients.php` — template com modal CRUD, busca, sidebar

### Dependencies

- Nenhuma nova dependência npm

---

## Dev Agent Record

### Implementation Plan

### Debug Log

### Completion Notes

---

## File List

| File | Action |
|:-----|:-------|
| `scripts/migrations/003_create_workers.sql` | Create |
| `api-backend/modules/domestic/workers.routes.js` | Create |
| `api-backend/modules/domestic/workers.controller.js` | Create |
| `api-backend/modules/domestic/workers.service.js` | Create |
| `api-backend/server.js` | Edit |
| `web-frontend/templates/workers.php` | Create |
| `web-frontend/public/index.php` | Edit |
| `web-frontend/templates/partials/sidebar.php` | Edit |

---

## Change Log

| Date | Change |
|:-----|:-------|
| 2026-07-28 | Story created from Epic 8 |

---

## Status

`ready-for-dev`