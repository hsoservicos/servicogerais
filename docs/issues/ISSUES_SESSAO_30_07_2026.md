# Issues — Sessão 30/07/2026

**Repo:** `hsoservicos/servicogerais`
**Data:** 30/07/2026
**Total de Issues:** 15 (abertas nesta sessão)

---

## Issues Técnicas

### #1 — Cobertura de testes abaixo do quality gate (55%)
**Severidade:** Crítica
**Épico:** E15
**Descrição:** Cobertura atual de 55% statements, 42% branches. Quality gate exige 70%. Foco em módulos críticos: admin (37%), domestic (33%), public (40%).
**Ação:** Adicionar testes para aumentar cobertura para >=70%.

### #2 — Módulo `leads` sem testes
**Severidade:** Crítica
**Épico:** E6
**Descrição:** Módulo leads tem 20% de cobertura. Nenhum teste direto. Endpoints: POST /leads, GET /leads, PUT /leads/:id.
**Ação:** Criar `__tests__/leads/leads.test.js`.

### #3 — Módulo `notifications` sem testes
**Severidade:** Crítica
**Épico:** E17
**Descrição:** Módulo notifications tem 17% de cobertura, 0% branches/functions. Endpoints de preferências e notificações admin sem teste.
**Ação:** Criar `__tests__/notifications/notifications.test.js`.

### #4 — ESLint sem arquivo de configuração
**Severidade:** Alta
**Épico:** E16
**Descrição:** `npm run lint` falha com "ESLint couldn't find a configuration file". Nenhum `eslintrc` ou `eslint.config.js` existe.
**Ação:** Criar `eslint.config.js` (flat config) com regras do projeto.

### #5 — `proposals.controller.js` não refatorado
**Severidade:** Alta
**Épico:** E16
**Descrição:** proposals.controller.js permanece como arquivo único grande. Admin.controller já foi extraído (E16.3), proposals é o próximo.
**Ação:** Extrair proposals.controller.js em controllers menores por domínio.

### #6 — Bug: `onLeadCreated` com `tenant_id` indefinido
**Severidade:** Alta
**Épico:** E6
**Descrição:** `public.controller.js:219` referencia `tenant_id` que não está definido no escopo da função `createLead`. O lead é criado sem contexto de tenant.
**Ação:** Investigar e corrigir a referência a `tenant_id`.

### #7 — 30 vulnerabilidades npm (1 critical)
**Severidade:** Alta
**Épico:** E17
**Descrição:** `npm audit` reporta 30 vulnerabilidades: 1 critical (tar), 27 high (brace-expansion), 2 moderate (uuid). Todas em dependências transitórias.
**Ação:** Executar `npm audit fix` e verificar breaking changes.

---

## Issues de Infraestrutura

### #8 — Ativar Brevo real (sair do sandbox)
**Severidade:** Alta
**Épico:** E17
**Descrição:** BREVO_SANDBOX=true atualmente. Necessário mudar para false e verificar entregabilidade. Conta Brevo: hsoservicos@gmail.com, 299 créditos restantes.
**Ação:** (1) Configurar SPF/DKIM/DMARC no DNS (2) Criar sender verificado (3) BREVO_SANDBOX=false.

### #9 — MP_ACCESS_TOKEN não configurado
**Severidade:** Alta
**Épico:** E5
**Descrição:** Mercado Pago opera em modo degradado. Payments controller carrega mas todas as operações falham.
**Ação:** Obter credenciais de produção MP_ACCESS_TOKEN e MP_PUBLIC_KEY.

### #10 — Migration framework sem tracking
**Severidade:** Média
**Épico:** —
**Descrição:** `scripts/migrate.js` existe mas não tem tabela `schema_migrations` para tracking. Migrations manuais via `init.sql`.
**Ação:** Finalizar migration framework com tabela de controle.

### #11 — WhatsApp apenas simulado (wa.me links)
**Severidade:** Média
**Épico:** E17
**Descrição:** `communication.service.js` apenas gera links wa.me. Sem integração com WhatsApp Business API ou provedor terceiro.
**Ação:** Avaliar integração com provedor WhatsApp (Brevo, Twilio, etc.).

---

## Issues de Qualidade de Código

### #12 — Test data usa nome 'Hack'
**Severidade:** Baixa
**Épico:** E15
**Descrição:** `categories.test.js`, `services.test.js`, `clients.crud.test.js` usam `.send({ name: 'Hack' })` como dado de teste.
**Ação:** Renomear para 'Teste' ou 'Produto Teste'.

### #13 — Cobertura baixa do módulo `admin` (37%)
**Severidade:** Média
**Épico:** E7
**Descrição:** Módulo admin com 37% statements, 23% branches. Dashboard, tenants, financeiro, auditoria com baixa cobertura.
**Ação:** Adicionar testes para controllers admin extraídos.

### #14 — Cobertura baixa do módulo `domestic` (33%)
**Severidade:** Média
**Épico:** E9
**Descrição:** Módulo domestic com 33% statements, 25% branches. Workers, schedules, agreements, CLT calculation.
**Ação:** Adicionar testes para schedules e agreements.

### #15 — Cobertura baixa do módulo `public` (40%)
**Severidade:** Média
**Épico:** E6
**Descrição:** Módulo public com 40% statements, 22% branches. Upload de arquivos e busca por município não testados.
**Ação:** Adicionar testes para upload e search endpoints.

---

## Resumo por Severidade

| Severidade | Count | Issues |
|:----------|:-----:|:-------|
| Critica | 3 | #1, #2, #3 |
| Alta | 6 | #4, #5, #6, #7, #8, #9 |
| Media | 5 | #10, #11, #13, #14, #15 |
| Baixa | 1 | #12 |

---

*Issues geradas em 30 de Julho de 2026 — ServiceSaaS V2*
