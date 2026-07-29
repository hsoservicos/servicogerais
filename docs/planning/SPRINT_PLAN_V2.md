# Sprint Plan V2 — ServiceSaaS (Serviços Flex)

**Data:** 29/07/2026
**Épicos:** 17 (9 completos + 3 parciais + 5 novos qualidade/refatoração/segurança)
**Estratégia:** QUALIDADE PRIMEIRO — testes obrigatórios antes de qualquer nova feature

---

## Sprint 1: 🧪 Fundação de Testes + Setup

**Foco:** Criar infraestrutura de testes + cobrir módulos core
**Duração:** 2 semanas

| Story | Épico | Descrição | Esforço | Dependências |
|:------|:-----:|:----------|:-------:|:-------------|
| 15.1 | E15 | Setup Jest + Supertest + banco teste + fixtures | M | Nenhuma |
| 15.2 | E15 | Testes Auth + Tenants + Clients + Catalog (~50 testes) | G | 15.1 |
| 17.1 | E17 | JWT Secret + CORS + Helmet | P | Nenhuma |
| — | — | Configurar ESLint + .editorconfig | P | Nenhuma |

**Marcos:** 🏁 `npm test` roda verde com 50+ testes, lint configurado

---

## Sprint 2: 🧪 Testes de Negócio + Propostas

**Foco:** Cobertura dos módulos de negócio mais críticos
**Duração:** 2 semanas

| Story | Épico | Descrição | Esforço | Dependências |
|:------|:-----:|:----------|:-------:|:-------------|
| 15.3 | E15 | Testes Propostas + Dashboard + Payments + Transactions (~60 testes) | G | 15.1 |
| 17.2 | E17 | Serviço de Email (SendGrid/Mailgun) | M | Nenhuma |
| 17.3 | E17 | Rate Limiting + Brute Force Protection | P | Nenhuma |

**Marcos:** 🏁 110+ testes, email funcional, rate limit reforçado

---

## Sprint 3: 🧪 Testes Restantes + Início Compliance

**Foco:** Completar cobertura total + iniciar compliance doméstico
**Duração:** 2 semanas

| Story | Épico | Descrição | Esforço | Dependências |
|:------|:-----:|:----------|:-------:|:-------------|
| 15.4 | E15 | Testes Public + Admin + Workers + Schedules + Domestic (~60 testes) | G | 15.1 |
| 13.2 | E13 | Eliminação de dados LGPD (direito ao esquecimento) | M | Nenhuma |
| — | — | Corrigir frequency-lock hardening (feriados, multi-tenant) | M | Nenhuma |

**Marcos:** 🏁 170+ testes (100% endpoints cobertos), LGPD deleção real, frequência hardened

---

## Sprint 4: 🕐 Ponto Eletrônico

**Foco:** Controle de jornada com GPS + foto (Art. 12 LC 150)
**Duração:** 2-3 semanas

| Story | Épico | Descrição | Esforço | Dependências |
|:------|:-----:|:----------|:-------:|:-------------|
| 10.1 | E10 | Registro de ponto com GPS + foto | G | E8 (workers existem) |
| 10.2 | E10 | Intervalo intra-jornada | M | 10.1 |
| — | — | Testes: clock-in/clock-out flow | M | 10.1 |

**Marcos:** 🏁 Ponto eletrônico funcional com geolocalização e intervalo

---

## Sprint 5: ⚙️ Engine Trabalhista + Início eSocial

**Foco:** Cálculos trabalhistas + job queue
**Duração:** 2-3 semanas

| Story | Épico | Descrição | Esforço | Dependências |
|:------|:-----:|:----------|:-------:|:-------------|
| 10.3 | E10 | Engine cálculo trabalhista (HE, noturno, 12×36) | G | 10.1 |
| 10.4 | E10 | Notificação de inconsistência de ponto | P | 10.3 |
| — | — | Redis + BullMQ no docker-compose | M | Nenhuma |
| 11.1 | E11 | Admissão via eSocial (job queue) | GG | 10.3, Redis |

