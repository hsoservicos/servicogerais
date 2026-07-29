# 🚀 ServiceSaaS — Plano Estratégico V2 (Pós-Auditoria)

**Plataforma de Gestão Inteligente para Prestadores de Serviços**

---

| Metadado | Valor |
|:---|---:|
| **Versão** | `3.0` |
| **Data** | `2026-07-29` |
| **Classificação** | Público Interno |
| **Status** | ✅ Planejamento V2 — Pós Auditoria Completa |
| **Stack** | PHP 8.2 · Node.js 20 · MySQL 8 · Docker |
| **Repositório** | `servicos-flex/` |

---

## 📑 Índice

1. [Resumo Executivo da Auditoria](#1-resumo-executivo-da-auditoria)
2. [Status Atual por Módulo](#2-status-atual-por-módulo)
3. [Gaps Críticos Identificados](#3-gaps-críticos-identificados)
4. [Nova Estrutura de Épicos](#4-nova-estrutura-de-épicos)
5. [Roadmap de Implementação](#5-roadmap-de-implementação)
6. [Pipeline de Qualidade (Testes)](#6-pipeline-de-qualidade-testes)
7. [Refatoração Técnica](#7-refatoração-técnica)
8. [Segurança e Hardening](#8-segurança-e-hardening)
9. [Matriz de Riscos Atualizada](#9-matriz-de-riscos-atualizada)
10. [Estrutura de Equipe e Governança](#10-estrutura-de-equipe-e-governança)

---

## 1. Resumo Executivo da Auditoria

### 1.1 O Que Foi Construído (86 endpoints, 18 tabelas, 30 templates)

O ServiceSaaS já possui uma base operacional completa:

| Dimensão | Quantidade |
|:---|---:|
| Endpoints de API | **86** (13 módulos) |
| Tabelas no banco | **18** (13 init.sql + 5 migrations) |
| Páginas frontend | **24** + 6 parciais = **30 PHP** |
| Containers Docker | **5** (nginx, php, api, pma, mysql) |
| Commits realizados | **6** (projeto em evolução) |

### 1.2 O Que Está Faltando

| Categoria | Item | Impacto |
|:---|---|:---:|
| 🔴 Qualidade | **ZERO testes automatizados** | Crítico |
| 🔴 Compliance | Controle de ponto eletrônico (GPS + foto) — Art. 12 LC 150 | Passivo trabalhista |
| 🔴 Compliance | Integração eSocial Doméstico | Passivo fiscal |
| 🟡 Compliance | Motor de cálculo trabalhista (horas extras, noturno, 12x36) | Passivo salarial |
| 🟡 Compliance | Incidentes, seguro e CAT | Risco civil |
| 🟡 Segurança | JWT secret padrão, CORS \*, email stub | Exploração |
| 🟡 LGPD | Deleção de dados (hoje só faz log) | Multa até 2% |
| 🟡 Qualidade | Refatoração de templates monolíticos | Manutenibilidade |
| 🟢 Infra | Migration framework | Versionamento |

---

## 2. Status Atual por Módulo

### ✅ Completos (100% operacional)

| Módulo | API | Frontend | Epic |
|--------|:---:|:--------:|:----:|
| Autenticação (register/login/me/forgot/reset) | ✅ | ✅ | E1 |
| Tenants (perfil + endereço) | ✅ | ✅ | E1/E14 |
| Clientes (CRUD + soft-delete) | ✅ | ✅ | E2 |
| Catálogo (categorias + serviços) | ✅ | ✅ | E2 |
| Propostas (CRUD + itens + PDF + WhatsApp + aprovação pública) | ✅ | ✅ | E3 |
| Dashboard (KPIs + gráfico Chart.js + follow-up) | ✅ | ✅ | E4 |
| Pagamentos MP (preference + webhook + estorno) | ✅ | 🔶 Parcial | E5 |
| Transações (histórico financeiro) | ✅ | ✅ | E4 |
| Leads (captura + wizard 3 etapas + gestão) | ✅ | ✅ | E6 |
| Público (landing page + busca + upload) | ✅ | ✅ | E6 |
| Admin (dashboard + tenants + planos + relatórios CSV + auditoria) | ✅ | ✅ | E7 |
| Workers (CRUD + CBO + 9 categorias LC 150) | ✅ | ✅ | E8 |
| Agendamentos (CRUD + trava frequência) | ✅ | ✅ | E9 |
| Cálculo CLT (INSS/FGTS/13º/férias) | ✅ | ✅ | E9 |
| Acordos CLT (domestic_agreements + transição) | ✅ | ✅ | E9 |
| LGPD Exportação + Consentimento | ✅ | ✅ | E13 |

### 🟡 Parciais (funcionando mas incompletos)

| Módulo | O Que Falta | Prioridade |
|--------|-------------|:----------:|
| Pagamentos Frontend | Checkout Pix incorporado na página pública | 🟡 Média |
| Background Check | Endpoint existe mas sem integração real com API externa | 🟡 Média |
| Frequência-Lock | Algoritmo existe mas precisa hardening (feriados, multi-tenant) | 🟡 Média |
| LGPD Deleção | Apenas log da solicitação — não executa anonimização real | 🟡 Média |

### ❌ Não Construídos

| Módulo | Descrição | Prioridade |
|--------|-----------|:----------:|
| Testes Automatizados | Jest + Supertest instalados, 0 testes escritos | 🔴 Crítica |
| Ponto Eletrônico | Clock-in/out com GPS + foto (Art. 12 LC 150) | 🔴 Alta |
| eSocial Doméstico | Admissão, DAE, FGTS via job queue | 🔴 Alta |
| Motor Trabalhista | Horas extras, noturno, escala 12x36 | 🟡 Média |
| Incidentes/Seguro | Reporte, botão SOS, emissão CAT | 🟡 Média |
| Serviço de Email | Hoje é stub (console.log) | 🟡 Média |
| Migration Framework | SQL manual sem version tracking | 🟢 Baixa |

---

## 3. Gaps Críticos Identificados

### 3.1 Quality Gap — ZERO Testes

O projeto tem **Jest 29.7.0 + Supertest 6.3.4 instalados** mas **zero arquivos de teste**. Cada novo módulo aumenta o risco de regressão. Este é o **gap mais crítico** e deve ser tratado como requisito obrigatório para qualquer novo desenvolvimento.

### 3.2 Compliance Gap — Doméstico (LC 150/2015)

| Requisito Legal | Status | Risco |
|:---|---|:---:|
| Art. 1º — Frequência ≤2d/sem diaristas | 🟡 Parcial (precisa hardening) | Descaracterização CLT |
| Art. 12 — Controle de ponto eletrônico | ❌ Não construído | Passivo trabalhista |
| eSocial Doméstico — Admissão | ❌ Não construído | Passivo fiscal |
| eSocial Doméstico — DAE mensal | ❌ Não construído | Passivo fiscal |
| Jornada 12×36 / HE / Noturno | ❌ Não construído | Passivo salarial |
| CAT — Acidente de trabalho | ❌ Não construído | Risco civil |

### 3.3 Security Gaps

| Item | Severidade | Ação |
|:---|---|:---:|
| JWT_SECRET = "change-me-in-production" | 🔴 Alta | Gerar secret 64 caracteres |
| CORS origin: * | 🟡 Média | Restringir por origem |
| Sem HTTPS (dev ok, prod precisa) | 🟡 Média | Cloudflare Tunnel |
| MP_ACCESS_TOKEN vazio | 🟡 Média | Degradado ok, mas documentar |
| Email service = console.log | 🟡 Média | Integrar SendGrid/Mailgun |

---

## 4. Nova Estrutura de Épicos

### Legenda

| Símbolo | Significado |
|:-------:|:------------|
| ✅ | **COMPLETO** — 100% operacional em produção |
| 🔶 | **PARCIAL** — Funciona mas precisa melhoria |
| 📝 | **PLANEJADO** — A construir |
| 🔴 | **BLOQUEADO** — Depende de outro épico |

### Mapa de Épicos V2

```
E1  🔐  Autenticação & Onboarding            [5 stories]  ✅ COMPLETO
E2  👥  Clientes & Catálogo                 [3 stories]  ✅ COMPLETO
E3  📄  Ciclo de Vida da Proposta           [6 stories]  ✅ COMPLETO
E4  📊  Dashboard & Métricas                [4 stories]  ✅ COMPLETO
E5  💳  Pagamentos Mercado Pago             [5 stories]  ✅ COMPLETO
E6  🌐  Presença Pública & Leads            [4 stories]  ✅ COMPLETO
E7  🏢  Administração da Plataforma         [4 stories]  ✅ COMPLETO
E8  🏠  Workers & Certificações             [3 stories]  ✅ COMPLETO
E9  ⏱️  Trava Frequência & Agendamento       [3 stories]  ✅ COMPLETO
E10 🕐  Ponto Eletrônico & Jornada          [4 stories]  📝 NOVO
E11 📋  eSocial Doméstico                   [3 stories]  📝 NOVO
E12 🚨  Incidentes & Emergência             [3 stories]  📝 NOVO
E13 🔐  LGPD & Privacidade                  [3 stories]  🔶 PARCIAL
E14 📍  Perfil & Proximidade               [3 stories]  ✅ COMPLETO
───────────────────────────────────────────────────────────────
E15 🧪  Pipeline de Qualidade & Testes     [4 stories]  📝 PRIORIDADE #1
E16 🏗️  Refatoração Técnica                [3 stories]  📝 MÉDIO
E17 🛡️  Hardening de Segurança             [4 stories]  📝 ALTA
```

### Detalhamento dos Novos Épicos

---

#### 🧪 E15 — Pipeline de Qualidade & Testes (PRIORIDADE #1)

**Justificativa:** Projeto tem 86 endpoints e 0 testes. Risco de regressão inviabiliza qualquer avanço seguro.

**Dependências:** Nenhuma
**Risco:** 🔴 Crítico — Sem testes, todo novo código é cego

**Story 15.1:** Setup de Testes — Configurar Jest + Supertest + banco de testes + fixtures
- **AC:** `npm test` executa suite completa com banco MySQL de teste via Docker
- **Arquivos:** `jest.config.js`, `__tests__/setup/`, `__tests__/helpers/`
- **Esforço:** M

**Story 15.2:** Testes de API — Auth + Tenants + Clients + Catalog
- **AC:** 100% dos endpoints de E1 e E2 cobertos com testes de contrato
- **Cobertura:** register, login, forgot/reset, me, tenants CRUD, clients CRUD, categories CRUD, services CRUD
- **Esforço:** G

**Story 15.3:** Testes de API — Propostas + Dashboard + Payments + Transactions
- **AC:** 100% dos endpoints de E3, E4, E5 cobertos
- **Cobertura:** proposals CRUD + status lifecycle + items, dashboard KPIs/chart/followup, payments preference/webhook/refund, transactions list
- **Esforço:** G

**Story 15.4:** Testes de API — Leads + Public + Admin + Workers + Schedules + Domestic
- **AC:** 100% dos endpoints de E6, E7, E8, E9 cobertos
- **Cobertura:** public endpoints, admin CRUD, workers CRUD + certifications, schedules + frequency lock, domestic costs, LGPD endpoints
- **Esforço:** G

**Entregáveis:** ~200 testes, cobertura >70%, CI/CD gate

---

#### 🏗️ E16 — Refatoração Técnica

**Justificativa:** 5 arquivos com >700 linhas cada, dificultam manutenção.

**Dependências:** Nenhuma
**Risco:** 🟡 Médio

**Story 16.1:** Refatorar `proposals.php` (1180 linhas) em includes modulares
- **AC:** proposals.php quebrado em: `proposals-list.php`, `proposals-form.php`, `proposals-view.php`
- **Esforço:** G

**Story 16.2:** Refatorar `solicitar.php` (1087 linhas) + `public-proposal.php` (734 linhas)
- **AC:** Wizard 3 etapas em includes separados por passo
- **Esforço:** M

**Story 16.3:** Refatorar `workers.php` (800 linhas) + controllers grandes
- **AC:** workers.php quebrado em partes, admin.controller.js (734 linhas) extraído para múltiplos controllers
- **Esforço:** M

---

#### 🛡️ E17 — Hardening de Segurança

**Justificativa:** Senhas padrão, CORS aberto, email inexistente.

**Dependências:** Nenhuma
**Risco:** 🔴 Alto

**Story 17.1:** JWT Secret + CORS + Headers de segurança
- **AC:** JWT_SECRET gerado com 64 caracteres aleatórios, CORS restrito a origens configuradas, helmet configurado com CSP, HSTS, X-Frame-Options
- **Esforço:** P

**Story 17.2:** Serviço de Email Real
- **AC:** Substituir `console.log` por integração SendGrid/Mailgun, template de e-mail para boas-vindas, recuperação de senha, notificações
- **Esforço:** M

**Story 17.3:** Rate Limiting + Brute Force Protection
- **AC:** Rate limit por IP + por rota, bloqueio temporário após N tentativas, headers `Retry-After`
- **Esforço:** P

**Story 17.4:** HTTPS + Cloudflare Tunnel
- **AC:** Cloudflare tunnel configurado para produção, SSL automático, DDoS protection
- **Esforço:** M

---

## 5. Roadmap de Implementação

### Fase 1: Fundação de Qualidade (Sprints 1-2)
**Prioridade máxima — sem testes, nada avança**

```
Sprint 1: Setup testes + Auth/Clients/Catalog tests (E15.1 + E15.2)
Sprint 2: Proposals/Dashboard/Payments tests (E15.3 + E15.4)
```

### Fase 2: Compliance Doméstico (Sprints 3-6)
**Módulos obrigatórios LC 150/2015**

```
Sprint 3: Ponto Eletrônico — GPS + foto + intervalo (E10.1 + E10.2)
Sprint 4: Motor Trabalhista — HE, noturno, 12x36 (E10.3 + E10.4)
Sprint 5: eSocial — Admissão + DAE + Dashboard (E11.1 + E11.2 + E11.3)
Sprint 6: Incidentes + SOS + CAT + LGPD Deleção (E12 + E13.2)
```

### Fase 3: Hardening & Refatoração (Sprint 7-8)
**Qualidade de código e segurança**

```
Sprint 7: Hardening — JWT, CORS, Email, Rate Limit (E17)
Sprint 8: Refatoração — Templates grandes (E16)
```

---

## 6. Pipeline de Qualidade (Testes)

### Stack de Testes

| Ferramenta | Função | Status |
|:---|---|:---:|
| Jest 29.7 | Test runner | ✅ Instalado |
| Supertest 6.3 | HTTP assertions | ✅ Instalado |
| MySQL test container | Banco de testes | 📝 Configurar |
| GitHub Actions | CI/CD | 📝 Configurar |
| ESLint | Linting | ✅ No package.json |

### Quality Gates

| Gate | Descrição | Obrigatório |
|:---|---|:---:|
| Lint | `npm run lint` sem erros | ✅ Sim |
| Test | `npm test` 100% verde | ✅ Sim |
| Coverage | Mínimo 70% | ✅ Sim |
| Build | Docker build sem警告 | ✅ Sim |
| Security Scan | Trivy sem críticos | 📝 Futuro |

### Estrutura de Testes Proposta

```
api-backend/
  __tests__/
    setup/
      jest.setup.js          # Setup global (DB, app)
      fixtures.js            # Dados de teste (tenants, users, etc)
    helpers/
      auth.helper.js         # Login helper para testes
      db.helper.js           # DB operations
    auth/
      auth.register.test.js
      auth.login.test.js
      auth.me.test.js
      auth.forgot.test.js
      auth.reset.test.js
    clients/
      clients.crud.test.js
    catalog/
      categories.test.js
      services.test.js
    proposals/
      proposals.crud.test.js
      proposals.status.test.js
      proposals.items.test.js
    payments/
      payments.preference.test.js
      payments.webhook.test.js
      payments.refund.test.js
    admin/
      admin.tenants.test.js
      admin.plans.test.js
      admin.reports.test.js
    workers/
      workers.crud.test.js
      workers.certifications.test.js
      workers.background.test.js
    schedules/
      schedules.crud.test.js
      schedules.frequency.test.js
    domestic/
      domestic.costs.test.js
      domestic.agreements.test.js
```

---

## 7. Refatoração Técnica

### Arquivos para Refatorar

| Arquivo | Linhas | Ação | Prioridade |
|---------|:------:|:-----|:----------:|
| `proposals.php` | 1180 | Extrair list/form/view | 🟡 Média |
| `solicitar.php` | 1087 | Extrair steps 1/2/3 | 🟡 Média |
| `workers.php` | 800 | Extrair list/form/cert | 🟢 Baixa |
| `public-proposal.php` | 734 | Extrair seções | 🟢 Baixa |
| `admin.controller.js` | 734 | Extrair por domínio | 🟢 Baixa |
| `proposals.controller.js` | 547 | Extrair service layer | 🟢 Baixa |

### Migration Framework Ausente

Atualmente: `init.sql` + migrations sequenciais sem controle de versão.

**Solução proposta:** Adotar `mysql2` + tabela `schema_migrations` para tracking.
- Criar `scripts/migrate.js` (CLI Node.js)
- Tabela `schema_migrations` com: `version`, `name`, `applied_at`, `checksum`
- Comando `make migrate` executa pendentes

---

## 8. Segurança e Hardening

### Checklist de Segurança

| Item | Status | Responsável |
|:---|---|:---:|
| JWT_SECRET 64 caracteres aleatórios | ❌ Pendente | Dev |
| CORS restrito por origem | ❌ Pendente | Dev |
| Helmet configurado (CSP, HSTS, XFO) | ❌ Pendente | Dev |
| Rate limiting por rota + IP | 🟡 Parcial | Dev |
| Email service real (SendGrid) | ❌ Pendente | Dev |
| HTTPS via Cloudflare Tunnel | ❌ Pendente | DevOps |
| Input validation server-side | ✅ OK | — |
| Prepared statements (SQLi) | ✅ OK | — |
| Bcrypt password hashing | ✅ OK | — |
| XSS prevention (htmlspecialchars) | ✅ OK | — |
| Audit logging (admin_audit_log) | ✅ OK | — |
| LGPD consent granular | ✅ OK | — |

---

## 9. Matriz de Riscos Atualizada

| Risco | Probabilidade | Impacto | Mitigação |
|:------|:------------:|:-------:|:----------|
| Regressão por falta de testes | 🔴 Alta | 🔴 Crítico | Sprint dedicado a testes ANTES de qualquer feature |
| Passivo trabalhista (LC 150) | 🔴 Alta | 🔴 Crítico | Construir time tracking + eSocial como prioridade |
| Violação LGPD (multa 2%) | 🟡 Média | 🔴 Alto | Implementar deleção real no Sprint 6 |
| Ataque por JWT secret padrão | 🟡 Média | 🔴 Alto | Hardening Sprint 7 |
| Perda de dados sem migration | 🟢 Baixa | 🟡 Médio | Migration framework Sprint 8 |
| Rotatividade por código monolítico | 🟡 Média | 🟡 Médio | Refatoração Sprint 8 |

---

## 10. Estrutura de Equipe e Governança

### Papéis Recomendados

| Papel | Responsabilidade |
|:------|:-----------------|
| **Tech Lead (Amelia)** | Arquitetura, revisão técnica, decisões |
| **Product Manager (John)** | Priorização, stakeholder, requisitos |
| **QA Engineer (Murat)** | Testes, CI/CD, quality gates |
| **UX Designer (Sally)** | Interface, experiência, design system |
| **Dev Full Stack** | Implementação sprints |

### Ritual de Sprints

| Evento | Frequência | Duração |
|:-------|:----------:|:-------:|
| Planning | 1/sprint | 2h |
| Daily | Diário | 15min |
| Review | 1/sprint | 1h |
| Retro | 1/sprint | 1h |

### Política de Commits

- `feat:` — Nova funcionalidade
- `fix:` — Correção de bug
- `test:` — Testes
- `refactor:` — Refatoração
- `docs:` — Documentação
- `chore:` — Manutenção

---

## Anexo A: Estado Atual do Código

| Métrica | Valor |
|:---|---:|
| Commits | 6 |
| Endpoints API | 86 |
| Tabelas | 18 |
| Templates PHP | 30 |
| Testes | 0 |
| Docker containers | 5 |
| Módulos backend | 13 |
| Arquivos JS | ~30 |
| Arquivos PHP | ~30 |

---

## Anexo B: Comandos de Desenvolvimento

```bash
make setup          # Build + up (primeira vez)
make up             # Start containers
make logs-api       # Logs da API
make api            # Shell no container Node
make npm-install    # Instalar dependências
make npm-dev        # Hot-reload (nodemon)
make test           # Rodar testes
make migrate        # Executar migrations
make seed           # Dados de exemplo
```

---

*Documento gerado em 29 de Julho de 2026 — Pós Auditoria Completa*
*Substitui PLANEJAMENTO_MODERNO_PROJETO.md como fonte da verdade*
