# 📋 Documentação do Projeto: ServiceSaaS (Serviços Flex)

**Data:** 28 de Julho de 2026
**Autora:** Mary (Business Analyst)
**Versão do Documento:** 1.0
**Tipo:** Full Project Scan

---

## 1. Identidade do Projeto

| Campo | Valor |
|:---|---|
| **Nome** | ServiceSaaS (Serviços Flex) |
| **Natureza** | Plataforma SaaS multi-tenant para prestadores de serviços |
| **Stack** | PHP 8.2 (frontend) + Node.js 20 (API) + MySQL 8.0 |
| **Infra** | Docker Compose (5 containers) + Nginx |
| **Exposição** | Cloudflare Tunnel (planejado) |
| **Pagamentos** | Mercado Pago (planejado — Epic 5) |

---

## 2. Estrutura de Diretórios (Organizada)

```
D:\projeto\servicos\
├── .claude/skills/          ← 54 BMad skills instalados
├── _bmad/                   ← Configuração do BMad Method
│   ├── bmm/config.yaml
│   ├── core/config.yaml
│   ├── tea/config.yaml
│   └── _config/manifest.yaml
├── _bmad-output/            ← Artefatos de planejamento
│   └── planning-artifacts/
│       ├── architecture/    ← Architecture Spine
│       ├── ux-designs/      ← DESIGN.md + EXPERIENCE.md
│       ├── epics.md         ← 7 épicos, 22 stories
│       ├── research/        ← Pesquisas (MP + Regulatório)
│       └── validation-report-prds.md
├── api-backend/             ← API Node.js (Express)
│   ├── config/              ← database.js, auth.js
│   ├── middlewares/         ← auth, error, requestId, tenant
│   ├── modules/             ← admin, auth, catalog, clients, dashboard, proposals
│   ├── services/            ← email.service.js
│   ├── server.js            ← Entry point
│   └── Dockerfile
├── web-frontend/            ← Frontend PHP
│   ├── config/              ← app.php
│   ├── public/              ← index.php, js/tailwind.config.js
│   ├── templates/           ← 12 templates PHP
│   │   ├── partials/        ← header.php, footer.php
│   │   └── home, login, register, dashboard, clients,
│   │       categories, services, proposals,
│   │       forgot-password, reset-password
│   └── Dockerfile
├── docs/                    ← Documentação
│   ├── admin/               ← PLANEJAMENTO_ADMIN.md
│   ├── issues/              ← ISSUES_PROXIMA_SESSAO.md
│   ├── lgpd/                ← 7 documentos LGPD
│   ├── planning/            ← PLANEJAMENTO_MODERNO_PROJETO.md
│   └── prd/                 ← PRDs arquivados
├── nginx/                   ← default.conf
├── scripts/                 ← init.sql, seed.sql, migrations/
├── layout/                  ← HTML designs (5 layouts)
├── docker-compose.yml
├── Makefile
├── .env / .env.example
└── README.md
```

---

## 3. Backend API (Node.js 20 + Express)

### 3.1. Arquitetura

```
api-backend/
├── config/
│   ├── database.js      ← Pool MySQL (mysql2)
│   └── auth.js          ← JWT config
├── middlewares/
│   ├── auth.middleware.js       ← JWT validation (Authorization: Bearer)
│   ├── tenant.middleware.js     ← Multi-tenancy injection
│   ├── requestId.middleware.js  ← Correlation ID (X-Request-ID)
│   └── error.middleware.js      ← Global error handler
├── modules/
│   ├── auth/                    ← Register + Login + Forgot/Reset Password
│   ├── clients/                 ← CRUD Clientes
│   ├── catalog/                 ← Categories + Services
│   ├── proposals/               ← Proposals + Items (mestre-detalhe)
│   ├── admin/                   ← Super admin dashboard + tenants
│   └── dashboard/               ← KPIs agregados
└── services/
    └── email.service.js   ← Stub (console.log)
```

### 3.2. Módulos por Status

| Módulo | Controller | Routes | Status | Observação |
|:---|---|:---:|:---:|:---|
| **auth** | auth.controller.js | auth.routes.js | ✅ Completo | Register, Login, Forgot/Reset Password |
| **clients** | clients.controller.js | clients.routes.js | ✅ Completo | CRUD + WhatsApp |
| **catalog** | categories.controller.js + services.controller.js | categories.routes.js + services.routes.js | ✅ Completo | CRUD Categorias + Serviços |
| **proposals** | proposals.controller.js + items.controller.js | proposals.routes.js + items.routes.js | ✅ Completo | State machine + itens |
| **dashboard** | dashboard.controller.js | dashboard.routes.js | ✅ Completo | KPIs agregados |
| **admin** | admin.controller.js | admin.routes.js | 🟡 Parcial | Dashboard + Tenants OK; Transactions/Refund pendentes |

