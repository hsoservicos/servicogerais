# Auditoria de Compliance & Mapeamento de Gap
## Implantação de Módulos de Empregados Domésticos — ServiceSaaS

**Data:** 28/07/2026
**Versão:** 1.0
**Base Legal:** Lei Complementar nº 150/2015, CBO (Classificação Brasileira de Ocupações)
**Fonte Auditada:** Portal Hora do Lar — Taxonomia de Empregados Domésticos

---

## Índice

1. [Resumo Executivo](#1-resumo-executivo)
2. [Estado Atual: Arquitetura ServiceSaaS](#2-estado-atual-arquitetura-servicesaas)
3. [Matriz de Gaps: Requisito vs. Realidade](#3-matriz-de-gaps-requisito-vs-realidade)
4. [Análise Detalhada por Camada](#4-análise-detalhada-por-camada)
5. [Plano de Ação e Recomendações](#5-plano-de-ação-e-recomendações)
6. [Glossário de Riscos](#6-glossário-de-riscos)
7. [Anexos Técnicos](#7-anexos-técnicos)

---

## 1. Resumo Executivo

### 1.1 Declaração de Escopo

Esta auditoria confronta o ecossistema atual do **ServiceSaaS (Serviços Flex)** — uma plataforma SaaS de intermediação de serviços entre prestadores e contratantes — contra os requisitos de compliance, modelagem de dados e regras de negócio necessários para operar **categorias de empregados domésticos** reguladas pela **LC nº 150/2015**.

### 1.2 Veredito

| Dimensão | Nota | Status |
|:---------|:----:|:-------|
| Cobertura de categorias profissionais | 0/9 | ❌ Nenhuma categoria doméstica modelada |
| Trava algorítmica diarista vs. CLT | 0/1 | ❌ Inexistente |
| Compliance LGPD | 3/5 | 🟡 Parcial (consentimento presente, sem fluxo de revogação completo) |
| Gestão de pagamentos | 4/5 | 🟢 Quase completo (MP integrado, sem PIX QR no webhook) |
| Controle de jornada/ponto | 0/1 | ❌ Inexistente |
| Integração eSocial Doméstico | 0/1 | ❌ Inexistente |
| Engine de cálculos trabalhistas | 0/1 | ❌ Inexistente |
| Certificação e background check | 0/1 | ❌ Inexistente |
| Seguro/garantia de serviços | 0/1 | ❌ Inexistente |
| Botão de emergência/acidentes | 0/1 | ❌ Inexistente |

**Geral:** O sistema atual é uma plataforma de **serviços agendados avulsos** (service-proposal-payment). Para operar **empregados domésticos com vínculo CLT (LC 150)**, são necessários **08 novos módulos** e a **reestruturação de 4 módulos existentes**.

---

## 2. Estado Atual: Arquitetura ServiceSaaS

### 2.1 Stack Tecnológica (Verificada)

| Camada | Tecnologia | Status |
|:-------|:-----------|:-------|
| Frontend | PHP 8.2 (sem framework) | ✅ Operacional |
| API REST | Node.js 20 + Express.js | ✅ Operacional |
| Banco de Dados | MySQL 8.0 (InnoDB, utf8mb4) | ✅ Operacional |
| Proxy/SSL | Nginx 1.25 Alpine | ✅ Operacional |
| Containers | Docker Compose (5 serviços) | ✅ Operacional |
| Pagamentos | Mercado Pago SDK v2 | ✅ Com modo degradado |
| JWT Auth | `jsonwebtoken` + bcrypt | ✅ Operacional |
| Testes | Jest + Supertest | ⚠️ Configurado, sem testes escritos |
| CI/CD | Nenhum | ❌ Inexistente |

### 2.2 Modelo de Dados Corrente (12 Tabelas)

```
tenants (root) ──┬── users
                 ├── clients
                 ├── categories ── services
                 ├── proposals ── proposal_items
                 ├── transactions
                 ├── audit_log
                 ├── lgpd_consent
                 ├── public_leads
                 └── admin_audit_log
```

**Observação crítica:** `clients` representa **clientes dos prestadores (tomadores)** — não trabalhadores/prestadores de serviço. O sistema não possui uma entidade `workers` com CBO, categorias de empregado doméstico, documento profissional etc.

### 2.3 Módulos da API (10 Domínios)

| Módulo | Rotas | Status |
|:-------|:------|:-------|
| `auth/` | register, login, me, forgot/reset-password | ✅ |
| `clients/` | CRUD clientes (tomadores) | ✅ |
| `catalog/` | CRUD categorias + serviços | ✅ |
| `proposals/` | Propostas + itens + máquina de estados | ✅ |
| `payments/` | Webhook MP, preferências, estorno | ✅ |
| `transactions/` | Listagem financeira | ✅ |
| `leads/` | Gestão de leads públicos | ⚠️ Limitado |
| `public/` | Landing page, wizard solicitação, proposta pública | ✅ |
| `admin/` | Dashboard, tenants, finanças, auditoria | ✅ |
| `dashboard/` | KPI do tenant | ✅ |

### 2.4 Estado dos Testes

```
api-backend/
  __tests__/          ❌ Diretório não existe
  **/*.test.js        ❌ Nenhum arquivo de teste encontrado (0 arquivos)
  jest config         ⚠️ package.json: "test": "jest --passWithNoTests"
  eslint config       ❌ Nenhum arquivo .eslintrc* encontrado
```

---

## 3. Matriz de Gaps: Requisito vs. Realidade

### 3.1 Taxonomia de Categorias Profissionais (GAP CRÍTICO)

| # | Categoria (CBO) | Regime Legal | Existe no Sistema? | Onde Implementar |
|:-:|:----------------|:-------------|:------------------:|:-----------------|
| 1 | Empregada Doméstica Geral (`5121-05`) | LC 150 (>2d) | ❌ | Nova entidade `workers` + `worker_categories` |
| 2 | Diarista Autônoma (`5121-05` alt) | Autônomo (≤2d) | ❌ | Idem + regra de frequência |
| 3 | Babá/Cuidador Infantil (`5162-05`) | LC 150 (>2d) | ❌ | Idem + certificações |
| 4 | Cuidador de Idosos (`5162-10`) | LC 150 (>2d) | ❌ | Idem + certificações |
| 5 | Cozinheiro(a) Doméstico (`5132-10`) | LC 150 (>2d) | ❌ | Nova entidade |
| 6 | Motorista Particular (`5151-05`/`7823`) | LC 150 (>2d) | ❌ | + controle de jornada |
| 7 | Jardineiro Residencial (`6112-05`) | LC 150 ou Autônomo | ❌ | + checklist EPI |
| 8 | Caseiro/Zelador (`5121-15`) | LC 150 (>2d) | ❌ | + regra moradia local |
| 9 | Governanta/Mordomo (`5121-10`/`20`) | LC 150 (>2d) | ❌ | + gestão de fundo fixo |

### 3.2 Regra de Ouro: Frequência Diarista vs. CLT (GAP CRÍTICO)

| Requisito | Estado Atual | Impacto |
|:----------|:-------------|:--------|
| Trava de 3º agendamento semanal | ❌ Inexistente | Risco de descaracterização de autônomo para CLT |
| Alerta educativo sobre LC 150 | ❌ Inexistente | Usuário sem orientação legal |
| Fluxo de transição diarista → CLT | ❌ Inexistente | Perda de receita recorrente |
| Contagem por CPF tomador × prestador | ❌ Inexistente | Sem rastreabilidade |

### 3.3 Gestão de Jornada e Ponto Eletrônico (GAP CRÍTICO)

| Requisito | Estado Atual | Base Legal |
|:----------|:-------------|:-----------|
| Controle de entrada/saída geolocalizado | ❌ Inexistente | Art. 12 LC 150 |
| Registro de intervalo intrajornada | ❌ Inexistente | Art. 13 LC 150 |
| Cálculo de horas extras automático | ❌ Inexistente | Art. 2º LC 150 |
| Adicional noturno (22h-5h) | ❌ Inexistente | Art. 14 LC 150 |
| Escala 12×36 | ❌ Inexistente | Acordo individual |
| Notificação push de inconsistência | ❌ Inexistente | SLA operacional |

### 3.4 Integração eSocial Doméstico (GAP CRÍTICO)

| Requisito | Estado Atual | Consequência |
|:----------|:-------------|:-------------|
| Admissão via eSocial | ❌ Inexistente | Sem vínculo legal |
| Geração mensal DAE (INSS+FGTS+Gilrat) | ❌ Inexistente | Passivo tributário |
| Registro de férias e 13º | ❌ Inexistente | Passivo trabalhista |
| Aviso prévio e rescisão | ❌ Inexistente | Passivo rescisório |

### 3.5 Smart Matching (GAP ALTO)

| Requisito | Estado Atual | Observação |
|:----------|:-------------|:-----------|
| Algoritmo trava-frequência por CPF | ❌ Inexistente | Deve cruzar agendamentos |
| Triagem de antecedentes | ❌ Inexistente | Serviço terceirizado |
| Verificação de certificações | ❌ Inexistente | Banco de certificados |
| Match geográfico | ❌ Inexistente | CEP na solicitação existe mas não é usado para matching |

### 3.6 Marketplace de Serviços Avulsos

| Requisito | Estado Atual | Observação |
|:----------|:-------------|:-----------|
| Checklist operacional por categoria | ❌ Inexistente | Escopo de tarefas não é validado |
| Seguro de danos acidentais | ❌ Inexistente | Sem cobertura |
| SLA por tipo de incidente | ❌ Inexistente | Sem matriz de responsabilidade |

### 3.7 LGPD e Privacidade (PARCIALMENTE COBERTO)

| Requisito | Estado Atual | Observação |
|:----------|:-------------|:-----------|
| Consentimento explícito para marketing | ✅ `lgpd_consent_marketing` em `public_leads` | Presente |
| Consentimento para termos | ✅ `lgpd_consent_terms` em `public_leads` | Presente |
| Tabela de consentimento genérica | ✅ `lgpd_consent` com opt-in/communications/terms | Presente |
| Fluxo de revogação de consentimento | ❌ Inexistente | Sem endpoint para revogar |
| Portabilidade de dados | ❌ Inexistente | Sem exportação |
| Eliminação de dados (direito ao esquecimento) | ❌ Inexistente | Sem fluxo de anonimização |

### 3.8 Segurança e Emergência (GAP CRÍTICO)

| Requisito | Estado Atual | Observação |
|:----------|:-------------|:-----------|
| Botão de emergência/pânico | ❌ Inexistente | Sem app mobile do trabalhador |
| Acionamento de seguro contra acidentes | ❌ Inexistente | Sem cobertura |
| Comunicação de Acidente de Trabalho (CAT) | ❌ Inexistente | Sem emissão |
| Suporte emergencial médico | ❌ Inexistente | Sem SLA definido |

---

## 4. Análise Detalhada por Camada

### 4.1 Modelo de Dados (Database Schema)

**Arquivo analisado:** `scripts/init.sql` (286 linhas)

#### 4.1.1 O Que Existe

- `tenants` — Raiz multi-tenant com planos (free, basic, pro, enterprise), slug único
- `users` — Autenticação com roles (super_admin, admin, viewer), bcrypt hash
- `clients` — **Tomadores de serviço** (não trabalhadores)
- `categories` — Categorias de serviço (genéricas, ex: "Corte de Cabelo")
- `services` — Itens de catálogo com preço e duração
- `proposals` — Propostas/orçamentos com máquina de estados (9 status)
- `proposal_items` — Itens de proposta com preço calculado
- `transactions` — Financeiro Mercado Pago
- `audit_log` + `admin_audit_log` — Auditoria
- `lgpd_consent` — Consentimento LGPD
- `public_leads` — Captura de leads públicos

#### 4.1.2 O Que FALTA para Compliance Doméstico

```sql
-- NOVAS TABELAS NECESSÁRIAS

-- 1. Workers (Prestadores/Trabalhadores)
CREATE TABLE workers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    cpf VARCHAR(14) UNIQUE NOT NULL,
    rg VARCHAR(20),
    cbo_code VARCHAR(10) NOT NULL COMMENT 'Código CBO (ex: 5121-05)',
    worker_category ENUM(
        'EMPREGADO_DOMESTICO_GERAL', 'DIARISTA', 'BABA',
        'CUIDADOR_IDOSOS', 'COZINHEIRO', 'MOTORISTA',
        'JARDINEIRO', 'CASEIRO', 'GOVERNANTA'
    ) NOT NULL,
    phone VARCHAR(20),
    whatsapp VARCHAR(20),
    pix_key VARCHAR(100),
    address JSON,
    avatar_url VARCHAR(500),
    background_check_status ENUM('PENDING', 'APPROVED', 'REJECTED') DEFAULT 'PENDING',
    background_check_date TIMESTAMP NULL,
    background_check_provider VARCHAR(100),
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_workers_tenant (tenant_id),
    INDEX idx_workers_cpf (cpf),
    INDEX idx_workers_category (worker_category),
    CONSTRAINT fk_workers_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
);

-- 2. Worker Certifications
CREATE TABLE worker_certifications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    worker_id INT UNSIGNED NOT NULL,
    certification_type ENUM(
        'CUIDADOR_IDOSOS', 'APH', 'BABÁ', 'COZINHA',
        'JARDINAGEM', 'PRIMEIROS_SOCORROS', 'OUTRO'
    ) NOT NULL,
    title VARCHAR(255) NOT NULL,
    issuer VARCHAR(255),
    issue_date DATE,
    expiry_date DATE,
    document_url VARCHAR(500),
    verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_cert_worker (worker_id),
    CONSTRAINT fk_cert_worker FOREIGN KEY (worker_id) REFERENCES workers(id) ON DELETE CASCADE
);

-- 3. Service Schedules (Agendamentos com controle de frequência)
CREATE TABLE service_schedules (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT UNSIGNED NOT NULL,
    worker_id INT UNSIGNED NOT NULL,
    client_id INT UNSIGNED NOT NULL,
    service_category ENUM(
        'EMPREGADO_DOMESTICO_GERAL', 'DIARISTA', 'BABA',
        'CUIDADOR_IDOSOS', 'COZINHEIRO', 'MOTORISTA',
        'JARDINEIRO', 'CASEIRO', 'GOVERNANTA'
    ) NOT NULL,
    regime ENUM('AUTONOMO_DIARISTA', 'LC_150_CLT') NOT NULL,
    scheduled_date DATE NOT NULL,
    start_time TIME,
    end_time TIME,
    status ENUM('scheduled', 'confirmed', 'in_progress', 'completed', 'cancelled') DEFAULT 'scheduled',
    hourly_rate DECIMAL(10,2),
    total_amount DECIMAL(10,2),
    transport_voucher DECIMAL(10,2) DEFAULT 0,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_sched_tenant (tenant_id),
    INDEX idx_sched_worker (worker_id),
    INDEX idx_sched_client (client_id),
    INDEX idx_sched_date (scheduled_date),
    INDEX idx_sched_status (status),
    CONSTRAINT fk_sched_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    CONSTRAINT fk_sched_worker FOREIGN KEY (worker_id) REFERENCES workers(id) ON DELETE CASCADE,
    CONSTRAINT fk_sched_client FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
);

-- 4. Time Tracking (Ponto Eletrônico)
CREATE TABLE time_tracking (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    schedule_id INT UNSIGNED NOT NULL,
    worker_id INT UNSIGNED NOT NULL,
    tenant_id INT UNSIGNED NOT NULL,
    clock_in DATETIME NOT NULL,
    clock_in_lat DECIMAL(10,8),
    clock_in_lng DECIMAL(11,8),
    clock_in_photo_url VARCHAR(500),
    break_start DATETIME,
    break_end DATETIME,
    clock_out DATETIME,
    clock_out_lat DECIMAL(10,8),
    clock_out_lng DECIMAL(11,8),
    clock_out_photo_url VARCHAR(500),
    total_regular_minutes INT GENERATED ALWAYS AS (
        TIMESTAMPDIFF(MINUTE, clock_in, clock_out) -
        COALESCE(TIMESTAMPDIFF(MINUTE, break_start, break_end), 0)
    ) STORED,
    total_overtime_minutes INT DEFAULT 0,
    night_shift_minutes INT DEFAULT 0,
    status ENUM('open', 'closed', 'disputed', 'approved') DEFAULT 'open',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tt_schedule (schedule_id),
    INDEX idx_tt_worker (worker_id),
    INDEX idx_tt_tenant (tenant_id),
    INDEX idx_tt_date (clock_in),
    CONSTRAINT fk_tt_schedule FOREIGN KEY (schedule_id) REFERENCES service_schedules(id) ON DELETE CASCADE,
    CONSTRAINT fk_tt_worker FOREIGN KEY (worker_id) REFERENCES workers(id) ON DELETE CASCADE,
    CONSTRAINT fk_tt_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
);

-- 5. eSocial Integration
CREATE TABLE esocial_integration (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT UNSIGNED NOT NULL,
    worker_id INT UNSIGNED NOT NULL,
    contract_type ENUM('CLT_DOMESTICO') NOT NULL DEFAULT 'CLT_DOMESTICO',
    admission_date DATE NOT NULL,
    termination_date DATE,
    salary DECIMAL(10,2) NOT NULL,
    weekly_hours INT NOT NULL DEFAULT 44,
    fgts_option BOOLEAN DEFAULT TRUE,
    transport_voucher_value DECIMAL(10,2) DEFAULT 0,
    esocial_status ENUM('PENDING', 'SYNCED', 'FAILED', 'CANCELLED') DEFAULT 'PENDING',
    esocial_error_message TEXT,
    last_sync_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_esocial_tenant (tenant_id),
    INDEX idx_esocial_worker (worker_id),
    INDEX idx_esocial_status (esocial_status),
    CONSTRAINT fk_esocial_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    CONSTRAINT fk_esocial_worker FOREIGN KEY (worker_id) REFERENCES workers(id) ON DELETE CASCADE
);

-- 6. DAE Payments (Guia do eSocial)
CREATE TABLE esocial_dae_payments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    esocial_id INT UNSIGNED NOT NULL,
    competence_month TINYINT NOT NULL,
    competence_year SMALLINT NOT NULL,
    inss_value DECIMAL(10,2) NOT NULL,
    fgts_value DECIMAL(10,2) NOT NULL,
    gilrat_value DECIMAL(10,2) NOT NULL,
    total_value DECIMAL(10,2) GENERATED ALWAYS AS (inss_value + fgts_value + gilrat_value) STORED,
    due_date DATE NOT NULL,
    paid_at TIMESTAMP NULL,
    dae_url VARCHAR(500),
    status ENUM('PENDING', 'PAID', 'OVERDUE', 'CANCELLED') DEFAULT 'PENDING',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_dae_esocial (esocial_id),
    INDEX idx_dae_competence (competence_year, competence_month),
    INDEX idx_dae_status (status),
    CONSTRAINT fk_dae_esocial FOREIGN KEY (esocial_id) REFERENCES esocial_integration(id) ON DELETE CASCADE
);

-- 7. Incident Reports (Acidentes de Trabalho)
CREATE TABLE incident_reports (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT UNSIGNED NOT NULL,
    worker_id INT UNSIGNED NOT NULL,
    schedule_id INT UNSIGNED,
    incident_type ENUM('ACCIDENT', 'HEALTH_EMERGENCY', 'PROPERTY_DAMAGE', 'THEFT', 'HARASSMENT', 'OTHER') NOT NULL,
    severity ENUM('LOW', 'MEDIUM', 'HIGH', 'CRITICAL') DEFAULT 'MEDIUM',
    description TEXT NOT NULL,
    occurred_at DATETIME NOT NULL,
    reported_by ENUM('WORKER', 'CLIENT', 'THIRD_PARTY') NOT NULL,
    cat_issued BOOLEAN DEFAULT FALSE,
    cat_number VARCHAR(50),
    insurance_claimed BOOLEAN DEFAULT FALSE,
    insurance_policy VARCHAR(100),
    status ENUM('OPEN', 'INVESTIGATING', 'RESOLVED', 'CLOSED') DEFAULT 'OPEN',
    resolution_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_inc_tenant (tenant_id),
    INDEX idx_inc_worker (worker_id),
    INDEX idx_inc_status (status),
    CONSTRAINT fk_inc_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    CONSTRAINT fk_inc_worker FOREIGN KEY (worker_id) REFERENCES workers(id) ON DELETE CASCADE
);

-- 8. LGPD Consent Evolution
ALTER TABLE lgpd_consent
    ADD COLUMN consent_version VARCHAR(10) DEFAULT '1.0',
    ADD COLUMN revoked_at TIMESTAMP NULL,
    ADD COLUMN revocation_ip VARCHAR(45),
    ADD COLUMN data_portability_requested BOOLEAN DEFAULT FALSE,
    ADD COLUMN data_erasure_requested BOOLEAN DEFAULT FALSE,
    ADD COLUMN data_erasure_completed_at TIMESTAMP NULL;
```

#### 4.1.3 Conflitos Identificados no Schema Atual

| Conflito | Detalhe | Recomendação |
|:---------|:--------|:-------------|
| `transactions` duplicada | Existe em `init.sql` e `migrations/002_create_transactions_table.sql` com schemas diferentes | Remover `migrations/002` e unificar em `init.sql` |
| `proposals.status` sem `paid` na migração | `init.sql` tem `paid` no ENUM; migração 002 não reflete | Descartar migrações avulsas, usar `init.sql` como única fonte |
| `migrations/001` já aplicado | `reset_token` e `reset_token_expires` existem na `users` do `init.sql` | Migrações são obsoletas — `init.sql` já as contém |

### 4.2 API REST (Node.js + Express)

#### 4.2.1 Padrões que Facilitam a Extensão

| Padrão | Arquivo | Benefício para Compliance |
|:-------|:--------|:--------------------------|
| Modular por domínio | `modules/{domain}/*.js` | Fácil adicionar `modules/domestic/`, `modules/esocial/`, `modules/incidents/` |
| Transaction helper | `config/database.js:transaction()` | Garante atomicidade em contratações CLT |
| Error codes padronizados | `middlewares/error.middleware.js` | Reutilizável para novos erros (`ERR_FREQUENCY_LIMIT`, `ERR_ESOCIAL_FAIL`) |
| Máquina de estados | `proposals/proposals.controller.js` | Padrão replicável para `service_schedules.status` |
| Structured JSON logging | `server.js:45-60` | Auditável para LGPD e rastreabilidade |
| Múltiplos rate limiters | `public/public.routes.js` | Padrão para novos endpoints sensíveis |
| HMAC webhook validation | `payments/payments.controller.js` | Padrão para webhooks de terceiros (eSocial, seguro) |

#### 4.2.2 Limitações que Precisam ser Superadas

| Limitação | Arquivo | Impacto | Solução |
|:----------|:--------|:--------|:--------|
| Sem websockets/Socket.io | N/A | Ponto eletrônico não pode ser real-time | Adicionar Socket.io ou polling curto (30s) |
| Sem fila de processamento | N/A | Cálculos trabalhistas e eSocial síncronos travam request | Adicionar Bull/BullMQ com Redis |
| Sem notificações push | N/A | Alertas de frequência, ponto, acidentes | Adicionar Firebase Cloud Messaging |
| Uploads em disco local | `upload.controller.js` | Sem HA, sem backup | Migrar para S3/Cloudflare R2 |
| `publicLeads` sem worker matching | `public.controller.js` | Leads ficam órfãos sem worker atribuído | Adicionar `worker_matching_queue` |
| Sem campo de `category` em `services` | `init.sql` | Serviços não mapeiam categorias CBO | Adicionar `cbo_code` em `services` |

### 4.3 Frontend (PHP)

#### 4.3.1 Estado Atual

- **Roteador:** Query string (`?page=...`)
- **Autenticação:** JWT em sessão PHP
- **Templates:** 19 templates, 6 parciais
- **JS:** Vanilla com `fetch()`, Tailwind CSS CDN

#### 4.3.2 Templates que Precisam ser Modificados

| Template | Mudança Necessária |
|:---------|:-------------------|
| `register.php` | Adicionar tipo de conta: Prestador vs. Contratante |
| `solicitar.php` | Adicionar categorias de empregado doméstico no wizard |
| `proposals.php` | Adaptar para regimes autônomo e CLT |
| `clients.php` | Separar conceito de client (tomador) vs. worker (trabalhador) |
| `home.php` | Adicionar landing page com categorias domésticas |

#### 4.3.3 Novos Templates Necessários

| Template | Finalidade |
|:---------|:-----------|
| `worker-register.php` | Cadastro de trabalhador doméstico |
| `workers.php` | CRUD de trabalhadores (prestadores) |
| `schedule.php` | Agendamento com controle de frequência |
| `time-tracking.php` | Ponto eletrônico (worker mobile view) |
| `esocial.php` | Dashboard eSocial com DAE |
| `incidents.php` | Reporte e acompanhamento de incidentes |
| `certifications.php` | Gestão de certificações do trabalhador |

### 4.4 Infraestrutura Docker

#### 4.4.1 Estado Atual

```
5 serviços: nginx, php, api, pma, mysql
Docker Compose com volumes bind-mount para dev
```

#### 4.4.2 Necessidades Adicionais

| Serviço | Justificativa |
|:--------|:--------------|
| Redis | Fila de processamento (BullMQ), cache de sessão, rate limiting distribuído |
| Worker Node | Processamento assíncrono (cálculos trabalhistas, eSocial, notificações) |

---

## 5. Plano de Ação e Recomendações

### 5.1 Fase 1: Fundação de Dados (Sprint 1-2)

**Prioridade:** CRÍTICA — Sem esses passos, nenhuma categoria doméstica pode ser operada.

| # | Ação | Artefatos | Esforço |
|:-:|:-----|:----------|:--------|
| 1.1 | Criar tabela `workers` com CBO e categorias LC 150 | `scripts/migrations/003_create_workers.sql` | M |
| 1.2 | Criar tabela `worker_certifications` | `scripts/migrations/004_create_worker_certifications.sql` | P |
| 1.3 | Criar tabela `service_schedules` com regime e frequência | `scripts/migrations/005_create_service_schedules.sql` | M |
| 1.4 | Migrar `proposals` para suportar `schedule_id` | `scripts/migrations/006_alter_proposals.sql` | P |
| 1.5 | Limpar migrações obsoletas (`001`, `002`) | Remoção de arquivos | P |
| 1.6 | Unificar schema: `init.sql` como única fonte da verdade | Revisão de `init.sql` | M |

### 5.2 Fase 2: Algoritmo Trava-Frequência (Sprint 2-3)

**Prioridade:** CRÍTICA — Risco jurídico direto.

| # | Ação | Artefatos | Esforço |
|:-:|:-----|:----------|:--------|
| 2.1 | Módulo `api-backend/modules/domestic/` com camadas routes, controller, service | `domestic.routes.js`, `domestic.controller.js`, `domestic.service.js` | M |
| 2.2 | `GET /api/v1/domestic/frequency-check/:workerCpf/:clientCpf` — verifica agendamentos da semana | `domestic.controller.js` | P |
| 2.3 | Trava no backend: bloquear `POST /schedule` se 3º+ dia na mesma semana para mesmo CPF | `domestic.service.js` | P |
| 2.4 | Fluxo de transição diarista→CLT com calculadora de custos | `domestic.service.js` + template | G |
| 2.5 | Notificação push ao contratante ao atingir 2 agendamentos (alerta preventivo) | `domestic.service.js` + FCM | M |

### 5.3 Fase 3: Módulo de Jornada e Ponto (Sprint 3-4)

**Prioridade:** ALTA — Exigência legal (Art. 12 LC 150).

| # | Ação | Artefatos | Esforço |
|:-:|:-----|:----------|:--------|
| 3.1 | Criar tabela `time_tracking` (já modelada na seção 4.1.2) | Migration SQL | M |
| 3.2 | Módulo `api-backend/modules/timetracking/` | `timetracking.routes.js`, `.controller.js`, `.service.js` | G |
| 3.3 | `POST /api/v1/timetracking/clock-in` com geolocalização | `timetracking.controller.js` | M |
| 3.4 | `POST /api/v1/timetracking/clock-out` com foto | `timetracking.controller.js` | M |
| 3.5 | `POST /api/v1/timetracking/break-start` + `break-end` | `timetracking.controller.js` | P |
| 3.6 | Engine de cálculo: horas extras, adicional noturno, 12×36 | `timetracking.service.js` | G |
| 3.7 | Notificação de inconsistência ao contratante (fim do dia) | `timetracking.service.js` | P |
| 3.8 | Endpoint para worker visualizar próprio espelho de ponto | `timetracking.controller.js` | M |

### 5.4 Fase 4: Integração eSocial Doméstico (Sprint 4-6)

**Prioridade:** ALTA — Sem eSocial, não há contratação CLT legal.

| # | Ação | Artefatos | Esforço |
|:-:|:-----|:----------|:--------|
| 4.1 | Criar tabelas `esocial_integration` e `esocial_dae_payments` | Migration SQL | M |
| 4.2 | Módulo `api-backend/modules/esocial/` | `esocial.routes.js`, `.controller.js`, `.service.js` | G |
| 4.3 | Integração com API eSocial Doméstico (produção) | `esocial.service.js` + webhook | GG |
| 4.4 | Calculadora de custos patronais (salário + INSS + FGTS + VT) | `esocial.service.js` | M |
| 4.5 | Geração mensal da guia DAE | `esocial.service.js` | M |
| 4.6 | Webhook para receber confirmação de pagamento DAE | `esocial.routes.js` | P |
| 4.7 | Dashboard de compliance trabalhista por trabalhador | Template PHP + API | M |

### 5.5 Fase 5: Worker Onboarding & Certificações (Sprint 4-5)

**Prioridade:** MÉDIA — Melhora qualidade e segurança.

| # | Ação | Artefatos | Esforço |
|:-:|:-----|:----------|:--------|
| 5.1 | `POST /api/v1/workers` — cadastro com CPF, CBO, foto | `modules/domestic/` | M |
| 5.2 | `POST /api/v1/workers/:id/background-check` — integração com Serasa/Lookout | `domestic.service.js` | M |
| 5.3 | `CRUD /api/v1/workers/:id/certifications` | `domestic.routes.js` | M |
| 5.4 | Verificação de certificação por categoria (cuidador exige curso) | `domestic.service.js` (validador) | P |
| 5.5 | Frontend: `workers.php` com busca por CBO + categoria | Template + API | M |

### 5.6 Fase 6: Incidentes, Seguro & Emergência (Sprint 6-7)

**Prioridade:** MÉDIA — Proteção ao trabalhador.

| # | Ação | Artefatos | Esforço |
|:-:|:-----|:----------|:--------|
| 6.1 | Criar tabela `incident_reports` | Migration SQL | P |
| 6.2 | Módulo `api-backend/modules/incidents/` | `incidents.routes.js`, `.controller.js`, `.service.js` | M |
| 6.3 | Botão de emergência com geolocalização e acionamento de contato de emergência | `incidents.controller.js` + FCM | M |
| 6.4 | Integração com seguradora para acionamento automático | `incidents.service.js` | G |
| 6.5 | Emissão de CAT (Comunicação de Acidente de Trabalho) | `incidents.service.js` | M |
| 6.6 | SLA de atendimento: <15min emergência, 4h acidente, 24h disputa | `incidents.service.js` | P |

### 5.7 Fase 7: LGPD Completo (Sprint 2 — Contínuo)

**Prioridade:** MÉDIA — Já parcialmente coberto.

| # | Ação | Artefatos | Esforço |
|:-:|:-----|:----------|:--------|
| 7.1 | `POST /api/v1/lgpd/revoke-consent` — revogação de consentimento | Módulo existente `auth` | P |
| 7.2 | `GET /api/v1/lgpd/export-data` — portabilidade (JSON + CSV) | `modules/lgpd/` | M |
| 7.3 | `DELETE /api/v1/lgpd/erase-data` — anonimização de dados pessoais | `modules/lgpd/` | G |
| 7.4 | Adicionar versionamento de consentimento na tabela `lgpd_consent` | Migration | P |
| 7.5 | Log de todas as operações LGPD no `audit_log` | Middleware existente | P |

### 5.8 Fase 8: Testes e Qualidade (Contínuo)

**Prioridade:** ALTA — Risco de regressão.

| # | Ação | Artefatos | Esforço |
|:-:|:-----|:----------|:--------|
| 8.1 | Criar `api-backend/__tests__/` com estrutura de diretórios por módulo | Pastas | P |
| 8.2 | Testes unitários para `domestic/frequency-check.service.js` | Jest | M |
| 8.3 | Testes de integração para `timetracking/clock-in` com banco de teste | Jest + Supertest | M |
| 8.4 | Testes de contrato para webhook eSocial | Jest + nock | M |
| 8.5 | Configurar ESLint com regras do projeto | `.eslintrc.js` | P |
| 8.6 | Adicionar GitHub Actions para CI (lint → test) | `.github/workflows/ci.yml` | M |

---

## 6. Glossário de Riscos

### 6.1 Risco Jurídico-Crítico (Prazo: Imediato)

| Risco | Probabilidade | Impacto | Mitigação |
|:------|:-------------:|:-------:|:----------|
| Descaracterização de diarista autônoma como CLT por falta de trava algorítmica | Alta | Passivo trabalhista (verbas rescisórias, multas) | Implementar Fase 2 antes de lançar categorias domésticas |
| Ausência de controle de ponto eletrônico | Alta | Passivo trabalhista (Art. 12 LC 150) | Fase 3 obrigatória antes de contratos CLT |
| Falta de integração eSocial | Alta | Passivo tributário e multas (INSS, FGTS) | Fase 4 obrigatória antes de contratos CLT |

### 6.2 Risco Operacional-Alto

| Risco | Impacto | Mitigação |
|:------|:--------|:----------|
| Acidente de trabalho sem seguro | Responsabilidade civil do contratante e da plataforma | Fase 6 obrigatória |
| Certificação falsa de cuidador | Risco à integridade física de idosos/crianças | Fase 5 + verificação documental |
| Vazamento de dados biométricos (foto ponto eletrônico) | LGPD — multa de até 2% do faturamento | Criptografia em repouso + consentimento explícito |

### 6.3 Risco Técnico

| Risco | Impacto | Mitigação |
|:------|:--------|:----------|
| Sem fila de processamento (eSocial síncrono) | Timeout em operações longas | Adicionar Redis + BullMQ antes da Fase 4 |
| Upload em disco local sem backup | Perda de fotos de ponto e certificações | Migrar para S3/R2 antes da Fase 5 |
| Tailwind CDN em produção | Dependência externa quebra frontend | Localizar Tailwind build antes de produção |

---

## 7. Anexos Técnicos

### Anexo A: Mapa de Rotas da API — Novos Endpoints

```
MÓDULO DOMESTIC (/api/v1/domestic)
  GET    /frequency-check/:workerCpf/:clientCpf  → Verifica frequência semanal
  POST   /transition-to-clt                      → Inicia fluxo diarista→CLT
  GET    /cost-calculator                        → Calculadora de custos patronais

MÓDULO WORKERS (/api/v1/workers)
  GET    /                                       → Lista trabalhadores
  POST   /                                       → Cadastra trabalhador
  GET    /:id                                    → Detalhes do trabalhador
  PUT    /:id                                    → Atualiza dados
  DELETE /:id                                    → Exclusão lógica
  GET    /:id/certifications                     → Lista certificações
  POST   /:id/certifications                     → Adiciona certificação
  POST   /:id/background-check                   → Solicita verificação

MÓDULO SCHEDULES (/api/v1/schedules)
  GET    /                                       → Lista agendamentos
  POST   /                                       → Cria agendamento (com trava)
  GET    /:id                                    → Detalhes
  PATCH  /:id/status                             → Atualiza status
  GET    /frequency/:workerId/:clientId           → Histórico de frequência

MÓDULO TIMETRACKING (/api/v1/timetracking)
  POST   /clock-in                               → Registra entrada (GPS + foto)
  POST   /clock-out                              → Registra saída
  POST   /break-start                            → Início do intervalo
  POST   /break-end                              → Fim do intervalo
  GET    /report/:workerId                       → Espelho de ponto
  GET    /report/:workerId/:period               → Relatório mensal

MÓDULO ESOCIAL (/api/v1/esocial)
  POST   /admission                              → Admissão eSocial
  POST   /termination                            → Rescisão
  GET    /dae/:competence                        → Consulta DAE mensal
  POST   /dae/:competence/pay                    → Marca DAE como pago
  GET    /dashboard/:workerId                    → Dashboard compliance
  POST   /webhook                                → Callback eSocial (IPN)

MÓDULO INCIDENTS (/api/v1/incidents)
  POST   /                                       → Reporta incidente
  GET    /                                       → Lista incidentes
  GET    /:id                                    → Detalhes
  POST   /:id/sos                                → Aciona emergência
  POST   /:id/cat                                → Emite CAT

MÓDULO LGPD (/api/v1/lgpd)
  POST   /revoke-consent                         → Revoga consentimento
  GET    /export-data                            → Portabilidade
  DELETE /erase-data                             → Anonimização
```

### Anexo B: JSON Schema do Contrato de Trabalho Doméstico

```json
{
  "$schema": "https://json-schema.org/draft/2020-12/schema",
  "title": "DomesticWorkerContract",
  "type": "object",
  "properties": {
    "contract_id": { "type": "string", "example": "CTR-2026-98421" },
    "employer": {
      "type": "object",
      "properties": {
        "cpf": { "type": "string" },
        "esocial_registered": { "type": "boolean" },
        "residence_type": { "type": "string", "enum": ["URBAN_HOUSE", "APARTMENT", "RURAL_PROPERTY"] }
      },
      "required": ["cpf", "esocial_registered", "residence_type"]
    },
    "worker": {
      "type": "object",
      "properties": {
        "cpf": { "type": "string" },
        "cbo_code": { "type": "string", "example": "5121-05" },
        "category": {
          "type": "string",
          "enum": [
            "EMPREGADO_DOMESTICO_GERAL", "DIARISTA", "BABA",
            "CUIDADOR_IDOSOS", "COZINHEIRO", "MOTORISTA",
            "JARDINEIRO", "CASEIRO", "GOVERNANTA"
          ]
        }
      },
      "required": ["cpf", "cbo_code", "category"]
    },
    "engagement_terms": {
      "type": "object",
      "properties": {
        "regime": { "type": "string", "enum": ["AUTONOMO_DIARISTA", "LC_150_CLT"] },
        "weekly_frequency_days": { "type": "integer", "minimum": 1, "maximum": 7 },
        "work_hours_per_week": { "type": "number" },
        "hourly_rate": { "type": "number" },
        "transportation_voucher_required": { "type": "boolean" }
      },
      "required": ["regime", "weekly_frequency_days"]
    },
    "compliance_checks": {
      "type": "object",
      "properties": {
        "is_diarista_limit_exceeded": { "type": "boolean" },
        "esocial_integration_status": {
          "type": "string", "enum": ["PENDING", "SYNCED", "FAILED"]
        },
        "mandatory_rest_interval_verified": { "type": "boolean" }
      }
    }
  },
  "required": ["contract_id", "employer", "worker", "engagement_terms", "compliance_checks"]
}
```

### Anexo C: Dependências npm a Adicionar

```json
{
  "dependencies": {
    "socket.io": "^4.7.0",
    "bull": "^4.12.0",
    "ioredis": "^5.4.0",
    "firebase-admin": "^12.0.0",
    "axios": "^1.7.0"
  },
  "devDependencies": {
    "nock": "^13.5.0",
    "mongodb-memory-server": "^9.0.0"
  }
}
```

### Anexo D: Docker Compose — Novos Serviços

```yaml
services:
  redis:
    image: redis:7-alpine
    container_name: flex_redis
    ports:
      - "6379:6379"
    volumes:
      - redis_data:/data
    networks:
      - flex-network
    restart: unless-stopped

  worker:
    build:
      context: ./api-backend
      dockerfile: Dockerfile
    container_name: flex_worker
    command: ["node", "worker.js"]
    volumes:
      - ./api-backend:/usr/src/app
      - /usr/src/app/node_modules
    env_file:
      - .env
    depends_on:
      - redis
      - mysql
    networks:
      - flex-network
    restart: unless-stopped

volumes:
  redis_data:
```

---

*Documento de auditoria gerado em 28/07/2026 para a plataforma ServiceSaaS (Serviços Flex).*
*Base legal: Lei Complementar nº 150/2015, CBO 2026, eSocial Doméstico.*