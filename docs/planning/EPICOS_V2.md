# Épicos e Histórias — ServiceSaaS V2

**Versão:** 4.0 | **Data:** 30/07/2026

---

## Sumário Executivo

| Épico | Status | Stories | Completas |
|:------|:-----:|:-------:|:---------:|
| E1 — Autenticação | ✅ | 5 | 5 |
| E2 — Clientes & Catálogo | ✅ | 3 | 3 |
| E3 — Propostas | ✅ | 6 | 6 |
| E4 — Dashboard & Métricas | ✅ | 4 | 4 |
| E5 — Pagamentos Mercado Pago | ✅ | 5 | 5 |
| E6 — Presença Pública & Leads | ✅ | 4 | 4 |
| E7 — Administração | ✅ | 4 | 4 |
| E8 — Workers & Certificações | ✅ | 3 | 3 |
| E9 — Frequência & Agendamento | ✅ | 3 | 3 |
| E10 — Ponto Eletrônico | ⛔ FORA DE ESCOPO | — | — |
| E11 — eSocial Doméstico | ⛔ FORA DE ESCOPO | — | — |
| E12 — Incidentes & Emergência | ✅ | 3 | 3 |
| E13 — LGPD & Privacidade | ✅ | 3 | 3 |
| E14 — Perfil & Proximidade | ✅ | 3 | 3 |
| **E15 — Testes & Qualidade** | ✅ | **4** | **4** |
| **E16 — Refatoração** | 🔶 | **3** | **2** |
| **E17 — Hardening Segurança** | 🔶 | **4** | **2** |
| **Total** | | **56** | **52** |

---

## E1-E9, E12-E14 ✅ COMPLETOS

*(Épicos implementados — ver código fonte para detalhes)*

---

## ⛔ E10 — Ponto Eletrônico (FORA DE ESCOPO)

**Decisão:** Este épico foi **removido do escopo de desenvolvimento**. O ServiceSaaS não realiza funções de Departamento Pessoal ou Controladoria Contábil de serviços domésticos. Não haverá implementação de controle de ponto eletrônico, GPS, foto, engine trabalhista ou intervalo intra-jornada.

**Histórico:** Originalmente planejado como E10 (Stories 10.1-10.4). Removido por decisão de produto em 30/07/2026.

## ⛔ E11 — eSocial Doméstico (FORA DE ESCOPO)

**Decisão:** Este épico foi **removido do escopo de desenvolvimento**. Não haverá integração com eSocial Doméstico, geração de DAE, admissão ou dashboard de compliance trabalhista.

**Histórico:** Originalmente planejado como E11 (Stories 11.1-11.3). Removido por decisão de produto em 30/07/2026.

---

## E12 🚨 Incidentes & Emergência ✅ COMPLETO

**Stories:** 12.1 (Reporte), 12.2 (SOS), 12.3 (CAT)
**Endpoints:** 7 — list, create, read, update, status, SOS, CAT
**Tabela:** `incidents`
**Testes:** 7

## E13 🔐 LGPD & Privacidade ✅ COMPLETO

**Stories:** 13.1 (Portabilidade), 13.2 (Eliminação com fila 15 dias), 13.3 (Consentimento)
**Endpoints:** 5 — export, delete-request, process-deletion, consent (GET/POST)
**NFRs LGPD:** 10/10 concluídos

## E14 📍 Perfil & Proximidade ✅ COMPLETO

**Stories:** 14.1 (Endereço cadastro), 14.2 (Perfil prestador), 14.3 (Busca por município)

---

## E15 🧪 Testes & Qualidade ✅ COMPLETO

**Stories:** 15.1 (Setup), 15.2 (Core), 15.3 (Negócio), 15.4 (Restantes)
**Total:** 192 testes, 25 suites, 100% passando

## E16 🏗️ Refatoração Técnica 🔶 PARCIAL

**Stories:** 16.1 (proposals.php ✅), 16.2 (solicitar.php + public-proposal.php ✅), 16.3 (controllers + workers.php 📝 pendente)

## E17 🛡️ Hardening de Segurança 🔶 PARCIAL

**Stories:** 17.1 (JWT/CORS/Helmet ✅), 17.2 (Email 📝 pendente), 17.3 (Rate Limit ✅), 17.4 (Cloudflare 📝 pendente)

---

## Anexo: Mapa de Endpoints por Épico

| Épico | Endpoints |
|:------|:---------:|
| E1 Auth | 5 |
| E2 Clients | 5 |
| E2 Catalog | 10 |
| E3 Proposals | 12 |
| E4 Dashboard | 4 |
| E5 Payments | 4 |
| E6 Public | 10 |
| E6 Leads | 2 |
| E7 Admin | 14 |
| E8 Workers | 10 |
| E9 Schedules | 8 |
| E12 Incidents | 7 |
| E13 Data/LGPD | 5 |
| **Total** | **93** |

---

*Documento gerado em 30 de Julho de 2026 — ServiceSaaS V2*
