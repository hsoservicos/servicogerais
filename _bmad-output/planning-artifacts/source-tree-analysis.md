# 🌳 ServiceSaaS — Análise da Árvore de Código-Fonte

**Documentado por:** Paige (Technical Writer)
**Data:** 28 de Julho de 2026

---

## 1. Estrutura Completa (Comentada)

```
D:\projeto\servicos\
│
├── 📁 .claude/skills/               ← 54 skills BMad Method (automação de planning/dev)
│   ├── bmad-agent-*/                ← Agentes (PM, Analyst, Architect, Tech Writer, UX, Dev)
│   ├── bmad-architecture/           ← Workflow de arquitetura
│   ├── bmad-create-epics-and-stories/ ← Workflow de épicos e stories
│   ├── bmad-dev-auto/               ← Workflow de desenvolvimento automatizado
│   ├── bmad-document-project/       ← Workflow de documentação
│   └── ...                          ← +48 skills adicionais
│
├── 📁 _bmad/                        ← Configuração BMad Method
│   ├── bmm/config.yaml             ← Config principal (user_name, language, paths)
│   ├── core/config.yaml            ← Config core
│   ├── tea/config.yaml             ← Config testarch
│   └── _config/manifest.yaml       ← Manifesto de skills instalados
│
├── 📁 _bmad-output/                 ← Artefatos gerados (⭐ DOCUMENTAÇÃO)
│   └── planning-artifacts/
│       ├── 📄 index.md              ← [NOVO] Portal de documentação
│       ├── 📄 project-overview.md   ← [NOVO] Visão geral
│       ├── 📄 source-tree-analysis.md ← [NOVO] Esta análise
│       ├── 📄 architecture.md       ← [NOVO] Arquitetura detalhada
│       ├── 📄 development-guide.md  ← [NOVO] Guia de desenvolvimento
│       ├── 📄 epics.md              ← 7 épicos, 22 stories
│       ├── 📄 validation-report-prds.md ← Validação de PRDs
│       ├── 📁 architecture/         ← Architecture Spine formal
│       ├── 📁 ux-designs/           ← DESIGN.md + EXPERIENCE.md
│       └── 📁 research/             ← Pesquisas (MP, Regulatório, Scan)
│
├── 📁 api-backend/                  ← ⭐ API NODE.JS (6 módulos)
│   │
│   ├── 📄 server.js                 ← Entry point (app Express)
│   ├── 📄 Dockerfile                ← node:20-alpine
│   ├── 📄 package.json              ← Dependências (express, mysql2, bcrypt, jwt...)
│   │
│   ├── 📁 config/                   ← Configurações
│   │   ├── database.js              ← Pool MySQL (mysql2)
│   │   └── auth.js                  ← JWT secret + expiração 24h
│   │
│   ├── 📁 middlewares/              ← 4 middlewares globais
│   │   ├── auth.middleware.js        ← Valida JWT (Authorization: Bearer)
│   │   ├── tenant.middleware.js     ← Isolamento multi-tenant
│   │   ├── requestId.middleware.js  ← Correlation ID tracking
│   │   └── error.middleware.js      ← Tratamento global de erros
│   │
│   ├── 📁 modules/                  ← 6 módulos de domínio
│   │   │
│   │   ├── 📁 auth/                 ← Autenticação (✅ Completo)
│   │   │   ├── auth.controller.js   ← Register, Login, Forgot/Reset
│   │   │   └── auth.routes.js       ← POST /auth/*
│   │   │
│   │   ├── 📁 clients/              ← Clientes (✅ Completo)
│   │   │   ├── clients.controller.js ← CRUD + WhatsApp
│   │   │   └── clients.routes.js    ← GET/POST/PUT/DELETE /clients
│   │   │
│   │   ├── 📁 catalog/              ← Catálogo (✅ Completo)
│   │   │   ├── categories.controller.js ← CRUD categorias
│   │   │   ├── categories.routes.js
│   │   │   ├── services.controller.js   ← CRUD serviços/produtos
│   │   │   └── services.routes.js
│   │   │
│   │   ├── 📁 proposals/            ← Propostas (✅ Completo, 12/12 testes)
│   │   │   ├── proposals.controller.js ← CRUD + state machine
│   │   │   ├── proposals.routes.js
│   │   │   ├── items.controller.js     ← CRUD itens + recálculo
│   │   │   └── items.routes.js         ← Nested /:proposalId/items
│   │   │
│   │   ├── 📁 dashboard/            ← Dashboard (✅ Completo)
│   │   │   ├── dashboard.controller.js ← KPIs agregados
│   │   │   └── dashboard.routes.js
│   │   │
│   │   └── 📁 admin/                ← Admin (🟡 Parcial)
│   │       ├── admin.controller.js   ← Dashboard + Tenants (OK)
│   │       ├── admin.middleware.js   ← super_admin check
│   │       └── admin.routes.js       ← Rotas (transactions/refund comentados)
│   │
│   └── 📁 services/
│       └── email.service.js         ← Email stub (console.log)
│
├── 📁 web-frontend/                 ← ⭐ FRONTEND PHP (12 templates)
│   │
│   ├── 📄 Dockerfile                ← php:8.2-fpm-alpine
│   ├── 📁 config/
│   │   └── app.php                  ← Config
│   ├── 📁 public/
│   │   ├── index.php                ← Roteador central
│   │   └── 📁 js/
│   │       └── tailwind.config.js   ← Design tokens
│   └── 📁 templates/                ← 12 templates PHP
│       ├── 📁 partials/
│       │   ├── header.php           ← Sidebar + Topbar + Nav
│       │   └── footer.php           ← Scripts + modais base
│       ├── home.php                 ← Landing Page
│       ├── login.php                ← Login
│       ├── register.php             ← Cadastro PF/PJ
│       ├── dashboard.php            ← Dashboard KPIs
│       ├── clients.php              ← CRUD Clientes
│       ├── categories.php           ← CRUD Categorias
│       ├── services.php             ← CRUD Serviços
│       ├── proposals.php            ← CRUD Propostas
│       ├── forgot-password.php      ← Recuperar senha
│       └── reset-password.php       ← Redefinir senha
│
├── 📁 nginx/                        ← Proxy reverso
│   └── default.conf                 ← PHP + API + Assets + PMA
│
├── 📁 scripts/                      ← Banco de dados
│   ├── init.sql                     ← Schema completo (7 tabelas)
│   ├── seed.sql                     ← Dados de exemplo (Maria Beleza)
│   └── 📁 migrations/
│       └── 001_add_reset_token_to_users.sql
│
├── 📁 docs/                         ← Documentação do projeto
│   ├── 📁 planning/
│   │   └── PLANEJAMENTO_MODERNO_PROJETO.md  ← PRD principal
│   ├── 📁 lgpd/                     ← 7 documentos de compliance
│   ├── 📁 issues/
│   │   └── ISSUES_PROXIMA_SESSAO.md ← Issues para próxima sessão
│   ├── 📁 admin/
│   │   └── PLANEJAMENTO_ADMIN.md    ← Planejamento da área admin
│   └── 📁 prd/                      ← PRDs arquivados
│
├── 📁 layout/                       ← HTML prototypes
│   └── stitch_saas_project_architect/
│       ├── landing_page_servi_os_flex/code.html
│       ├── dashboard_in_cio/code.html
│       ├── nova_proposta/code.html
│       ├── admin_platform/code.html
│       └── solicitar_servi_o_clientes/code.html
│
├── 📄 docker-compose.yml            ← 5 containers
├── 📄 Makefile                      ← Comandos (setup, seed, logs)
├── 📄 .env                          ← Variáveis de ambiente
├── 📄 .env.example                  ← Template de env vars
└── 📄 README.md                     ← Instruções básicas
```

