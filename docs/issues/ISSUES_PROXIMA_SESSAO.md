# 🎯 ServiceSaaS — Issues para Próxima Sessão

> **Data:** 27 de Julho de 2026
> **Projeto:** ServiceSaaS (Serviços Flex)
> **Stack:** PHP 8.2 · Node.js 20 · MySQL 8.0 · Docker Compose
> **Documento de referência:** `_bmad-output/planning-artifacts/epics.md`

---

## 📊 Status Atual do Projeto

| Épico | Status | Stories Completas | Stories Pendentes |
|:---:|:---:|:---:|:---:|
| **Epic 1** 🔐 Onboarding & Autenticação | 🟡 Parcial | 1.1, 1.2, 1.3, 1.4 | **1.5** |
| **Epic 2** 👥 Gestão de Clientes e Catálogo | 🟡 Parcial | 2.1 | **2.2** |
| **Epic 3** 📄 Ciclo de Vida da Proposta | 🔴 Não iniciado | — | **3.1 a 3.6** |
| **Epic 4** 📊 Dashboard e Métricas | 🔴 Não iniciado | — | **4.1 a 4.3** |
| **Epic 5** 💳 Pagamentos Mercado Pago | 🔴 Não iniciado | — | **5.1 a 5.4** |
| **Epic 6** 🌐 Presença Pública e Leads | 🔴 Não iniciado | — | **6.1** |
| **Epic 7** 🏢 Administração | 🟡 Parcial | API pronta | **Frontend PHP** |

### ✅ Já Implementado e Testado

| Funcionalidade | Arquivos |
|:---|---|
| Docker Compose (5 containers) | `docker-compose.yml`, `nginx/`, `Dockerfile.*` |
| Auth (Register + Login + JWT) | `api-backend/modules/auth/*`, `web-frontend/templates/{register,login}.php` |
| Auth Middleware + Tenant Middleware | `api-backend/middlewares/{auth,tenant}.middleware.js` |
| CRUD Clientes (API + Frontend) | `api-backend/modules/clients/*`, `web-frontend/templates/clients.php` |
| Dashboard com KPIs | `api-backend/modules/dashboard/*`, `web-frontend/templates/dashboard.php` |
| Admin API (Super Admin) | `api-backend/modules/admin/*` |
| Seed Data (Maria Beleza + dados) | `scripts/seed.sql` |
| Bug fix: API_BASE_URL | ✅ Corrigido (relative URL /api/v1) |
| Fluxo completo navegador | ✅ Cadastro → Login → Dashboard → Clientes |

---

## 🥇 Issue #1: Story 2.2 — Catálogo de Produtos/Serviços

**Prioridade:** 🔥 CRÍTICA
**FRs:** FR-020, FR-021, FR-022
**Depende de:** Story 2.1 (completa)
**Tempo estimado:** 2-3h

### Descrição
Criar CRUD completo de **Categorias** e **Serviços** no catálogo do prestador. O usuário (Maria) precisa cadastrar categorias (ex: Corte, Manicure) e serviços dentro delas (ex: Corte Feminino R$ 50,00).

### Schema (já existe no BD)

**categories:**
```sql
id, tenant_id, name, description, icon, color, active, sort_order, created_at, updated_at
```

**services:**
```sql
id, tenant_id, category_id (FK → categories), name, description, price, duration_minutes, active, created_at, updated_at
```

### Ações Necessárias

#### 1.1 API — Categories Controller + Routes (Node.js)
- [ ] Criar `api-backend/modules/catalog/categories.controller.js`
  - `list(req, res)` — GET /categories?search=&page=&perPage=&active=
  - `create(req, res)` — POST /categories `{ name, description, icon, color }`
  - `read(req, res)` — GET /categories/:id
  - `update(req, res)` — PUT /categories/:id
  - `remove(req, res)` — DELETE /categories/:id (desativa)
- [ ] Criar `api-backend/modules/catalog/categories.routes.js`
  - Protegido: `authenticate` + `injectTenant`
- [ ] Registrar em `api-backend/server.js`: `app.use('/api/v1/categories', ...)`

#### 1.2 API — Services Controller + Routes (Node.js)
- [ ] Criar `api-backend/modules/catalog/services.controller.js`
  - `list(req, res)` — GET /services?search=&category_id=&page=&perPage=
  - `create(req, res)` — POST /services `{ name, description, price, duration_minutes, category_id }`
  - `read(req, res)` — GET /services/:id
  - `update(req, res)` — PUT /services/:id
  - `remove(req, res)` — DELETE /services/:id (desativa)
