# 🏢 Área Administrativa — ServiceSaaS

**Versão:** 1.0 | **Data:** 2026-07-27
**Status:** ⬜ Planejamento | **Prioridade:** P1 (pós-MVP)

---

## 1. Visão Geral

A Área Administrativa (Admin) é o painel interno utilizado pela **equipe comercial e de desenvolvimento** para gestão e controle total da plataforma ServiceSaaS. Diferente do Web SaaS (usado pelos prestadores), o Admin opera **acima dos tenants** — é a camada de super-administração.

### 1.1 Quem usa o Admin?

| Perfil | Equipe | Acesso | Funcionalidades |
|:---|---|:---:|:---|
| **Super Admin** | Desenvolvimento | Total | Tudo |
| **Comercial** | Vendas/CRM | Parcial | Tenants, Planos, Relatórios |
| **Suporte** | Customer Success | Parcial | Usuários, Tickets, Visualização |
| **Financeiro** | Administrativo | Parcial | Transações, Estornos, Relatórios |

### 1.2 Arquitetura de Acesso

```
┌──────────────────────────────────────────────────────┐
│              ADMIN (internal.seudominio.com.br)       │
│                                                       │
│  ┌──────────────┐  ┌──────────────────────────────┐  │
│  │   Admin PHP   │  │     Admin API (Node.js)      │  │
│  │  /admin/*.php │  │  /api/v1/admin/*             │  │
│  └──────┬───────┘  └──────────────┬───────────────┘  │
│         │                         │                   │
│         └──────────┬──────────────┘                   │
│                    ▼                                  │
│         ┌──────────────────────┐                     │
│         │  MySQL (ALL tenants) │ ← Sem tenancy filter │
│         └──────────────────────┘                      │
└──────────────────────────────────────────────────────┘
```

**Regra crítica:** O Admin NÃO aplica o middleware de tenancy (AD-2). As queries do Admin consultam TODOS os tenants para métricas globais.

---

## 2. Funcionalidades

### 2.1 📊 Dashboard Global

| Card | Descrição | Fonte |
|:---|---|:---:|
| Total de Tenants | Quantidade de prestadores cadastrados | `tenants` |
| Tenants Ativos | Prestadores com conta ativa (últimos 30 dias) | `tenants.active` + `users.last_login` |
| MRR (Monthly Recurring Revenue) | Receita recorrente estimada | `tenants.plan_type` |
| Propostas (Mês) | Total de propostas criadas no mês | `proposals` |
| Taxa de Conversão | Propostas aprovadas / totais enviadas | `proposals.status` |
| Valor Transacionado (Mês) | Soma de transações aprovadas | `transactions` |

**Gráficos:**
- 📈 Crescimento de Tenants (últimos 12 meses)
- 📊 Receita x Propostas (linha do tempo)
- 🥧 Distribuição de Planos (pizza)
- 📋 Top 10 Prestadores por Receita

### 2.2 👥 Gestão de Tenants

| Funcionalidade | Descrição | Ações |
|:---|---|:---:|
| Listar Tenants | Tabela com todos os prestadores | Filtrar por plano, status, data |
| Visualizar Tenant | Detalhes completos do prestador | Ver clientes, propostas, transações |
| Editar Tenant | Alterar dados do prestador | Nome, plano, status, logo |
| Suspender Tenant | Bloquear acesso temporário | `tenants.active = false` |
| Alterar Plano | Mudar de plano (free → pro) | `tenants.plan_type` |
| Excluir Tenant | Exclusão lógica (LGPD) | Anonimizar + desativar |

### 2.3 📋 Gestão de Usuários

| Funcionalidade | Descrição |
|:---|---|:---|
| Listar Usuários | Todos os usuários de todos os tenants |
| Visualizar | Dados do usuário + último login + logs |
| Bloquear | Desativar conta (`users.active = false`) |
| Alterar Role | Mudar permissão (`admin`, `viewer`, `super_admin`) |
| Resetar Senha | Forçar redefinição de senha |

### 2.4 💳 Gestão de Planos

| Plano | Preço | Limite Clientes | Limite Propostas | Recursos |
|:---|:---:|:---:|:---:|:---|
| **Free** | R$ 0 | 10 | 20/mês | Básico |
| **Basic** | R$ 29,90 | 50 | 100/mês | WhatsApp + Relatórios |
| **Pro** | R$ 79,90 | Ilimitado | Ilimitado | PDF + MP + Prioridade |
| **Enterprise** | Sob consulta | Ilimitado | Ilimitado | API + Suporte 24h |

