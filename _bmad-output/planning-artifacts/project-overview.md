# 🎯 ServiceSaaS — Visão Geral do Projeto

**Documentado por:** Paige (Technical Writer)
**Data:** 28 de Julho de 2026

---

## 1. Identidade

| Campo | Valor |
|:---|---|
| **Nome** | ServiceSaaS (Serviços Flex) |
| **Tipo** | Plataforma SaaS Multi-tenant |
| **Proposta** | Permitir que prestadores de serviço (MEI/autônomos) gerenciem clientes, criem propostas, recebam pagamentos e cresçam seu negócio |
| **Diferencial** | Simplicidade — foco no profissional que não é tech-savvy |
| **Estágio** | 🟡 MVP funcional — cadastro, login, clientes, catálogo, propostas, dashboard |

---

## 2. Personas

### 👩 Maria — Prestadora de Serviço (Usuário Principal)

| Atributo | Detalhe |
|:---|---|
| **Profissão** | Cabeleireira / Manicure |
| **Regime** | MEI (faturamento < R$ 81k/ano) |
| **Objetivo** | Organizar clientes, enviar propostas profissionais, receber pagamentos |
| **Dores** | Usava papel/caderno, perdia propostas, não tinha controle financeiro |
| **Tech level** | Baixo — usa WhatsApp e Instagram, não é expert em tecnologia |

### 👨 Carlos — Cliente Final

| Atributo | Detalhe |
|:---|---|
| **Perfil** | Consumidor final (pessoa física) |
| **Objetivo** | Contratar serviço, aprovar proposta, pagar via Pix |
| **Interação** | Links de WhatsApp, página pública de proposta |

### 🧑 Administrador ServiceSaaS

| Atributo | Detalhe |
|:---|---|
| **Perfil** | Equipe interna da plataforma |
| **Objetivo** | Gerenciar tenants, planos, transações e auditoria |
| **Acesso** | Rota `/admin/` separada, role `super_admin` |

---

## 3. Stack Tecnológica

### 3.1. Stack Principal

```
┌─────────────────────────────────────────────────────┐
│                    Usuário                          │
├─────────────────────────────────────────────────────┤
│                   Nginx :8080                       │
├─────────────────────┬───────────────────────────────┤
│  PHP 8.2 (Frontend) │  Node.js 20 (API REST)        │
│  web-frontend/      │  api-backend/                 │
│  Templates + Tailwind│  Express + mysql2            │
├─────────────────────┴───────────────────────────────┤
│                   MySQL 8.0                         │
│                   init.sql + migrations              │
├─────────────────────────────────────────────────────┤
│                   Docker Compose                    │
│         nginx │ php │ api │ mysql │ pma             │
└─────────────────────────────────────────────────────┘
```

### 3.2. Pacotes e Dependências

**Backend (api-backend/package.json):**

| Pacote | Versão | Função |
|:---|---|:---|
| express | ^4.18 | Framework HTTP |
| mysql2 | ^3.9 | Driver MySQL |
| jsonwebtoken | ^9.0 | JWT auth |
| bcrypt | ^5.1 | Password hashing |
| cors | ^2.8 | CORS middleware |
| express-rate-limit | ^7.1 | Rate limiting |
| uuid | ^9.0 | Correlation IDs |

---

## 4. Arquitetura de Módulos

### 4.1. API Backend (Node.js)

```
api-backend/
├── server.js                    ← Entry point (Express app)
├── config/
│   ├── database.js              ← MySQL pool
│   └── auth.js                  ← JWT secret + expiresIn
├── middlewares/
│   ├── auth.middleware.js        ← Valida JWT via Authorization: Bearer
│   ├── tenant.middleware.js     ← Injeta tenant_id em queries
│   ├── requestId.middleware.js  ← Correlation ID (UUID)
│   └── error.middleware.js      ← Tratamento global de erros
├── modules/
│   ├── auth/                    ← Register, Login, Forgot/Reset Password
│   ├── clients/                 ← CRUD Clientes
│   ├── catalog/                 ← Categorias + Serviços
│   ├── proposals/               ← Propostas + Itens (state machine)
│   ├── dashboard/               ← KPIs agregados
│   └── admin/                   ← Super admin (dashboard + tenants)
└── services/
    └── email.service.js         ← Stub (console.log)
```

