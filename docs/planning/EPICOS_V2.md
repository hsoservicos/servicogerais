# Épicos e Histórias — ServiceSaaS V2 (Pós-Auditoria)

**Versão:** 3.0 | **Data:** 2026-07-29

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
| E10 — Ponto Eletrônico | 📝 | 4 | 0 |
| E11 — eSocial Doméstico | 📝 | 3 | 0 |
| E12 — Incidentes & Emergência | 📝 | 3 | 0 |
| E13 — LGPD & Privacidade | 🔶 | 3 | 2 |
| E14 — Perfil & Proximidade | ✅ | 3 | 3 |
| **E15 — Testes & Qualidade** | **📝** | **4** | **0** |
| **E16 — Refatoração** | **📝** | **3** | **0** |
| **E17 — Hardening Segurança** | **📝** | **4** | **0** |
| **Total** | | **64** | **54** |

---

## E1 🔐 Autenticação & Onboarding ✅ COMPLETO

**Stories:** 1.1 (Setup + Registro PF), 1.2 (Design System), 1.3 (Registro PJ), 1.4 (Login + JWT), 1.5 (Recuperação Senha)
**Endpoints:** 5 — register, login, me, forgot-password, reset-password
**Frontend:** login.php, register.php, forgot-password.php, reset-password.php

## E2 👥 Clientes & Catálogo ✅ COMPLETO

**Stories:** Clientes CRUD, Categorias CRUD, Serviços CRUD
**Endpoints:** 15 — clients (5), categories (5), services (5)
**Frontend:** clients.php, categories.php, services.php

## E3 📄 Propostas ✅ COMPLETO

**Stories:** 3.1 (CRUD), 3.2 (Itens), 3.3 (WhatsApp), 3.4 (Aprovação Pública), 3.5 (PDF)
**Endpoints:** 12 — proposals (7), items (4), public (1)
**Frontend:** proposals.php (1180 linhas), public-proposal.php (734 linhas)

## E4 📊 Dashboard & Métricas ✅ COMPLETO

**Stories:** 4.1 (KPIs), 4.2 (Gráfico Chart.js), 4.3 (Follow-up), 4.4 (Transações)
**Endpoints:** 4 — dashboard, chart, followup, transactions
**Frontend:** dashboard.php (360 linhas), transactions.php

## E5 💳 Pagamentos Mercado Pago ✅ COMPLETO

**Stories:** 5.1 (Setup MP), 5.2 (Preference), 5.3 (Webhook), 5.4 (Pix), 5.5 (Estorno)
**Endpoints:** 4 — webhook, preference, get, refund
**Frontend:** 🔶 Checkout Pix precisa incorporar na página pública

## E6 🌐 Presença Pública & Leads ✅ COMPLETO

**Stories:** 6.1 (Landing Page), 6.2 (Wizard 3 passos), 6.3 (Proposta Pública), 6.4 (CTA + Leads)
**Endpoints:** 10 — categories, services, leads, upload, proposals/:token (GET, PATCH, PDF, pay, payment)
**Frontend:** home.php (479), solicitar.php (1087), leads.php, public-proposal.php

## E7 🏢 Administração ✅ COMPLETO

**Stories:** 7.1 (Admin Auth + Dashboard), 7.2 (Tenants CRUD), 7.3 (Financeiro + Planos), 7.4 (Auditoria)
**Endpoints:** 14 — dashboard, tenants CRUD, transactions, refund, plans CRUD, reports, audit
**Frontend:** admin-*.php (6 páginas)

## E8 🏠 Workers & Certificações ✅ COMPLETO

**Stories:** 8.1 (Workers + CBO), 8.2 (Certificações), 8.3 (Background Check)
**Endpoints:** 10 — workers CRUD, certifications CRUD, background-check, certification-required
**Frontend:** workers.php (800 linhas)

## E9 ⏱️ Frequência & Agendamento ✅ COMPLETO

**Stories:** 9.1 (Trava Frequência), 9.2 (Transição CLT), 9.3 (Calculadora Custos)
**Endpoints:** 8 — schedules CRUD + status, domestic calculate-costs, transition-to-clt
**Frontend:** schedules.php
**Observação:** Algoritmo de frequência existe mas precisa hardening (feriados, multi-tenant edge cases)

---

## E10 🕐 Ponto Eletrônico & Jornada 📝 NOVO

**Dependência:** E8 (Workers)
**Risco:** 🔴 Alto — Exigência legal Art. 12 LC 150
**Base Legal:** LC 150/2015 Art. 12

