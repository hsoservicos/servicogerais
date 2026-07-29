# 🎯 ServiceSaaS — Issues para Próxima Sessão

> **Data:** 29 de Julho de 2026
> **Projeto:** ServiceSaaS (Serviços Flex)
> **Stack:** PHP 8.2 · Node.js 20 · MySQL 8.0 · Docker Compose
> **Documento de referência:** `_bmad-output/planning-artifacts/epics.md`

---

## 📊 Status Atual do Projeto

| Épico | Status | Stories Completas | Stories Pendentes |
|:---:|:---:|:---:|:---:|
| **Epic 1** 🔐 Onboarding & Autenticação | 🟢 Completo | 1.1, 1.2, 1.3, 1.4, 1.5 | — |
| **Epic 2** 👥 Gestão de Clientes e Catálogo | 🟢 Completo | 2.1, 2.2 | — |
| **Epic 3** 📄 Ciclo de Vida da Proposta | 🟢 Completo | 3.1, 3.2, 3.3, 3.4, 3.5 | — |
| **Epic 4** 📊 Dashboard e Métricas | 🔴 Não iniciado | — | 4.1 a 4.3 |
| **Epic 5** 💳 Pagamentos Mercado Pago | 🟡 Parcial | 5.1, 5.4 (API + PIX) | 5.2 (webhook), 5.3 |
| **Epic 6** 🌐 Presença Pública e Leads | 🟡 Parcial | 6.1, 6.2, 6.3 | Frontend admin leads |
| **Epic 7** 🏢 Administração | 🟡 Parcial | API pronta | Frontend PHP |

### ✅ Já Implementado e Testado

| Funcionalidade | Arquivos |
|:---|---|
| Docker Compose (5 containers) | `docker-compose.yml`, `nginx/`, `Dockerfile.*` |
| Auth (Register + Login + JWT + Forgot/Reset Password) | `api-backend/modules/auth/*`, `web-frontend/templates/{register,login,forgot-password,reset-password}.php` |
| Auth Middleware + Tenant Middleware | `api-backend/middlewares/{auth,tenant}.middleware.js` |
| CRUD Clientes (API + Frontend) | `api-backend/modules/clients/*`, `web-frontend/templates/clients.php` |
| CRUD Categorias (API + Frontend) | `api-backend/modules/catalog/categories.*`, `web-frontend/templates/categories.php` |
| CRUD Serviços (API + Frontend) | `api-backend/modules/catalog/services.*`, `web-frontend/templates/services.php` |
| Dashboard com KPIs | `api-backend/modules/dashboard/*`, `web-frontend/templates/dashboard.php` |
| Admin API (Super Admin) | `api-backend/modules/admin/*` |
| Perfil do Prestador (Tenant Profile) | `api-backend/modules/tenants/*`, `web-frontend/templates/tenant-profile.php` |
| Workers Domésticos (API + Frontend) | `api-backend/modules/domestic/workers.*`, `web-frontend/templates/workers.php` |
| CRUD Propostas (API + Frontend) | `api-backend/modules/proposals/*`, `web-frontend/templates/proposals.php` |
| Itens da Proposta (API CRUD) | `api-backend/modules/proposals/items.*` |
| Aprovação Pública (Story 3.4/6.3) | `api-backend/modules/public/publicProposals.controller.js`, `web-frontend/templates/public-proposal.php` |
| Geração PDF (Story 3.5) | `api-backend/services/pdfService.js`, endpoints `/proposals/:id/pdf` e `/public/proposals/:token/pdf` |
| Mercado Pago Pix (Story 5.4) | `api-backend/modules/public/publicProposals.controller.js` (createPaymentPreference) |
| Busca por Proximidade (city filter) | `api-backend/modules/public/public.controller.js`, `web-frontend/templates/{home,solicitar}.php` |
| Seed Data (Maria Beleza + dados) | `scripts/seed.sql` |
| Bug fix: API_BASE_URL | ✅ Corrigido (relative URL /api/v1) |
| Bug fix: WhatsApp prefix duplicado | ✅ Corrigido em workers.php, proposals.php, leads.php |
| Bug fix: services.read tenantFilter | ✅ Corrigido (COALESCE + alias s.) |
| Fluxo completo navegador | ✅ Cadastro → Login → Dashboard → Clientes → Categorias → Serviços → Propostas |

---

## 🥇 Issue #1: Epic 3 — Ciclo de Vida da Proposta ✅ CONCLUÍDO

**Stories 3.1 a 3.5 — todas implementadas e testadas.**

