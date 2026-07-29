# Auditoria de Épicos — ServiceSaaS V2

**Data:** 29/07/2026
**Objetivo:** Verificar status real de cada épico vs planejado

---

## 🔴 Épicos Não Construídos (3)

| Épico | Descrição | Risco | Stories |
|:------|:----------|:------|:--------|
| **E10** 🕐 Ponto Eletrônico | Registro GPS+foto, intervalo, engine trabalhista | 🔴 Passivo trabalhista (Art. 12 LC 150) | 10.1-10.4 |
| **E11** 📋 eSocial Doméstico | Admissão, DAE, FGTS, job queue | 🔴 Passivo fiscal | 11.1-11.3 |
| **E12** 🚨 Incidentes & Emergência | Reporte, SOS, CAT | 🟡 Risco civil | 12.1-12.3 |

**Ação:** Priorizar no Sprint 4+ do plano V2.

---

## 🟡 Épicos Parciais (4)

| Épico | Status Real | O Que Falta |
|:------|:-----------|:------------|
| **E13** 🔐 LGPD | Export/consent/deleção OPERACIONAIS | Deleção imediata (falta fila 15 dias); propostas/transactions não anonimizados |
| **E15** 🧪 Testes | 169 testes (22 suites) | Payments tem 0 testes; leads sem testes próprios |
| **E16** 🏗️ Refatoração | ~10% redução de linhas | Nenhuma modularização real de templates/controllers |
| **E17** 🛡️ Hardening | JWT/CORS/Helmet/RateLimit OK | Email em modo log; Cloudflare Tunnel ausente; JWT secret default |

---

## ✅ Épicos Completos (10)

| Épico | Módulos | Endpoints | Testes |
|:------|:--------|:---------:|:------:|
| E1 🔐 Auth | register, login, me, forgot, reset | 5 | 40 |
| E2 👥 Clients & Catalog | clients, categories, services CRUD | 15 | 45 |
| E3 📄 Proposals | CRUD + items + status + PDF + WhatsApp | 12 | 25 |
| E4 📊 Dashboard | KPIs + chart + followup + transactions | 4 | 11 |
| E5 💳 Payments | preference + webhook + refund | 4 | ❌ 0 |
| E6 🌐 Public & Leads | landing page + wizard + prop públicas | 10 | 2 |
| E7 🏢 Admin | dashboard + tenants + plans + reports + audit | 14 | 6 |
| E8 🏠 Workers | CRUD + certs + background | 10 | 6 |
| E9 ⏱️ Schedules | CRUD + frequency lock + CLT costs | 8 | 4 |
| E14 📍 Profile | perfil + endereço + busca município | 2 | 6 |

---

## 📊 Métricas Consolidadas

| Métrica | Planejado | Real | Delta |
|:--------|:---------:|:----:|:-----:|
| Épicos completos | 10 | 10 | ✅ |
| Épicos parciais | 4 | 4 | ✅ |
| Épicos faltantes | 3 | 3 | ✅ |
| Endpoints API | 86 | 86 | ✅ |
| Tabelas DB | 18 | 18 | ✅ |
| Templates PHP | 30 | 30 | ✅ |
| Testes | 95+ | 169 | ✅ +74 |
| Testes Payments | — | 0 | ❌ Gap |

---

## 🐛 Bugs Pré-existentes Encontrados

| Bug | Arquivo | Linha | Impacto |
|:----|:--------|:-----:|:--------|
| `tenantFilter` sem prefixo em JOIN | `schedules.service.js` | 32,52 | Schedules list/read quebram |
| `AND ?` com string ao invés de SQL | `workers.service.js` | 151,174 | Certifications não encontram worker |
| `email.service` console.log default | `config/auth.js` | 9 | Emails não são enviados |

---

## 📋 Ações Recomendadas

### Imediatas (Sprint 4+)
1. **Escrever testes para Payments** — único módulo sem testes
2. **Corrigir bugs no schedules.service.js** — tenantFilter ambíguo
3. **Corrigir workers.service.js** — AND ? com parâmetro string

### Próximos Sprints
4. **E10** — Ponto eletrônico (GPS + foto) — prioridade #1 compliance
5. **E11** — eSocial Doméstico — prioridade #2 compliance
6. **E12** — Incidentes + CAT — prioridade #3 compliance

### Contínuo
7. **E16** — Refatorar templates grandes (proposals.php, solicitar.php)
8. **E17** — Email real + Cloudflare Tunnel + JWT hardening

---

*Documento gerado em 29 de Julho de 2026*