### 2.5 📄 Monitoramento de Propostas

Visualização global de todas as propostas da plataforma. Filtros por:
- Tenant
- Status
- Período
- Valor mínimo/máximo

### 2.6 💰 Transações Financeiras

| Funcionalidade | Descrição |
|:---|---|:---|
| Listar Transações | Todas as transações MP da plataforma |
| Visualizar | Detalhes da transação + dados do pagador |
| Estornar | Estorno manual via Admin |
| Relatórios | Exportar CSV/JSON de transações |
| Chargebacks | Lista de chargebacks com alerta |

### 2.7 🔧 Suporte e Tickets (v2.0)

Sistema de tickets para prestadores solicitarem suporte. **Adiado para v2.0.**

### 2.8 📈 Relatórios

| Relatório | Formato | Frequência |
|:---|---|:---:|
| Relatório de Crescimento | CSV + Gráfico | Mensal |
| Relatório Financeiro | CSV + PDF | Mensal |
| Relatório de Propostas | CSV | Semanal |
| Relatório de Tenants Inativos | CSV | Semanal |
| Export Completo LGPD | JSON | Sob demanda |

### 2.9 ⚙️ Configurações do Sistema

| Configuração | Descrição |
|:---|---|
| Planos | Criar/editar planos e preços |
| Templates WhatsApp | Template global para envio de propostas |
| Taxas MP | Configurar taxas de processamento |
| Manutenção | Ativar/desativar modo de manutenção |
| Logs do Sistema | Visualizar logs de auditoria |

### 2.10 🔐 Auditoria e LGPD

| Funcionalidade | Descrição |
|:---|---|
| Logs de Auditoria | Visualização centralizada de todos os audit logs |
| Solicitações de Titulares | Gerenciar solicitações de exclusão/portabilidade |
| Relatório LGPD | Exportar dados de um titular específico |

---

## 3. Estrutura de Diretórios

```
servicos-flex/
├── web-frontend/
│   ├── public/
│   │   └── admin/                         # 🏢 Admin PHP
│   │       ├── index.php                  # Dashboard Global
│   │       ├── login.php                  # Admin Login (separado)
│   │       ├── tenants.php                # Lista de Tenants
│   │       ├── tenant_view.php            # Detalhes do Tenant
│   │       ├── tenant_edit.php            # Editar Tenant
│   │       ├── users.php                  # Lista de Usuários
│   │       ├── plans.php                  # Gestão de Planos
│   │       ├── transactions.php           # Transações
│   │       ├── proposals.php              # Propostas Globais
│   │       ├── reports.php                # Relatórios
│   │       ├── settings.php               # Configurações
│   │       ├── audit.php                  # Logs de Auditoria
│   │       └── lgpd.php                   # Solicitações LGPD
│   └── config/
│       └── admin_auth.php                 # Admin auth (JWT + role check)
│
└── api-backend/
    └── modules/
        └── admin/                          # 🏢 Admin API
            ├── admin.routes.js             # GET /api/v1/admin/*
            ├── admin.controller.js
            ├── admin.service.js
            └── admin.validator.js
```

**Rotas da API:**

| Método | Rota | Descrição |
|:---|:---|---|
| GET | `/api/v1/admin/dashboard` | KPIs globais + gráficos |
| GET | `/api/v1/admin/tenants` | Lista de tenants (paginada) |
| GET | `/api/v1/admin/tenants/:id` | Detalhes do tenant |
| PUT | `/api/v1/admin/tenants/:id` | Atualizar tenant |
| DELETE | `/api/v1/admin/tenants/:id` | Suspender/excluir tenant |
| GET | `/api/v1/admin/users` | Lista de usuários |
| PUT | `/api/v1/admin/users/:id` | Bloquear/alterar role |
| GET | `/api/v1/admin/transactions` | Transações globais |
| POST | `/api/v1/admin/transactions/:id/refund` | Estorno |
| GET | `/api/v1/admin/plans` | Lista de planos |
| PUT | `/api/v1/admin/plans/:id` | Atualizar plano |
| GET | `/api/v1/admin/reports/:type` | Exportar relatório |
| GET | `/api/v1/admin/audit-logs` | Logs de auditoria |
| GET | `/api/v1/admin/lgpd-requests` | Solicitações de titulares |

