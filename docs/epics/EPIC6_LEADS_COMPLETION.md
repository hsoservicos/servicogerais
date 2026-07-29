# Epic 6: Presença Pública e Captura de Leads — Relatório de Compleção

**Data:** 2026-07-29 | **Status:** ✅ Completo (98%) | **Versão:** 1.0

## Stories Cobertas

| Story | FR | Status | Artefatos |
|-------|----|--------|-----------|
| 6.1 Landing Page + Busca | — | ✅ Completo | `public.controller.js`, `index.php` |
| 6.2 Wizard de Solicitação (4 passos) | — | ✅ Completo | `solicitar.php` (1087 linhas) |
| 6.3 Página Pública de Proposta | FR-034 | ✅ Completo | `publicProposals.controller.js`, `public-proposal.php` |
| 6.4 Gestão de Leads (Prestador) | — | ✅ Completo | `leads.controller.js`, `leads.php` (467 linhas) |

## Arquitetura

### Backend — Módulo Public (`api-backend/modules/public/`)

| Arquivo | Linhas | Função |
|---------|--------|--------|
| `public.routes.js` | 81 | 11 rotas públicas (sem autenticação) |
| `public.controller.js` | 277 | 4 funções: categorias, serviços, criar lead, categorias worker |
| `publicProposals.controller.js` | 443 | 5 funções: ver proposta, aprovar/rejeitar, status pagamento, criar preferência MP, download PDF |
| `upload.controller.js` | 123 | Upload de fotos (Multer, UUID, 5MB, JPEG/PNG/WebP/GIF) |

**Rotas Públicas:**

| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/api/v1/public/categories` | Categorias de serviço |
| GET | `/api/v1/public/worker-categories` | Categorias de trabalhador doméstico |
| GET | `/api/v1/public/services` | Busca de serviços (query: search, category, city) |
| POST | `/api/v1/public/leads` | Criar lead (rate limit: 5/min/IP) |
| POST | `/api/v1/public/upload` | Upload de fotos (rate limit: 10/min/IP) |
| GET | `/api/v1/public/proposals/:token` | Ver proposta pública |
| GET | `/api/v1/public/proposals/:token/pdf` | Download PDF |
| GET | `/api/v1/public/proposals/:token/payment` | Status pagamento |
| POST | `/api/v1/public/proposals/:token/pay` | Criar preferência MP |
| PATCH | `/api/v1/public/proposals/:token/status` | Aprovar/rejeitar |
| GET | `/api/v1/public/uploads/:filename` | Servir arquivos (proteção directory traversal) |

### Backend — Módulo Leads (`api-backend/modules/leads/`)

| Arquivo | Linhas | Função |
|---------|--------|--------|
| `leads.routes.js` | 20 | 2 rotas autenticadas: `GET /`, `PATCH /:id` |
| `leads.controller.js` | 151 | `list()` + `updateStatus()` — sem service layer (padrão do projeto) |

### Frontend

**`web-frontend/templates/solicitar.php`** (1087 linhas)
- Wizard 4 passos: Serviço → Detalhes → Dados → Confirmação
- Busca com autocomplete por serviço
- Upload de fotos com preview drag-and-drop
- CEP autocomplete (ViaCEP)
- LGPD consent dual checkbox
- Validação inline em cada passo

**`web-frontend/templates/leads.php`** (467 linhas)
- Filtros por status (novo/contactado/convertido/arquivado)
- Busca por nome/telefone/serviço
- Botão WhatsApp direto
- Modal de detalhes com fotos
- Transições de status: PATCH `/api/v1/leads/:id`
- Paginação

## Banco de Dados

Tabela `public_leads` em `scripts/init.sql`:
- 20 colunas: id, tenant_id, service_name, description, desired_date/time, address fields, photo_urls (JSON), customer data, LGPD consents, status, notes, timestamps
- Índices em: status, tenant_id, created_at
- FK opcional para tenants (ON DELETE SET NULL)

## Gaps Encontrados

| Gap | Impacto | Ação |
|-----|---------|------|
| ❌ Página `termos-de-uso` não existe (link quebrado no wizard) | Médio — LGPD Art. 9 | Criar página de termos |
| 🟡 `leads.service.js` ausente | Baixo — controller faz query direta | Criar service layer (padrão não seguido) |
| 🟡 Sem fluxo de atribuição de lead a tenant | Baixo — leads são cross-tenant sem match | Adicionar em v2.0 |

## Testes Realizados

- Auditoria de código completa em 29/07/2026
- Verificação de todas as rotas registradas
- Verificação do wizard solicitar.php (4 passos)
- Verificação do painel de leads (CRUD status)
- Consulta de endpoints públicos com curl