**Marcos:** 🏁 Espelho de ponto com cálculos, admissão eSocial em background

---

## Sprint 6: 📋 eSocial (Parte 2) + Incidentes

**Foco:** DAE + Dashboard Compliance + Emergência
**Duração:** 2 semanas

| Story | Épico | Descrição | Esforço | Dependências |
|:------|:-----:|:----------|:-------:|:-------------|
| 11.2 | E11 | Geração mensal de DAE | G | 11.1 |
| 11.3 | E11 | Dashboard de compliance trabalhista | M | 11.2 |
| 12.1 | E12 | Reporte de incidentes | M | Nenhuma |
| 12.2 | E12 | Botão de emergência SOS | M | 12.1 |

**Marcos:** 🏁 DAE gerado automaticamente, incidentes reportáveis, SOS funcional

---

## Sprint 7: 🔚 Finalização Compliance + CAT

**Foco:** Últimos compliance + refatoração
**Duração:** 2 semanas

| Story | Épico | Descrição | Esforço | Dependências |
|:------|:-----:|:----------|:-------:|:-------------|
| 12.3 | E12 | Emissão de CAT | M | 12.1 |
| 16.1 | E16 | Refatorar proposals.php (1180 → 3 arquivos) | G | Nenhuma |
| 16.2 | E16 | Refatorar solicitar.php + public-proposal.php | M | Nenhuma |
| 17.4 | E17 | Cloudflare Tunnel + HTTPS | M | Nenhuma |

**Marcos:** 🏁 CAT funcional, templates refatorados, HTTPS em produção

---

## Sprint 8: 🏗️ Refatoração Final + Migration Framework

**Foco:** Refatorar controllers + migration framework
**Duração:** 2 semanas

| Story | Épico | Descrição | Esforço | Dependências |
|:------|:-----:|:----------|:-------:|:-------------|
| 16.3 | E16 | Refatorar controllers grandes + workers.php | M | Nenhuma |
| — | — | Migration framework (scripts/migrate.js + schema_migrations) | M | Nenhuma |
| — | — | Testes E2E Playwright (fluxos críticos) | G | Setup |
| — | — | CI/CD GitHub Actions (lint → test → build) | M | Sprints 1-3 |

**Marcos:** 🏁 Migration framework no ar, CI/CD verde, refatoração completa

---

## Resumo de Esforço

| Sprint | Foco | Duração | Stories | Testes Novos |
|:-----:|:-----|:-------:|:-------:|:------------:|
| 1 | Setup testes + Core | 2 sem | 3 | ~50 |
| 2 | Testes negócio + Segurança | 2 sem | 3 | ~60 |
| 3 | Testes restantes + LGPD | 2 sem | 3 | ~60 |
| 4 | Ponto eletrônico | 2-3 sem | 3 | ~20 |
| 5 | Engine trabalhista + eSocial | 2-3 sem | 4 | ~30 |
| 6 | DAE + Incidentes | 2 sem | 4 | ~30 |
| 7 | CAT + Refatoração | 2 sem | 4 | — |
| 8 | Refatoração final + CI/CD | 2 sem | 3 | ~10 (E2E) |
| **Total** | | **16-19 sem** | **27** | **~260** |

---

## Riscos Atualizados

| Risco | Probabilidade | Impacto | Mitigação |
|:------|:------------:|:-------:|:----------|
| eSocial API sem sandbox | 🔴 Alta | 🔴 Crítico | Mock eSocial para dev/test |
| Redis aumenta complexidade | 🟡 Média | 🟡 Médio | Redis Cloud gratuito 30MB para MVP |
| Cálculos trabalhistas incorretos | 🟡 Média | 🔴 Crítico | Revisão jurídica antes de produção |
| Cobertura de testes insuficiente | 🟡 Média | 🟡 Médio | Quality gate de 70% no CI |
| Proposals sem tenant filter | ✅ Corrigido | — | Já verificado em auditoria anterior |

---

*Plano gerado em 29 de Julho de 2026 — Pós Auditoria Completa*