### 3.3. Configuração e Conexão

```javascript
// Config: auth.js
module.exports = {
  jwt: { secret: process.env.JWT_SECRET, expiresIn: '24h' },
  bcrypt: { rounds: 12 },
};

// Config: database.js
const pool = mysql.createPool({
  host: process.env.DB_HOST || 'mysql',
  user: process.env.DB_USER || 'servicos',
  password: process.env.DB_PASS || 'servicos_pass',
  database: process.env.DB_NAME || 'servicos_flex',
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0,
});
```

---

## 4. Frontend PHP (8.2)

### 4.1. Roteamento

```php
// web-frontend/public/index.php — Roteador central
$allowedPages = [
  'home' => 'Home',
  'login' => 'Login',
  'register' => 'Cadastro',
  'dashboard' => 'Dashboard',
  'clients' => 'Clientes',
  'categories' => 'Categorias',
  'services' => 'Serviços',
  'proposals' => 'Propostas',
  'forgot-password' => 'Recuperar Senha',
  'reset-password' => 'Redefinir Senha',
];
```

### 4.2. Templates

| Template | Funcionalidade | Status |
|:---|---|:---:|
| `home.php` | Landing Page | ✅ Completo |
| `login.php` | Login | ✅ Completo |
| `register.php` | Cadastro PF/PJ | ✅ Completo |
| `dashboard.php` | Dashboard com KPIs + Sidebar | ✅ Completo |
| `clients.php` | CRUD Clientes | ✅ Completo |
| `categories.php` | CRUD Categorias | ✅ Completo |
| `services.php` | CRUD Serviços | ✅ Completo |
| `proposals.php` | CRUD Propostas + Itens + Status | ✅ Completo |
| `forgot-password.php` | Solicitar recuperação | ✅ Completo |
| `reset-password.php` | Redefinir senha | ✅ Completo |
| `partials/header.php` | Header + Sidebar | ✅ Completo |
| `partials/footer.php` | Footer + Scripts | ✅ Completo |

### 4.3. Design System

| Componente | Framework/style | Status |
|:---|---|:---:|
| Tailwind CSS | CDN (`cdn.tailwindcss.com`) | ✅ Completo |
| Cor primária | `#10B981` (verde esmeralda) | ✅ Definida |
| Tipografia | Poppins (Google Fonts) | ✅ Completo |
| Sidebar | 260px, fundo `#0F172A` | ✅ Completo |
| Topbar | 64px, avatar + dropdown | ✅ Completo |
| Botões | Primary, Secondary, WhatsApp | ✅ Completo |
| Badges | success, warning, info, danger | ✅ Completo |
| Cards KPI | shadow, ícone + valor + label | ✅ Completo |
| Modais | Create/Edit/View/Delete | ✅ Completo |
| Toast | Sucesso/Erro (auto-dismiss) | ✅ Completo |

---

## 5. Infraestrutura

### 5.1. Docker Compose (5 containers)

| Serviço | Imagem | Porta | Depende de |
|:---|---|:---:|:---|
| **nginx** | nginx:1.25-alpine | 8080 → 80 | php |
| **php** | php:8.2-fpm-alpine | 9000 | mysql |
| **api** | node:20-alpine | 3000 | mysql |
| **pma** | phpmyadmin:latest | 8081 | mysql |
| **mysql** | mysql:8.0 | 3306 | — |

### 5.2. Nginx

```nginx
# nginx/default.conf — Principais regras:
# - PHP: proxy pass para php:9000
# - API: proxy pass para api:3000 (/api/v1/)
# - Assets: cache público 1 ano
# - PMA: proxy pass para pma:80 (/pma/)
```

### 5.3. Banco de Dados

**Tabelas criadas pelo `init.sql`:**

```
tenants          ← Prestadores (PF/PJ)
users            ← Usuários
clients          ← Clientes dos prestadores
categories       ← Categorias de serviços
products_services ← Serviços/Produtos
proposals        ← Propostas (mestre)
proposal_items   ← Itens da proposta (detalhe)
transactions     ← Tabela MENCIONADA no código mas NÃO CRIADA
```

**Migrations:**

| Migration | Status |
|:---|---|
| `001_add_reset_token_to_users.sql` | ✅ Aplicada |

**Seed:**

| Script | Descrição | Status |
|:---|---|:---:|
| `seed.sql` | Maria Beleza + clientes + categorias + serviços | ✅ Completo |

---

## 6. Banco de Dados — Schema Detalhado

### 6.1. Tabelas Existentes

