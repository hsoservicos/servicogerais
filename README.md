# 🚀 ServiceSaaS — Gestão Inteligente para Prestadores de Serviços

> Plataforma SaaS multi-tenant para prestadores de serviços gerenciarem clientes, propostas, pagamentos e trabalhadores domésticos com compliance legal (LC 150/2015).

---

## 🧭 Visão Geral

**ServiceSaaS** (Serviços Flex) é uma plataforma completa para prestadores de serviços profissionais e domésticos. Oferece cadastro multi-tenant, catálogo de serviços, propostas mestre-detalhe, pagamentos via Mercado Pago, dashboard de KPIs, captura pública de leads e gestão de trabalhadores domésticos com suporte a eSocial e controle de frequência.

### Público-Alvo

- **Prestadores de serviços** (autônomos, MEIs, pequenas empresas) — gerenciam clientes, serviços, propostas e finanças
- **Clientes finais** — buscam serviços próximos e solicitam orçamentos via landing page pública
- **Trabalhadores domésticos** — diaristas, babás, cuidadores, motoristas etc., com enquadramento LC 150

---

## ✨ Funcionalidades

### Módulos Core

| Módulo | Funcionalidades |
|--------|----------------|
| **Auth Multi-Tenant** | Registro de prestador (2 passos), login JWT, recuperação de senha |
| **Clientes** | CRUD com busca, soft-delete, dados de contato e endereço |
| **Catálogo** | Categorias + Serviços/Produtos com preço e duração |
| **Propostas** | Mestre-detalhe com itens, status workflow (draft→sent→viewed→accepted→paid), PDF automático, WhatsApp, aprovação pública |
| **Dashboard** | KPIs agregados, gráfico financeiro Chart.js (6 meses), follow-up de propostas pendentes |
| **Financeiro** | Transações, estorno via Mercado Pago, resumo por status |
| **Leads Públicos** | Captura de leads via wizard 3 passos na landing page |
| **Admin Plataforma** | Super admin com visão global (dashboard, tenants, transações, auditoria, planos CRUD, relatório financeiro com export CSV) |

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

### Ciclo de Vida da Proposta (Epic 3)

| Funcionalidade | Status | Detalhes |
|--------|--------|------------|
| CRUD Propostas mestre-detalhe | ✅ Completo | Itens vinculados, números automáticos, status workflow |
| Frontend Propostas | ✅ Completo | 1142 linhas, filtros tabs, modal create/edit, view modal |
| WhatsApp | ✅ Completo | Link wa.me com template + link público copiável |
| Aprovação Pública | ✅ Completo | Landing page por token, aprovar/rejeitar, PDF público |
| PDF | ✅ Completo | pdfkit, cabeçalho/tabela/totais, endpoints auth + público, botões frontend |

### Perfil do Prestador & Proximidade

- **Endereço no cadastro** — Prestador informa CEP, endereço, bairro, cidade e estado
- **Página "Meu Perfil"** — Edição de dados cadastrais com sidecar (plano, status)
- **Busca por município** — Clientes encontram serviços próximos via `?city=` na landing page

---

## 🏗️ Arquitetura

```
┌──────────────────────────────────────────────┐
│            🌐 Nginx 1.25 (Reverse Proxy)      │
│         localhost:8080 → PHP :9000            │
│         /api/* → Node.js :3000                │
├──────────┬───────────────────┬───────────────┤
│  🖥️ PHP  │   ⚙️ Node.js API  │  🗄️ MySQL    │
│  8.2 FPM  │   20 LTS + Express│  8.0          │
│  Templates│   Modular routes  │  utf8mb4      │
│  Tailwind │   JWT auth        │  Multi-tenant │
│  Chart.js │   Mercado Pago    │  FK cascade   │
└──────────┴───────────────────┴───────────────┘
```

### Decisões Arquiteturais (ADs)

