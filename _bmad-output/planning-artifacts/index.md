# 📚 ServiceSaaS — Índice da Documentação

**Projeto:** ServiceSaaS (Serviços Flex)
**Stack:** PHP 8.2 · Node.js 20 · MySQL 8.0 · Docker Compose
**Última atualização:** 28 de Julho de 2026
**Documentado por:** Paige (Technical Writer)

---

## Visão Rápida

```
🎯 Plataforma SaaS multi-tenant para prestadores de serviços
👥 Público-alvo: MEI e pequenos prestadores (Maria, a cabeleireira)
🏗️ Arquitetura: Frontend PHP + API Node.js + MySQL
🐳 Infraestrutura: 5 containers Docker
📄 70+ arquivos de código | 7 épicos | 22 stories
```

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
| [📊 epics.md](epics.md) | 7 épicos, 22 stories detalhadas |
| [🔬 Pesquisa Técnica MP](research/technical-mercado-pago-integration-research.md) | Integração Mercado Pago — SDK, webhooks, Pix, estornos |
| [🔍 Pesquisa Regulatória](research/domain-regulamentacao-profissionais-servicos-brasil-research.md) | MEI, LGPD, NFSe — regulamentação brasileira |
| [📋 Project Scan](research/project-scan-servicos-20260728.md) | Scan completo do código-fonte (Mary) |

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
| CDN/Security | Cloudflare Tunnel | Planejado |
| Pagamentos | Mercado Pago SDK | 2.x (planejado) |

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

## Mapa de Épicos

```
Epic 1  🔐  Onboarding & Autenticação     [5 stories  ✅]
Epic 2  👥  Gestão de Clientes e Catálogo  [pendente   📝]
Epic 3  📄  Ciclo de Vida da Proposta      [pendente   📝]
Epic 4  📊  Dashboard e Métricas           [4 stories  ✅]
Epic 5  💳  Pagamentos Mercado Pago        [5 stories  ✅]
Epic 6  🌐  Presença Pública e Leads       [4 stories  ✅]
Epic 7  🏢  Administração da Plataforma    [4 stories  ✅]
Epic 8  🏠  Workers e Cadastro Doméstico   [3 stories  📝 NOVO]
Epic 9  ⏱️  Trava de Frequência            [3 stories  📝 NOVO]
Epic 10 🕐  Ponto Eletrônico e Jornada     [4 stories  📝 NOVO]
Epic 11 📋  Integração eSocial Doméstico   [3 stories  📝 NOVO]
Epic 12 🚨  Incidentes, Seguro e Emergência[3 stories  📝 NOVO]
Epic 13 🔐  LGPD Completo                  [3 stories  📝 NOVO]
```

---

*Documentação gerada por Paige (Technical Writer) em 28 de Julho de 2026*