---

## 4. Modelo de Dados (Extensão)

### Nova coluna em `users`:

```sql
ALTER TABLE users
  MODIFY COLUMN role ENUM('admin', 'viewer', 'super_admin') NOT NULL DEFAULT 'admin';
```

### Novas tabelas:

```sql
-- Histórico de ações do Admin
CREATE TABLE admin_audit_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_user_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,         -- Ex: 'tenant_suspended', 'plan_changed'
    target_type VARCHAR(50) NOT NULL,      -- Ex: 'tenant', 'user', 'transaction'
    target_id INT,
    payload JSON,                          -- Dados da ação
    ip VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_user_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- Planos
CREATE TABLE plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,             -- free, basic, pro, enterprise
    display_name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    max_clients INT DEFAULT NULL,          -- NULL = ilimitado
    max_proposals_month INT DEFAULT NULL,
    features JSON,                         -- Array de recursos
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Solicitações LGPD (titulares)
CREATE TABLE privacy_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    requester_name VARCHAR(150) NOT NULL,
    requester_email VARCHAR(150) NOT NULL,
    request_type ENUM('access', 'correction', 'deletion', 'portability', 'opposition') NOT NULL,
    description TEXT,
    status ENUM('pending', 'in_progress', 'completed', 'rejected') DEFAULT 'pending',
    admin_notes TEXT,
    tenant_id INT,
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Tickets de suporte (v2.0)
CREATE TABLE support_tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    user_id INT NOT NULL,
    subject VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    status ENUM('open', 'in_progress', 'waiting_tenant', 'resolved', 'closed') DEFAULT 'open',
    assigned_to INT,                       -- admin_user_id
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (assigned_to) REFERENCES users(id)
) ENGINE=InnoDB;

-- Mensagens de tickets
CREATE TABLE ticket_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT NOT NULL,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES support_tickets(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;
```

---

## 5. Layout (Telas)

### 5.1 Admin Login
- Tela separada em `admin.seudominio.com.br` ou `/admin/login.php`
- Autenticação com role `super_admin`
- 2FA recomendado (adiado v2.0)

### 5.2 Admin Dashboard
```
┌────────────────────────────────────────────────────┐
│ 🏢 Admin ServiceSaaS         👤 Super Admin ▼     │
├────────────────────────────────────────────────────┤
│                                                    │
│  ┌────────┐ ┌────────┐ ┌────────┐ ┌────────┐      │
│  │ Tenants │ │ Users  │ │  MRR   │ │ Trans. │      │
│  │ 1.234   │ │ 2.567  │ │R$ 29k  │ │R$ 185k │      │
│  └────────┘ └────────┘ └────────┘ └────────┘      │
│                                                    │
│  ┌──────────────────┐ ┌──────────────────┐         │
│  │ Crescimento      │ │ Top Prestadores  │         │
│  │ [Gráfico 12m]   │ │ [Tabela Top 10]  │         │
│  └──────────────────┘ └──────────────────┘         │
│                                                    │
│  ┌──────────────────────────────────────┐          │
│  │ Últimas Atividades                   │          │
│  │ • Novo tenant: Condomínio Solar (2m) │          │
│  │ • Plano alterado: Maria → Pro        │          │
│  │ • Transação: R$ 8.900 (aprovada)     │          │
│  └──────────────────────────────────────┘          │
└────────────────────────────────────────────────────┘
```

### 5.3 Sidebar Navigation
```
🏢 Admin ServiceSaaS
─────────────────────
📊 Dashboard
👥 Tenants
👤 Usuários
📄 Propostas
💰 Transações
💎 Planos
📈 Relatórios
🔐 Auditoria LGPD
⚙️ Configurações
─────────────────────
```

---

## 6. NFRs Específicos do Admin