```sql
tenants (id, name, slug, document_cpf, document_cnpj, phone, whatsapp,
         logo_url, active, plan, settings, created_at, updated_at)

users (id, tenant_id, name, email, password_hash, role, active,
       reset_token, reset_token_expires, last_login, created_at, updated_at)

clients (id, tenant_id, name, document_cpf, document_cnpj, email, phone,
         whatsapp, address, notes, active, created_at, updated_at)

categories (id, tenant_id, name, description, icon, color, active,
            created_at, updated_at)

products_services (id, tenant_id, category_id, name, description, type,
                   price, duration_minutes, active, created_at, updated_at)

proposals (id, tenant_id, client_id, number, title, description,
           status, total_amount, valid_until, payment_terms, notes,
           sent_at, accepted_at, created_at, updated_at)

proposal_items (id, proposal_id, tenant_id, product_service_id,
                description, quantity, unit_price, total_price, sort_order)
```

### 6.2. Tabela Faltante: `transactions`

**Status:** 🔴 **A tabela `transactions` NÃO EXISTE no `init.sql`**, mas é referenciada em:
- `admin.controller.js` (SELECT + SUM)
- `tenant.middleware.js` (incluída na lista de tabelas com tenant_id)

---

## 7. State Machine — Propostas

```
┌─────────┐    enviar     ┌──────────┐    visualizar    ┌──────────┐
│  draft   │ ───────────→ │   sent   │ ───────────────→ │  viewed  │
└────┬─────┘              └────┬─────┘                  └────┬─────┘
     │                         │                             │
     │ cancelar                │  ┌──────────────────────────┘
     ↓                         ↓  ↓
┌─────────┐              ┌─────────────┐         ┌────────────┐
│cancelled│              │  accepted   │ ←────── │  rejected  │
└─────────┘              └──────┬──────┘         └────────────┘
                                │
                           ┌────┴─────┐
                           │   paid   │  (via Mercado Pago)
                           └──────────┘
```

---

## 8. Documentação de Compliance

### 8.1. Documentos LGPD (7 arquivos)

| Documento | Arquivo | Status |
|:---|---|:---:|
| Termos de Uso + DPA | `docs/lgpd/termos-de-uso.html` | ✅ Completo |
| Termos de Uso (Markdown) | `docs/lgpd/termos-de-uso.md` | ✅ Completo |
| Política de Privacidade | `docs/lgpd/politica-privacidade.md` | ✅ Completo |
| Registro de Operações | `docs/lgpd/registro-operacoes.md` | ✅ Completo |
| Política de Retenção | `docs/lgpd/politica-retencao.md` | ✅ Completo |
| Plano de Resposta a Incidentes | `docs/lgpd/plano-resposta-incidentes.md` | ✅ Completo |
| Checklist Privacy by Design | `docs/lgpd/checklist-privacy-by-design.md` | ✅ Completo |

### 8.2. Planejamento

| Documento | Arquivo | Status |
|:---|---|:---:|
| Planejamento Moderno | `docs/planning/PLANEJAMENTO_MODERNO_PROJETO.md` | ✅ Principal |
| PRD Cloudflare/Docker | `docs/prd/prd_servicos_flex_cloudflare_docker.md` | 📦 Arquivado |
| PRD PHP/Node.js | `docs/prd/prd_servicos_flex_php_nodejs.md` | 📦 Arquivado |
| Service Design | `docs/prd/service.md` | 📦 Arquivado |
| Issues Próxima Sessão | `docs/issues/ISSUES_PROXIMA_SESSAO.md` | ✅ Ativo |
| Planejamento Admin | `docs/admin/PLANEJAMENTO_ADMIN.md` | ✅ Ativo |

---

## 9. Artefatos BMad

### 9.1. Planning Artifacts

| Artefato | Caminho | Descrição |
|:---|---|:---|
| Architecture Spine | `_bmad-output/planning-artifacts/architecture/.../ARCHITECTURE-SPINE.md` | Decisões arquiteturais |
| UX Design | `_bmad-output/planning-artifacts/ux-designs/.../DESIGN.md` | Design tokens e componentes |
| UX Experience | `_bmad-output/planning-artifacts/ux-designs/.../EXPERIENCE.md` | Flows e superfícies |
| Epics + Stories | `_bmad-output/planning-artifacts/epics.md` | 7 épicos, 22 stories |
| Pesquisa MP | `_bmad-output/planning-artifacts/research/technical-mercado-pago-integration-research.md` | 🔬 Nova |
| Pesquisa Regulatória | `_bmad-output/planning-artifacts/research/domain-regulamentacao-profissionais-servicos-brasil-research.md` | 🔬 Nova |
| Validation PRDs | `_bmad-output/planning-artifacts/validation-report-prds.md` | Report de validação |