---

## 2. Estatísticas por Diretório

| Diretório | Arquivos | Descrição |
|:---|---:|:---|
| `api-backend/` | ~18 | Código-fonte Node.js |
| `web-frontend/` | ~15 | Templates PHP + config |
| `scripts/` | ~4 | SQL + migrações |
| `nginx/` | ~1 | Configuração |
| `docs/` | ~14 | Documentação + LGPD |
| `_bmad-output/` | ~10 | Artefatos de planejamento |
| `layout/` | ~6 | Protótipos HTML |
| Root | ~5 | Docker, Makefile, .env |

---

## 3. Arquivos por Tipo

| Tipo | Extensão | Quantidade Aprox. |
|:---|---:|:---:|
| JavaScript | `.js` | ~20 |
| PHP | `.php` | ~14 |
| Markdown | `.md` | ~25 |
| HTML | `.html` | ~7 |
| SQL | `.sql` | ~3 |
| YAML/JSON | `.yaml/.json` | ~8 |
| Config | `.conf`, `Dockerfile`, `Makefile` | ~6 |

---

## 4. Pontos de Entrada Principais

| Ponto de Entrada | Localização | Função |
|:---|---|:---|
| Frontend | `web-frontend/public/index.php` | Roteador da aplicação web |
| API | `api-backend/server.js` | Servidor Express |
| Database | `scripts/init.sql` | Schema inicial |
| Infra | `docker-compose.yml` | Orquestração de containers |
| Config Global | `_bmad/bmm/config.yaml` | Configuração BMad |

---

## 5. Arquivos por Complexidade

### 🔴 Alta Complexidade (>200 linhas)

| Arquivo | Linhas | Módulo |
|:---|---:|:---|
| `proposals.php` | ~700 | Frontend |
| `proposals.controller.js` | ~450 | API |
| `dashboard.php` | ~400 | Frontend |
| `proposals.controller.js` | ~350 | API |
| `clients.php` | ~350 | Frontend |

### 🟡 Média Complexidade (100-200 linhas)

| Arquivo | Módulo |
|:---|---:|
| `auth.controller.js` | API Auth |
| `services.php` | Frontend |
| `categories.php` | Frontend |
| `register.php` | Frontend |
| `login.php` | Frontend |

### 🟢 Baixa Complexidade (<100 linhas)

| Arquivo | Módulo |
|:---|---:|
| Todos os middlewares | API |
| `admin.middleware.js` | API Admin |
| `email.service.js` | API Services |
| `forgot-password.php` | Frontend |
| `reset-password.php` | Frontend |

---

*Documento gerado por Paige (Technical Writer) em 28 de Julho de 2026*