| ID | Requisito | Especificação |
|:---:|:---|---|
| **NFR-ADM-01** | **Autenticação Admin** | Login separado com role `super_admin`. JWT com `is_admin: true` no payload |
| **NFR-ADM-02** | **Sem Tenancy Filter** | Admin NÃO aplica tenant.middleware — consulta dados de TODOS os tenants |
| **NFR-ADM-03** | **Audit Trail Admin** | Toda ação do Admin é registrada em `admin_audit_log` (imutável, 5 anos) |
| **NFR-ADM-04** | **Acesso Restrito** | Rotas /api/v1/admin/* rejeitam requisições sem role `super_admin` |
| **NFR-ADM-05** | **Separação de Domínio** | Admin em subdomínio separado (`admin.seudominio.com.br`) para segurança |

---

## 7. Roadmap de Implementação

| Fase | Story | Descrição | Esforço | Dependência |
|:---:|:---:|---|---|:---:|
| **Fase 1** | ADM-01 | Admin Auth + Dashboard Global | 16h | Auth base (Epic 1) |
| **Fase 1** | ADM-02 | API Admin + Rotas base | 8h | ADM-01 |
| **Fase 2** | ADM-03 | Gestão de Tenants (CRUD) | 12h | ADM-02 |
| **Fase 2** | ADM-04 | Gestão de Usuários + Planos | 12h | ADM-02 |
| **Fase 2** | ADM-05 | Transações + Estorno | 8h | Epic 5 (MP) |
| **Fase 2** | ADM-06 | Relatórios + Exportação | 8h | ADM-02 |
| **Fase 3** | ADM-07 | Auditoria + Solicitações LGPD | 8h | ADM-02 |
| **Fase 3** | ADM-08 | Suporte (Tickets) | 16h | ADM-02 |

---

## 8. FRs (Functional Requirements)

| ID | Título | Descrição | Critério de Aceitação |
|:---:|:---|---|:---|
| FR-ADM-01 | Login Admin | Super Admin autentica no painel administrativo | Dado um usuário com role super_admin, Quando faz login em /admin/login.php, Então o sistema redireciona ao admin dashboard |
| FR-ADM-02 | Dashboard Global | Admin visualiza KPIs globais da plataforma | Dado que existem tenants e transações cadastradas, Quando o admin acessa o dashboard, Então os cards exibem totais em < 2s |
| FR-ADM-03 | Listar Tenants | Admin visualiza todos os prestadores | Dado que existem tenants cadastrados, Quando o admin acessa /admin/tenants.php, Então o sistema exibe lista paginada com busca e filtros |
| FR-ADM-04 | Editar Tenant | Admin altera dados de um prestador | Dado um tenant selecionado, Quando o admin altera plano ou status, Então as alterações são persistidas e auditadas |
| FR-ADM-05 | Suspender Tenant | Admin bloqueia acesso de um prestador | Dado um tenant ativo, Quando o admin confirma a suspensão, Então o tenant é marcado como inativo e usuários perdem acesso |
| FR-ADM-06 | Visualizar Transações | Admin consulta transações de todos os tenants | Dado que existem transações, Quando o admin acessa /admin/transactions.php, Então o sistema exibe lista com filtros por tenant, status e período |
| FR-ADM-07 | Estornar Pagamento | Admin realiza estorno manual de transação | Dado uma transação aprovada, Quando o admin solicita estorno, Então o sistema envia requisição ao MP e registra em audit log |
| FR-ADM-08 | Gerenciar Planos | Admin cria e altera planos de preços | Dado que o admin acessa /admin/plans.php, Quando altera preço ou recursos de um plano, Então as alterações valem para novos cadastros |
| FR-ADM-09 | Relatório Financeiro | Admin exporta relatório de transações | Dado que existem transações no período, Quando o admin solicita relatório mensal, Então o sistema gera CSV/PDF com dados financeiros |
| FR-ADM-10 | Auditoria de Ações | Admin visualiza logs de ações administrativas | Dado que ações foram realizadas no admin, Quando o admin acessa /admin/audit.php, Então o sistema exibe log imutável com filtros |

---

## 9. Epic 7 — 🏢 Administração da Plataforma

**Para inclusão no `epics.md`:**

### Epic 7: 🏢 Administração da Plataforma
**Equipe ServiceSaaS gerencia tenants, planos, transações e auditoria**

**FRs cobertos:** FR-ADM-01 a FR-ADM-10
**UX Surfaces:** Admin Dashboard, Admin Tenants, Admin Financeiro
**NFRs:** NFR-ADM-01 a NFR-ADM-05
**ARs:** — (Admin bypassa tenancy)
**Depende de:** Epic 1 (Auth), Epic 5 (Payments/MP)
**Prioridade:** P1 (pós-MVP, implementar antes do lançamento comercial)
