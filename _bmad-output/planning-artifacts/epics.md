---
stepsCompleted:
  - "step-01-validate-prerequisites"
  - "step-02-design-epics"
  - "step-03-create-stories"
  - "step-04-final-validation"
inputDocuments:
  - "docs/planning/PLANEJAMENTO_MODERNO_PROJETO.md"
  - "_bmad-output/planning-artifacts/architecture/architecture-servicos-20260727/ARCHITECTURE-SPINE.md"
  - "_bmad-output/planning-artifacts/ux-designs/ux-servicos-20260727/DESIGN.md"
  - "_bmad-output/planning-artifacts/ux-designs/ux-servicos-20260727/EXPERIENCE.md"
---

# ServiceSaaS (Serviços Flex) - Epic Breakdown

## Overview

This document provides the complete epic and story breakdown for **ServiceSaaS (Serviços Flex)**, decomposing the requirements from the PRD (`PLANEJAMENTO_MODERNO_PROJETO.md`), Architecture (`ARCHITECTURE-SPINE.md`), and UX Design (`DESIGN.md` + `EXPERIENCE.md`) into implementable stories.

**Stack:** PHP 8.2 · Node.js 20 · MySQL 8.0 · Docker Compose · Cloudflare Tunnel

---

## Requirements Inventory

### Functional Requirements

**Módulo: Autenticação & Cadastro**

| ID | Título | Descrição | Critério de Aceitação |
|:---:|:---|---|:---|
| FR-001 | Cadastro de usuário (PF) | Usuário pessoa física cria conta com nome, CPF, e-mail, telefone e senha | Dado que o usuário preenche o formulário com dados válidos, Quando clica em "Cadastrar", Então o sistema cria a conta, retorna token JWT e redireciona ao dashboard |
| FR-002 | Cadastro de usuário (PJ) | Usuário pessoa jurídica cria conta com nome da empresa, CNPJ, contato, e-mail e senha | Dado que o usuário seleciona "Pessoa Jurídica", Quando preenche CNPJ válido e dados obrigatórios, Então o sistema valida o CNPJ na Receita Federal e cria a conta |
| FR-003 | Login com e-mail e senha | Usuário autentica no sistema com credenciais cadastradas | Dado um usuário cadastrado, Quando informa e-mail e senha corretos, Então o sistema retorna JWT válido por 24h e redireciona ao dashboard |
| FR-004 | Recuperação de senha | Usuário solicita redefinição de senha via e-mail | Dado um usuário cadastrado, Quando solicita recuperação de senha, Então o sistema envia e-mail com link único válido por 1h para redefinição |
| FR-005 | Validação de unicidade de CPF/CNPJ/E-mail | Sistema impede duplicidade de documentos e e-mails | Dado um CPF/CNPJ/e-mail já cadastrado, Quando um novo usuário tenta se registrar com o mesmo valor, Então o sistema rejeita com mensagem "documento/e-mail já cadastrado" |

**Módulo: Clientes**

| ID | Título | Descrição | Critério de Aceitação |
|:---:|:---|---|:---|
| FR-010 | Criar cliente | Usuário cadastra novo cliente com nome, documento, contato e endereço | Dado que o usuário está logado, Quando preenche o formulário de cliente e salva, Então o cliente é persistido no banco e exibido na listagem em < 2s |
| FR-011 | Listar clientes | Usuário visualiza todos os clientes cadastrados com paginação | Dado que existem clientes cadastrados, Quando o usuário acessa a página de clientes, Então o sistema exibe até 20 clientes por página com busca por nome |
| FR-012 | Editar cliente | Usuário atualiza dados de um cliente existente | Dado um cliente selecionado, Quando o usuário altera campos e salva, Então o sistema persiste as alterações e exibe confirmação |
| FR-013 | Excluir cliente (lógico) | Usuário marca cliente como inativo (exclusão lógica) | Dado um cliente sem propostas em aberto, Quando o usuário confirma a exclusão, Então o cliente é marcado como inativo mas mantido no histórico |
| FR-014 | Ação rápida WhatsApp | Usuário aciona WhatsApp do cliente com 1 clique | Dado um cliente com WhatsApp cadastrado, Quando o usuário clica no ícone WhatsApp, Então o sistema abre wa.me/55... em nova aba |

**Módulo: Produtos & Serviços**

| ID | Título | Descrição | Critério de Aceitação |
|:---:|:---|---|:---|
| FR-020 | Criar produto/serviço | Usuário cadastra item no catálogo com nome, descrição, tipo e preço | Dado que o usuário está logado, Quando preenche nome, tipo (produto/serviço) e preço em R$, Então o item é adicionado ao catálogo com status ativo |
| FR-021 | Listar catálogo | Usuário visualiza todos os itens do catálogo com filtros | Dado que existem itens cadastrados, Quando o usuário acessa o catálogo, Então pode filtrar por tipo (produto/serviço) e buscar por nome |
| FR-022 | Alternar status ativo/inativo | Usuário ativa ou desativa um item do catálogo | Dado um item selecionado, Quando o usuário alterna o status, Então o item não aparece mais em novas propostas mas permanece em propostas existentes |

**Módulo: Propostas (Mestre-Detalhe)**

| ID | Título | Descrição | Critério de Aceitação |
|:---:|:---|---|:---|
| FR-030 | Criar proposta | Usuário cria nova proposta selecionando cliente e adicionando itens | Dado que o usuário está na página de nova proposta, Quando seleciona um cliente e adiciona ao menos 1 item, Então o sistema calcula automaticamente o total e permite salvar como rascunho |
| FR-031 | Adicionar item à proposta | Usuário adiciona produto/serviço à proposta sem refresh da página | Dado que o usuário está editando uma proposta, Quando seleciona um produto/serviço e define quantidade, Então o item é adicionado à tabela em memória e o total é recalculado em < 100ms |
| FR-032 | Remover item da proposta | Usuário remove item da proposta sem refresh | Dado que existem itens na proposta, Quando o usuário clica em remover, Então o item é removido e o total é recalculado |
| FR-033 | Enviar proposta via WhatsApp | Usuário envia link da proposta para o cliente via WhatsApp | Dado uma proposta salva, Quando o usuário clica em "Enviar WhatsApp", Então o sistema gera link wa.me/... com template substituído |
| FR-034 | Aprovação pública | Cliente aprova ou reprova proposta via link público sem login | Dado que o cliente acessa o link público, Quando clica em "Aprovar" ou "Reprovar", Então o status da proposta é atualizado sem necessidade de autenticação |
| FR-035 | Gerar PDF da proposta | Sistema gera PDF profissional da proposta com logo e dados do tenant | Dado uma proposta finalizada, Quando o usuário clica em "Gerar PDF", Então o sistema retorna PDF com cabeçalho, itens, totais e QR code PIX em < 3s |

**Módulo: Dashboard & Financeiro**

| ID | Título | Descrição | Critério de Aceitação |
|:---:|:---|---|:---|
| FR-040 | KPIs do dashboard | Dashboard exibe indicadores em cards (total clientes, propostas, aprovadas, pendentes, valor) | Dado dados cadastrados, Quando o usuário acessa o dashboard, Então os cards são atualizados com valores do mês corrente em < 2s |
| FR-041 | Gráfico financeiro | Dashboard exibe gráfico de propostas aprovadas dos últimos 6 meses | Dado que existem propostas aprovadas, Quando o dashboard carrega, Então o gráfico Chart.js exibe os valores mensais com tooltip |
| FR-042 | Follow-up do dia | Dashboard lista propostas pendentes com botão de WhatsApp direto | Dado que existem propostas enviadas há mais de 48h sem resposta, Quando o dashboard carrega, Então elas aparecem na lista de follow-up |
| FR-043 | Histórico de transações | Usuário visualiza extrato de transações Mercado Pago | Dado que existem pagamentos processados, Quando o usuário acessa a seção financeira, Então o sistema exibe lista de transações com status, valor, método e data |

**Módulo: Mercado Pago**