### Story 10.1: Registro de Ponto com Geolocalização

**Como** trabalhador doméstico,
**Quero** registrar entrada e saída com GPS e foto do local,
**Para** cumprir a exigência legal de controle de ponto eletrônico.

**Critérios de Aceitação:**
- Given trabalhador chega ao local, When registra clock-in, Then sistema salva data/hora + coordenadas GPS + foto
- Given trabalhador em serviço, When tenta segundo clock-in no mesmo dia, Then sistema bloqueia com erro
- Given fim do expediente, When registra clock-out com foto, Then sistema calcula horas trabalhadas
- Given registros do dia, When consulta espelho de ponto, Then exibe: entrada, saída, intervalo, total

**Endpoint:** POST/GET /api/v1/timeclock/*
**Tabela:** `time_clock_records` (worker_id, tenant_id, clock_in, clock_out, break_start, break_end, gps_coordinates, photo_url)
**Esforço:** G

### Story 10.2: Intervalo Intra-jornada

**Como** trabalhador,
**Quero** registrar início e fim do intervalo de almoço/descanso,
**Para** cumprir a legislação trabalhista.

**Critérios de Aceitação:**
- Given trabalhador em jornada ativa, When inicia intervalo, Then break_start é registrado
- Given intervalo >1h para jornada >6h, When sistema processa, Then gera alerta de inconsistência
- Given jornada <6h, When intervalo registrado, Then não gera alerta (opcional)

**Esforço:** M

### Story 10.3: Engine de Cálculo Trabalhista

**Como** sistema,
**Quero** calcular automaticamente horas extras, adicional noturno e saldo do dia,
**Para** garantir pagamento correto conforme a CLT.

**Critérios de Aceitação:**
- Given clock-out registrado, When sistema processa, Then calcula horas regulares (até 8h/dia)
- Given jornada >8h/dia, When calcula, Then horas excedentes marcadas como HE (50% dias úteis, 100% domingos/feriados)
- Given trabalho entre 22h-5h, When calcula, Then adicional noturno de 20% aplicado
- Given escala 12x36, When calcula, Then respeita 12h trabalho + 36h descanso
- Given mês fechado, When gera espelho, Then total de horas + HE + noturno exibido

**Esforço:** G

### Story 10.4: Notificação de Inconsistência

**Como** contratante,
**Quero** ser notificado se houver discrepância no ponto do dia,
**Para** aprovar ajustes antes do fechamento mensal.

**Critérios de Aceitação:**
- Given ponto com horas extras, When sistema detecta, Then push/email para contratante aprovar
- Given inconsistência (ex: clock-in sem clock-out), When identificada, Then worker pode incluir justificativa
- Given justificativa aceita, When contratante aprova, Then ponto é ajustado

**Esforço:** P

---

## E11 📋 eSocial Doméstico 📝 NOVO

**Dependência:** E8 + E10
**Risco:** 🔴 Alto — Passivo tributário
**Infra:** Redis + BullMQ (job queue)

### Story 11.1: Admissão via eSocial

**Como** sistema,
**Quero** automatizar a admissão do trabalhador no eSocial Doméstico,
**Para** cumprir obrigações fiscais do empregador.

**Critérios de Aceitação:**
- Given contrato CLT assinado, When sistema envia dados ao eSocial, Then retorna 202 com job_id
- Given job em processamento, When consulta status, Then exibe progresso
- Given integração concluída, When finalizada, Then esocial_integration.status = SYNCED
- Given erro na integração, When ocorre, Then job retenta 3x com backoff exponencial

**Esforço:** GG

### Story 11.2: Geração Mensal de DAE

**Como** contratante,
**Quero** receber a guia DAE mensal calculada automaticamente,
**Para** pagar INSS + FGTS + Gilrat em dia.

**Critérios de Aceitação:**
- Given mês competência fechado, When sistema gera DAE, Then calcula INSS (8-12%) + FGTS (8%) + Gilrat (0.8%)
- Given DAE gerada, When disponível, Then contratante pode pagar via PIX/boleto
- Given DAE paga, When sistema confirma, Then status atualizado e registrado

**Esforço:** G

### Story 11.3: Dashboard de Compliance Trabalhista

**Como** contratante,
**Quero** acompanhar a situação trabalhista de cada empregado,
**Para** evitar passivo fiscal.

**Critérios de Aceitação:**
- Given contratante acessa dashboard eSocial, When carrega, Then exibe: status eSocial, DAE mês, férias, 13º, aviso prévio
- Given DAE atrasado, When dashboard carrega, Then alerta vermelho destacado
- Given múltiplos empregados, When visualiza, Then tabela consolidada por worker

**Esforço:** M

---

## E12 🚨 Incidentes & Emergência 📝 NOVO

**Dependência:** E10
**Risco:** 🟡 Médio — Responsabilidade civil

### Story 12.1: Reporte de Incidentes

**Como** trabalhador ou contratante,
**Quero** reportar acidentes ou incidentes durante a prestação de serviço,
**Para** registrar formalmente a ocorrência.

**Critérios de Aceitação:**
- Given incidente ocorrido, When reporta, Then sistema registra: tipo, severidade, descrição, geolocalização
- Given incidente CRITICAL, When registrado, Then notificação imediata ao tenant e admin
- Given reporte concluído, When finaliza, Then número de protocolo único gerado

**Tabela:** `incidents` (id, tenant_id, worker_id, type, severity, description, gps, protocol, status)
**Esforço:** M

### Story 12.2: Botão de Emergência SOS

**Como** trabalhador,
**Quero** um botão de pânico que envia minha localização para contatos de emergência,
**Para** receber ajuda rapidamente.

**Critérios de Aceitação:**
- Given trabalhador em emergência, When aciona SOS, Then SMS/WhatsApp com geolocalização para 3 contatos
- Given SOS acionado, When registrado, Then incidente CRITICAL criado automaticamente
- Given contatos não cadastrados, When tenta SOS, Then orienta a cadastrar contatos primeiro

**Esforço:** M

### Story 12.3: Emissão de CAT

**Como** contratante,
**Quero** emitir a Comunicação de Acidente de Trabalho (CAT),
**Para** cumprir obrigação legal junto ao INSS.

**Critérios de Aceitação:**
- Given acidente de trabalho reportado, When contratante emite CAT, Then sistema gera CAT digital
- Given CAT emitida, When enviada ao INSS, Then número CAT registrado no incidente
- Given CAT já emitida para o incidente, When tenta novamente, Then bloqueia com mensagem

**Esforço:** M

---

## E13 🔐 LGPD & Privacidade 🔶 PARCIAL

**Status Atual:**
- ✅ Portabilidade (export JSON+CSV) — OPERACIONAL
- ✅ Consentimento (grant/revoke) — OPERACIONAL
- ❌ Eliminação de dados — APENAS LOG, sem execução

### Story 13.2: Eliminação de Dados (Direito ao Esquecimento) 📝

**Como** usuário,
**Quero** solicitar a eliminação dos meus dados pessoais da plataforma,
**Para** exercer meu direito LGPD.

**Critérios de Aceitação:**
- Given usuário solicita eliminação, When confirma identidade, Then sistema anonimiza dados pessoais
- Given dados anonimizados, When processo concluído, Then registros operacionais mantidos sem PII
- Given solicitação recebida, When within 15 dias, Then conclusão confirmada por e-mail
- Given tenant com propostas em aberto, When solicita eliminação, Then bloqueia até propostas finalizadas

**Esforço:** M

---

## E14 📍 Perfil & Proximidade ✅ COMPLETO

**Stories:** 14.1 (Endereço cadastro), 14.2 (Perfil prestador), 14.3 (Busca por município)
**Migration:** 005_add_tenant_address.sql
**Frontend:** register.php (com CEP), tenant-profile.php

---

## 🧪 E15 — Testes & Qualidade 📝 PRIORIDADE #1

### Story 15.1: Setup de Ambiente de Testes

**Como** desenvolvedor,
**Quero** configurar Jest + Supertest + banco de testes + fixtures,
**Para** escrever e rodar testes de forma isolada.

**Critérios de Aceitação:**
- Given Jest configurado, When roda `npm test`, Then executa com `--forceExit` e banco MySQL de teste
- Given fixtures carregadas, When teste inicia, Then tenants, users, clients de teste disponíveis
- Given setup completo, When CI executa, Then container MySQL de teste sobe via Docker
- Given testes rodando, When finalizam, Then banco de teste é limpo entre execuções

**Arquivos:** `jest.config.js`, `__tests__/setup/jest.setup.js`, `__tests__/setup/fixtures.js`, `__tests__/helpers/auth.helper.js`, `__tests__/helpers/db.helper.js`
**Esforço:** M

### Story 15.2: Testes de API — Módulos Core

**Como** desenvolvedor,
**Quero** testes para Auth, Tenants, Clients e Catalog,
**Para** garantir que funcionalidades básicas não quebrem.

**Esforço:** G (50+ testes)

### Story 15.3: Testes de API — Módulos de Negócio

**Como** desenvolvedor,
**Quero** testes para Propostas, Dashboard, Payments e Transactions,
**Para** garantir que o core business esteja protegido.

**Esforço:** G (60+ testes)

### Story 15.4: Testes de API — Módulos Restantes

**Como** desenvolvedor,
**Quero** testes para Public, Admin, Workers, Schedules, Domestic,
**Para** cobertura completa de todos os endpoints.

**Esforço:** G (60+ testes)

---

## 🏗️ E16 — Refatoração Técnica 📝

### Story 16.1: Refatorar proposals.php (1180 linhas)

**Como** desenvolvedor,
**Quero** quebrar proposals.php em includes modulares,
**Para** facilitar manutenção e evolução.

**Critérios de Aceitação:**
- Given proposals.php original, When refatorado, Then separado em: `proposals-list.php` (tabela + filtros), `proposals-form.php` (modal create/edit), `proposals-view.php` (modal view + PDF)
- Given templates separados, When carregados, Then comportamento idêntico ao original
- Given refatoração concluída, When testado, Then todas as funcionalidades preservadas

**Esforço:** G

### Story 16.2: Refatorar solicitar.php + public-proposal.php

**Critérios de Aceitação:**
- Given solicitar.php (1087 linhas), When refatorado, Then steps 1/2/3 em includes separados
- Given public-proposal.php (734 linhas), When refatorado, Then seções extraídas

**Esforço:** M

### Story 16.3: Refatorar controllers + workers.php

**Critérios de Aceitação:**
- Given admin.controller.js (734 linhas), When refatorado, Then extraído em controllers por domínio
- Given proposals.controller.js (547 linhas), When refatorado, Then service layer extraído
- Given workers.php (800 linhas), When refatorado, Then list/form/certificações separados

**Esforço:** M

---

## 🛡️ E17 — Hardening de Segurança 📝

### Story 17.1: JWT + CORS + Helmet

**Critérios de Aceitação:**
- Given JWT_SECRET atual, When hardening, Then secret de 64 caracteres gerado, armazenado em .env
- Given CORS atual (\*), When configurado, Then origens permitidas definidas em CORS_ORIGINS
- Given Helmet aplicado, When configurado, Then CSP, HSTS, X-Frame-Options, X-Content-Type-Options ativos

**Esforço:** P

### Story 17.2: Serviço de Email

**Critérios de Aceitação:**
- Given console.log atual, When substituído, Then integração SendGrid/Mailgun ativa
- Given templates de e-mail, When carregados, Then: boas-vindas, reset senha, lead recebido, pagamento confirmado
- Given e-mail enviado, When processado, Then log com status e event_id

**Esforço:** M

### Story 17.3: Rate Limiting Avançado

**Critérios de Aceitação:**
- Given rate limit atual, When reforçado, Then limites por rota + por IP distintos
- Given N tentativas falhas, When excedido, Then bloqueio temporário com Retry-After
- Given admin autenticado, When acessa, Then rate limit mais permissivo

**Esforço:** P

### Story 17.4: Cloudflare Tunnel + HTTPS

**Critérios de Aceitação:**
- Given Cloudflare configurado, When tunnel ativo, Then HTTPS automático válido
- Given tunnel operacional, When acessa, Then DDoS protection ativo
- Given sem tunnel, When desenvolvimento, Then HTTP local mantido

**Esforço:** M

---

## Anexo: Mapa de Endpoints por Épico

| Épico | Endpoints | % Cobertura |
|:------|:---------:|:-----------:|
| E1 Auth | 5 | ✅ |
| E2 Clients | 5 | ✅ |
| E2 Catalog | 10 | ✅ |
| E3 Proposals | 12 | ✅ |
| E4 Dashboard | 4 | ✅ |
| E5 Payments | 4 | ✅ |
| E6 Public | 10 | ✅ |
| E6 Leads | 2 | ✅ |
| E7 Admin | 14 | ✅ |
| E8 Workers | 10 | ✅ |
| E9 Schedules | 8 | ✅ |
| E13 Data/LGPD | 4 | ✅ |
| **Total** | **86** | **100%** |

---

*Documento gerado em 29 de Julho de 2026 — ServiceSaaS V2*
