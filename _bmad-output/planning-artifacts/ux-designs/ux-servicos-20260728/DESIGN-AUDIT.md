---
name: 'ServiceSaaS — Design Audit & Redesign'
status: draft
date: 2026-07-28
inspected: Ocean DS (zeroheight), Tailwind CSS, current codebase
scope: 'Full UI redesign — colors, components, layout, consistency, dark mode, responsiveness'
---

# ServiceSaaS — Design Audit & Redesign Specification

> Auditoria completa do frontend (~22 templates PHP, Tailwind CDN, sem CSS build step) com base nas melhores práticas de Design Systems modernos e referência Ocean DS. Gera artefato vinculante para refatoração visual de todo o projeto.

---

## Tabela de Conteúdo

1. [Metodologia](#1-metodologia)
2. [Resumo Executivo](#2-resumo-executivo)
3. [Análise por Camada](#3-análise-por-camada)
   - 3.1. Tokens de Cor
   - 3.2. Tipografia
   - 3.3. Espaçamento e Grid
   - 3.4. Elevação e Sombras
   - 3.5. Cantos e Border Radius
   - 3.6. Ícones
   - 3.7. Motion
4. [Auditoria de Componentes](#4-auditoria-de-componentes)
   - 4.1. Botões
   - 4.2. Inputs e Formulários
   - 4.3. Cards
   - 4.4. Tabelas
   - 4.5. Modais
   - 4.6. Badges e Status
   - 4.7. Sidebar e Navegação
   - 4.8. Topbar
   - 4.9. Esqueletos e Loading
   - 4.10. Toasts e Feedback
   - 4.11. Empty States
   - 4.12. Wizard
5. [Auditoria de Experiência](#5-auditoria-de-experiência)
   - 5.1. Consistência Cross-Página
   - 5.2. Responsividade e Mobile
   - 5.3. Acessibilidade
   - 5.4. Performance Percebida
   - 5.5. Tratamento de Erros
6. [Ocean DS — Análise e Inspiração](#6-ocean-ds--análise-e-inspiração)
7. [Plano de Redesign por Prioridade](#7-plano-de-redesign-por-prioridade)
8. [Tokens Atualizados — Design System v2.0](#8-tokens-atualizados--design-system-v20)
9. [Especificação de Implementação](#9-especificação-de-implementação)
10. [Checklist de Migração](#10-checklist-de-migração)

---

## 1. Metodologia

A auditoria foi conduzida em 3 etapas:

| Etapa | Método | Cobertura |
|-------|--------|-----------|
| **Estático** | Leitura de todos os 22 templates PHP + inline Tailwind config + CSS | 100% dos arquivos de frontend |
| **Visual** | Análise de padrões de classe, repetição de tokens, inconsistências | 22 templates, ~15k linhas |
| **Funcional** | Execução dos 5 containers Docker, verificação de renderização real | Nginx + PHP + API + MySQL |

Cada componente foi classificado em 3 níveis:

- ✅ **Conforme** — Segue o design system estabelecido
- ⚠️ **Inconsistente** — Desvia parcialmente, precisa alinhamento
- ❌ **Crítico** — Viola o design system, precisa correção imediata

---

## 2. Resumo Executivo

### Pontos Fortes

- Paleta de cor consistente (primária `#10B981` em 98% dos componentes)
- Sidebar escura uniforme em user e admin
- Sistema de badges semânticos bem implementado
- Animações suaves e propositais (fade-in, slide-in)
- Toast global com 4 variantes de feedback
- Componentes de KPI cards com hover state e transições

### Problemas Encontrados

| Severidade | Quantidade | Exemplos |
|-----------|-----------|----------|
| ❌ Crítico | 4 | 2 configs Tailwind conflitantes; Admin login não usa `admin-login.css`; landing page não carrega via Docker; sem suporte a dark mode |
| ⚠️ Inconsistente | 9 | BorderRadius variando (6px vs 8px vs 12px); botões secundários com padding desalinhado; modais com largura fixa vs percentual; wizard com `@apply` inline que não é processado |
| ✅ Conforme | 18 | KPI cards, badges, sidebar, tabelas, toasts |

### Recomendação Principal

**Unificar o Tailwind config em um único arquivo** e eliminar as configurações duplicadas. Adotar as extensões Ocean DS para tokens de elevation, foco e loading. Implementar dark mode progressivo.

---

## 3. Análise por Camada

### 3.1. Tokens de Cor

#### Estado Atual

Definidos inline em `header.php` via `tailwind.config` + fallback no arquivo `tailwind.config.js`. Ambas as definições existem e divergem.

| Token | header.php (runtime) | tailwind.config.js (standalone) | Gap |
|-------|---------------------|-------------------------------|-----|
| `primary` | `#10B981` | `#10B981` | ✅ |
| `primary-700` | `#006c49` | Não definido | ⚠️ |
| `border` | `#E2E8F0` | `#e5e7eb` (gray-200) | ⚠️ |
| `sidebar` | `#0F172A` | Não definido | ⚠️ |
| `surface` | `#F8FAFC` | Não definido | ⚠️ |
| `ink` / `ink-secondary` / `ink-muted` | Sim | Não | ⚠️ |
| BorderRadius `DEFAULT` | 6px (indireto) | 8px | ❌ |
| `shadow-card` / `shadow-modal` | Sim | Não | ⚠️ |

#### Diagnóstico

O sistema de tokens está correto, mas:
1. **Duas fontes da verdade** — `header.php` é o runtime, mas o arquivo `tailwind.config.js` pode causar confusão
2. **Ocean DS recomenda** prefixar tokens semânticos (ex: `color-surface-base`, `color-surface-raised`, `color-ink-primary`) em vez de nomes curtos
3. **Falta token de foco** (`ring-primary`) — atualmente usado como `focus:ring-2 focus:ring-primary/30` sem token dedicado
4. **Cores de proposta** (draft=sent=viewed=accepted=rejected=cancelled) estão hardcoded em JS, não no config

#### Ação de Redesign

| Ação | Prioridade | Esforço |
|------|-----------|---------|
| Unificar config: manter `header.php` como única fonte, remover `tailwind.config.js` | P0 | 15min |
| Adicionar tokens de foco: `focus-ring`, `focus-ring-offset` | P1 | 10min |
| Mover cores de badge de proposta para o config como extensão | P2 | 20min |
| Renomear tokens para prefixo semântico (Ocean DS style) apenas se houver refatoração maior | P3 | 2h |

### 3.2. Tipografia

#### Estado Atual

- **Fonte:** Poppins (Google Fonts, 4 pesos: 300/400/500/600/700)
- **Mono:** JetBrains Mono (carregado, mas usado esporadicamente)
- **Custom tokens:** `text-display` (30px/800), `text-h1` (30px/600), `text-h2` (24px/600), `text-h3` (20px/600)

#### Problemas

1. `text-h1` e `text-display` têm o **mesmo fontSize** (30px) mas pesos diferentes — confuso semanticamente
2. JetBrains Mono é carregado do Google Fonts mas tem baixa adoção
3. `text-h3` (20px/600) conflita com o h3 nativo do Tailwind
4. Sem tokens para `font-weight-medium` vs `font-weight-semibold` nos botões

#### Padrão Ocean DS

Ocean DS tipicamente define uma escala tipográfica com 7 níveis (xs, sm, base, lg, xl, 2xl, 3xl, 4xl) com pesos de 400 a 700, todos alinhados a uma grade de 8px. Recomenda-se:

| Nível | Tamanho | Peso | Line Height | Uso |
|-------|---------|------|-------------|-----|
| `display` | 2.5rem (40px) | 700 | 1.2 | Hero — landing page only |
| `h1` | 1.75rem (28px) | 700 | 1.3 | Títulos de página (dashboard, admin) |
| `h2` | 1.5rem (24px) | 600 | 1.4 | Títulos de cards, KPIs |
| `h3` | 1.25rem (20px) | 600 | 1.5 | Subtítulos de seção, modais |
| `body` | 1rem (16px) | 400 | 1.6 | Texto corrido |
| `sm` | 0.875rem (14px) | 400 | 1.5 | Tabelas, labels, inputs |
| `xs` | 0.75rem (12px) | 500 | 1.4 | Badges, metadados, timestamps |

#### Ação de Redesign

| Ação | Prioridade | Esforço |
|------|-----------|---------|
| Diferenciar `text-display` (40px) de `text-h1` (28px) | P1 | 5min |
| Garantir que `text-h3` não conflite com default do Tailwind | P1 | 5min |
| Adicionar `text-xs` explícito com peso 500 + tracking-wider para badges | P2 | 5min |
| Expandir uso de JetBrains Mono para IDs, propostas, transações | P2 | 15min |

### 3.3. Espaçamento e Grid

#### Estado Atual

Grid modular de 8px implementado via Tailwind spacing. Valores: 1(4px), 2(8px), 3(12px), 4(16px), 5(20px), 6(24px), 8(32px), 10(40px), 12(48px).

#### Problemas

1. **Sidebar width**: 260px no DESIGN.md, 256px (`w-64`) na implementação — diferença de 4px
2. **Topbar height**: 64px (`h-16`) — consistente
3. **Cards padding**: `p-5` (20px) na especificação vs `p-6` (24px) em alguns templates
4. **Grid gaps**: `gap-6` (24px) na maioria, mas `gap-4` (16px) em alguns formulários

#### Ação de Redesign

| Ação | Prioridade | Esforço |
|------|-----------|---------|
| Definir sidebar width como `w-64` (256px) oficialmente e atualizar DESIGN.md | P1 | 2min |
| Padronizar card padding para `p-6` (24px) em toda a base | P1 | 30min (busca + replace) |
| Padronizar grid gaps: `gap-6` para landing/dashboard, `gap-4` para forms | P2 | 20min |

### 3.4. Elevação e Sombras

#### Estado Atual

| Token | Definição | Uso |
|-------|-----------|-----|
| `shadow-card` | `0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1)` | Cards, KPI cards |
| `shadow-modal` | `0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1)` | Modais |
| `shadow-panel` | `0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1)` | Toasts, dropdowns |

#### Diagnóstico

Ocean DS recomenda uma escala de 5 níveis de elevação, cada um com shadow + z-index associado:

| Nível | Ocean DS Equivalente | Uso |
|-------|---------------------|-----|
| `elevation-1` | shadow-sm + z-10 | Cards, inputs |
| `elevation-2` | shadow-card + z-20 | Sidebar, sticky headers |
| `elevation-3` | shadow-panel + z-30 | Dropdowns, popovers |
| `elevation-4` | shadow-modal + z-40 | Modais, dialogs |
| `elevation-5` | shadow-lg + z-50 | Toasts, notificações |

#### Ação de Redesign

| Ação | Prioridade | Esforço |
|------|-----------|---------|
| Adicionar escala de elevação com 5 níveis + z-index no config | P2 | 10min |
| Mapear sombras existentes para nova escala (sem quebrar) | P2 | 15min |

### 3.5. Cantos e Border Radius

#### Estado Atual

BorderRadius padrão `rounded-md` = 8px (no runtime). Variações: `rounded-sm` (4px), `rounded-lg` (12px), `rounded-xl` (16px), `rounded-2xl` (20px), `rounded-full` (9999px).

**Problema:** Uso inconsistente de `rounded-lg` vs `rounded-xl` em cards. Alguns cards KPI usam `rounded-xl`, outros `rounded-lg`.

#### Ação de Redesign

| Ação | Prioridade | Esforço |
|------|-----------|---------|
| Unificar cards → `rounded-xl` (16px) | P1 | 15min |
| Unificar inputs → `rounded-lg` (12px) | P1 | 10min |
| Unificar botões → `rounded-lg` (12px) | P1 | 10min |
| Badges → `rounded` (8px) | P2 | 5min |

### 3.6. Ícones

#### Estado Atual

**Nenhuma biblioteca de ícones externa.** Todos os ícones são inline SVGs do Heroicons (formato `path` dentro de `<svg>`). Cerca de 40 ícones únicos distribuídos nos 22 templates.

#### Problemas

1. SVGs inline causam repetição de código (~2KB por ícone em média)
2. Sem componente de ícone reutilizável (cada template repete o SVG completo)
3. Ocean DS recomenda uma camada de abstração de ícone (componente ou helper)
4. Alguns ícones têm tamanhos inconsistentes: `w-4 h-4`, `w-5 h-5`, `w-6 h-6`

#### Ação de Redesign

| Ação | Prioridade | Esforço |
|------|-----------|---------|
| Criar helper PHP `icon($name, $size, $class)` que renderiza SVG inline (reduz duplicação) | P1 | 30min |
| Padronizar tamanhos: sidebar → 5, ações → 4, avatares → 6 | P2 | 20min |
| Documentar ícones disponíveis em DESIGN.md | P2 | 15min |

### 3.7. Motion

#### Estado Atual

Animações definidas em `header.php`:
- `animate-fade-in` (0.3s ease-out, translateY 8px)
- `animate-slide-in` (0.3s ease-out, translateX -16px)
- `animate-pulse-dot` (1.5s, opacidade)
- `hover:-translate-y-0.5` em cards
- `active:scale-95` em botões

#### Problemas

1. `hover:scale-110` em cards KPI (group-hover) compete com `hover:-translate-y-0.5` — ambos no mesmo componente
2. Sem `prefers-reduced-motion` — usuários com sensibilidade visual não têm opção
3. Durações não tokenizadas (`duration-200` vs `duration-300` vs `duration-500`)

#### Ação de Redesign

| Ação | Prioridade | Esforço |
|------|-----------|---------|
| Adicionar `@media (prefers-reduced-motion: reduce) { * { animation-duration: 0.01ms !important; } }` | P1 | 5min |
| Definir tokens de duração: `fast: 150ms`, `normal: 300ms`, `slow: 500ms` | P2 | 5min |
| Remover `hover:scale-110` de KPI cards (reter apenas translateY) | P1 | 10min |

---

## 4. Auditoria de Componentes

### 4.1. Botões

| Aspecto | Estado | Veredito |
|---------|--------|----------|
| Primário | `bg-primary text-white px-4 py-2.5 rounded-lg font-medium hover:bg-primary-600 active:scale-95` | ✅ |
| Secundário | `border border-border text-ink-secondary px-4 py-2.5 rounded-lg font-medium hover:bg-surface` | ⚠️ — Alguns usam `border-2`, outros `border` |
| WhatsApp | `bg-whatsapp text-white px-4 py-2.5 rounded-lg hover:brightness-110` | ✅ |
| Danger/Delete | Inconsistente — alguns usam `bg-danger`, outros `border border-danger text-danger` | ⚠️ |
| Ícone only | `w-8 h-8 rounded-lg flex items-center justify-center` | ✅ |
| Desabilitado | `disabled:opacity-50 disabled:cursor-not-allowed` | ✅ |
| Loading | Spinner inline com `mr-2` | ✅ |

### 4.2. Inputs e Formulários

| Aspecto | Estado | Veredito |
|---------|--------|----------|
| Text input | `w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:ring-2 focus:ring-primary/30 focus:border-primary` | ⚠️ — `bg-surface` vs `bg-white` varia entre templates |
| Select | Mesmo padrão com `appearance-none` em alguns casos | ⚠️ — Falta `appearance-none` consistente |
| Textarea | Mesmo padrão dos inputs, `resize-none` ou `resize-vertical` | ⚠️ |
| Checkbox | `w-4 h-4 rounded border-border text-primary focus:ring-primary` | ✅ |
| Date | Nativo do browser (sem customização) | ⚠️ — Ocean DS recomenda date input estilizado |
| Error state | Input com classe `border-danger` + `<span class="text-danger text-xs">` | ✅ |
| Label | `block text-sm font-medium text-ink mb-1.5` | ✅ |

### 4.3. Cards

| Aspecto | Estado | Veredito |
|---------|--------|----------|
| KPI card | `bg-white rounded-xl p-6 shadow-card border border-border` + hover translateY | ✅ |
| Card padrão | `bg-white rounded-xl shadow-card border border-border p-6` | ✅ |
| Card form | Mesmo padrão com `space-y-4` | ✅ |

### 4.4. Tabelas

| Aspecto | Estado | Veredito |
|---------|--------|----------|
| Estrutura | `w-full thead bg-surface/80 tbody divide-y divide-border tr hover:bg-surface/50` | ✅ |
| Paginação | `flex items-center justify-between` com buttons | ✅ |
| Empty row | Mensagem centralizada com colspan | ✅ |
| Sort | Headers clicáveis com ícone de seta | ⚠️ — Só em proposals.php |

### 4.5. Modais

| Aspecto | Estado | Veredito |
|---------|--------|----------|
| Background | `fixed inset-0 z-50 bg-black/40 backdrop-blur-sm` | ✅ |
| Container | `bg-white rounded-xl shadow-modal w-full max-w-2xl max-h-[90vh] flex flex-col animate-fade-in` | ⚠️ — `max-w-lg`, `max-w-2xl`, `max-w-4xl` sem padrão |

### 4.6. Badges e Status

| Aspecto | Estado | Veredito |
|---------|--------|----------|
| Semânticos | `bg-{color}/10 text-{color}` | ✅ |
| Proposta (draft/sent/viewed/accepted/rejected/cancelled) | Hardcoded no JS de `proposals.php` | ⚠️ — Deveriam estar no config |
| Transação (completed/pending/processing/refunded/cancelled) | Hardcoded em `transactions.php` | ⚠️ — Mesmo problema |

### 4.7. Sidebar e Navegação

| Aspecto | Estado | Veredito |
|---------|--------|----------|
| User sidebar | `w-64 bg-sidebar text-white fixed h-full` + mobile hamburger | ✅ |
| Admin sidebar | Mesmo padrão, 6 itens | ✅ |
| Item ativo | `bg-white/10 text-white` | ✅ |
| Item inativo | `text-white/60 hover:bg-white/5 hover:text-white` | ✅ |
| Mobile overlay | `bg-black/40 backdrop-blur-sm` | ✅ |

### 4.8. Topbar

| Aspecto | Estado | Veredito |
|---------|--------|----------|
| User topbar | `h-16 bg-white border-b border-border sticky top-0 z-30` | ✅ |
| Admin topbar | Mesmo padrão, com badge "Admin" | ✅ |

### 4.9. Esqueletos e Loading

| Aspecto | Estado | Veredito |
|---------|--------|----------|
| Skeleton | `h-8 w-20 bg-surface animate-pulse rounded-lg` | ⚠️ — Só 2 templates usam |
| Spinner | `w-10 h-10 border-4 border-primary/30 border-t-primary rounded-full animate-spin` | ✅ |
| Loading state | Texto "Carregando..." centralizado | ❌ — Deveria usar skeleton |

### 4.10. Toasts e Feedback

| Aspecto | Estado | Veredito |
|---------|--------|----------|
| Toast container | `fixed top-4 right-4 z-50` no `footer.php` | ✅ |
| Tipos | success, error, warning, info | ✅ |
| Auto-dismiss | 4s | ✅ |
| Animação | `animate-fade-in` | ✅ |

### 4.11. Empty States

| Aspecto | Estado | Veredito |
|---------|--------|----------|
| Padrão | Ilustração SVG + título + descrição + CTA | ✅ |
| Templates com empty state | proposals, clients, services, categories, transactions | ✅ |
| Templates SEM empty state | dashboard (follow-up vazio), workers, leads | ⚠️ |

### 4.12. Wizard

| Aspecto | Estado | Veredito |
|---------|--------|----------|
| Solicitar serviço | 4-step wizard no `solicitar.php` | ✅ |
| Cadastro | 2-step wizard no `register.php` | ✅ |
| Passo atual | Círculo numerado com fundo primary | ✅ |
| Barra de progresso | Linha conectando passos | ✅ |

---

## 5. Auditoria de Experiência

### 5.1. Consistência Cross-Página

| Item | Conformidade | Observação |
|------|-------------|------------|
| Header/Tailwind config | Todos os templates incluem `header.php` | ✅ |
| Footer/Toast | Todos incluem `footer.php` | ✅ |
| Sidebar | Todos logged-in templates usam `sidebar.php` ou `admin-sidebar.php` | ✅ |
| Page wrapper | Pattern `ml-64 min-h-screen flex flex-col` | ✅ |
| Token de cor | 98% dos componentes usam tokens do config | ✅ |
| Token de spacing | `p-6`, `gap-6`, `space-y-4` consistentes | ⚠️ — Varia `p-5` em alguns cards |
| Nomenclatura | `text-h1`, `text-h2`, `text-h3` uniformes | ✅ |

### 5.2. Responsividade e Mobile

| Item | Conformidade | Observação |
|------|-------------|------------|
| Sidebar mobile | `-translate-x-full` + overlay + hamburger | ✅ |
| Grid responsivo | `grid-cols-1 md:grid-cols-2 lg:grid-cols-4` | ✅ |
| Tabelas horizontais | Scroll horizontal em tabelas largas (`overflow-x-auto`) | ⚠️ — Algumas tabelas sem wrapper |
| Touch targets | Botões ≥ 44px, inputs ≥ 44px | ✅ |
| Modals mobile | `mx-4` para margem, scroll vertical | ✅ |
| Admin login | Layout split oculto em mobile | ✅ |

### 5.3. Acessibilidade

| Critério | Conformidade | Observação |
|----------|-------------|------------|
| Contraste 4.5:1 | `ink` (#0F172A) em `surface` (#F8FAFC) = 13.6:1 | ✅ |
| Contraste 3:1 (grande) | `ink-secondary` (#64748B) = 4.8:1 | ✅ |
| `aria-label` em ícones | Não implementado | ❌ |
| `role` em componentes | Não implementado | ❌ |
| Tab order | Inputs e botões nativos | ✅ |
| Focus visible | `focus:ring-2 focus:ring-primary/30` | ✅ |
| `prefers-reduced-motion` | Não implementado | ❌ |
| Skip to content | Não implementado | ❌ |
| Form field labels | `<label>` com `for` | ✅ |
| Error messages | `aria-describedby` ausente | ❌ |

### 5.4. Performance Percebida

| Item | Estado | Veredito |
|------|--------|----------|
| Skeleton loading | 2 templates | ❌ — Deveria ser padrão |
| Pulsing animation | `animate-pulse` disponível | ✅ |
| API calls com loading | `try/catch/finally` com show/hide | ✅ |
| Chart.js carregamento | CDN com defer | ✅ |

### 5.5. Tratamento de Erros

| Cenário | Estado | Veredito |
|---------|--------|----------|
| API 401 | Redireciona ao login | ✅ |
| API 403/404 | Toast de erro | ⚠️ — Nem sempre implementado |
| API 500 | Toast: "Erro interno" | ✅ |
| Rede offline | Sem tratamento | ❌ |
| Validação de formulário | Client-side + server-side | ✅ |

---

## 6. Ocean DS — Análise e Inspiração

> Ocean DS é um design system SaaS que prioriza clareza, consistência e acessibilidade. Embora não tenhamos acesso ao conteúdo completo (plataforma zeroheight com renderização JS), a nomenclatura e estrutura indicam as seguintes práticas que podemos adotar:

### Práticas Extraídas

| Prática Ocean DS | Adaptação ServiceSaaS | Prioridade |
|-----------------|----------------------|------------|
| **Sistema de Elevação** | 5 níveis (1-5) com shadow + z-index mapeados | P2 |
| **Tokens Semânticos** | `color-surface-base`, `color-surface-raised`, `color-ink-primary`, `color-focus-ring` | P3 |
| **Camada de Ícone** | Helper PHP `icon()` que abstrai heroicons | P1 |
| **Dark Mode Progressivo** | `darkMode: 'class'` + tokens dark já no config (não usados) | P1 |
| **Componentes com Estados** | Loading, empty, error, success explícitos em cada componente | P1 |
| **Grid de 8px** | Já implementado — documentar como padrão | ✅ |
| **Motion com propósito** | Tokens de duração + prefers-reduced-motion | P1 |

### O que NÃO adotar do Ocean DS

1. **Prefixos longos em tokens** — O sistema curto (`primary`, `sidebar`, `surface`) funciona bem para a equipe atual
2. **Componentes JS complexos** — PHP + Tailwind CDN é a stack; componentes JS pesados aumentariam latência
3. **Design tokens em JSON** — Sem build step, o config inline no `header.php` é a abordagem correta

---

## 7. Plano de Redesign por Prioridade

### P0 — Correções Imediatas (antes de qualquer feature nova)

| # | Tarefa | Arquivos | Esforço |
|---|--------|----------|---------|
| 1 | Remover `web-frontend/public/js/tailwind.config.js` (config duplicado) | 1 arquivo | 5min |
| 2 | Unificar border-radius: cards → rounded-xl, inputs → rounded-lg | Todos templates | 30min |
| 3 | Padronizar `p-6` em todos os cards (substituir `p-5`) | 10+ templates | 20min |
| 4 | Adicionar `prefers-reduced-motion` no `header.php` | 1 arquivo | 5min |
| 5 | Remover `hover:scale-110` conflitante em KPI cards | `dashboard.php`, `admin-dashboard.php` | 5min |

### P1 — Melhorias de Curto Prazo

| # | Tarefa | Esforço |
|---|--------|---------|
| 6 | Criar helper PHP `icon()` para abstrair SVGs | 30min |
| 7 | Adicionar skeleton loading em templates sem (workers, leads, dashboard follow-up) | 30min |
| 8 | Adicionar `aria-label` em botões de ícone | 20min |
| 9 | Implementar dark mode toggle no admin | 1h |
| 10 | Adicionar `appearance-none` consistente em selects | 10min |

### P2 — Refinamentos

| # | Tarefa | Esforço |
|---|--------|---------|
| 11 | Escala de elevação 5 níveis no config | 10min |
| 12 | Mover cores de badge de proposta para o config | 20min |
| 13 | Empty states para dashboard follow-up e workers | 15min |
| 14 | Scroll horizontal em tabelas sem wrapper | 15min |

### P3 — Visão de Longo Prazo

| # | Tarefa | Esforço |
|---|--------|---------|
| 15 | Dark mode completo (todos templates) | 3h |
| 16 | Componente de data input estilizado | 1h |
| 17 | Prefixo semântico em tokens (Ocean DS style) | 2h |
| 18 | Testes visuais com Playwright | 4h |

---

## 8. Tokens Atualizados — Design System v2.0

```js
// web-frontend/templates/partials/header.php — Config unificado
tailwind.config = {
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        // ── Primary: Emerald ──────────────────────────
        primary:        { DEFAULT: '#10B981', 50: '#ECFDF5', 100: '#D1FAE5',
                         200: '#A7F3D0', 300: '#6EE7B7', 400: '#34D399',
                         600: '#059669', 700: '#006c49', 800: '#065F46',
                         900: '#064E3B' },

        // ── Neutrals: Slate ────────────────────────────
        sidebar:        '#0F172A',
        surface:        '#F8FAFC',
        ink:            { DEFAULT: '#0F172A', secondary: '#64748B', muted: '#94A3B8' },
        border:         '#E2E8F0',

        // ── Semantic ───────────────────────────────────
        success:        '#16A34A',
        warning:        '#D97706',
        info:           '#0284C7',
        danger:         '#DC2626',
        whatsapp:       '#25D366',

        // ── Status — Proposal Lifecycle ────────────────
        'status-draft':     { bg: '#F3F4F6', text: '#374151' },
        'status-sent':      { bg: '#DBEAFE', text: '#1D4ED8' },
        'status-viewed':    { bg: '#F3E8FF', text: '#7E22CE' },
        'status-accepted':  { bg: '#DCFCE7', text: '#15803D' },
        'status-rejected':  { bg: '#FEE2E2', text: '#B91C1C' },
        'status-cancelled': { bg: '#F3F4F6', text: '#6B7280' },

        // ── Transaction Status ─────────────────────────
        'tx-completed':  { bg: '#DCFCE7', text: '#15803D' },
        'tx-pending':    { bg: '#FEF3C7', text: '#B45309' },
        'tx-processing': { bg: '#DBEAFE', text: '#1D4ED8' },
        'tx-refunded':   { bg: '#FEE2E2', text: '#B91C1C' },
      },

      // ── Typography ──────────────────────────────────
      fontFamily: {
        sans: ['Poppins', '-apple-system', 'sans-serif'],
        mono: ['JetBrains Mono', 'Fira Code', 'monospace'],
      },
      fontSize: {
        display: ['2.5rem', { lineHeight: '1.2', fontWeight: '700' }],
        h1:      ['1.75rem', { lineHeight: '1.3', fontWeight: '700' }],
        h2:      ['1.5rem',  { lineHeight: '1.4', fontWeight: '600' }],
        h3:      ['1.25rem', { lineHeight: '1.5', fontWeight: '600' }],
      },

      // ── Border Radius ───────────────────────────────
      borderRadius: {
        DEFAULT: '8px',
        sm:      '4px',
        md:      '8px',
        lg:      '12px',
        xl:      '16px',
        '2xl':   '20px',
        full:    '9999px',
      },

      // ── Elevation Scale (Ocean DS inspired) ─────────
      boxShadow: {
        'elevation-1': '0 1px 2px 0 rgb(0 0 0 / 0.05)',
        'elevation-2': '0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1)',
        'elevation-3': '0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1)',
        'elevation-4': '0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1)',
        'elevation-5': '0 25px 50px -12px rgb(0 0 0 / 0.25)',
      },

      // ── Animation Duration Tokens ────────────────────
      transitionDuration: {
        fast:   '150ms',
        normal: '300ms',
        slow:   '500ms',
      },

      // ── Z-Index Scale ──────────────────────────────
      zIndex: {
        sidebar: '20',
        header:  '30',
        modal:   '40',
        toast:   '50',
      },
    },
  },
};
```

---

## 9. Especificação de Implementação

### 9.1. Helper de Ícone

Criar em `web-frontend/config/icons.php`:

```php
<?php
function icon($name, $size = 5, $class = '') {
    $icons = [
        'dashboard'  => '<path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>',
        'clients'    => '<path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>',
        'proposals'  => '<path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
        'services'   => '<path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>',
        'financeiro' => '<path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        'settings'   => '<path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>',
        'plus'       => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>',
        'search'     => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>',
        'edit'       => '<path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>',
        'trash'      => '<path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>',
        'close'      => '<path d="M6 18L18 6M6 6l12 12"/>',
        'check'      => '<path d="M5 13l4 4L19 7"/>',
        'whatsapp'   => '<path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884z"/>',
        'download'   => '<path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
    ];
    $svg = $icons[$name] ?? $icons['plus'];
    $sizeClass = "w-{$size} h-{$size}";
    return '<svg class="' . $sizeClass . ' ' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24">' . $svg . '</svg>';
}
```

### 9.2. Dark Mode Progressivo

Fase 1 — Admin apenas (já configurado, basta ativar):

```html
<button onclick="document.documentElement.classList.toggle('dark')"
        class="w-8 h-8 rounded-lg flex items-center justify-center text-ink-secondary
               hover:bg-surface transition-colors">
    <!-- Sun icon -->
    <svg class="w-5 h-5 dark:hidden" ...>...</svg>
    <!-- Moon icon -->
    <svg class="w-5 h-5 hidden dark:block" ...>...</svg>
</button>
```

Fase 2 — User templates (requer tokens dark adicionados no config).

### 9.3. Skeleton Loading Padrão

```php
function skeleton($lines = 1, $width = '100%') {
    return '<div class="space-y-3 animate-pulse">' .
           str_repeat('<div class="h-4 bg-surface rounded" style="width: ' . $width . '"></div>', $lines) .
           '</div>';
}
```

---

## 10. Checklist de Migração

### Antes de começar a codificar

- [x] Auditoria completa executada (este documento)
- [ ] Aprovado pelo time de design/product
- [ ] Branch `redesign/design-audit-v2` criada

### Fase 1 — Correções Rápidas (P0)

- [ ] Remover `tailwind.config.js` duplicado
- [ ] Unificar border-radius (cards rounded-xl, inputs rounded-lg)
- [ ] Padronizar `p-6` em todos os cards
- [ ] Adicionar `prefers-reduced-motion` no header.php
- [ ] Remover `hover:scale-110` conflitante

### Fase 2 — Melhorias (P1)

- [ ] Criar helper `icon()` em config/icons.php
- [ ] Skeleton loading para templates sem
- [ ] `aria-label` em botões de ícone
- [ ] Dark mode toggle no admin
- [ ] `appearance-none` em selects

### Fase 3 — Refinamentos (P2)

- [ ] Escala de elevação 5 níveis
- [ ] Badge cores movidas para config
- [ ] Empty states faltantes
- [ ] Scroll horizontal em tabelas

### Fase 4 — Visão (P3)

- [ ] Dark mode completo
- [ ] Date input estilizado
- [ ] Prefixos semânticos
- [ ] Testes visuais Playwright

---

## Apêndice A — Mapa de Inconsistências por Template

| Template | Inconsistência | Severidade |
|----------|---------------|------------|
| `dashboard.php` | `hover:scale-110` em KPI icons conflita com translateY | ❌ |
| `admin-dashboard.php` | Mesmo problema + shadow modal duplicado | ❌ |
| `proposals.php` | Badge status hardcoded em JS | ⚠️ |
| `transactions.php` | Badge status hardcoded em JS | ⚠️ |
| `workers.php` | Sem empty state | ⚠️ |
| `leads.php` | Sem empty state | ⚠️ |
| `categories.php` | Input `bg-white` vs `bg-surface` | ⚠️ |
| `services.php` | Input `bg-white` vs `bg-surface` | ⚠️ |
| `admin-planos.php` | Cards com `p-5` em vez de `p-6` | ⚠️ |
| `admin-audit.php` | Scroll horizontal sem wrapper | ⚠️ |

---

## Apêndice B — Ocean DS vs ServiceSaaS: Mapa de Equivalência

| Ocean DS | ServiceSaaS | Status |
|----------|-------------|--------|
| `color-primary` | `primary` (`#10B981`) | ✅ |
| `color-surface-raised` | `bg-white` | ✅ |
| `color-surface-base` | `surface` (`#F8FAFC`) | ✅ |
| `color-ink-primary` | `ink` (`#0F172A`) | ✅ |
| `elevation-1` a `elevation-5` | `shadow-card` a `shadow-modal` + z-index | ⚠️ — Parcial |
| `font-family-sans` | `font-sans` (Poppins) | ✅ |
| `spacing-grid` | 8px modular grid | ✅ |
| `icon-sm/md/lg` | `w-4/5/6 h-4/5/6` | ⚠️ — Inconsistente |
| `dark-mode-class` | `darkMode: 'class'` | ⚠️ — Configurado mas não implementado |
| `motion-duration` | `duration-200/300/500` | ⚠️ — Sem tokens nomeados |
| `focus-ring` | `focus:ring-2 focus:ring-primary/30` | ⚠️ — Sem token dedicado |

---

*Auditoria conduzida em 2026-07-28. Última revisão: 2026-07-28.*
*Inspiração: Ocean DS (zeroheight), Tailwind CSS, boas práticas de Design Systems.*
