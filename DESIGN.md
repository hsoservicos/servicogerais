---
name: 'ServiceSaaS'
description: 'Plataforma SaaS multi-tenant de gestão de serviços, propostas e pagamentos. Profissional, acolhedor, verde-esmeralda.'
implementation: 'tailwind-css'
colors:
  primary-500: '#10B981'
  primary-600: '#059669'
  primary-700: '#006c49'
  primary-900: '#064E3B'
  primary-50: '#ECFDF5'
  sidebar: '#0F172A'
  surface: '#F8FAFC'
  surface-raised: '#FFFFFF'
  ink: '#0F172A'
  ink-secondary: '#64748B'
  ink-muted: '#94A3B8'
  border: '#E2E8F0'
  success: '#16A34A'
  warning: '#D97706'
  info: '#0284C7'
  danger: '#DC2626'
  whatsapp: '#25D366'
typography:
  base:
    fontFamily: "'Poppins', sans-serif"
  display:
    fontSize: '30px'
    fontWeight: '800'
  heading-1:
    fontSize: '24px'
    fontWeight: '700'
  heading-2:
    fontSize: '20px'
    fontWeight: '700'
  heading-3:
    fontSize: '18px'
    fontWeight: '600'
  body:
    fontSize: '16px'
    fontWeight: '400'
  body-small:
    fontSize: '14px'
    fontWeight: '400'
  caption:
    fontSize: '12px'
    fontWeight: '400'
  mono:
    fontFamily: "'JetBrains Mono', monospace"
    fontSize: '13px'
rounded:
  DEFAULT: '6px'
  lg: '12px'
  xl: '16px'
  full: '9999px'
spacing:
  unit: '4px'
  sidebar-width: '260px'
  topbar-height: '64px'
---

# ServiceSaaS — Design System

**Ultima atualizacao:** 2026-07-28
**Implementacao:** Tailwind CSS (CDN via web-frontend/public/index.php + tailwind.config.js inline)

## Brand Voice

ServiceSaaS e a plataforma que profissionais autonomos e pequenas empresas usam para criar propostas profissionais com 1 clique. A identidade visual traduz essa promessa: **crescimento profissional com solidez**.

Tom: **Profissional, direto e otimista.** Nada de jargao. Nada de frescura. Como um bom prestador de servico que entrega o que promete.

## Color System

### Primary: Verde Esmeralda

| Token | Cor | Uso |
|:---|:---:|:---|
| `primary` | `#10B981` | Acoes principais, botoes, links, indicadores de sucesso |
| `primary-700` | `#006c49` | Sidebar, headers escuros, contrastes profundos |
| `primary-50` | `#ECFDF5` | Fundo sutil para cards destacados |

### Neutrals: Slate

| Token | Cor | Uso |
|:---|:---:|:---|
| `sidebar` | `#0F172A` | Sidebar fixa, fundo escuro de navegacao |
| `surface` | `#F8FAFC` | Fundo geral da aplicacao |
| `ink` | `#0F172A` | Titulos, valores KPI |
| `ink-secondary` | `#64748B` | Textos de apoio |
| `ink-muted` | `#94A3B8` | Placeholders, desabilitados |

### Status Colors

| Status | Cor | Uso |
|:---|:---:|:---|
| Success | `#16A34A` | Proposta aprovada, pagamento confirmado |
| Warning | `#D97706` | Pendente, aguardando acao |
| Info | `#0284C7` | Em andamento, processando |
| Danger | `#DC2626` | Rejeitado, cancelado, erros |
| WhatsApp | `#25D366` | Botao exclusivo WhatsApp |

## Typography

**Primary font:** Poppins (Google Fonts) — moderna, arredondada, legivel.

**Monospace:** JetBrains Mono — para codigos, IDs, valores de transacao.

| Token | Size | Weight | Uso |
|:---|:---:|:---:|:---|
| Display | 30px | 800 | KPIs, numeros grandes |
| H1 | 24px | 700 | Titulos de pagina |
| H2 | 20px | 700 | Titulos de cards |
| H3 | 18px | 600 | Subtitulos, modais |
| Body | 16px | 400 | Texto corrido |
| Small | 14px | 400 | Tabelas, inputs, labels |
| Caption | 12px | 400 | Badges, metadados |
| Mono | 13px | 400 | Codigos, props numbers |

## Components

### Primary Button
`bg-primary text-white px-4 py-2.5 rounded-lg font-medium hover:bg-primary-600 active:scale-95 disabled:opacity-50 transition-all`

### Secondary Button
`border-2 border-border text-ink font-medium px-4 py-2.5 rounded-lg hover:bg-surface transition-all`

### WhatsApp Button
`bg-[#25D366] text-white px-4 py-2.5 rounded-lg font-medium flex items-center gap-2 hover:brightness-110 transition-all`

### KPI Card
`bg-white p-5 rounded-xl border border-border shadow-sm`

### Input
`w-full px-4 py-2.5 rounded-lg border border-border bg-white text-ink focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all outline-none`

### Badge
`px-2 py-0.5 rounded text-xs font-semibold` + color variant

### Status Badge Variants
- Draft: `bg-gray-100 text-gray-700`
- Sent: `bg-blue-100 text-blue-700`
- Viewed: `bg-purple-100 text-purple-700`
- Accepted: `bg-green-100 text-green-700`
- Rejected: `bg-red-100 text-red-700`
- Paid: `bg-green-100 text-green-700`

### Sidebar
`w-64 h-screen bg-[#0F172A] text-white fixed left-0 top-0 z-30`

### Table
`w-full` + `thead bg-surface/80 border-b` + `tbody divide-y divide-border` + `tr hover:bg-surface/50`

### Modal
`bg-white rounded-xl shadow-modal max-w-2xl w-full` + backdrop `bg-black/30 backdrop-blur-sm`

## Layout

- **Sidebar:** 260px, fixa a esquerda, fundo #0F172A, texto branco
- **Topbar:** 64px, branca com borda inferior, sticky
- **Main content:** `ml-64` (desktop), padding `p-6`
- **Cards:** Padding `p-5`, borda sutil, sombra leve
- **Responsivo:** Desktop sidebar visivel, tablet icones, mobile hamburguer

## Elevation

| Level | Shadow | Use |
|:---|:---|:---|
| Card | `shadow-sm` | KPI cards, tables |
| Dropdown | `shadow-md` | Menus, selects |
| Modal | `shadow-xl` | Modals, panels |

## Motion

- **Transitions:** `duration-200` para hovers, `duration-300` para modals
- **Scale:** `active:scale-95` em botoes para feedback tactil
- **Fade:** `animate-fade-in` para modals e toasts
- **No bounce/elastic easing** (evitar efeitos datados)

## Anti-Patterns (Do Not)

- ❌ Nao usar Inter, Arial, ou system-ui (usar Poppins)
- ❌ Nao usar texto cinza em fundo colorido
- ❌ Nao usar preto/cinza puro (sempre tintar)
- ❌ Nao empilhar cards dentro de cards
- ❌ Nao usar bounce/elastic easing
- ❌ Nao usar gradients roxo-azul (estetica SaaS generica)
- ❌ Nao usar bordas laterais em abas
- ❌ Nao usar sombras escuras em dark mode (glow suave)
- ❌ Nao usar azul como cor primaria (verde esmeralda e a identidade)