| ID | Título | Descrição | Critério de Aceitação |
|:---:|:---|---|:---|
| FR-050 | Criar preferência de pagamento | API cria preferência no Mercado Pago para cobrança | Dado uma proposta aprovada, Quando o usuário clica em "Cobrar", Então a API retorna preference_id e init_point em < 2s |
| FR-051 | Processar webhook IPN | Sistema recebe e processa notificações de pagamento do MP | Dado que um pagamento é concluído, Quando o MP envia webhook, Então o sistema valida o payload, atualiza a transação e marca a proposta como paga |
| FR-052 | Exibir QR Code Pix | Cliente visualiza QR Code para pagamento via Pix | Dado que o cliente escolhe Pix, Quando o checkout é carregado, Então o sistema exibe QR Code para leitura + código copia e cola |
| FR-053 | Estornar pagamento | Usuário solicita estorno total ou parcial de transação | Dado uma transação aprovada, Quando o usuário solicita estorno, Então o sistema envia requisição ao MP e atualiza status para "refunded" |

### NonFunctional Requirements

| ID | Título | Descrição | Especificação |
|:---:|:---|---|:---|
| NFR-01 | Performance — Carregamento | Tempo de carregamento (LCP) | ≤ 2.5s (OKR de produto) |
| NFR-02 | Performance — API Response | Tempo de resposta da API p95 | ≤ 500ms; alerta > 800ms |
| NFR-03 | Performance — Dashboard Load | Carregamento do dashboard | < 2s com endpoint agregado |
| NFR-04 | Performance — PDF Generation | Geração de PDF | < 3s por proposta (pdfkit) |
| NFR-05 | Disponibilidade — Uptime | Disponibilidade da plataforma | ≥ 99.9% (alerta < 99.5%) |
| NFR-06 | Disponibilidade — Backup | Backup automático do banco | Diário + point-in-time recovery |
| NFR-07 | Segurança — SQL Injection | Prevenção contra SQLi | Prepared Statements (mysql2 ?) |
| NFR-08 | Segurança — XSS | Prevenção contra Cross-Site Scripting | Output escaping (htmlspecialchars no PHP) |
| NFR-09 | Segurança — Brute Force | Proteção contra força bruta | Rate limiting (express-rate-limit na API) |
| NFR-10 | Segurança — Autenticação | Armazenamento de senhas | bcrypt hash + JWT (Authorization: Bearer) 24h |
| NFR-11 | Segurança — Transporte | Criptografia em trânsito | HTTPS via Cloudflare SSL/TLS |
| NFR-12 | Segurança — Dados em Repouso | Criptografia em repouso | MySQL TDE + backups criptografados |
| NFR-13 | LGPD — Consentimento | Coleta de consentimento | Checkbox explícito no cadastro |
| NFR-14 | LGPD — Direito de Exclusão | Exclusão de dados pessoais | Endpoint DELETE /api/v1/account com anonimização após 90 dias |
| NFR-15 | Multi-tenancy — Isolamento | Isolamento entre tenants | tenant_id injetado via middleware em TODAS as queries |
| NFR-16 | Observabilidade — Formato de Logs | Schema JSON padronizado | timestamp, level, message, service, environment, correlation_id |
| NFR-17 | Observabilidade — Níveis de Log | Hierarchy de severidade | debug, info, warn, error, fatal. Configurável via LOG_LEVEL |
| NFR-18 | Observabilidade — Correlation ID | Rastreamento ponta-a-ponta | UUID v4 gerado no Nginx, propagado para PHP e API |
| NFR-19 | Observabilidade — Audit Logging | Registro imutável LGPD | audit: true para ações sensíveis. Retenção: 5 anos |
| NFR-20 | Observabilidade — API Access Log | Log de requisições HTTP | Nginx access log JSON. Health check filtrados |
| NFR-21 | Observabilidade — Error Tracking | Captura centralizada de exceções | Middleware global + JSON response sem stack trace |
| NFR-22 | Observabilidade — Business Events | Log de eventos de domínio | event_type: proposta_criada, pagamento_aprovado, etc. |
| NFR-23 | Observabilidade — Log Retention | Política de retenção | Dev 7d, Staging 30d, Produção 90d Loki + 5y S3 audit |
| NFR-24 | Observabilidade — Métricas/Dashboards | Monitoramento com SLOs | Infraestrutura + Negócio. Alertas: API Down, Latência, Erro Rate |
| NFR-25 | CI/CD — Quality Gate | Pipeline de qualidade | GitHub Actions: lint → test → build → security scan → deploy |
| NFR-26 | Containerização — Isolamento | Serviços isolados | Docker Compose multi-container |
| NFR-27 | Responsividade | Suporte a múltiplos dispositivos | Desktop, Tablet, Mobile |
| NFR-28 | Acessibilidade — Teclado | Navegação por teclado | Tab navegável, foco visível |
| NFR-29 | Acessibilidade — Leitores de Tela | Suporte a screen readers | aria-label, aria-expanded, role |
| NFR-30 | Acessibilidade — Contraste | Contraste mínimo WCAG AA | 4.5:1 texto normal, 3:1 texto grande |
| NFR-31 | Acessibilidade — Touch | Touch targets em mobile | ≥ 44px |
| NFR-32 | Cache — Assets Estáticos | Cache de arquivos estáticos | Cache-Control: public, max-age=31536000 |
| NFR-33 | Cache — Dashboard | Cache de endpoint de dashboard | Nginx cache 30s |
| NFR-LGPD-01 | LGPD — DPA no Contrato | Termo de Uso deve conter DPA | Disponível em termos-de-uso.html |
| NFR-LGPD-02 | LGPD — Inventário de Dados | Mapeamento de dados pessoais | docs/lgpd/registro-operacoes.md |
| NFR-LGPD-03 | LGPD — Consentimento Granular | Checkboxes separados | Cadastro, Marketing, Compartilhamento MP |
| NFR-LGPD-04 | LGPD — Direito de Exclusão | Endpoint para titular solicitar exclusão | POST /api/v1/data-subject-request. Prazo: 15 dias |
| NFR-LGPD-05 | LGPD — Canal DPO | Canal de comunicação com titular | privacidade@seudominio.com.br |
| NFR-LGPD-06 | LGPD — Criptografia em Repouso | Dados pessoais criptografados | MySQL TDE + AES-256 backups |
| NFR-LGPD-07 | LGPD — Política de Retenção | Prazos de retenção por tipo de dado | Fiscais 5y, Operacionais 90d, Audit 5y |
| NFR-LGPD-08 | LGPD — Plano de Incidente | Notificação de vazamento | Script de resposta + template. Notificação ANPD em 72h |
| NFR-LGPD-09 | LGPD — Minimização de Coleta | Coletar apenas dados necessários | Privacy by Design checklist |
| NFR-LGPD-10 | LGPD — Privacy by Design | Segurança desde a concepção | Revisão de privacidade no PRD de cada funcionalidade |
| NFR-ADM-01 | Admin — Autenticação | Login separado para administradores | JWT com is_admin: true |
| NFR-ADM-02 | Admin — Sem Tenancy Filter | Admin consulta dados de TODOS os tenants | Middleware bypass |
| NFR-ADM-03 | Admin — Audit Trail | Toda ação administrativa registrada | Tabela admin_audit_log. Retenção: 5 anos |
| NFR-ADM-04 | Admin — Acesso Restrito | Rotas /api/v1/admin/* rejeitam sem role super_admin | Middleware adminAuth retorna 403 |
| NFR-ADM-05 | Admin — Subdomínio Separado | Admin em subdomínio isolado | admin.seudominio.com.br ou /admin/ |

### Additional Requirements (Architecture)

**Decisões Arquiteturais:**

| ID | Descrição | Fonte |
|:---:|:---|---|
| AR-01 | JWT como única fonte de verdade (Authorization: Bearer, 24h, $_SESSION['jwt']) | AD-1 |
| AR-02 | Multi-tenancy com tenant_id injetado em toda query via middleware | AD-2 |
| AR-03 | Endpoint agregado GET /api/v1/dashboard/summary para KPIs + gráfico + follow-up | AD-3 |
| AR-04 | Persistência atômica de proposta em transação MySQL (header + items) | AD-4 |
| AR-05 | Idempotência de pagamentos via X-Idempotency-Key + UNIQUE constraint | AD-5 |
| AR-06 | Geração de PDF via pdfkit (nativo Node.js) | AD-6 |
| AR-07 | Cache Nginx para assets estáticos + endpoint dashboard | AD-7 |
| AR-08 | Exclusão lógica: active=false para clients/products; status=cancelled | AD-8 |

**Stack com Versões:**

| Tecnologia | Versão | Função |
|:---|---|:---|
| PHP + HTML5/CSS3/JS | 8.2 | Frontend rendering |
| Node.js + Express.js | 20 LTS | API REST |
| MySQL | 8.0 | Banco de dados relacional |
| Nginx | 1.25-alpine | Reverse proxy + cache |
| Docker Compose | 3.8+ | Container orchestration |
| Cloudflare Tunnel (cloudflared) | 2024.6.1 | Exposição web segura |
| Mercado Pago SDK JS | 4.0 | Checkout (frontend) |
| Mercado Pago SDK Node | 2.1 | Payments (backend) |
| pdfkit | 0.15.0 | Geração de PDF |
| Chart.js | 4.4 | Gráficos do dashboard |
| mysql2 | 3.10 | Driver MySQL Node.js |
| jsonwebtoken | 9.0 | JWT auth |
| bcrypt | 5.1 | Password hashing |

**Convenções Técnicas:**

- API Routes: `/api/v1/{module}/{action}` (plural resources)
- DB Tables: snake_case, plural (tenants, users, clients, products_services, proposals, proposal_items, public_leads, transactions)
- Arquivos JS: kebab-case (auth.middleware.js, payments.service.js)
- Arquivos PHP: snake_case (api_client.php, proposta_form.php)
- IDs: INT AUTO_INCREMENT (DB), UUID v4 (public tokens), OS-{year}-{sequential} (proposal numbers)
- Datas: TIMESTAMP UTC (storage), dd/mm/YYYY (display)
- Moeda: DECIMAL(10,2) (storage), R$ 0.000,00 (display)
- Erros: JSON `{ error: string, code: string, details?: any }`
- Estrutura: web-frontend/ (PHP) + api-backend/ (Node.js) — modular por domínio

**Infraestrutura:**

- 3 ambientes: Development (localhost), Staging, Production (Cloudflare Tunnel)
- GitHub Actions: PHP Lint → ESLint → Jest tests → Docker build → Trivy scan → Deploy
- Observabilidade: LGTM Stack (Loki + Grafana + Tempo + Mimir)

### UX Design Requirements

**Visual Identity (DESIGN.md):**

| ID | Descrição | Prioridade |
|:---:|:---|---:|
| UX-DR01 | Implementar sistema de design tokens (cores, tipografia, espaçamento) | P0 |
| UX-DR02 | Criar variáveis CSS para paleta de cores completa (primary #10B981, Slate) | P0 |
| UX-DR03 | Implementar escala tipográfica Poppins (display 30px → caption 12px) | P0 |
| UX-DR04 | Implementar grid de 8px para todos os espaçamentos | P0 |
| UX-DR05 | Construir sistema de botões: Primário, Secundário, WhatsApp | P0 |
| UX-DR06 | Construir sistema de Badges semânticos (success/warning/info/danger) | P0 |
| UX-DR07 | Construir sistema de Cards: KPI e Proposta | P0 |
| UX-DR08 | Implementar Sidebar (260px, fundo #0F172A, 6 itens) | P0 |
| UX-DR09 | Implementar Topbar (64px, busca global, notificações, avatar) | P0 |
| UX-DR10 | Implementar Input component com focus ring 2px primary-500 | P0 |
| UX-DR11 | Implementar elevadores (shadow-sm/md/lg) | P1 |
| UX-DR12 | Implementar 3 breakpoints responsivos | P0 |

**Experiência (EXPERIENCE.md):**

| ID | Descrição | Prioridade |
|:---:|:---|---:|
| UX-DR13 | Construir 10 superfícies: Landing, Login, Cadastro, Dashboard, etc. | P0 |
| UX-DR14 | Implementar Voice & Tone | P0 |
| UX-DR15 | Implementar Empty States com ilustração + CTA | P0 |
| UX-DR16 | Implementar Loading States com skeleton loader | P0 |
| UX-DR17 | Implementar Error States: toast/banner, validação inline | P0 |
| UX-DR18 | Implementar Success State: toast verde, auto-dismiss 3s | P0 |
| UX-DR19 | Implementar Session Expired Modal | P0 |
| UX-DR20 | Implementar página 404 | P1 |
| UX-DR21 | Implementar navegação por teclado | P0 |
| UX-DR22 | Implementar suporte a screen readers | P1 |
| UX-DR23 | Garantir contraste WCAG AA | P0 |
| UX-DR24 | Garantir touch targets ≥ 44px | P0 |
| UX-DR25 | Flow 1 — Criação de proposta | P0 |
| UX-DR26 | Flow 2 — Aprovação pública + pagamento | P0 |
| UX-DR27 | Flow 3 — Dashboard: KPI cards, gráfico, follow-up | P0 |
| UX-DR28 | Flow 4 — Captura de lead na Landing Page | P0 |

---

### FR Coverage Map

| FR | Épico | Descrição |
|:---:|:---:|:---|
| FR-001 | **Epic 1** | Cadastro de usuário (PF) |
| FR-002 | **Epic 1** | Cadastro de usuário (PJ) |
| FR-003 | **Epic 1** | Login com e-mail e senha |
| FR-004 | **Epic 1** | Recuperação de senha |
| FR-005 | **Epic 1** | Validação de unicidade CPF/CNPJ/E-mail |
| FR-010 | **Epic 2** | Criar cliente |
| FR-011 | **Epic 2** | Listar clientes |
| FR-012 | **Epic 2** | Editar cliente |
| FR-013 | **Epic 2** | Excluir cliente (lógico) |
| FR-014 | **Epic 2** | Ação rápida WhatsApp |
| FR-020 | **Epic 2** | Criar produto/serviço |
| FR-021 | **Epic 2** | Listar catálogo |
| FR-022 | **Epic 2** | Alternar status ativo/inativo |
| FR-030 | **Epic 3** | Criar proposta |
| FR-031 | **Epic 3** | Adicionar item à proposta |
| FR-032 | **Epic 3** | Remover item da proposta |
| FR-033 | **Epic 3** | Enviar proposta via WhatsApp |
| FR-034 | **Epic 3** | Aprovação pública |
| FR-035 | **Epic 3** | Gerar PDF da proposta |
| FR-040 | **Epic 4** | KPIs do dashboard |
| FR-041 | **Epic 4** | Gráfico financeiro |
| FR-042 | **Epic 4** | Follow-up do dia |
| FR-043 | **Epic 4** | Histórico de transações |
| FR-050 | **Epic 5** | Criar preferência de pagamento |
| FR-051 | **Epic 5** | Processar webhook IPN |
| FR-052 | **Epic 5** | Exibir QR Code Pix |
| FR-053 | **Epic 5** | Estornar pagamento |
| Leads | **Epic 6** | Captura de leads na Landing Page |
| FR-ADM-01 | **Epic 7** | Login Admin |
| FR-ADM-02 | **Epic 7** | Dashboard Global |
| FR-ADM-03 | **Epic 7** | Listar Tenants |
| FR-ADM-04 | **Epic 7** | Editar Tenant |
| FR-ADM-05 | **Epic 7** | Suspender Tenant |
| FR-ADM-06 | **Epic 7** | Visualizar Transações |
| FR-ADM-07 | **Epic 7** | Estornar Pagamento |
| FR-ADM-08 | **Epic 7** | Gerenciar Planos |
| FR-ADM-09 | **Epic 7** | Relatório Financeiro |
| FR-ADM-10 | **Epic 7** | Auditoria de Ações |

## Epic List

### Epic 1: 🔐 Onboarding & Autenticação
**Prestador cria conta, autentica e gerencia seu perfil**

**FRs cobertos:** FR-001, FR-002, FR-003, FR-004, FR-005
**UX Surfaces:** Cadastro (PF/PJ), Login, Configurações
**NFRs:** NFR-07 (SQLi), NFR-08 (XSS), NFR-10 (bcrypt/JWT), NFR-11 (HTTPS), NFR-15 (tenancy)
**ARs:** AR-01 (JWT), AR-02 (multi-tenancy)

### Epic 2: 👥 Gestão de Clientes e Catálogo
**Prestador gerencia sua base de clientes e catálogo de produtos/serviços**

**FRs cobertos:** FR-010, FR-011, FR-012, FR-013, FR-014, FR-020, FR-021, FR-022
**UX Surfaces:** Clientes, Produtos/Serviços
**ARs:** AR-02 (tenancy), AR-08 (logical delete)

### Epic 3: 📄 Ciclo de Vida da Proposta
**Prestador cria, envia e gerencia propostas; cliente aprova/rejeita**

**FRs cobertos:** FR-030, FR-031, FR-032, FR-033, FR-034, FR-035
**UX Surfaces:** Propostas (lista + form), Proposta Pública
**UX Flows:** Flow 1 (Maria cria proposta), Flow 2 (Carlos aprova)
**ARs:** AR-04 (atomic persistence), AR-06 (pdfkit)

### Epic 4: 📊 Dashboard e Métricas
**Prestador consulta KPIs, gráficos financeiros e follow-up de propostas**

**FRs cobertos:** FR-040, FR-041, FR-042, FR-043
**UX Surfaces:** Dashboard
**UX Flows:** Flow 3 (Maria consulta dashboard)
**ARs:** AR-03 (aggregated endpoint), AR-07 (Nginx cache)

### Epic 5: 💳 Pagamentos com Mercado Pago
**Cliente paga proposta via Pix/Cartão/Boleto; prestador gerencia transações**

**FRs cobertos:** FR-050, FR-051, FR-052, FR-053
**UX Surfaces:** Financeiro, Proposta Pública (checkout)
**UX Flows:** Flow 2 (Carlos paga)
**ARs:** AR-05 (idempotency)

### Epic 6: 🌐 Presença Pública e Captura de Leads
**Cliente encontra profissional, solicita orçamento via Landing Page**

**FRs cobertos:** Landing Page + Leads
**UX Surfaces:** Landing Page
**UX Flows:** Flow 4 (Carlos chega pela LP)
**ARs:** —

### Epic 7: 🏢 Administração da Plataforma
**Equipe ServiceSaaS gerencia tenants, planos, transações e auditoria**

**FRs cobertos:** FR-ADM-01 a FR-ADM-10
**UX Surfaces:** Admin Dashboard, Admin Tenants, Admin Financeiro
**NFRs:** NFR-ADM-01 a NFR-ADM-05
**ARs:** — (Admin bypassa tenancy)
**Depende de:** Epic 1 (Auth), Epic 5 (Payments/MP)

<!-- Template: .claude/skills/bmad-create-epics-and-stories/templates/epics-template.md -->

## Epic 1: 🔐 Onboarding & Autenticação

**Goal:** Prestador cria conta (PF/PJ), faz login, recupera senha e gerencia perfil.

### Story 1.1: 📦 Setup de Infraestrutura e Cadastro de Usuário (PF)

**As a** Prestador de serviço (Maria),
**I want** criar minha conta com nome, CPF, e-mail, telefone e senha e ser redirecionado ao dashboard,
**So that** eu possa começar a usar o ServiceSaaS imediatamente após o cadastro.

**Acceptance Criteria:**

**Given** Docker Compose está instalado no ambiente
**When** o desenvolvedor executa `docker compose up -d`
**Then** todos os 5 containers (nginx, php, api, pma, mysql) sobem sem erro
**And** a aplicação fica acessível em `localhost:8080`

**Given** o projeto não possui estrutura de diretórios
**When** o desenvolvedor clona o repositório
**Then** a estrutura `servicos-flex/` é criada com as pastas padrão
**And** cada diretório contém os arquivos iniciais

**Given** o MySQL está rodando
**When** o script SQL de inicialização executa
**Then** as tabelas `tenants` e `users` são criadas com índices e FKs

**Given** o usuário acessa `/register.php`
**When** preenche nome, CPF, e-mail, telefone e senha com dados válidos
**Then** o sistema cria `tenants` + `users`, retorna JWT válido por 24h
**And** o usuário é redirecionado ao dashboard

**Given** o usuário tenta cadastrar com e-mail já existente
**When** envia formulário
**Then** o sistema exibe "E-mail já cadastrado" sem criar duplicidade

**Given** POST `/api/v1/auth/register` com payload válido
**When** a API processa
**Then** HTTP 201 com `{ token, user: { id, name, email } }`
**And** header `X-Request-ID` presente

**Entregáveis:** Docker Compose, Nginx config, Dockerfiles, Migration SQL, API Register, Frontend Register

**FRs:** FR-001 | **NFRs:** NFR-07, NFR-08, NFR-10, NFR-11, NFR-15, NFR-26

---

### Story 1.2: 🎨 Design System Base (Tailwind CSS)

**As a** desenvolvedor do ServiceSaaS,
**I want** implementar o design system base com Tailwind CSS configurado com a paleta oficial,
**So that** todas as páginas tenham identidade visual consistente.

**Acceptance Criteria:**

**Given** `tailwind.config.js` existe
**When** inspecionado
**Then** cores primárias (`primary: #10B981`), neutras e de status estão definidas

**Given** tipografia implementada
**When** verificado
**Then** Poppins configurada via Google Fonts

**Given** grid de espaçamento configurado
**When** inspecionado
**Then** tokens spacing-1 a spacing-12 disponíveis

**Given** botão primário existe
**When** inspecionado
**Then** `.btn-primary` com hover, active, disabled states

**Given** sidebar implementada
**When** inspecionado
**Then** w-64 bg-sidebar text-white, com 6 itens de navegação

**Given** topbar implementada
**When** inspecionado
**Then** h-16, logo, busca, avatar dropdown

**UX-DRs:** UX-DR01 a UX-DR11

---

### Story 1.3: 📝 Cadastro de Usuário (PJ) e Validação de Unicidade

**As a** Prestador de serviço (empresa),
**I want** criar minha conta PJ informando CNPJ, razão social e contato,
**So that** eu possa usar a plataforma para minha empresa.

**Acceptance Criteria:**

**Given** usuário no cadastro
**When** seleciona "Pessoa Jurídica"
**Then** campos CNPJ, Razão Social aparecem; CPF é ocultado

**Given** CNPJ válido
**When** cadastra
**Then** API valida CNPJ + dígitos, cria `tenants` + `users`

**Given** CNPJ inválido
**When** tenta cadastrar
**Then** sistema exibe "CNPJ inválido"

**Given** e-mail já cadastrado
**When** tenta cadastrar
**Then** rejeita com "E-mail já cadastrado"

**Given** CNPJ já cadastrado
**When** outro usuário tenta mesmo CNPJ
**Then** rejeita com "CNPJ já cadastrado"

**Entregáveis:** Extensão API Register, Validação CNPJ, Uniqueness checks, Seletor PF/PJ

**FRs:** FR-002, FR-005 | **Depende de:** Story 1.1

---

### Story 1.4: 🔑 Autenticação (Login + JWT + Middleware)

**As a** Prestador de serviço (Maria),
**I want** fazer login e acessar o dashboard de forma segura,
**So that** o sistema saiba quem sou e a qual tenant pertenço.

**Acceptance Criteria:**

**Given** usuário cadastrado acessa `/login.php`
**When** informa e-mail e senha corretos
**Then** PHP faz POST `/api/v1/auth/login`, API valida bcrypt, gera JWT
**And** PHP armazena token em `$_SESSION['jwt']` e redireciona ao dashboard

**Given** usuário tenta login com e-mail inexistente
**When** informa credenciais
**Then** sistema exibe "Credenciais inválidas" (genérica)

**Given** 5+ tentativas falhas em 1 min
**When** sistema detecta brute force
**Then** HTTP 429 com `Retry-After`, bloqueio de 60s

**Given** usuário autenticado acessa rota privada
**When** não há JWT em `$_SESSION`
**Then** redireciona para `/login.php` com "Faça login para continuar"

**Given** API recebe requisição autenticada
**When** `auth.middleware.js` extrai JWT do header `Authorization: Bearer <token>`
**Then** valida assinatura, popula `req.user`, `tenant.middleware` injeta tenant_id

**Given** POST `/api/v1/auth/login` válido
**When** requisição enviada
**Then** HTTP 200 + `{ token, user, tenant }`

**Entregáveis:** API Login, Auth Middleware, Tenant Middleware, Rate Limiter, Frontend Login

**FRs:** FR-003 | **NFRs:** NFR-09, NFR-10 | **ARs:** AR-01, AR-02 | **Depende de:** Story 1.1

---

### Story 1.5: 🔄 Recuperação de Senha

**As a** Prestador de serviço (Maria),
**I want** solicitar redefinição de senha caso eu a tenha esquecido,
**So that** eu possa recuperar o acesso sem suporte manual.

**Acceptance Criteria:**

**Given** usuário não autenticado em `/login.php`
**When** clica em "Esqueci minha senha"
**Then** redirecionado para `/forgot-password.php`

**Given** usuário informa e-mail cadastrado
**When** clica em "Enviar link"
**Then** API gera token único (expira 1h), salva em `users.reset_token`
**And** e-mail enviado com link `/reset-password.php?token=UUID`

**Given** e-mail NÃO cadastrado
**When** tenta recuperar
**Then** mensagem genérica "Se o e-mail existir, você receberá um link"

**Given** token válido
**When** acessa link
**Then** formulário de nova senha é exibido

**Given** token inválido ou expirado
**When** acessa link
**Then** "Link inválido ou expirado"

**Given** nova senha + confirmação válidas
**When** envia
**Then** bcrypt, atualiza `users.password_hash`, limpa `reset_token`
**And** redireciona para `/login.php` com "Senha redefinida com sucesso!"

**Entregáveis:** API Forgot/Reset Password, Email Stub, Frontend Forgot/Reset, Migration

**FRs:** FR-004 | **Depende de:** Story 1.4

---

## Epic 2: 👥 Gestão de Clientes e Catálogo

**Goal:** Prestador gerencia clientes e produtos/serviços do catálogo.

<!-- Stories will be created in Step 3 -->

## Epic 3: 📄 Ciclo de Vida da Proposta

**Goal:** Prestador cria proposta com mestre-detalhe, envia via WhatsApp, gera PDF.

<!-- Stories will be created in Step 3 -->

## Epic 4: 📊 Dashboard e Métricas

**Goal:** Prestador visualiza KPIs, gráfico financeiro e lista de follow-up.

### Story 4.1: 📊 KPIs do Dashboard + Endpoint Agregado

**As a** Prestador de serviço (Maria),
**I want** ver no dashboard os indicadores principais em cards visuais,
**So that** eu possa ter uma visão rápida do meu negócio.

**Acceptance Criteria:**

**Given** o dashboard carrega
**When** a página é aberta
**Then** 4 cards KPI são exibidos: Clientes, Propostas (mês), Faturamento (R$), Pendentes
**And** cada card tem ícone, valor destacado, label

**Given** não existem dados
**When** dashboard carrega
**Then** cards exibem "0" ou "--"
**And** mensagem de boas-vindas é mostrada

**Given** API retorna erro
**When** dashboard tenta carregar
**Then** cards mantêm último valor ou "--"
**And** toast de erro não-intrusivo

**Given** GET /api/v1/dashboard
**When** autenticado
**Then** retorna JSON com tenant isolation
**And** resposta < 500ms com Nginx cache 30s

**FRs:** FR-040 | **ARs:** AR-03 | **NFRs:** NFR-03

---

### Story 4.2: 📈 Gráfico Financeiro (Chart.js 6 meses)

**As a** Prestador (Maria),
**I want** visualizar gráfico de faturamento dos últimos 6 meses,
**So that** eu possa identificar tendências sazonais.

**Acceptance Criteria:**

**Given** existem propostas aprovadas nos últimos 6 meses
**When** dashboard carrega
**Then** gráfico Chart.js é exibido com tooltips

**Given** não existem dados
**When** dashboard carrega
**Then** mensagem "Nenhum dado financeiro" + CTA

**Given** GET /api/v1/dashboard/chart
**When** autenticado
**Then** retorna JSON com tenant isolation

**FRs:** FR-041 | **UX-DRs:** UX-DR27 | **Depende de:** Story 4.1

---

### Story 4.3: 🔔 Follow-up do Dia + Propostas Pendentes

**As a** Prestador (Maria),
**I want** ver propostas enviadas ha mais de 48h sem resposta,
**So that** eu possa fazer follow-up rapido.

**Acceptance Criteria:**

**Given** existem propostas "sent" ou "viewed" ha > 48h
**When** dashboard carrega
**Then** secao "Follow-up do Dia" exibe ate 10 itens
**And** cada item: cliente, numero, valor, dias, botao WhatsApp

**Given** nao existem pendentes
**When** dashboard carrega
**Then** secao exibe "Nenhum follow-up pendente"

**Given** GET /api/v1/dashboard/followup
**When** autenticado
**Then** retorna JSON com tenant isolation

**FRs:** FR-042 | **UX-DRs:** UX-DR27 | **Depende de:** Story 4.1

---

### Story 4.4: 💰 Historico de Transacoes (Secao Financeira)

**As a** Prestador (Maria),
**I want** acessar o extrato de transacoes Mercado Pago,
**So that** eu possa acompanhar pagamentos e estornos.

**Acceptance Criteria:**

**Given** existem transacoes
**When** usuario acessa secao financeira
**Then** tabela com: proposta, cliente, valor, taxa, liquido, metodo, status, data
**And** paginada (20 itens) com busca

**Given** nao existem transacoes
**When** secao carrega
**Then** empty state com CTA

**Given** GET /api/v1/transactions
**When** autenticado
**Then** JSON paginado com tenant isolation

**Given** transacao "approved"
**When** visualizada
**Then** badge verde "Aprovado" com tooltip

**FRs:** FR-043 | **Depende de:** Epic 5 ou Story 4.1

---

## Epic 5: 💳 Pagamentos com Mercado Pago

**Goal:** Cliente paga proposta (Pix/Cartao/Boleto); prestador gerencia transacoes.

**FRs cobertos:** FR-050, FR-051, FR-052, FR-053
**UX Surfaces:** Financeiro, Proposta Publica (checkout)
**UX Flows:** Flow 2 - Carlos paga proposta
**ARs:** AR-05 (idempotencia)

### Story 5.1: 🏗️ Setup SDK Mercado Pago + Tabela transactions

**As a** Prestador (Maria),
**I want** que o sistema esteja configurado para integrar com o MP,
**So that** eu possa receber pagamentos de forma segura.

**Acceptance Criteria:**

**Given** credenciais MP nao configuradas
**When** API inicia
**Then** loga aviso mas nao impede inicializacao
**And** endpoints de pagamento retornam 503

**Given** credenciais configuradas no .env
**When** API inicia
**Then** mercadopagoService.js valida credenciais
**And** expoe funcoes: createPreference, getPayment, refundPayment

**Given** migration executada
**When** script roda
**Then** tabela transactions criada com FK para proposals e tenants

**Entregaveis:** mercadopagoService.js, config/mercadopago.js, migration transactions

**FRs:** — (Setup base) | **ARs:** AR-05

---

### Story 5.2: 🔗 Criar Preferencia de Pagamento + Botao Cobrar

**As a** Prestador (Maria),
**I want** clicar em "Cobrar" e gerar link de pagamento MP,
**So that** meu cliente possa pagar integradamente.

**Acceptance Criteria:**

**Given** proposta com status "accepted"
**When** usuario clica em "Cobrar"
**Then** API chama POST /api/v1/payments/create-preference
**And** retorna preference_id, init_point em < 2s

**Given** proposta ja possui preferencia ativa
**When** clica em "Cobrar" novamente
**Then** reutiliza preferencia existente (idempotencia)

**Given** itens da proposta
**When** preferencia criada
**Then** payload MP com items: title, quantity, unit_price, currency_id: BRL

**Given** MP retorna erro
**When** tenta criar preferencia
**Then** mensagem amigavel + log completo

**FRs:** FR-050 | **Depende de:** Story 5.1

---

### Story 5.3: 📩 Webhook IPN + Atualizacao de Status

**As a** Sistema ServiceSaaS,
**I want** processar notificacoes de pagamento do MP,
**So that** status das transacoes seja atualizado automaticamente.

**Acceptance Criteria:**

**Given** MP envia webhook POST /api/v1/payments/webhook
**When** type: payment com data_id valido
**Then** API valida autenticidade consultando GET /v1/payments/:id
**And** processa apenas se nao processado (idempotencia)

**Given** pagamento aprovado
**When** webhook processado
**Then** transacao -> completed, proposta -> paid

**Given** pagamento recusado
**When** webhook processado
**Then** transacao -> cancelled, proposta permanece accepted

**Given** chargeback
**When** webhook processado
**Then** transacao -> chargeback, proposta volta para accepted

**Given** tipo nao suportado (ex: merchant_order)
**When** handler executa
**Then** retorna 200 OK sem processamento, log debug

**FRs:** FR-051 | **Depende de:** Story 5.1

---

### Story 5.4: 💠 Checkout Pix (QR Code + Copia e Cola)

**As a** Cliente (Carlos),
**I want** pagar via Pix vendo QR Code e copiando o codigo,
**So that** eu possa pagar rapidamente pelo app do banco.

**Acceptance Criteria:**

**Given** cliente opta por Pix
**When** checkout MP carregado
**Then** QR Code exibido + codigo Copia e Cola com botao Copiar
**And** toast "Codigo Pix copiado!" ao clicar

**Given** pagamento Pix concluido
**When** webhook confirma
**Then** tela exibe "Pagamento confirmado!"
**And** redireciona apos 3s

**Given** QR Code expira (30 min)
**When** sem pagamento
**Then** exibe "Tempo expirado" com botao "Gerar novo QR"

**FRs:** FR-052 | **UX-DRs:** UX-DR26 | **Depende de:** Story 5.2

---

### Story 5.5: 🔄 Estornar Pagamento

**As a** Prestador (Maria),
**I want** solicitar estorno de transacao aprovada,
**So that** eu possa reembolsar clientes sem sair da plataforma.

**Acceptance Criteria:**

**Given** transacao "approved"
**When** usuario acessa detalhe
**Then** botao "Estornar Pagamento" com modal (total/parcial)

**Given** estorno total
**When** confirma
**Then** API chama POST /api/v1/payments/:id/refund
**And** transacao -> refunded, proposta -> accepted

**Given** estorno parcial (ex: R$50)
**When** informa valor
**Then** transacao original permanece approved com net ajustado
**And** nova transacao de estorno criada

**Given** transacao ja estornada
**When** tenta novamente
**Then** botao desabilitado com tooltip

**Given** MP retorna erro (prazo > 180 dias)
**When** API processa
**Then** mensagem amigavel + log completo

**FRs:** FR-053 | **Depende de:** Story 5.1 + 5.3

---

## Epic 6: 🌐 Presença Pública e Captura de Leads

**Goal:** Cliente encontra profissionais, solicita orçamento via Landing Page.

**FRs cobertos:** Landing Page + Leads
**UX Surfaces:** Landing Page
**UX Flows:** Flow 4 - Carlos chega pela LP
**UX-DRs:** UX-DR28 (Flow 4 - Captura de lead)

---

### Story 6.1: 🏠 Landing Page + Busca por Categoria

**As a** Cliente final (Carlos),
**I want** acessar a Landing Page, ver categorias e buscar servicos,
**So that** eu encontre rapidamente o profissional ideal.

**Acceptance Criteria:**

**Given** cliente acessa http://localhost:8080/
**When** pagina carrega
**Then** Hero com titulo, subtitulo, barra de busca, tags de categorias
**And** secao "Como Funciona" (3 passos), grid de categorias, CTA profissionais

**Given** cliente digita na busca
**When** termo >= 2 caracteres
**Then** requisicao AJAX para GET /api/v1/public/services?search=
**And** sugestoes com nome, categoria e valor medio

**Given** cliente clica em categoria
**When** selecionada
**Then** pagina scrolla para wizard com categoria pre-selecionada

**Given** cliente no mobile
**When** LP carrega
**Then** layout responsivo: grid 2 colunas, hero menor, menu hamburguer
**And** touch targets >= 44px

**Given** cliente clica em "Cadastrar" no header
**When** nao logado
**Then** redireciona para ?page=register

**UX-DRs:** UX-DR13, UX-DR28 | **FRs:** — (Criar endpoint publico GET /api/v1/public/services sem auth) | **NFRs:** NFR-27, NFR-01

---

### Story 6.2: 📝 Wizard de Solicitacao (3 Passos)

**As a** Cliente (Carlos),
**I want** solicitar orcamento em wizard de 3 passos,
**So that** eu descreva o que preciso sem me cadastrar.

**Acceptance Criteria:**

**Given** cliente clica em "Solicitar Orcamento"
**When** wizard abre
**Then** Passo 1: busca/selecao do servico com categorias em grid
**And** progresso "Passo 1 de 3"

**Given** cliente seleciona servico e avanca
**When** Passo 2 carrega
**Then** formulario: descricao, data/horario, endereco (CEP autocomplete), referencia
**And** fotos (upload opcional)

**Given** cliente preenche detalhes e avanca
**When** Passo 3 carrega
**Then** formulario: nome, telefone (mascara), email (opcional)
**And** checkbox LGPD "Aceito ser contactado"
**And** resumo do pedido

**Given** cliente confirma
**When** clica em "Solicitar Orcamento"
**Then** POST /api/v1/public/leads chamado
**And** tela exibe "Solicitacao enviada com sucesso!"

**Given** campos obrigatorios nao preenchidos
**When** tenta avancar
**Then** borda vermelha + erro inline
**And** wizard nao avanca

**Tabela:** public_leads | **FRs:** — (Captura de lead) | **UX-DRs:** UX-DR28 | **NFRs:** NFR-LGPD-01, NFR-LGPD-03, NFR-LGPD-09, NFR-27

---

### Story 6.3: 🔗 Pagina Publica de Proposta (Link Compartilhavel)

**As a** Cliente (Carlos),
**I want** acessar link publico da proposta, ver itens e aprovar/rejeitar,
**So that** eu possa responder sem fazer login.

**Acceptance Criteria:**

**Given** profissional envia proposta via WhatsApp
**When** cliente clica no link
**Then** abre pagina publica /p/{public_token} sem autenticacao
**And** exibe: empresa, itens, totais

**Given** proposta "sent" ou "viewed"
**When** pagina publica carrega
**Then** botoes "Aprovar" (verde) e "Rejeitar" (vermelho) exibidos

**Given** cliente clica em "Aprovar"
**When** confirma
**Then** PATCH /api/v1/public/proposals/:token/status com action: approve
**And** proposta -> viewed (se sent) ou accepted (se viewed)

**Given** cliente clica em "Rejeitar"
**When** confirma com motivo
**Then** PATCH com action: reject
**And** proposta -> rejected

**Given** proposta ja aprovada/rejeitada
**When** acessa link
**Then** exibe status atual sem botoes de acao

**Given** token invalido
**When** acessa
**Then** "Proposta nao encontrada" com ilustracao 404

**Endpoint:** GET /api/v1/public/proposals/:token (sem auth) | **FRs:** FR-034 (requer bypass auth.middleware + publicRouter separado) | **UX-DRs:** UX-DR26 | **Depende de:** Epic 3

---

### Story 6.4: 🤝 Call-to-action Profissional + Painel de Leads

**As a** Prestador (Maria),
**I want** que a LP tenha CTA para novos profissionais e que eu veja leads recebidos,
**So that** novos prestadores descubram a plataforma.

**Acceptance Criteria:**

**Given** visitante na LP
**When** scrolla ao final
**Then** secao "Seja um Profissional Parceiro": headline, 3 cards, botao "Cadastre-se Gratis"
**And** botao redireciona para /register.php

**Given** profissional logado no dashboard
**When** acessa secao "Leads"
**Then** lista: nome, telefone, servico, data, status (novo/contactado/convertido)
**And** paginacao 20 itens

**Given** lead novo
**When** clica no lead
**Then** detalhes: descricao, endereco, email, data
**And** botoes: Ligar, WhatsApp, "Marcar como Contactado"

**Given** clica em WhatsApp
**When** lead tem telefone
**Then** abre wa.me/55... com saudacao personalizada

**Given** nao existem leads
**When** acessa secao
**Then** empty state "Nenhum lead recebido ainda"

**Given** clica em "Marcar como Contactado"
**When** confirma
**Then** PATCH /api/v1/leads/:id com status: contacted
**And** badge muda de "Novo" (azul) para "Contactado" (amarelo)

**Endpoint:** GET /api/v1/leads, PATCH /api/v1/leads/:id | **Tabela:** public_leads | **UX-DRs:** UX-DR13, UX-DR28 | **Depende de:** Story 6.2

---

<!-- Fim do Epic 6 -->

## Epic 7: 🏢 Administração da Plataforma

**Goal:** Equipe ServiceSaaS gerencia tenants, planos, transações e auditoria.

**FRs cobertos:** FR-ADM-01 a FR-ADM-10
**UX Surfaces:** Admin Dashboard, Admin Tenants, Admin Financeiro
**NFRs:** NFR-ADM-01 a NFR-ADM-05
**ARs:** — (Admin bypassa tenancy)
**Depende de:** Epic 1 (Auth), Epic 5 (Payments/MP)

---

### Story 7.1: 🔐 Admin Autenticação + Dashboard Global

**As a** Administrador ServiceSaaS,
**I want** fazer login separado com role super_admin e acessar dashboard global,
**So that** eu possa gerenciar toda a plataforma consolidada.

**Acceptance Criteria:**

**Given** admin acessa /admin/login.php
**When** informa credenciais com role super_admin
**Then** JWT gerado com is_admin: true, bypass do tenant.middleware
**And** redirecionado para admin dashboard

**Given** admin sem role super_admin
**When** tenta acessar rota /api/v1/admin/*
**Then** middleware adminAuth retorna 403

**Given** admin logado no dashboard global
**When** pagina carrega
**Then** exibe: total tenants ativos/suspensos, receita total do mes, transacoes recentes, alertas

**Given** tenants em estado de alerta (suspensos, com erros)
**When** dashboard carrega
**Then** cards de alerta destacados com contagem e cor de status

**FRs:** FR-ADM-01, FR-ADM-02 | **NFRs:** NFR-ADM-01, NFR-ADM-02, NFR-ADM-04, NFR-ADM-05

---

### Story 7.2: 🏢 Gestão de Tenants (CRUD + Suspensão)

**As a** Administrador,
**I want** listar, editar e suspender/reativar tenants,
**So that** eu possa gerenciar quem usa a plataforma.

**Acceptance Criteria:**

**Given** admin acessa pagina de tenants
**When** carrega
**Then** tabela com: nome, documento, plano, status, data cadastro, ultimo login
**And** busca por nome/documento, filtro por status (ativo/suspenso)

**Given** admin clica em editar tenant
**When** altera dados
**Then** PUT /api/v1/admin/tenants/:id atualiza o registro

**Given** admin clica em "Suspender"
**When** confirma no modal
**Then** tenant.status = suspended, usuario nao consegue fazer login
**And** admin_audit_log registra acao

**Given** admin clica em "Reativar"
**When** confirma
**Then** tenant.status = active, login restaurado

**Given** rota /admin/ isolada via Nginx
**When** acessa
**Then** location /admin/ configurado no default.conf

**FRs:** FR-ADM-03, FR-ADM-04, FR-ADM-05 | **NFRs:** NFR-ADM-02, NFR-ADM-03, NFR-ADM-05

---

### Story 7.3: 💰 Admin Financeiro + Planos

**As a** Administrador,
**I want** ver transacoes de todos os tenants, estornar pagamentos e gerenciar planos,
**So that** eu tenha controle financeiro total da plataforma.

**Acceptance Criteria:**

**Given** admin acessa financeiro
**When** carrega
**Then** tabela com transacoes de TODOS os tenants (sem filtro de tenant_id)
**And** colunas: tenant, proposta, valor, taxa, liquido, metodo, status, data

**Given** admin seleciona transacao "approved"
**When** clica em "Estornar"
**Then** POST /api/v1/admin/payments/:id/refund com override de tenant

**Given** admin acessa gerenciamento de planos
**When** carrega
**Then** CRUD de planos: nome, limite clientes, limite propostas, preco mensal, recursos

**Given** admin acessa relatorio financeiro
**When** seleciona periodo (mes/trimestre/ano)
**Then** exibe: receita total, receita por plano, novos tenants, taxa de conversao
**And** botao "Exportar CSV"

**FRs:** FR-ADM-06, FR-ADM-07, FR-ADM-08, FR-ADM-09 | **NFRs:** NFR-ADM-02

---

### Story 7.4: 📋 Auditoria de Ações Administrativas

**As a** Administrador,
**I want** visualizar o log de todas as acoes administrativas,
**So that** eu possa auditar quem fez o que na plataforma.

**Acceptance Criteria:**

**Given** admin acessa auditoria
**When** carrega
**Then** tabela com: timestamp, admin, acao, target (tenant/usuario), payload resumido, IP
**And** filtros por: admin, acao, tenant, periodo

**Given** qualquer acao administrativa executada (suspender, editar, estornar)
**When** concluida
**Then** admin_audit_log registra: admin_id, action, target_type, target_id, payload, ip, timestamp

**Given** registros com mais de 5 anos
**When** auditoria e consultada
**Then** registros antigos sao arquivados em S3 glacial

**FRs:** FR-ADM-10 | **NFRs:** NFR-ADM-03 (retencao 5 anos)

---

<!-- Novos Épicos — Compliance Doméstico (LC 150/2015) — Adicionado em 28/07/2026 -->

# 🏠 Épico 8: Workers e Cadastro de Trabalhadores Domésticos

**Dependência:** Nenhuma (paralelo com Épicos 5-6)
**Risco:** 🔴 Crítico — Sem workers, nenhuma operação CLT é possível
**Base Legal:** CBO 2026, LC 150/2015

## Story 8.1: Modelagem da Tabela Workers + CBO

**Description:** Como prestador (tenant), quero cadastrar trabalhadores domésticos com CPF, CBO e categoria LC 150 para começar a operar contratações.

**Acceptance Criteria:**
**Given** tenant acessa "Trabalhadores"
**When** cadastra novo trabalhador com CPF, nome, CBO, categoria (9 tipos)
**Then** worker é persistido com status active e background_check = PENDING
**And** CPF duplicado retorna erro 409 ERR_DUPLICATE_ENTRY
**And** categoria inválida retorna erro 422 ERR_VALIDATION

**FRs:** FR-WK-01 a FR-WK-05

## Story 8.2: Gestão de Certificações

**Description:** Como tenant, quero anexar e verificar certificações dos trabalhadores para liberar categorias restritas (cuidador, babá).

**Acceptance Criteria:**
**Given** tenant na página de detalhes do worker
**When** anexa certificado (PDF/imagem) com tipo, emissor, validade
**Then** certificação é salva com verified=FALSE
**And** cuidador de idosos sem certificação verified=TRUE não pode ser agendado

**FRs:** FR-WK-06 a FR-WK-10

## Story 8.3: Background Check Integrado

**Description:** Como plataforma, quero integrar com serviço de verificação de antecedentes para aprovar/rejeitar trabalhadores.

**Acceptance Criteria:**
**Given** worker com CPF informado
**When** tenant solicita background check
**Then** sistema envia dados para API de verificação externa
**And** status atualiza para APPROVED ou REJECTED
**And** worker REJECTED não pode ser agendado

**FRs:** FR-WK-11

---

# ⏱️ Épico 9: Trava de Frequência e Agendamento

**Dependência:** Épico 8 (workers)
**Risco:** 🔴 Crítico — Risco jurídico direto (descaracterização CLT)
**Base Legal:** LC 150/2015 Art. 1º

## Story 9.1: Algoritmo Trava-Frequência

**Description:** Como sistema, quero impedir o 3º agendamento avulso da mesma diarista no mesmo tomador na mesma semana para evitar descaracterização de trabalho autônomo.

**Acceptance Criteria:**
**Given** worker diarista já agendado 2x na semana para o mesmo tomador
**When** tentativa de 3º agendamento
**Then** sistema BLOQUEIA com erro ERR_FREQUENCY_LIMIT
**And** retorna transition_url para fluxo CLT
**And** alerta educativo sobre LC 150 é exibido

**FRs:** FR-FR-01, FR-FR-02

## Story 9.2: Fluxo de Transição Diarista → CLT

**Description:** Como contratante, quero converter uma diarista frequente em empregada doméstica registrada via eSocial.

**Acceptance Criteria:**
**Given** trava de frequência acionada
**When** contratante opta por "Contratar como CLT"
**Then** wizard de 3 passos: dados do contrato → calculadora de custos → confirmação
**And** ao confirmar, sistema inicia fluxo de admissão eSocial

**FRs:** FR-FR-03 a FR-FR-05

## Story 9.3: Calculadora de Custos Patronais

**Description:** Como contratante, quero simular custos mensais (salário + INSS + FGTS + VT) antes de contratar.

**Acceptance Criteria:**
**Given** contratante no wizard CLT
**When** informa salário proposto e jornada semanal
**Then** sistema calcula: INSS (8-12%), FGTS (8%), Gilrat (0.8%), VT (6% desconto)
**And** exibe custo total mensal e anual

**FRs:** FR-FR-06

---

# 🕐 Épico 10: Ponto Eletrônico e Jornada

**Dependência:** Épico 8 + 9
**Risco:** 🔴 Alto — Exigência legal Art. 12 LC 150

## Story 10.1: Registro de Ponto com Geolocalização

**Description:** Como trabalhador, quero registrar entrada e saída com GPS e foto para cumprir a LC 150.

**Acceptance Criteria:**
**Given** trabalhador chega ao local de trabalho
**When** registra clock-in no app
**Then** sistema salva: data/hora, coordenadas GPS, foto
**And** não permite segundo clock-in no mesmo dia sem clock-out
**Given** fim do expediente
**When** registra clock-out com foto
**Then** sistema calcula horas trabalhadas automaticamente

**FRs:** FR-TT-01 a FR-TT-05

## Story 10.2: Intervalo Intra-jornada

**Description:** Como trabalhador, quero registrar início e fim do intervalo de almoço/descanso.

**Acceptance Criteria:**
**Given** trabalhador em jornada ativa
**When** inicia intervalo
**Then** break_start é registrado
**And** break_end é registrado ao retornar
**And** intervalo inferior a 1h para jornadas >6h gera alerta

**FRs:** FR-TT-06

## Story 10.3: Engine de Cálculo Trabalhista

**Description:** Como sistema, quero calcular automaticamente horas extras, adicional noturno e saldo do dia.

**Acceptance Criteria:**
**Given** clock-out registrado
**When** sistema processa o período
**Then** calcula: horas regulares, horas extras (≥8h/dia ou ≥44h/sem), adicional noturno (22h-5h, +20%)
**And** suporta escala 12×36 (12h trabalho, 36h descanso)
**And** exibe espelho de ponto para conferência

**FRs:** FR-TT-07 a FR-TT-10

## Story 10.4: Notificação de Inconsistência

**Description:** Como contratante, quero ser notificado se houver discrepância no ponto do dia.

**Acceptance Criteria:**
**Given** ponto fechado com horas extras ou inconsistência
**When** sistema detecta
**Then** push notification enviada ao contratante para aprovar ajuste
**And** worker pode incluir justificativa

**FRs:** FR-TT-11

---

# 📋 Épico 11: Integração eSocial Doméstico

**Dependência:** Épico 8 + 10
**Risco:** 🔴 Alto — Passivo tributário
**Infra:** Redis + BullMQ (job queue)

## Story 11.1: Admissão via eSocial

**Description:** Como sistema, quero automatizar a admissão do trabalhador no eSocial Doméstico.

**Acceptance Criteria:**
**Given** contrato CLT assinado digitalmente
**When** sistema envia dados ao eSocial
**Then** retorna 202 Accepted com job_id
**And** job queue processa em background
**And** ao finalizar, esocial_integration.status = SYNCED
**And** contratante recebe notificação

**FRs:** FR-ES-01, FR-ES-02

## Story 11.2: Geração Mensal de DAE

**Description:** Como contratante, quero receber a guia DAE mensal calculada automaticamente.

**Acceptance Criteria:**
**Given** mês competência fechado
**When** sistema gera DAE
**Then** calcula: INSS + FGTS + Gilrat
**And** disponibiliza guia para pagamento via PIX/boleto
**And** registra vencimento e status de pagamento

**FRs:** FR-ES-03 a FR-ES-05

## Story 11.3: Dashboard de Compliance Trabalhista

**Description:** Como contratante, quero acompanhar a situação trabalhista de cada empregado.

**Acceptance Criteria:**
**Given** contratante no dashboard eSocial
**When** carrega página
**Then** exibe: status eSocial, DAE do mês (pago/pendente), férias, 13º, aviso prévio
**And** alerta de DAE atrasado

**FRs:** FR-ES-06

---

# 🚨 Épico 12: Incidentes, Seguro e Emergência

**Dependência:** Épico 10
**Risco:** 🟡 Médio — Responsabilidade civil

## Story 12.1: Reporte de Incidentes

**Description:** Como trabalhador ou contratante, quero reportar acidentes ou incidentes durante a prestação de serviço.

**Acceptance Criteria:**
**Given** incidente ocorrido
**When** usuário reporta via app
**Then** sistema registra: tipo (acidente, emergência, dano), severidade, descrição, geolocalização
**And** incidentes CRITICAL acionam notificação imediata ao tenant e admin
**And** incidente recebe número de protocolo único

**FRs:** FR-IN-01, FR-IN-02

## Story 12.2: Botão de Emergência SOS

**Description:** Como trabalhador, quero um botão de pânico que envia minha localização para contatos de emergência.

**Acceptance Criteria:**
**Given** trabalhador em situação de emergência
**When** aciona SOS
**Then** sistema envia SMS/whatsapp com geolocalização para 3 contatos pré-cadastrados
**And** registra incidente CRITICAL automaticamente

**FRs:** FR-IN-03

## Story 12.3: Emissão de CAT

**Description:** Como contratante, quero emitir a Comunicação de Acidente de Trabalho (CAT) diretamente pela plataforma.

**Acceptance Criteria:**
**Given** acidente de trabalho reportado
**When** contratante emite CAT
**Then** sistema gera CAT digital e envia ao INSS
**And** registra número CAT no incidente

**FRs:** FR-IN-04

---

# 🔐 Épico 13: LGPD Completo — Portabilidade e Eliminação

**Dependência:** Nenhuma (paralelo com Épicos 5-6)
**Risco:** 🟡 Médio — Multa de até 2% do faturamento

## Story 13.1: Portabilidade de Dados

**Description:** Como usuário, quero exportar todos os meus dados pessoais da plataforma.

**Acceptance Criteria:**
**Given** usuário solicita exportação
**When** confirma identidade
**Then** sistema gera arquivo JSON + CSV com todos os dados pessoais
**And** disponibiliza para download por 7 dias
**And** registra operação no audit_log

**FRs:** FR-LG-01

## Story 13.2: Eliminação de Dados (Direito ao Esquecimento)

**Description:** Como usuário, quero solicitar a eliminação dos meus dados pessoais da plataforma.

**Acceptance Criteria:**
**Given** usuário solicita eliminação
**When** confirma identidade e consentimento
**Then** sistema anonimiza dados pessoais (mantém registros operacionais anonimizados)
**And** remove consentimentos LGPD
**And** envia confirmação por e-mail
**And** conclui em até 15 dias úteis

**FRs:** FR-LG-02

## Story 13.3: Revogação de Consentimento

**Description:** Como usuário, quero revogar consentimentos LGPD anteriormente concedidos.

**Acceptance Criteria:**
**Given** usuário com consentimento ativo
**When** revoga consentimento de marketing
**Then** lgpd_consent registra revoked_at
**And** sistema para de enviar comunicações de marketing
**And** consentimento de termos não pode ser revogado (dados operacionais)

**FRs:** FR-LG-03

---

# 📍 Épico 14: Perfil do Prestador e Busca por Proximidade

**Dependência:** Nenhuma (paralelo com Épicos 1-6)
**Risco:** 🟢 Baixo — Sem dependências externas
**Base Legal:** N/A — Requisito de negócio

## Story 14.1: Endereço no Cadastro do Prestador

**Description:** Como novo prestador (tenant), quero informar meu endereço (cidade/estado) durante o cadastro para que clientes possam me encontrar por proximidade.

**Acceptance Criteria:**
**Given** visitante no formulário de cadastro
**When** preenche dados da empresa
**Then** campos de CEP, endereço, bairro, cidade e estado estão disponíveis
**And** cidade e estado são obrigatórios
**And** endereço é persistido na tabela tenants
**And** prestadores sem endereço aparecem em buscas sem filtro de localidade

**FRs:** FR-PR-01, FR-PR-02

## Story 14.2: Perfil do Prestador (Editar Endereço)

**Description:** Como prestador logado, quero acessar uma página "Meu Perfil" para visualizar e editar meus dados cadastrais, incluindo endereço completo.

**Acceptance Criteria:**
**Given** prestador autenticado
**When** acessa "Meu Perfil" no menu lateral
**Then** exibe formulário com nome, CPF/CNPJ (readonly), telefone, whatsapp, endereço completo
**And** pode salvar alterações em todos os campos editáveis
**And** sidecar exibe: plano, data de cadastro, status

**FRs:** FR-PR-03

## Story 14.3: Busca Pública por Município

**Description:** Como cliente visitando a landing page, quero filtrar serviços por cidade para encontrar prestadores próximos à minha localidade.

**Acceptance Criteria:**
**Given** cliente na landing page
**When** realiza busca ou navega por categorias
**Then** resultados exibem a cidade/estado do prestador
**And** pode filtrar por cidade via query param
**And** busca sem cidade retorna todos os resultados (limitados a 50)

**FRs:** FR-PR-04

---

<!-- Fim do documento — 14 épicos (E1-E14) atualizado em 28/07/2026 -->