### 4.2. Frontend PHP

```
web-frontend/
├── config/app.php                ← Configurações
├── public/
│   ├── index.php                 ← Roteador central
│   └── js/tailwind.config.js     ← Design tokens CDN
├── templates/
│   ├── partials/
│   │   ├── header.php            ← Sidebar + Topbar
│   │   └── footer.php            ← Scripts
│   ├── home.php                  ← Landing Page
│   ├── login.php                 ← Login
│   ├── register.php              ← Cadastro PF/PJ
│   ├── dashboard.php             ← Dashboard + KPIs
│   ├── clients.php               ← CRUD Clientes
│   ├── categories.php            ← CRUD Categorias
│   ├── services.php              ← CRUD Serviços
│   ├── proposals.php             ← CRUD Propostas + Itens + Status
│   ├── forgot-password.php       ← Recuperação de senha
│   └── reset-password.php        ← Redefinição de senha
└── Dockerfile
```

### 4.3. Fluxo de Requisição

```
Navegador → http://localhost:8080
                │
                ▼
           Nginx (default.conf)
                │
        ┌───────┴───────┐
        ▼               ▼
   /api/v1/*       *.php
        │               │
        ▼               ▼
   Node.js:3000    PHP:9000
        │               │
        ▼               ▼
      API → MySQL ← Templates HTML
```

---

## 5. Modelo de Dados

### 5.1. Entidades Principais

```
tenants ──┬── users
          ├── clients
          ├── categories ── products_services
          └── proposals ── proposal_items
```

### 5.2. Relacionamentos

| Tabela | FK | Tipo |
|:---|---|:---:|
| users | tenant_id → tenants | M:1 |
| clients | tenant_id → tenants | M:1 |
| categories | tenant_id → tenants | M:1 |
| products_services | tenant_id → tenants, category_id → categories | M:1 |
| proposals | tenant_id → tenants, client_id → clients | M:1 |
| proposal_items | proposal_id → proposals, tenant_id → tenants | M:1 |
| transactions | tenant_id → tenants, proposal_id → proposals | M:1 (🔴 a criar) |

### 5.3. State Machine da Proposta

```
draft ──→ sent ──→ viewed ──→ accepted ──→ paid (futuro)
  │          │         │           │
  └──→ cancelled ←────┴──→ rejected
```

---

## 6. Status Atual da Implementação

### ✅ Completo (MVP Funcional)

| Funcionalidade | Módulo | Testado |
|:---|---|:---:|
| Docker Compose (5 containers) | Infra | ✅ |
| Cadastro PF/PJ | auth | ✅ |
| Login + JWT | auth | ✅ |
| CRUD Clientes | clients | ✅ |
| CRUD Categorias | catalog | ✅ |
| CRUD Serviços | catalog | ✅ |
| CRUD Propostas + Itens | proposals | ✅ (12/12 testes) |
| Dashboard KPIs | dashboard | ✅ |
| Admin Dashboard + Tenants | admin | ✅ |
| Recuperação de Senha | auth | ✅ |
| Seed Data (Maria Beleza) | scripts | ✅ |

### 🟡 Pendente (Próximas Sessões)

| Funcionalidade | Épico | Prioridade |
|:---|---|:---:|
| Stories Epic 2 e 3 | 2, 3 | 📝 |
| Mercado Pago (SDK + Webhook + Pix) | 5 | 🔴 |
| Tabela `transactions` | 5 | 🔴 |
| Frontend Admin completo | 7 | 🟡 |
| Landing Page avançada (wizard) | 6 | 🟡 |
| Testes automatizados | — | 🟡 |
| CI/CD (GitHub Actions) | — | 🟢 |

---

*Documento gerado por Paige (Technical Writer) em 28 de Julho de 2026*