| Story | Status | Detalhes |
|:---|---:|---|
| 3.1 — API CRUD Propostas (mestre-detalhe) | ✅ | proposals.controller.js + items.controller.js + routes |
| 3.2 — Frontend proposals.php (1142 linhas) | ✅ | Filtros, tabs, modal create/edit com itens, view modal, timeline, status transitions |
| 3.3 — Envio WhatsApp | ✅ | Link wa.me com template + link público |
| 3.4 — Aprovação pública (public-proposal.php) | ✅ | 713 linhas, show + approve/reject + Pix |
| 3.5 — Geração PDF (pdfkit) | ✅ | pdfService.js, 2 endpoints, botões frontend |

---

## 🥈 Issue #2: Epic 4 — Dashboard e Métricas

**Prioridade:** 🟡 MÉDIA
**FRs:** FR-040 a FR-043
**Depende de:** Epic 3 (propostas) ✅
**Tempo estimado:** 3-4h

### Stories
- **Story 4.1:** Gráfico Chart.js (propostas aprovadas últimos 6 meses)
- **Story 4.2:** Lista de follow-up (propostas pendentes > 48h)
- **Story 4.3:** Financeiro — histórico de transações

---

## 🥉 Issue #3: Epic 5 — Pagamentos com Mercado Pago

**Prioridade:** 🟡 MÉDIA
**FRs:** FR-050 a FR-053
**Depende de:** Epic 3 (propostas aprovadas) ✅
**Tempo estimado:** 4-6h

### Stories
- **Story 5.1:** Criar preferência de pagamento (SDK MP) — ✅ API implementada
- **Story 5.2:** Webhook IPN (notificação de pagamento) — ✅ `payments.controller.js`
- **Story 5.3:** QR Code Pix no checkout — 🔶 Parcial (público tem Pix, admin cobrança via MP link)
- **Story 5.4:** Estorno de pagamento — ❌ Pendente

---

## Issue #4: Epic 6 — Landing Page + Captura de Leads

**Prioridade:** 🟡 MÉDIA
**Tempo estimado:** 2-4h

### Stories
- **Story 6.1:** Formulário de lead (wizard 3 passos) — ✅ home.php + solicitar.php
- **Story 6.2:** Upload de fotos — ✅ upload.controller.js
- **Story 6.3:** Propostas públicas — ✅ (compartilhado com Epic 3)
- **Story 6.4:** Admin de leads (frontend) — ❌ Pendente

---

## Issue #5: Epic 7 — Frontend Administrativo

**Prioridade:** 🟡 MÉDIA
**Depende de:** API Admin (pronta)
**Tempo estimado:** 3-4h

### Ações Necessárias
- [ ] Criar `web-frontend/templates/admin-login.php`
- [ ] Criar `web-frontend/templates/admin-dashboard.php`
- [ ] Criar `web-frontend/templates/admin-tenants.php`
- [ ] Adicionar nginx `location /admin/` no `nginx/default.conf`
- [ ] Criar rota separada para admin no `index.php`

---

## 🛠️ Issue #6: Infrastructure & Bug Fixes

**Prioridade:** 🔥 ALTA
**Tempo estimado:** 1-2h

### Pendências Técnicas
- [ ] **Fix health check**: API container está "unhealthy" — health check tenta `localhost:3000` ao invés do endpoint correto
- [ ] **LGPD Docs**: Criar `docs/lgpd/registro-operacoes.md` (NFR-LGPD-02)
- [ ] **Termos de Uso**: Criar `termos-de-uso.html` com DPA (NFR-LGPD-01)

### Futuro (v1.1+)
- [ ] Observabilidade: Loki + Grafana + Promtail
- [ ] CI/CD: GitHub Actions (lint → test → build → scan → deploy)
- [ ] Cloudflare Tunnel para exposição segura (staging/production)

---

## 📋 Credenciais de Teste

| Papel | E-mail | Senha | Observação |
|:---|---|:---:|:---|
| 👩 Prestadora | `maria@beleza.com` | `novaSenha123` | Tenant: Maria Beleza Estética (ID 2) — 5 clientes, 10 serviços |
| 👤 Prestadora | `navegadorok@teste.com` | `12345678` | Tenant criado no teste anterior |
| 🏢 Super Admin | `admin@servicesaas.com` | `12345678` | Tenant: ServiceSaaS Admin (ID 1) |

---

## 🔧 Comandos Úteis

```bash
# Subir ambiente
docker-compose up -d

# Ver logs
docker-compose logs -f api

# Popular dados de teste (idempotente)
make seed

# Login Maria Beleza (senha atualizada após teste de forgot/reset)
curl -X POST http://localhost:8080/api/v1/auth/login \
  -H 'Content-Type: application/json' \
  -d '{"email":"maria@beleza.com","password":"novaSenha123"}'

# Login Admin
curl -X POST http://localhost:8080/api/v1/auth/login \
  -H 'Content-Type: application/json' \
  -d '{"email":"admin@servicesaas.com","password":"12345678"}'

# Testar PDF
TOKEN="<jwt>"
curl -o proposta.pdf http://localhost:8080/api/v1/proposals/3/pdf \
  -H 'Authorization: Bearer '$TOKEN

# PDF Público
curl -o proposta.pdf http://localhost:8080/api/v1/public/proposals/<public_token>/pdf

# Listar clientes (com token)
TOKEN="<jwt>"
curl http://localhost:8080/api/v1/clients \
  -H 'Authorization: Bearer '$TOKEN
```

