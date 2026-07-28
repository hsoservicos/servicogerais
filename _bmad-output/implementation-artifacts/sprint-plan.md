# Sprint Plan — ServiceSaaS (Serviços Flex)
## Fase 4: Implementação

**Data:** 28/07/2026
**Épicos:** 13 (7 originais + 6 compliance doméstico)
**Estratégia:** Paralelismo — manter código existente rodando enquanto desenvolve novos módulos

---

## Sprint 1: Fundação Workers + Correções de Schema

**Foco:** Criar base de dados para compliance doméstico + corrigir conflitos de schema
**Duração:** 2 semanas

| Story | Épico | Descrição | Esforço | Dependências |
|:------|:------|:----------|:-------:|:-------------|
| 8.1 | E8 | Modelagem `workers` + CBO (tabela, migration, validação) | M | Nenhuma |
| 8.2 | E8 | CRUD Workers API + frontend (`workers.php`) | G | 8.1 |
| 14.1 | E14 | Endereço no cadastro do prestador (register.php + migration 005) | M | Nenhuma |
| 14.2 | E14 | Perfil do prestador (tenant-profile.php + API tenants/me) | M | Nenhuma |
| 14.3 | E14 | Busca pública por município (public.controller.js + home.php) | P | 14.1 |
| — | — | Remover `migrations/002_create_transactions_table.sql` (schema conflitante) | P | Nenhuma |
| — | — | Unificar schema: `init.sql` como única fonte da verdade | M | Nenhuma |
| 13.1 | E13 | Portabilidade de dados (export JSON+CSV) | M | Nenhuma |
| 13.3 | E13 | Revogação de consentimento LGPD | P | Nenhuma |

**Marcos:** 🏁 Tabela `workers` no ar, CRUD funcional, schema unificado

---

## Sprint 2: Trava de Frequência + Background Check

**Foco:** Algoritmo crítico de compliance + verificação de trabalhadores
**Duração:** 2 semanas

| Story | Épico | Descrição | Esforço | Dependências |
|:------|:------|:----------|:-------:|:-------------|
| 9.1 | E9 | Algoritmo trava-frequência (limite 2d/sem) | M | 8.1 |
| 9.3 | E9 | Calculadora de custos patronais | M | 8.1 |
| 8.3 | E8 | Background check integrado (API externa) | M | 8.2 |
| — | — | Testes unitários: frequency-lock algorithm | M | 9.1 |

**Marcos:** 🏁 Trava de frequência bloqueando 3º agendamento, calculadora de custos

---

## Sprint 3: Fluxo CLT + Certificações

**Foco:** Transição diarista→CLT + verificação de qualificação
**Duração:** 2 semanas

| Story | Épico | Descrição | Esforço | Dependências |
|:------|:------|:----------|:-------:|:-------------|
| 9.2 | E9 | Fluxo de transição diarista→CLT (wizard 3 passos) | G | 9.1, 9.3 |
| 8.2 (cert) | E8 | Gestão de certificações + verificação documental | M | 8.2 |
| — | — | Testes de integração: transição CLT flow | M | 9.2 |

**Marcos:** 🏁 Wizard CLT funcional, certificações obrigatórias por categoria

---

## Sprint 4: Ponto Eletrônico + Redis

**Foco:** Time tracking geolocalizado + infraestrutura de fila
**Duração:** 2 semanas

| Story | Épico | Descrição | Esforço | Dependências |
|:------|:------|:----------|:-------:|:-------------|
| 10.1 | E10 | Registro de ponto com GPS + foto | G | 8.1 |
| 10.2 | E10 | Intervalo intra-jornada | M | 10.1 |
| — | — | Adicionar Redis + BullMQ ao docker-compose | M | Nenhuma |
| — | — | Testes: clock-in/clock-out flow | M | 10.1 |

**Marcos:** 🏁 Redis no ar, ponto eletrônico funcional com geolocalização

---

## Sprint 5: Engine de Cálculos + eSocial (Parte 1)

**Foco:** Cálculos trabalhistas + início integração eSocial
**Duração:** 2-3 semanas

| Story | Épico | Descrição | Esforço | Dependências |
|:------|:------|:----------|:-------:|:-------------|
| 10.3 | E10 | Engine de cálculo trabalhista (HE, noturno, 12×36) | G | 10.1 |
| 10.4 | E10 | Notificação de inconsistência de ponto | P | 10.3 |
| 11.1 | E11 | Admissão via eSocial (job queue) | GG | 10.3, Redis |

**Marcos:** 🏁 Espelho de ponto com cálculos, admissão eSocial em background

---

## Sprint 6: eSocial (Parte 2) + Dashboard Compliance

**Foco:** Geração DAE + dashboard completo
**Duração:** 2 semanas

| Story | Épico | Descrição | Esforço | Dependências |
|:------|:------|:----------|:-------:|:-------------|
| 11.2 | E11 | Geração mensal de DAE (INSS+FGTS+Gilrat) | G | 11.1 |
| 11.3 | E11 | Dashboard de compliance trabalhista | M | 11.2 |
| — | — | Testes de integração: eSocial mock | G | 11.1, 11.2 |

**Marcos:** 🏁 DAE gerado automaticamente, dashboard compliance

---

## Sprint 7: Incidentes + Seguro + LGPD Final

**Foco:** Últimos módulos de compliance
**Duração:** 2 semanas

| Story | Épico | Descrição | Esforço | Dependências |
|:------|:------|:----------|:-------:|:-------------|
| 12.1 | E12 | Reporte de incidentes | M | Nenhuma |
| 12.2 | E12 | Botão de emergência SOS | M | 12.1 |
| 12.3 | E12 | Emissão de CAT | M | 12.1 |
| 13.2 | E13 | Eliminação de dados (direito ao esquecimento) | M | Nenhuma |

**Marcos:** 🏁 Incidentes reportáveis, SOS funcional, LGPD completo

---

## Sprint 8: QA Final + Documentação

**Foco:** Testes, lint, documentação
**Duração:** 2 semanas

| Atividade | Descrição | Esforço |
|:----------|:----------|:-------:|
| Testes E2E | Playwright para fluxos críticos | G |
| Testes de segurança | OWASP Top 10 + Trivy | M |
| Documentação | Swagger/OpenAPI para módulos novos | M |
| ESLint | Configurar `.eslintrc.js` com regras do projeto | P |
| CI/CD | GitHub Actions workflow (lint → test → build) | M |

**Marcos:** 🏁 Cobertura de testes >60%, CI/CD verde, documentação publicada

---

## Riscos do Sprint Plan

| Risco | Impacto | Mitigação |
|:------|:--------|:----------|
| eSocial API instável ou sem sandbox | Atraso Sprint 5-6 | Mock eSocial para desenvolvimento, validar em homologação |
| Redis aumenta complexidade DevOps | Overhead operacional | Usar Redis Cloud gratuito (30MB) para MVP |
| Pontos de função trabalhistas incorrectos | Passivo trabalhista | Revisão jurídica antes de liberar para produção |
| Sem testes escritos desde o início | Dívida técnica | Exigir testes em cada PR desde o Sprint 1 |
| 2 queries sem tenant filter em proposals/items | Vazamento cross-tenant | Corrigir proposals.controller.js:337 e items.controller.js:21-24 |

**Épico 14 (Perfil + Proximidade):** ✅ Implementado em 28/07/2026 — migration 005, módulo `tenants/`, register com endereço, `tenant-profile.php`, busca por `?city=` na API pública.