### 9.2. Layouts HTML

| Layout | Status |
|:---|---|
| Landing Page | `layout/stitch_saas_project_architect/landing_page_servi_os_flex/code.html` |
| Dashboard | `layout/stitch_saas_project_architect/dashboard_in_cio/code.html` |
| Nova Proposta | `layout/stitch_saas_project_architect/nova_proposta/code.html` |
| Admin Platform | `layout/stitch_saas_project_architect/admin_platform/code.html` |
| Solicitar Serviço | `layout/stitch_saas_project_architect/solicitar_servi_o_clientes/code.html` |

---

## 10. Epics e Stories (Resumo)

| Épico | Stories | Status | Observação |
|:---:|:---:|:---:|:---|
| **Epic 1** 🔐 Onboarding | 5 (1.1 a 1.5) | ✅ Documentado | Setup, Design System, PF/PJ, Login, Password |
| **Epic 2** 👥 Clientes | Pendente | 📝 A criar | CRUD Clientes + Catálogo |
| **Epic 3** 📄 Propostas | Pendente | 📝 A criar | Ciclo de vida completo |
| **Epic 4** 📊 Dashboard | 4 (4.1 a 4.4) | ✅ Documentado | KPIs, Chart, Follow-up, Transações |
| **Epic 5** 💳 Pagamentos | 5 (5.1 a 5.5) | ✅ Documentado | MP Setup, Preferência, Webhook, Pix, Estorno |
| **Epic 6** 🌐 Presença | 4 (6.1 a 6.4) | ✅ Documentado | LP, Wizard, Proposta Pública, CTA |
| **Epic 7** 🏢 Admin | 4 (7.1 a 7.4) | ✅ Documentado | Auth, Tenants, Financeiro, Auditoria |

**Total:** 22 stories documentadas (Epics 2 e 3 pendentes de criação de stories)

---

## 11. Pesquisas Realizadas (Nesta Sessão)

| Pesquisa | Tipo | Arquivo | Achados Principais |
|:---|:---:|:---|---|
| Integração Mercado Pago | 🔬 Técnica | `research/technical-...md` | HMAC obrigatório, transactions table faltante, Pix Bricks recomendado |
| Regulamentação Brasil | 🔍 Domínio | `research/domain-...md` | MEI 81k, NFSe obrigatória para PJ, ATPP prazo dobrado 144h |

---

## 12. Arquivos por Categoria

### 12.1. Total de Arquivos

| Categoria | Quantidade |
|:---|---:|
| **Código** (JS, PHP, SQL, HTML, CSS) | ~30 |
| **Infra** (Docker, Nginx, YML, Makefile) | ~6 |
| **Documentação** (MD, HTML) | ~18 |
| **Layout/Design** (HTML, MD) | ~7 |
| **BMad Skills** (SKILL.md) | 54 |
| **Artefatos BMad** | ~8 |
| **Config** (JSON, YAML, TOML) | ~6 |

### 12.2. Tecnologias Presentes

| Tecnologia | Onde | Versão |
|:---|---|:---:|
| Node.js | API Backend | 20 LTS |
| Express.js | API Framework | 4.x |
| PHP | Frontend | 8.2 |
| MySQL | Database | 8.0 |
| Nginx | Proxy reverso | 1.25-alpine |
| Docker | Container | Compose 3.8+ |
| mysql2 | Driver MySQL Node | 3.x |
| bcrypt | Password hashing | 5.x |
| jsonwebtoken | JWT auth | 9.x |
| Tailwind CSS | Frontend styling | CDN |
| Chart.js | Dashboard charts | 4.x (planejado) |
| Mercado Pago | Pagamentos | SDK 2.x (planejado) |

---

## 13. Riscos Técnicos Identificados

| Risco | Impacto | Probabilidade | Mitigação |
|:---|---|:---:|:---|
| Tabela `transactions` não criada | 🔴 ALTO | 100% | Criar migration antes de implementar Epic 5 |
| Webhook sem validação HMAC | 🔴 ALTO | 100% | Adicionar validação no handler |
| CDN Tailwind em produção | 🟡 MÉDIO | 100% | Migrar para build local |
| Seed SQL com hash bcrypt hardcoded | 🟡 MÉDIO | 100% | Regenerar hash na primeira execução |
| Sem testes automatizados | 🟡 MÉDIO | 100% | Jest configurado mas sem testes |
| Sem pipeline CI/CD | 🟢 BAIXO | 100% | GitHub Actions planejado |

---

*Documentação gerada por Mary (Business Analyst) em 28 de Julho de 2026*