---

## Issue #4: Epic 4 — Dashboard e Métricas

**Prioridade:** 🟡 MÉDIA
**FRs:** FR-040 a FR-043
**Depende de:** Epic 3 (propostas)
**Tempo estimado:** 3-4h

### Stories
- **Story 4.1:** Gráfico Chart.js (propostas aprovadas últimos 6 meses)
- **Story 4.2:** Lista de follow-up (propostas pendentes > 48h)
- **Story 4.3:** Financeiro — histórico de transações

---

## Issue #5: Epic 5 — Pagamentos com Mercado Pago

**Prioridade:** 🟡 MÉDIA
**FRs:** FR-050 a FR-053
**Depende de:** Epic 3 (propostas aprovadas)
**Tempo estimado:** 8-10h

### Stories
- **Story 5.1:** Criar preferência de pagamento (SDK MP Node.js)
- **Story 5.2:** Webhook IPN (notificação de pagamento)
- **Story 5.3:** QR Code Pix no checkout público
- **Story 5.4:** Estorno de pagamento

---

## Issue #6: Epic 6 — Landing Page + Captura de Leads

**Prioridade:** 🟡 MÉDIA
**Tempo estimado:** 3-4h

### Stories
- **Story 6.1:** Formulário de lead na Landing Page (3 passos)
  - Criar tabela `public_leads`
  - API: POST /api/v1/public/leads
  - Frontend: wizard na home.php

---

## Issue #7: Epic 7 — Frontend Administrativo

**Prioridade:** 🟡 MÉDIA
**Depende de:** API Admin (pronta)
**Tempo estimado:** 3-4h

### Ações Necessárias
- [ ] Criar `web-frontend/templates/admin-login.php`
- [ ] Criar `web-frontend/templates/admin-dashboard.php`
- [ ] Criar `web-frontend/templates/admin-tenants.php`
- [ ] Adicionar nginx `location /admin/` no `nginx/default.conf`
- [ ] Criar rota separada para admin no `index.php`

---

## 🛠️ Issue #8: Infrastructure & Bug Fixes

**Prioridade:** 🔥 ALTA
**Tempo estimado:** 1-2h

### Pendências Técnicas
- [ ] **Fix health check**: API container está "unhealthy" — health check tenta `localhost:3000` ao invés do endpoint correto ❌
- [ ] **Fix update() validação**: `clients.controller.js` — validação de nome no update (CR dos code-reviewers)
- [ ] **Fix WhatsApp prefix**: Tratar prefixo `55` duplicado no link wa.me (CR dos code-reviewers)
- [ ] **LGPD Docs**: Criar `docs/lgpd/registro-operacoes.md` (NFR-LGPD-02)
- [ ] **Termos de Uso**: Criar `termos-de-uso.html` com DPA (NFR-LGPD-01)

### Futuro (v1.1+)
- [ ] Observabilidade: Loki + Grafana + Promtail
- [ ] CI/CD: GitHub Actions (lint → test → build → scan → deploy)
- [ ] Cloudflare Tunnel para exposição segura (staging/production)

---

## 📋 Credenciais de Teste

| Papel | E-mail | Senha | Observação |
|:---|---|:---:|:---|
| 👩 Prestadora | `maria@beleza.com` | `12345678` | Tenant: Maria Beleza Estética (ID 2) — 5 clientes, 10 serviços |
| 👤 Prestadora | `navegadorok@teste.com` | `12345678` | Tenant criado no teste anterior |
| 🏢 Super Admin | `admin@servicesaas.com` | `12345678` | Tenant: ServiceSaaS Admin (ID 1) |

---

## 🔧 Comandos Úteis

```bash
# Subir ambiente
docker-compose up -d

# Ver logs
docker-compose logs -f api

# Popular dados de teste (idempotente)
make seed

# Login Maria Beleza
curl -X POST http://localhost:8080/api/v1/auth/login \
  -H 'Content-Type: application/json' \
  -d '{"email":"maria@beleza.com","password":"12345678"}'

# Login Admin
curl -X POST http://localhost:8080/api/v1/auth/login \
  -H 'Content-Type: application/json' \
  -d '{"email":"admin@servicesaas.com","password":"12345678"}'

# Listar clientes (com token)
TOKEN="<jwt>"
curl http://localhost:8080/api/v1/clients \
  -H 'Authorization: Bearer '$TOKEN
```