- [ ] Criar `api-backend/modules/catalog/services.routes.js`
  - Protegido: `authenticate` + `injectTenant`
- [ ] Registrar em `server.js`: `app.use('/api/v1/services', ...)`

#### 1.3 Frontend — Página de Categorias
- [ ] Criar `web-frontend/templates/categories.php`
  - Sidebar + Topbar (reaproveitar padrão do clients.php)
  - Tabela listando categorias (nome, ícone, cor, ordem)
  - Modal de criação/edição (nome, descrição, ícone, cor, ordem)
  - Ação toggle ativo/inativo
  - Empty state: "Nenhuma categoria cadastrada"
  - Busca com debounce

#### 1.4 Frontend — Página de Serviços
- [ ] Criar `web-frontend/templates/services.php`
  - Sidebar + Topbar
  - Tabela listando serviços (nome, categoria, preço R$, duração, status)
  - Modal de criação/edição (nome, descrição, categoria [select], preço, duração)
  - Filtro por categoria (dropdown)
  - Busca com debounce
  - Formatação de moeda (R$ 0.000,00)
  - Empty state
  - Ação toggle ativo/inativo

#### 1.5 Atualizar Roteamento
- [ ] Adicionar `categories` e `services` ao `$allowedPages` no `index.php`
- [ ] Adicionar entradas no `$pageTitle` match
- [ ] Atualizar sidebar no `dashboard.php`:
  - Link atual "Serviços" aponta para `?page=dashboard&sub=servicos` (rota inexistente) → mudar para `?page=services`
  - Adicionar link para `?page=categories` (ou sub-menu dentro de Serviços)
- [ ] Adicionar **auth guard** (`if (!isAuthenticated())`) no topo de `categories.php` e `services.php` (mesmo padrão do `clients.php`)

#### 1.6 Testes
- [ ] Testar CRUD categorias via curl
- [ ] Testar CRUD serviços via curl
- [ ] Testar login → categorias → serviços no navegador
- [ ] Verificar isolation por tenant

---

## 🥈 Issue #2: Story 1.5 — Recuperação de Senha

**Prioridade:** 🔥 ALTA
**FRs:** FR-004
**Depende de:** Story 1.4 (completa)
**Tempo estimado:** 1.5-2h

### Ações Necessárias
- [ ] Migration: adicionar colunas `reset_token VARCHAR(255) NULL` e `reset_token_expires TIMESTAMP NULL` na tabela `users`
- [ ] API: POST /auth/forgot-password (gera token UUID, salva com expiração 1h)
- [ ] API: POST /auth/reset-password (valida token, bcrypt nova senha, limpa token)
- [ ] Email service: stub (console.log no MVP)
- [ ] Frontend: `templates/forgot-password.php`
- [ ] Frontend: `templates/reset-password.php`
- [ ] Link "Esqueci minha senha" na página de login

---

## 🥉 Issue #3: Epic 3 — Ciclo de Vida da Proposta

**Prioridade:** 🔥 ALTA
**FRs:** FR-030 a FR-035
**Depende de:** Story 2.2 (catálogo) + Story 2.1 (clientes)
**Tempo estimado:** 6-8h

### Stories
- **Story 3.1:** Criar tabelas `proposals` + `proposal_items`
  - ⚠️ **Atenção:** MySQL já está rodando com volume persistido. Alterar `scripts/init.sql` não criará as tabelas automaticamente.
  - **Opção A:** Remover volume MySQL (`docker-compose down -v`) e rebuildar (⚠️ destrói dados existentes)
  - **Opção B:** Executar migration manual: `docker-compose exec -T mysql mysql -u root -proot servicos_flex < scripts/migrations/XXX_create_proposals.sql`
  - Recomendado: **Opção B** — criar `scripts/migrations/` com migration numerada
- **Story 3.2:** API CRUD Propostas (mestre-detalhe em transação)
- **Story 3.3:** Frontend Proposta — wizard cliente + itens com autocomplete
- **Story 3.4:** Envio via WhatsApp (link público wa.me)
- **Story 3.5:** Aprovação pública (link público sem login)
- **Story 3.6:** Geração de PDF (pdfkit)

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