| AD | Decisão | Status |
|:---|:--------|:-------|
| AD-1 | JWT como única fonte de verdade de autenticação | ✅ |
| AD-2 | Multi-tenancy via injeção de `tenant_id` em toda query SQL | ✅ |
| AD-3 | Endpoints agregados para dashboards (evita N chamadas) | ✅ |
| AD-4 | Propostas atômicas mestre-detalhe em transação MySQL | ✅ |
| AD-5 | Pagamentos idempotentes com Mercado Pago | ✅ |
| AD-6 | PDF via pdfkit (sem Puppeteer pesado) | ✅ |
| AD-7 | Cache Nginx para estáticos + dashboard (sem Redis no MVP) | ✅ |
| AD-8 | Soft-delete com `active BOOLEAN` (nunca hard-delete) | ✅ |
| AD-9 | Workers como entidade separada de clients | ✅ |
| AD-10 | Trava de frequência (diarista max 2d/sem) bloqueia + CLT | 📋 |
| AD-11 | Ponto eletrônico geolocalizado com foto | 📋 |
| AD-12 | eSocial assíncrono via fila BullMQ + Redis | 📋 |
| AD-13 | Certificação obrigatória para categorias sensíveis | ✅ |
| AD-14 | Reporte de incidentes com escalação de emergência | 📋 |
| AD-15 | Endereço do prestador para busca por proximidade | ✅ |

---

## 🛠️ Stack Tecnológica

| Camada | Tecnologia | Versão |
|--------|-----------|--------|
| 🌐 Frontend | PHP + HTML5 + CSS3 + JS ES6+ | 8.2 |
| ⚙️ API REST | Node.js + Express.js | 20 LTS |
| 🗄️ Banco de Dados | MySQL | 8.0 |
| 🔁 Proxy | Nginx Alpine | 1.25 |
| 🐳 Conteinerização | Docker Compose | 3.8+ |
| 🔒 Exposição | Cloudflare Tunnel (cloudflared) | 2024.6+ |
| 💳 Pagamentos | Mercado Pago SDK (Node + JS) | 4.x / 2.x |
| 📊 Gráficos | Chart.js | 4.4 |
| 📄 PDF | pdfkit | 0.15 |
| 🧪 Testes | Jest + Supertest | 29.x / 6.x |

---

## 📁 Estrutura do Projeto

```
servicos/
├── api-backend/                      # 🟢 API Node.js (Express)
│   ├── server.js                     # Entry point + registro de rotas
│   ├── config/                       # database.js, auth.js, mercadopago.js
│   ├── middlewares/                  # auth, tenant, error, requestId
│   ├── modules/                      # Domínios verticais (cada um com routes + controller + service)
│   │   ├── auth/                     # Login, registro, JWT, recuperação de senha
│   │   ├── tenants/                  # Perfil do prestador (endereço, settings)
│   │   ├── clients/                  # CRUD de clientes com soft-delete
│   │   ├── catalog/                  # Categorias + Serviços/Produtos
│   │   ├── proposals/                # Propostas mestre-detalhe + itens
│   │   ├── dashboard/                # KPIs agregados + gráficos
│   │   ├── payments/                 # Mercado Pago + webhook + estorno
│   │   ├── transactions/             # Histórico financeiro
│   │   ├── leads/                    # Painel de leads capturados
│   │   ├── public/                   # Landing Page (categorias, serviços, leads, proposals)
│   │   ├── admin/                    # Super admin (dashboard, tenants, planos, relatórios, financeiro, auditoria)
│   │   └── domestic/                 # Workers domésticos (CRUD + certificações)
│   ├── services/                     # Serviços transversais (email, whatsapp, pdf)
│   └── uploads/                      # Uploads de fotos
│
├── web-frontend/                     # 🟠 PHP Frontend (Templates)
│   ├── public/index.php              # Roteador por query string
│   ├── config/app.php                # Helpers de sessão, token
│   └── templates/                    # Views
│       ├── home.php                  # Landing page com busca e categorias
│       ├── login.php                 # Login
│       ├── register.php              # Cadastro 2 passos (com endereço)
│       ├── dashboard.php             # Dashboard do prestador
│       ├── clients.php               # CRUD clientes
│       ├── categories.php            # CRUD categorias
│       ├── services.php              # CRUD serviços
│       ├── proposals.php             # Propostas
│       ├── leads.php                 # Painel de leads
│       ├── workers.php               # CRUD trabalhadores domésticos
│       ├── transactions.php          # Financeiro
│       ├── tenant-profile.php        # Perfil do prestador
│       ├── admin-planos.php          # CRUD de planos (admin)
│       ├── admin-relatorios.php      # Relatório financeiro (admin)
│       ├── solicitar.php             # Wizard de solicitação (3 passos)
│       ├── public-proposal.php       # Proposta pública por token + PDF
│       └── partials/                 # Sidebar, topbar, header, footer
│
├── scripts/                          # 🔵 Banco de Dados
│   ├── init.sql                      # Schema completo + seed inicial
│   ├── seed.sql                      # Dados de teste (dev)
│   └── migrations/                   # Migrações incrementais
│       ├── 003_create_workers_tables.sql
│       ├── 004_create_worker_categories_table.sql
│       ├── 005_add_tenant_address.sql
│       └── 006_create_plans_table.sql
│
├── nginx/                            # ⚪ Configuração do Proxy
│   └── default.conf
│
├── docs/                             # 📚 Documentação
│   ├── planning/PLANEJAMENTO_MODERNO_PROJETO.md  # Plano estratégico (fonte da verdade)
│   └── auditoria/AUDITORIA_COMPLIANCE_DOMESTICO.md
│
├── _bmad-output/                     # 📐 Artefatos BMad
│   ├── planning-artifacts/
│   │   ├── epics.md
│   │   ├── architecture/…/ARCHITECTURE-SPINE.md
│   │   └── ux-designs/…/DESIGN.md + EXPERIENCE.md
│   └── implementation-artifacts/sprint-plan.md
│
├── _bmad/                            # 🧠 Framework BMad
├── docker-compose.yml                # 5 serviços
├── Makefile                          # Comandos auxiliares
├── .env.example                      # Template de variáveis
└── AGENTS.md                         # Contexto para agentes AI
```

