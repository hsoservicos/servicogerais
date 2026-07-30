# Sprint Plan V2 — ServiceSaaS (Serviços Flex)

**Data:** 30/07/2026
**Épicos Ativos:** 13 (12 completos + 1 parcial refatoração + hardening)
**Épicos Fora de Escopo:** E10 (Ponto Eletrônico), E11 (eSocial Doméstico)

---

## Sprints Concluídos (1-3)

| Sprint | Foco | Status |
|:-----:|:-----|:------:|
| 1 | Setup testes + Core | ✅ 95 testes |
| 2 | Testes negócio + Segurança | ✅ Email service + Rate Limit |
| 3 | Testes restantes + LGPD | ✅ 192 testes totais |

---

## Sprint 4: 🏗️ Refatoração Controllers + workers.php

**Foco:** Refatorar controllers grandes + migration framework
**Duração:** 2 semanas

| Story | Épico | Descrição | Esforço | Dependências |
|:------|:-----:|:----------|:-------:|:-------------|
| 16.3 | E16 | Refatorar admin.controller.js + proposals.controller.js + workers.php | M | Nenhuma |
| — | — | Migration framework (scripts/migrate.js + schema_migrations) | M | Nenhuma |

**Marcos:** 🏁 Controllers extraídos, migration tracking funcional

---

## Sprint 5: 🔧 CI/CD Final + Hardening

**Foco:** Finalizar pipeline + segurança
**Duração:** 2 semanas

| Story | Épico | Descrição | Esforço | Dependências |
|:------|:-----:|:----------|:-------:|:-------------|
| 17.2 | E17 | Serviço de Email real (SendGrid) | M | Nenhuma |
| 17.4 | E17 | Cloudflare Tunnel + HTTPS | M | Domínio |
| — | — | Quality gate 70% cobertura no CI | P | Sprint 1-3 |

**Marcos:** 🏁 Emails enviando, HTTPS ativo, CI com quality gate

---

## Resumo de Esforço

| Sprint | Foco | Duração | Stories |
|:-----:|:-----|:-------:|:-------:|
| 1-3 | Testes + LGPD + Qualidade | 6 sem | 12 (✅ concluído) |
| 4 | Refatoração + Migration | 2 sem | 2 |
| 5 | Hardening + CI/CD final | 2 sem | 3 |
| **Total** | | **10 sem** | **17** |

---

## Épicos Fora de Escopo

| Épico | Motivo |
|:------|:--------|
| **E10** 🕐 Ponto Eletrônico | O projeto não realiza funções de Departamento Pessoal |
| **E11** 📋 eSocial Doméstico | O projeto não realiza funções de Controladoria Contábil |

---

*Plano gerado em 30 de Julho de 2026 — ServiceSaaS V2*
