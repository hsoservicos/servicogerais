# ServiceSaaS — Gestão Inteligente para Prestadores de Serviços

> Plataforma SaaS multi-tenant para prestadores de serviços gerenciarem clientes, propostas, pagamentos e trabalhadores domésticos com compliance legal (LC 150/2015).

![Docker](https://img.shields.io/badge/docker-compose-2496ED?logo=docker)
![Node](https://img.shields.io/badge/node-20_LTS-339933?logo=node.js)
![PHP](https://img.shields.io/badge/php-8.2-777BB4?logo=php)
![MySQL](https://img.shields.io/badge/mysql-8.0-4479A1?logo=mysql)
![Nginx](https://img.shields.io/badge/nginx-1.25-009639?logo=nginx)
![License](https://img.shields.io/badge/license-private-red)

---

## Sumário

- [Visão Geral](#visão-geral)
- [Funcionalidades](#funcionalidades)
- [Stack Tecnológica](#stack-tecnológica)
- [Arquitetura](#arquitetura)
- [Design System](#design-system)
- [Modelo de Dados](#modelo-de-dados)
- [API Endpoints](#api-endpoints)
- [Multi-Tenancy](#multi-tenancy)
- [Fluxo de Autenticação](#fluxo-de-autenticação)
- [Docker Development](#docker-development)
- [Testes](#testes)
- [Auditoria de Compliance](#auditoria-de-compliance)
- [Roadmap](#roadmap)
- [Estrutura do Projeto](#estrutura-do-projeto)
- [Documentação Principal](#documentação-principal)
- [Variáveis de Ambiente](#variáveis-de-ambiente)

---

## Visão Geral

**ServiceSaaS** (Serviços Flex) é uma plataforma completa para prestadores de serviços profissionais e domésticos. Opera em 5 containers Docker com Nginx como reverse proxy, PHP 8.2 FPM para frontend e Node.js 20 LTS (Express) para API REST.

### Público-Alvo

- **Prestadores de serviços** (autônomos, MEIs, pequenas empresas) — gerenciam clientes, serviços, propostas e finanças
- **Clientes finais** — buscam serviços próximos e solicitam orçamentos via landing page pública responsiva
- **Trabalhadores domésticos** — diaristas, babás, cuidadores, motoristas etc., com enquadramento LC 150/2015

### Principais Diferenciais

- **Multi-tenancy** em nível de aplicação (não há separação por banco de dados)
- **Ciclo completo da proposta**: criação → envio → WhatsApp → aprovação pública → PDF → pagamento
- **Dashboard com Chart.js** — receita mensal (6 meses), follow-up de propostas pendentes
- **Admin global** — super_admin com visão cross-tenant, planos de assinatura, relatório financeiro com export CSV
- **Compliance doméstico** — workers com CBO, certificações, base para eSocial e ponto eletrônico
- **Landing page pública** — busca por categoria/cidade, wizard 3 passos para lead

---

## Funcionalidades

### Módulos Core

| Módulo | Funcionalidades |
|--------|----------------|
| **Auth Multi-Tenant** | Registro de prestador (2 passos com endereço via CEP), login JWT, recuperação de senha |
| **Clientes** | CRUD com busca, soft-delete, dados de contato e endereço |
| **Catálogo** | Categorias + Serviços/Produtos com preço e duração |
| **Propostas** | Mestre-detalhe com itens, status workflow (draft→sent→viewed→accepted→paid), PDF automático (pdfkit), WhatsApp (wa.me), aprovação pública por token |
| **Dashboard** | KPIs agregados (propostas, clientes, receita), gráfico financeiro Chart.js (6 meses), follow-up de propostas sent/viewed sem resposta há >48h |
| **Financeiro** | Transações, webhook Mercado Pago, estorno, resumo por status |
| **Leads Públicos** | Captura via wizard 3 passos na landing page, painel de administração |
| **Perfil do Prestador** | Edição de dados cadastrais, endereço (via CEP), sidecar com plano e status |
| **Admin Plataforma** | Super admin: dashboard global, gestão de tenants, transações cross-tenant, auditoria, planos CRUD (cards com toggle), relatório financeiro com filtro datas e export CSV |

### Ciclo de Vida da Proposta (Epic 3)

| Funcionalidade | Status | Detalhes |
|--------|--------|------------|
| CRUD Propostas mestre-detalhe | ✅ Completo | Itens vinculados, números automáticos, status workflow |
| Frontend Propostas | ✅ Completo | ~1142 linhas, filtros tabs (todos/pendentes/aprovados), modal create/edit com itens dinâmicos, view modal |
| WhatsApp | ✅ Completo | Link wa.me com template + link público copiável |
| Aprovação Pública | ✅ Completo | Landing page pública por token, aprovar/rejeitar, PDF público |
| PDF | ✅ Completo | pdfkit com cabeçalho/tabela/totais, endpoints autenticado + público, botões no frontend |

### Módulos de Compliance Doméstico (LC 150/2015)

| Módulo | Status | Base Legal |
|--------|--------|------------|
| Workers + CBO | ✅ Implementado (CRUD + certif.) | Lei Complementar 150, CBO 2026 |
| Trava de Frequência | 📋 Planejado | Art. 1º LC 150 (limite 2d/sem) |
| Ponto Eletrônico GPS | 📋 Planejado | Art. 12 LC 150 |
| eSocial Doméstico | 📋 Planejado | Decreto 8.758/2016 |
| Calculadora Trabalhista | 📋 Planejado | CLT (HE, noturno, 12×36) |
| Certificação Obrigatória | ✅ Implementado | Cuidador/Babá precisam certificação |
| Incidentes + SOS | 📋 Planejado | CAT, seguro |
| LGPD Completo | 📋 Planejado | Portabilidade + eliminação |

---

## Stack Tecnológica

| Camada | Tecnologia | Versão |
|--------|-----------|--------|
| 🌐 Frontend | PHP + HTML5 + CSS3 + JS ES6+ (Tailwind CDN) | 8.2 |
| ⚙️ API REST | Node.js + Express.js | 20 LTS |
| 🗄️ Banco de Dados | MySQL | 8.0 |
| 🔁 Proxy | Nginx Alpine | 1.25 |
| 🐳 Conteinerização | Docker Compose | 3.8+ |
| 🔒 Exposição | Cloudflare Tunnel (cloudflared) | 2024.6+ |
| 💳 Pagamentos | Mercado Pago SDK (Node + JS) | 4.x / 2.x |
| 📊 Gráficos | Chart.js | 4.4 |
| 📄 PDF | pdfkit | 0.15 |
| 🧪 Testes | Jest + Supertest | 29.x / 6.x |
| 🎨 Design Tokens | Tailwind Config custom | — |
| 📝 Fonte | Poppins (Google Fonts) | — |

---

## Arquitetura

```
┌──────────────────────────────────────────────────────────────┐
│                    🌐 Nginx 1.25 (Reverse Proxy)              │
│           localhost:8080 → PHP-FPM :9000                     │
│           /api/* → Node.js :3000                             │
├──────────────┬──────────────────────┬───────────────────────┤
│  🖥️ PHP 8.2  │  ⚙️ Node.js 20 LTS   │  🗄️ MySQL 8.0        │
│  FPM Alpine  │  Express.js          │  utf8mb4              │
│  ─────────── │  ───────────         │  ───────────          │
│  Tailwind    │  Modular routes      │  Multi-tenant FK      │
│  Chart.js    │  JWT auth            │  ON DELETE CASCADE    │
│  pdfkit (via │  Mercado Pago        │  17 tabelas           │
│   API)       │  pdfkit              │                       │
└──────────────┴──────────────────────┴───────────────────────┘
```

### Decisões Arquiteturais (ADs)

| AD | Decisão | Status |
|:---|:--------|:-------|
| AD-1 | JWT como única fonte de verdade de autenticação | ✅ |
| AD-2 | Multi-tenancy via injeção de tenant_id em toda query SQL | ✅ |
| AD-3 | Endpoints agregados para dashboards (evita N chamadas) | ✅ |
| AD-4 | Propostas atômicas mestre-detalhe em transação MySQL | ✅ |
| AD-5 | Pagamentos idempotentes com Mercado Pago | ✅ |
| AD-6 | PDF via pdfkit (sem Puppeteer pesado) | ✅ |
| AD-7 | Cache Nginx para estáticos + dashboard (sem Redis no MVP) | ✅ |
| AD-8 | Soft-delete com active BOOLEAN (nunca hard-delete) | ✅ |
| AD-9 | Workers como entidade separada de clients | ✅ |
| AD-10 | Trava de frequência (diarista max 2d/sem) bloqueia + CLT | 📋 |
| AD-11 | Ponto eletrônico geolocalizado com foto | 📋 |
| AD-12 | eSocial assíncrono via fila BullMQ + Redis | 📋 |
| AD-13 | Certificação obrigatória para categorias sensíveis | ✅ |
| AD-14 | Reporte de incidentes com escalação de emergência | 📋 |
| AD-15 | Endereço do prestador para busca por proximidade | ✅ |
| AD-16 | Design tokens centralizados no Tailwind config (header.php) | ✅ |
| AD-17 | Admin planos CRUD com cards visuais + toggle ativar/desativar | ✅ |

### API Architecture

```
api-backend/
├── server.js                    # Entry point: middleware stack, route registry
├── config/
│   ├── database.js              # mysql2/promise pool — query(), transaction()
│   ├── auth.js                  # JWT helpers
│   └── mercadopago.js           # MP SDK init (degraded mode se sem token)
├── middlewares/
│   ├── auth.middleware.js       # JWT verification
│   ├── tenant.middleware.js     # Injeção de tenantFilter
│   ├── error.middleware.js      # Error handler padronizado
│   └── requestId.middleware.js  # Correlation ID
└── modules/                     # Domínios verticais
    ├── auth/                    # Login, registro, JWT, forgot/reset password
    ├── tenants/                 # Perfil do prestador (address, settings)
    ├── clients/                 # CRUD clientes com soft-delete
    ├── catalog/                 # Categorias + Serviços/Produtos
    ├── proposals/               # Propostas mestre-detalhe + itens + status
    ├── dashboard/               # KPIs agregados + chart + followup
    ├── payments/                # MP checkout + webhook + refund
    ├── transactions/            # Histórico financeiro
    ├── leads/                   # Painel de leads capturados
    ├── public/                  # Landing page (categories, services, leads, proposals)
    ├── admin/                   # Super admin (dashboard, tenants, plans, reports, audit)
    └── domestic/                # Workers domésticos (CRUD + certifications)
```

Cada módulo contém tipicamente: `*.routes.js`, `*.controller.js`, `*.service.js`.

### Padrão de Resposta da API

```json
// Sucesso
{ "data": { ... } }

// Erro (snake_case)
{ "error": "ERR_NOT_FOUND", "message": "Proposta não encontrada" }
```

---

## Design System

O design system é definido via Tailwind Config custom em `web-frontend/templates/partials/header.php:13-77`.

### Paleta de Cores

| Token | Hex | Uso |
|-------|-----|-----|
| `primary` | `#2563EB` | Botões, links, ações primárias |
| `primary-600` | `#2563EB` | Hover de botões primários |
| `primary-700` | `#1D4ED8` | Sidebars, headers, contraste |
| `sidebar` | `#0F172A` | Sidebar (dark) |
| `surface` | `#F8FAFC` | Fundo da página |
| `ink` | `#0F172A` | Texto principal |
| `ink-secondary` | `#64748B` | Texto secundário |
| `ink-muted` | `#94A3B8` | Texto de apoio |
| `border` | `#E2E8F0` | Bordas de cards e tabelas |

### Tipografia

| Token | Tamanho | Peso | Uso |
|-------|---------|------|-----|
| `display` | 2.5rem | 700 | Título principal da página |
| `h1` | 1.875rem | 600 | Título de seção |
| `h2` | 1.5rem | 600 | Subtítulo ou heading de card |
| `h3` | 1.25rem | 600 | Heading de modal/grupo |
| `body` | 1rem | 400 | Texto corrido |
| `body-small` | 0.875rem | 400 | Texto secundário |
| `caption` | 0.75rem | 500 | Tags, badges, rótulos |

Fonte: **Poppins** (Google Fonts) — weights 300, 400, 500, 600, 700.

### Sombras (Elevation)

| Token | Box Shadow | Uso |
|-------|-----------|-----|
| `card` | `0 1px 3px 0 rgb(0 0 0 / 0.1)` | Cards e painéis |
| `panel` | `0 4px 6px -1px rgb(0 0 0 / 0.1)` | Dropdowns, menus |
| `modal` | `0 20px 25px -5px rgb(0 0 0 / 0.1)` | Modais e overlays |

### Animação

| Keyframe | Duração | Uso |
|----------|---------|-----|
| `fadeIn` | 0.3s | Cards, alerts, notificações |
| `slideIn` | 0.3s | Sidebar, painéis laterais |
| `pulse-dot` | 1.5s | Status indicators (loading) |

### Cores de Status

| Status | Background | Texto |
|--------|-----------|-------|
| draft / cancelled | `#F3F4F6` | `#374151` / `#6B7280` |
| sent | `#DBEAFE` | `#1D4ED8` |
| viewed | `#F3E8FF` | `#7E22CE` |
| accepted / tx completed | `#DCFCE7` | `#15803D` |
| rejected / tx refunded | `#FEE2E2` | `#B91C1C` |
| tx pending | `#FEF3C7` | `#B45309` |
| tx processing | `#DBEAFE` | `#1D4ED8` |

---

## Modelo de Dados

**17 tabelas** no schema (`scripts/init.sql` + migrações incrementais em `scripts/migrations/`):

| Tabela | Finalidade |
|--------|-----------|
| `tenants` | Prestadores (raiz multi-tenant, com endereço — CEP, bairro, cidade, estado) |
| `users` | Usuários (admin do tenant + super_admin) |
| `clients` | Clientes do prestador (soft-delete via `active BOOLEAN`) |
| `categories` | Categorias de serviço (por tenant) |
| `services` | Serviços/produtos com preço e duração |
| `proposals` | Propostas mestre-detalhe (status lifecycle, número automático) |
| `proposal_items` | Itens da proposta (descrição, qtd, valor unitário) |
| `transactions` | Transações financeiras (via Mercado Pago) |
| `audit_log` | Auditoria LGPD |
| `public_leads` | Leads capturados na landing page pública |
| `lgpd_consent` | Consentimentos LGPD |
| `admin_audit_log` | Auditoria de ações administrativas |
| `workers` | Trabalhadores domésticos (com CBO, RG, CPF, data de nascimento) |
| `worker_certifications` | Certificações de workers |
| `service_schedules` | Agendamentos com controle de frequência |
| `worker_categories` | Categorias profissionais (9 tipos domésticos + extensível) |
| `plans` | Planos de assinatura (free/basic/pro/enterprise) com limites e features |

### Migrações

| # | Arquivo | Descrição |
|:-:|---------|-----------|
| 001 | `init.sql` | Schema completo + seed inicial |
| 002 | (removido) | Duplicava `transactions` — conflito com `init.sql` |
| 003 | `003_create_workers_tables.sql` | Workers, certifications, schedules |
| 004 | `004_create_worker_categories_table.sql` | 9 categorias + seed |
| 005 | `005_add_tenant_address.sql` | Address columns em tenants |
| 006 | `006_create_plans_table.sql` | Planos (free/basic/pro/enterprise) + seed |

Execute com: `make migrate` (executa init.sql + migrations em ordem).

---

## API Endpoints

### Autenticação

```
POST   /api/v1/auth/register              # Cadastro prestador + JWT (2 passos com endereço)
POST   /api/v1/auth/login                 # Login + JWT
GET    /api/v1/auth/me                    # Dados do usuário logado
POST   /api/v1/auth/forgot-password       # Solicitar reset de senha
POST   /api/v1/auth/reset-password        # Executar reset de senha
```

### Perfil do Prestador

```
GET    /api/v1/tenants/me                 # Perfil do tenant logado (inclui endereço, plano)
PUT    /api/v1/tenants/me                 # Atualizar perfil (nome, endereço via CEP, etc.)
```

### Negócio (protegido — requer JWT + tenant)

```
GET    /api/v1/clients                    # Listar clientes
POST   /api/v1/clients                    # Criar cliente
PUT    /api/v1/clients/:id                # Atualizar cliente
DELETE /api/v1/clients/:id                # Excluir (soft-delete)

GET    /api/v1/categories                 # Listar categorias (do tenant)
POST   /api/v1/categories                 # Criar categoria
PUT    /api/v1/categories/:id             # Atualizar categoria
DELETE /api/v1/categories/:id             # Excluir categoria

GET    /api/v1/services                   # Listar serviços/produtos
POST   /api/v1/services                   # Criar serviço
PUT    /api/v1/services/:id               # Atualizar serviço
DELETE /api/v1/services/:id               # Excluir serviço

GET    /api/v1/proposals                  # Listar propostas (filtros: status, cliente)
POST   /api/v1/proposals                  # Criar proposta mestre-detalhe
GET    /api/v1/proposals/:id              # Detalhes + itens
PUT    /api/v1/proposals/:id              # Atualizar proposta
DELETE /api/v1/proposals/:id              # Excluir
PATCH  /api/v1/proposals/:id/status       # Avançar status
GET    /api/v1/proposals/:id/pdf          # Baixar PDF (autenticado)

GET    /api/v1/transactions               # Histórico financeiro

GET    /api/v1/workers                    # Listar trabalhadores domésticos
POST   /api/v1/workers                    # Cadastrar trabalhador
GET    /api/v1/workers/:id                # Detalhes do trabalhador
PUT    /api/v1/workers/:id                # Atualizar trabalhador
DELETE /api/v1/workers/:id                # Excluir trabalhador
POST   /api/v1/workers/:id/certifications # Adicionar certificação
```

### Dashboard

```
GET    /api/v1/dashboard/chart            # Receita mensal (6 meses) para Chart.js
GET    /api/v1/dashboard/followup         # Propostas sent/viewed há >48h sem resposta
```

### Público (sem autenticação — rate limited)

```
GET    /api/v1/public/categories          # Categorias de todos tenants (com busca)
GET    /api/v1/public/services            # Busca serviços (?search=&category_id=&city=)
POST   /api/v1/public/leads               # Criar lead (rate limit: 5/min)
GET    /api/v1/public/proposals/:token     # Visualizar proposta pública
PATCH  /api/v1/public/proposals/:token/status  # Aprovar/rejeitar proposta
POST   /api/v1/public/proposals/:token/pay     # Pagar com Pix (Mercado Pago)
GET    /api/v1/public/proposals/:token/pdf     # Baixar PDF da proposta (público)
```

### Admin (super_admin)

```
GET    /api/v1/admin/dashboard            # KPIs globais (tenants, transactions, revenue)
GET    /api/v1/admin/tenants              # Listar todos tenants
PUT    /api/v1/admin/tenants/:id          # Editar tenant (suspender/reativar)
DELETE /api/v1/admin/tenants/:id          # Suspender/reativar tenant
GET    /api/v1/admin/transactions         # Transações cross-tenant
POST   /api/v1/admin/transactions/:id/refund  # Estorno de transação
GET    /api/v1/admin/audit                # Auditoria de ações administrativas
GET    /api/v1/admin/plans                # Listar planos
POST   /api/v1/admin/plans                # Criar plano
GET    /api/v1/admin/plans/:id            # Detalhes do plano
PUT    /api/v1/admin/plans/:id            # Atualizar plano
DELETE /api/v1/admin/plans/:id            # Excluir plano
GET    /api/v1/admin/reports/financial    # Relatório financeiro (?start_date=&end_date=&format=csv)
```

### Payments

```
POST   /api/v1/payments/create            # Criar preferência de pagamento MP
POST   /api/v1/payments/webhook           # Webhook Mercado Pago (IPN)
POST   /api/v1/payments/:id/refund        # Estornar transação
```

---

## Multi-Tenancy

O isolamento entre tenants é feito em nível de aplicação (AD-2):

1. Cada `tenant_id` é extraído do JWT no middleware `tenant.middleware.js`
2. Toda query SQL inclui `WHERE tenant_id = ?` (via `req.tenantFilter`)
3. Super admin (`role = super_admin`) tem bypass com `tenantFilter = '1=1'`
4. Tabelas de negócio possuem FK `tenant_id` com `ON DELETE CASCADE`

### Roles

| Role | Escopo | Acesso Admin |
|------|--------|-------------|
| `super_admin` | Todos os tenants | ✅ Painel admin completo (dashboard, tenants, planos, relatórios) |
| `admin` | Próprio tenant | ❌ |
| `viewer` | Próprio tenant (read-only) | ❌ (futuro) |

---

## Fluxo de Autenticação

```
                    ┌──────────────┐
                    │  Usuário     │
                    └──────┬───────┘
                           │ POST /auth/login
                           ▼
                    ┌──────────────┐
                    │  auth.routes │
                    └──────┬───────┘
                           │
                           ▼
                    ┌──────────────┐      ┌──────────────┐
                    │ auth.service  │─────▶│  users table  │
                    │ (bcrypt cmp)  │      └──────────────┘
                    └──────┬───────┘
                           │ JWT { id, tenant_id, role }
                           ▼
                    ┌──────────────┐
                    │  Cliente     │ ← JWT armazenado em cookie/sessionStorage
                    └──────┬───────┘
                           │
              ┌────────────┴────────────┐
              │                         │
              ▼                         ▼
    ┌──────────────────┐     ┌──────────────────┐
    │  tenantFilter    │     │  auth.middleware  │
    │  extraído do JWT │     │  verifica JWT     │
    └──────────────────┘     └──────────────────┘
              │                         
              ▼                         
    ┌──────────────────────────────────┐
    │  Toda query: WHERE tenant_id = ? │
    │  (req.tenantFilter)              │
    └──────────────────────────────────┘
```

---

## Docker Development

### Pré-requisitos

- Docker Desktop (Windows) ou Docker Engine (Linux/Mac)
- Git
- Node.js 20+ (opcional — para ferramentas locais)

### Setup rápido

```bash
# Clonar
git clone https://github.com/hsoservicos/servicogerais.git
cd servicos

# Configurar variáveis
cp .env.example .env
# Edite .env com suas credenciais

# Primeira execução (build + up)
make setup
```

### Comandos diários

| Comando | Descrição |
|---------|-----------|
| `make up` | Iniciar containers |
| `make down` | Parar containers |
| `make build` | Rebuild das imagens (--no-cache) |
| `make logs` | Logs de todos serviços |
| `make logs-api` | Logs apenas da API Node |
| `make php` | Shell no container PHP |
| `make api` | Shell no container Node |
| `make mysql` | CLI MySQL |
| `make migrate` | Executar init.sql manualmente |
| `make seed` | Popular dados de teste (seed.sql) |
| `make db-reset` | Resetar volume MySQL |
| `make npm-install` | npm install dentro do container API |
| `make npm-dev` | Nodemon watch mode dentro do container |
| `make health-all` | Verificar saúde de todos serviços |

### Acessos

| Serviço | URL | Descrição |
|---------|-----|-----------|
| **Aplicação** | http://localhost:8080 | Frontend PHP + API via Nginx |
| **phpMyAdmin** | http://localhost:8081 | Gerenciamento visual do MySQL |
| **API Health** | http://localhost:8080/api/v1/health | Status JSON da API |
| **Health** | http://localhost:8080/health | Status texto do PHP |

### Credenciais de Teste

| Papel | Email | Senha | Tenant |
|-------|-------|-------|--------|
| **Super Admin** | `admin@servicesaas.com` | `12345678` | Global |
| **Prestador** | `admin@maria.com` | `novaSenha123` | Maria Beleza (tenant_id=2) |

---

## Testes

```bash
make npm-install          # Instalar dependências (uma vez)
make api                  # Shell no container API
cd /usr/src/app && npm test  # Rodar testes Jest
```

Framework: **Jest + Supertest** (configurados com `--passWithNoTests` — aguardando implementação dos testes).

---

## Auditoria de Compliance

Relatório completo: `docs/auditoria/AUDITORIA_COMPLIANCE_DOMESTICO.md`

### Hall of Fame — Correções Realizadas

| # | Issue | Severidade | Status |
|:-:|-------|:----------:|:------:|
| 1 | Workers + CBO com 9 categorias domésticas | Crítica | ✅ |
| 2 | Schema duplicado de transactions (migration 002) | Média | ✅ Removido |
| 3 | Tenant isolation: services.read sem tenantFilter | Alta | ✅ |
| 4 | Propostas: category_id COALESCE sem join | Média | ✅ |
| 5 | WhatsApp prefix duplicado | Média | ✅ |
| 6 | Bug alias `t` no report financeiro admin | Alta | ✅ |
| 7 | Bug NOW() como alias em query MySQL | Média | ✅ |
| 8 | Encoding corrompido em categorias (latin1→utf8) | Média | ✅ |

---

## Roadmap

### Sprint 1 (concluído)
- Workers + CBO (CRUD completo com 9 categorias)
- Endereço no cadastro do prestador (via CEP)
- Perfil do prestador (tenant-profile com sidecar)
- Busca pública por município
- Correção de tenant isolation (2 queries)

### Sprint 2 (concluído)
- Epic 3 — Ciclo de Vida da Proposta (CRUD, frontend 1142 linhas, WhatsApp, aprovação pública, PDF)
- Bugfixes: tenantFilter, category_id COALESCE, WhatsApp prefix

### Sprint 3 (concluído)
- Epic 4 — Dashboard com Chart.js (gráfico 6 meses + follow-up)
- Epic 5 — Webhook MP + estorno
- Epic 6 — Presença pública + leads (já completo)
- Epic 7 — Admin planos CRUD + relatório financeiro com export CSV
- Design Audit UX (18 páginas analisadas, recomendações Ocean DS)
- Build Docker, README, push GitHub

### Próximos Sprints

| Sprint | Foco | Prioridade |
|:------:|------|:----------:|
| 4 | Frontend Admin PHP (páginas pendentes) | Alta |
| 5 | Trava de frequência + background check | Alta |
| 6 | Fluxo CLT + certificações | Alta |
| 7 | Ponto eletrônico com geolocalização | Média |
| 8 | eSocial Doméstico (admissão, DAE, FGTS) | Média |
| 9 | Incidentes, seguro, LGPD completo | Média |
| 10 | QA final, testes E2E, CI/CD | Alta |

---

## Estrutura do Projeto

```
servicos/
├── api-backend/                      # 🟢 API Node.js (Express)
│   ├── server.js                     # Entry point + registro de rotas
│   ├── config/                       # database.js, auth.js, mercadopago.js
│   ├── middlewares/                  # auth, tenant, error, requestId
│   ├── modules/                      # Domínios verticais
│   │   ├── auth/                     # Login, registro, JWT, forgot/reset
│   │   ├── tenants/                  # Perfil do prestador
│   │   ├── clients/                  # CRUD clientes
│   │   ├── catalog/                  # Categorias + Serviços
│   │   ├── proposals/                # Propostas mestre-detalhe
│   │   ├── dashboard/                # KPIs + gráficos
│   │   ├── payments/                 # Mercado Pago + webhook
│   │   ├── transactions/             # Histórico financeiro
│   │   ├── leads/                    # Painel de leads
│   │   ├── public/                   # Landing page
│   │   ├── admin/                    # Super admin
│   │   └── domestic/                 # Workers domésticos
│   ├── services/                     # Email, WhatsApp, PdfService
│   └── uploads/                      # Uploads
│
├── web-frontend/                     # 🟠 PHP Frontend
│   ├── public/index.php              # Roteador por query string
│   ├── config/app.php                # Helpers de sessão, token
│   └── templates/
│       ├── home.php                  # Landing page
│       ├── login.php                 # Login
│       ├── register.php              # Cadastro 2 passos
│       ├── dashboard.php             # Dashboard do prestador
│       ├── clients.php               # CRUD clientes
│       ├── categories.php            # CRUD categorias
│       ├── services.php              # CRUD serviços
│       ├── proposals.php             # Propostas
│       ├── leads.php                 # Painel de leads
│       ├── workers.php               # CRUD trabalhadores
│       ├── transactions.php          # Financeiro
│       ├── tenant-profile.php        # Perfil do prestador
│       ├── admin-planos.php          # CRUD planos (admin)
│       ├── admin-relatorios.php      # Relatório financeiro (admin)
│       ├── solicitar.php             # Wizard 3 passos
│       ├── public-proposal.php       # Proposta pública
│       └── partials/                 # Sidebar, topbar, header, footer
│
├── scripts/                          # 🔵 Banco de Dados
│   ├── init.sql                      # Schema completo + seed
│   ├── seed.sql                      # Dados de teste
│   └── migrations/                   # Migrações incrementais
│
├── nginx/                            # ⚪ Configuração do Proxy
│   └── default.conf
│
├── docs/                             # 📚 Documentação
│   ├── planning/PLANEJAMENTO_MODERNO_PROJETO.md
│   ├── auditoria/AUDITORIA_COMPLIANCE_DOMESTICO.md
│   └── prd/
│
├── _bmad-output/                     # 📐 Artefatos BMad
│   └── planning-artifacts/
│       ├── epics.md
│       ├── architecture/.../
│       └── ux-designs/.../DESIGN.md + EXPERIENCE.md
│
├── docker-compose.yml                # 5 serviços
├── Makefile                          # Comandos auxiliares
├── .env.example                      # Template de variáveis
├── AGENTS.md                         # Contexto para agentes AI
└── README.md                         # Este documento
```

---

## Documentação Principal

| Documento | Localização |
|-----------|-------------|
| 🥇 Plano Estratégico | `docs/planning/PLANEJAMENTO_MODERNO_PROJETO.md` |
| 🏛️ Architecture Spine | `_bmad-output/planning-artifacts/architecture/ARCHITECTURE-SPINE.md` |
| 📐 Épicos e Stories | `_bmad-output/planning-artifacts/epics.md` |
| 🗓️ Sprint Plan | `_bmad-output/implementation-artifacts/sprint-plan.md` |
| 🎨 Design System | `_bmad-output/planning-artifacts/ux-designs/ux-servicos-20260728/DESIGN.md` |
| 🎨 Design Audit | `_bmad-output/planning-artifacts/ux-designs/ux-servicos-20260728/DESIGN-AUDIT.md` |
| 🧭 UX Experience | `_bmad-output/planning-artifacts/ux-designs/ux-servicos-20260728/EXPERIENCE.md` |
| 📋 Auditoria Compliance | `docs/auditoria/AUDITORIA_COMPLIANCE_DOMESTICO.md` |
| 🤖 Contexto para AI | `AGENTS.md` |

---

## Variáveis de Ambiente

Copie `.env.example` para `.env` e preencha:

| Variável | Obrigatório | Descrição |
|----------|:-----------:|-----------|
| `MYSQL_ROOT_PASSWORD` | ✅ | Senha root MySQL |
| `MYSQL_DATABASE` | ✅ | Nome do banco (`servicos_flex`) |
| `JWT_SECRET` | ✅ | Chave secreta para assinar JWT |
| `MP_ACCESS_TOKEN` | ❌ | Token Mercado Pago (modo degradado sem ele) |
| `CLOUDFLARE_TUNNEL_TOKEN` | ❌ | Token Cloudflare Tunnel (produção) |

---

## Licença

Projeto privado — todos os direitos reservados.

---

<p align="center">
  <strong>ServiceSaaS</strong> · Gestão Inteligente para Prestadores de Serviços<br>
  <sub>Construído com Docker · Node.js · PHP · MySQL · Tailwind · Chart.js · Mercado Pago</sub>
</p>