---

## 🐳 Docker Development

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

# Primeira execução
make setup
```

### Comandos diários

| Comando | Descrição |
|---------|-----------|
| `make up` | Iniciar containers |
| `make down` | Parar containers |
| `make logs` | Logs de todos serviços |
| `make logs-api` | Logs apenas da API |
| `make php` | Shell no container PHP |
| `make api` | Shell no container Node |
| `make mysql` | CLI MySQL |
| `make migrate` | Executar init.sql manualmente |
| `make seed` | Popular dados de teste |
| `make db-reset` | Resetar volume MySQL |
| `make npm-install` | npm install dentro do container |
| `make npm-dev` | Nodemon watch mode |
| `make health-all` | Verificar saúde de todos serviços |

### Acessos

| Serviço | URL |
|---------|-----|
| **Aplicação** | http://localhost:8080 |
| **phpMyAdmin** | http://localhost:8081 |
| **API Health** | http://localhost:8080/api/v1/health |

---

## 🔐 Multi-Tenancy

O isolamento entre tenants é feito em nível de aplicação:

1. Cada `tenant_id` é extraído do JWT no middleware `tenant.middleware.js`
2. Toda query SQL inclui `WHERE tenant_id = ?` (via `req.tenantFilter`)
3. Super admin (`role = super_admin`) tem bypass com `tenantFilter = '1=1'`
4. Tabelas de negócio possuem FK `tenant_id` com `ON DELETE CASCADE`

### Roles

| Role | Escopo | Acesso Admin |
|------|--------|-------------|
| `super_admin` | Todos os tenants | ✅ Painel admin completo |
| `admin` | Próprio tenant | ❌ |
| `viewer` | Próprio tenant (read-only) | ❌ (futuro) |

---

## 🔌 API Endpoints

### Autenticação
```
POST   /api/v1/auth/register          # Cadastro prestador + JWT
POST   /api/v1/auth/login             # Login + JWT
GET    /api/v1/auth/me                # Dados do usuário logado
POST   /api/v1/auth/forgot-password   # Solicitar reset
POST   /api/v1/auth/reset-password    # Executar reset
```

### Perfil do Prestador
```
GET    /api/v1/tenants/me             # Perfil do tenant logado
PUT    /api/v1/tenants/me             # Atualizar perfil (endereço, nome, etc.)
```

### Negócio (protegido — requer JWT + tenant)
```
GET    /api/v1/clients                # Listar clientes
POST   /api/v1/clients                # Criar cliente
...

