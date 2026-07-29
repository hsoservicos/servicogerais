# 📚 ServiceSaaS — Índice da Documentação

**Projeto:** ServiceSaaS (Serviços Flex)
**Stack:** PHP 8.2 · Node.js 20 · MySQL 8.0 · Docker Compose
**Última atualização:** 29 de Julho de 2026
**Documentado por:** Auditoria V2

---

## Visão Rápida

```
🎯 Plataforma SaaS multi-tenant para prestadores de serviços
👥 Público-alvo: MEI e pequenos prestadores
🏗️ Arquitetura: Frontend PHP + API Node.js + MySQL
🐳 Infraestrutura: 5 containers Docker
📄 86 endpoints API | 18 tabelas | 30 templates PHP | 17 épicos
```

---

## 📋 Planos de Desenvolvimento (ATUALIZADO 29/07)

| Documento | Descrição |
|:---|---|
| [📄 PLANEJAMENTO_V2.md](../../docs/planning/PLANEJAMENTO_V2.md) | **NOVO — Plano estratégico pós-auditoria (fonte da verdade)** |
| [📄 EPICOS_V2.md](../../docs/planning/EPICOS_V2.md) | **NOVO — 17 épicos, 64 stories detalhadas** |
| [📄 SPRINT_PLAN_V2.md](../../docs/planning/SPRINT_PLAN_V2.md) | **NOVO — 8 sprints priorizados** |
| [📄 PLANEJAMENTO_MODERNO_PROJETO.md](../../docs/planning/PLANEJAMENTO_MODERNO_PROJETO.md) | 📦 Arquivado — substituído pelo V2 |

---

## Documentos do Projeto

### 📋 Visão Geral

| Documento | Descrição |
|:---|---|
| [📄 project-overview.md](project-overview.md) | Identidade, stack, personas, objetivos |
| [📄 source-tree-analysis.md](source-tree-analysis.md) | Árvore completa com anotações por diretório |
| [📄 architecture.md](architecture.md) | Decisões arquiteturais, módulos, middlewares, rotas |
| [📄 development-guide.md](development-guide.md) | Setup, Docker, variáveis de ambiente, comandos |

### 📊 Artefatos de Planejamento

| Documento | Descrição |
|:---|---|
| [📊 epics.md](epics.md) | Versão anterior (épicos E1-E14 apenas) |
| [🔬 Pesquisa Técnica MP](research/technical-mercado-pago-integration-research.md) | Integração Mercado Pago |
| [🔍 Pesquisa Regulatória](research/domain-regulamentacao-profissionais-servicos-brasil-research.md) | MEI, LGPD, NFSe |
| [📋 Project Scan](research/project-scan-servicos-20260728.md) | Scan completo do código-fonte |

### 🏗️ Arquitetura e Design

| Documento | Descrição |
|:---|---|
| [🏛️ Architecture Spine](architecture/architecture-servicos-20260727/ARCHITECTURE-SPINE.md) | Decisões arquiteturais formais |
| [🎨 UX Design](ux-designs/ux-servicos-20260727/DESIGN.md) | Design tokens, cores, tipografia, componentes |
| [🎭 UX Experience](ux-designs/ux-servicos-20260727/EXPERIENCE.md) | Flows de usuário, superfícies, voice & tone |
| [✅ Validation PRDs](validation-report-prds.md) | Relatório de validação dos PRDs |

### 📐 Layouts HTML

| Layout | Descrição |
|:---|---|
| [🏠 Landing Page](../../layout/stitch_saas_project_architect/landing_page_servi_os_flex/code.html) | Página inicial |
| [📊 Dashboard](../../layout/stitch_saas_project_architect/dashboard_in_cio/code.html) | Painel do prestador |
| [📝 Nova Proposta](../../layout/stitch_saas_project_architect/nova_proposta/code.html) | Formulário de proposta |
| [🏢 Admin](../../layout/stitch_saas_project_architect/admin_platform/code.html) | Painel administrativo |
| [📋 Solicitar Serviço](../../layout/stitch_saas_project_architect/solicitar_servi_o_clientes/code.html) | Formulário público |

### 📜 Compliance LGPD

| Documento | Descrição |
|:---|---|
| Termos de Uso + DPA | [`docs/lgpd/termos-de-uso.html`](../../docs/lgpd/termos-de-uso.html) |
| Política de Privacidade | [`docs/lgpd/politica-privacidade.md`](../../docs/lgpd/politica-privacidade.md) |
| Registro de Operações | [`docs/lgpd/registro-operacoes.md`](../../docs/lgpd/registro-operacoes.md) |
| Política de Retenção | [`docs/lgpd/politica-retencao.md`](../../docs/lgpd/politica-retencao.md) |
| Plano de Incidentes | [`docs/lgpd/plano-resposta-incidentes.md`](../../docs/lgpd/plano-resposta-incidentes.md) |
| Checklist Privacy by Design | [`docs/lgpd/checklist-privacy-by-design.md`](../../docs/lgpd/checklist-privacy-by-design.md) |

---

## Stack Tecnológica

| Camada | Tecnologia | Versão |
|:---|---|:---:|
| Frontend | PHP + HTML5/Tailwind CSS | 8.2 |
| API REST | Node.js + Express.js | 20 LTS |
| Database | MySQL | 8.0 |
| Proxy | Nginx | 1.25-alpine |
| Container | Docker Compose | 3.8+ |

---

## Guia Rápido

```bash
# Subir ambiente
make setup

# Popular dados de exemplo
make seed

# Logs
docker compose logs -f api

# Acessar
open http://localhost:8080
```

---

## Mapa de Épicos V2

```
✅ E1   🔐  Autenticação & Onboarding              [5 stories  COMPLETO]
✅ E2   👥  Clientes & Catálogo                    [3 stories  COMPLETO]
✅ E3   📄  Ciclo de Vida da Proposta              [6 stories  COMPLETO]
✅ E4   📊  Dashboard & Métricas                    [4 stories  COMPLETO]
✅ E5   💳  Pagamentos Mercado Pago                 [5 stories  COMPLETO]
✅ E6   🌐  Presença Pública & Leads                [4 stories  COMPLETO]
✅ E7   🏢  Administração da Plataforma             [4 stories  COMPLETO]
✅ E8   🏠  Workers & Certificações                 [3 stories  COMPLETO]
✅ E9   ⏱️  Trava Frequência & Agendamento           [3 stories  COMPLETO]
📝 E10  🕐  Ponto Eletrônico & Jornada              [4 stories  NOVO]
📝 E11  📋  eSocial Doméstico                       [3 stories  NOVO]
📝 E12  🚨  Incidentes & Emergência                 [3 stories  NOVO]
🔶 E13  🔐  LGPD & Privacidade                      [3 stories  PARCIAL]
✅ E14  📍  Perfil & Proximidade                    [3 stories  COMPLETO]
📝 E15  🧪  Pipeline de Qualidade & Testes          [4 stories  PRIORIDADE #1]
📝 E16  🏗️  Refatoração Técnica                     [3 stories  MÉDIO]
📝 E17  🛡️  Hardening de Segurança                  [4 stories  ALTA]
```

---

*Documentação gerada em 29 de Julho de 2026 — Pós Auditoria V2*