GET    /api/v1/categories             # Listar categorias (do tenant)
POST   /api/v1/categories             # Criar categoria
...

GET    /api/v1/services               # Listar serviços
POST   /api/v1/services               # Criar serviço
...

GET    /api/v1/proposals              # Listar propostas
POST   /api/v1/proposals              # Criar proposta (mestre-detalhe)
...

GET    /api/v1/transactions           # Histórico financeiro
...

GET    /api/v1/workers                # Listar trabalhadores domésticos
POST   /api/v1/workers                # Cadastrar trabalhador
```

### Público (sem autenticação)
```
GET    /api/v1/public/categories      # Categorias de todos tenants
GET    /api/v1/public/services        # Busca serviços (?search=&category_id=&city=)
POST   /api/v1/public/leads           # Criar lead (rate limit: 5/min)
GET    /api/v1/public/proposals/:token # Visualizar proposta pública
PATCH  /api/v1/public/proposals/:token/status  # Aprovar/rejeitar
POST   /api/v1/public/proposals/:token/pay     # Pagar com Pix
GET    /api/v1/public/proposals/:token/pdf     # Baixar PDF da proposta (público)
```

### Dashboard (prestador)
```
GET    /api/v1/dashboard/chart        # Receita mensal (6 meses) para Chart.js
GET    /api/v1/dashboard/followup     # Propostas sent/viewed há >48h sem resposta
```

### Propostas
```
GET    /api/v1/proposals              # Listar propostas (filtros: status, cliente)
POST   /api/v1/proposals              # Criar proposta mestre-detalhe
GET    /api/v1/proposals/:id          # Detalhes + itens
PUT    /api/v1/proposals/:id          # Atualizar proposta
DELETE /api/v1/proposals/:id          # Excluir
PATCH  /api/v1/proposals/:id/status   # Avançar status
GET    /api/v1/proposals/:id/pdf      # Baixar PDF (autenticado)
```

### Admin (super_admin)
```
GET    /api/v1/admin/dashboard        # KPIs globais
GET    /api/v1/admin/tenants          # Listar todos tenants
PUT    /api/v1/admin/tenants/:id      # Editar tenant
DELETE /api/v1/admin/tenants/:id      # Suspender/reativar
GET    /api/v1/admin/transactions     # Transações cross-tenant
POST   /api/v1/admin/transactions/:id/refund  # Estorno
GET    /api/v1/admin/audit            # Auditoria de ações
GET    /api/v1/admin/plans            # Listar planos
POST   /api/v1/admin/plans            # Criar plano
GET    /api/v1/admin/plans/:id        # Detalhes do plano
PUT    /api/v1/admin/plans/:id        # Atualizar plano
DELETE /api/v1/admin/plans/:id        # Excluir plano
GET    /api/v1/admin/reports/financial # Relatório financeiro (?start_date=&end_date=&format=csv)
```

---

## 📊 Modelo de Dados

**17 tabelas** no schema (`scripts/init.sql` + migrations):

| Tabela | Finalidade |
|--------|-----------|
| `tenants` | Prestadores (raiz multi-tenant, com endereço) |
| `users` | Usuários (admin do tenant + super_admin) |
| `clients` | Clientes do prestador |
| `categories` | Categorias de serviço (por tenant) |
| `services` | Serviços/produtos com preço |
| `proposals` | Propostas mestre-detalhe |
| `proposal_items` | Itens da proposta |
| `transactions` | Transações financeiras (MP) |
| `audit_log` | Auditoria LGPD |
| `public_leads` | Leads capturados na landing page |
| `lgpd_consent` | Consentimentos LGPD |
| `admin_audit_log` | Auditoria de ações administrativas |
| `workers` | Trabalhadores domésticos |
| `worker_certifications` | Certificações de workers |
| `service_schedules` | Agendamentos com controle de frequência |
| `worker_categories` | Categorias profissionais (9 tipos, extensível) |
| `plans` | Planos de assinatura (free/basic/pro/enterprise) com limites e features |

---

## 🧪 Testes

```bash
make npm-install          # Instalar dependências (uma vez)
cd api-backend && npm test  # Rodar testes Jest
```

Framework: **Jest + Supertest** (configurados, aguardando implementação dos testes).

---

## 🚀 Roadmap

### Sprint 1 (concluído)
- ✅ Workers + CBO (CRUD completo)
- ✅ Endereço no cadastro do prestador
- ✅ Perfil do prestador (tenant-profile)
- ✅ Busca pública por município
- ✅ Correção de tenant isolation (2 queries)

### Sprint 2 (concluído)
- ✅ Epic 3 — Ciclo de Vida da Proposta (CRUD, frontend, WhatsApp, aprovação pública, PDF)
- ✅ Bugfixes: tenantFilter, category_id COALESCE, WhatsApp prefix

### Sprint 3 (atual)
- ✅ Epic 4 — Dashboard com Chart.js (gráfico 6 meses, follow-up de propostas)
- ✅ Epic 5 — Webhook MP + estorno (Endpoint `POST /api/v1/payments/webhook`)
- ✅ Epic 6 — Presença pública + leads (já completo)
- ✅ Epic 7 — Admin planos CRUD + relatório financeiro com export CSV

### Próximos Sprints
- Sprint 4: Frontend Admin PHP (páginas pendentes)
- Sprint 5: Trava de frequência + background check
- Sprint 6: Fluxo CLT + certificações
- Sprint 7: Ponto eletrônico com geolocalização
- Sprint 8: eSocial Doméstico (admissão, DAE, FGTS)
- Sprint 9: Incidentes, seguro, LGPD completo
- Sprint 10: QA final, testes E2E, CI/CD

---

## 📚 Documentação Principal

| Documento | Localização |
|-----------|-------------|
| 🥇 Plano Estratégico | `docs/planning/PLANEJAMENTO_MODERNO_PROJETO.md` |
| 🏛️ Architecture Spine | `_bmad-output/planning-artifacts/architecture/…/ARCHITECTURE-SPINE.md` |
| 📐 Épicos e Stories | `_bmad-output/planning-artifacts/epics.md` |
| 🗓️ Sprint Plan | `_bmad-output/implementation-artifacts/sprint-plan.md` |
| 🎨 UX Design | `_bmad-output/planning-artifacts/ux-designs/…/DESIGN.md` |
| 🧭 UX Experience | `_bmad-output/planning-artifacts/ux-designs/…/EXPERIENCE.md` |
| 📋 Auditoria Compliance | `docs/auditoria/AUDITORIA_COMPLIANCE_DOMESTICO.md` |
| 🤖 Contexto para AI | `AGENTS.md` |

---

## ⚠️ Variáveis de Ambiente

Copie `.env.example` para `.env` e preencha:

| Variável | Obrigatório | Descrição |
|----------|:-----------:|-----------|
| `MYSQL_ROOT_PASSWORD` | ✅ | Senha root MySQL |
| `MYSQL_DATABASE` | ✅ | Nome do banco (`servicos_flex`) |
| `JWT_SECRET` | ✅ | Chave secreta para assinar JWT |
| `MP_ACCESS_TOKEN` | ❌ | Token Mercado Pago (modo degradado sem ele) |
| `CLOUDFLARE_TUNNEL_TOKEN` | ❌ | Token Cloudflare Tunnel (produção) |

---

## 🤝 Contribuição

1. Faça um fork do projeto
2. Crie uma branch (`git checkout -b feature/nova-funcionalidade`)
3. Commit suas mudanças (`git commit -m 'feat: adiciona nova funcionalidade'`)
4. Push para a branch (`git push origin feature/nova-funcionalidade`)
5. Abra um Pull Request

---

## 📄 Licença

Este é um projeto privado — todos os direitos reservados.

---

<p align="center">
  <strong>ServiceSaaS</strong> · Gestão Inteligente para Prestadores de Serviços<br>
  <sub>Construído com Docker · Node.js · PHP · MySQL</sub>
</p>