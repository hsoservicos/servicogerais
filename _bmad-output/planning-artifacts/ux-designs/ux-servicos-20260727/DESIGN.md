---
name: 'ServiceSaaS'
description: 'Plataforma SaaS multi-tenant de gestão de serviços, propostas e pagamentos. Profissional, acolhedor, verde-esmeralda como identidade.'
implementation: 'tailwind-css'
status: 'v2.0 — revisado com base no Design Audit 2026-07-28 (Ocean DS inspired)'
colors:
  primary: '#2563EB'
  primary-50: '#EFF6FF'
  primary-100: '#DBEAFE'
  primary-200: '#BFDBFE'
  primary-300: '#93C5FD'
  primary-400: '#60A5FA'
  primary-500: '#3B82F6'
  primary-600: '#2563EB'
  primary-700: '#1D4ED8'
  primary-800: '#1E40AF'
  primary-900: '#1E3A8A'
  sidebar: '#0F172A'
  surface: '#F8FAFC'
  white: '#FFFFFF'
  ink: '#0F172A'
  ink-secondary: '#64748B'
  ink-muted: '#94A3B8'
  border: '#E2E8F0'
  success: '#16A34A'
  warning: '#D97706'
  info: '#0284C7'
  danger: '#DC2626'
  whatsapp: '#25D366'
  status-draft: { bg: '#F3F4F6', text: '#374151' }
  status-sent: { bg: '#DBEAFE', text: '#1D4ED8' }
  status-viewed: { bg: '#F3E8FF', text: '#7E22CE' }
  status-accepted: { bg: '#DCFCE7', text: '#15803D' }
  status-rejected: { bg: '#FEE2E2', text: '#B91C1C' }
  status-cancelled: { bg: '#F3F4F6', text: '#6B7280' }
  tx-completed: { bg: '#DCFCE7', text: '#15803D' }
  tx-pending: { bg: '#FEF3C7', text: '#B45309' }
  tx-processing: { bg: '#DBEAFE', text: '#1D4ED8' }
  tx-refunded: { bg: '#FEE2E2', text: '#B91C1C' }
  dark-sidebar: '#0F172A'
  dark-surface: '#1E293B'
  dark-ink: '#F8FAFC'
  dark-ink-secondary: '#94A3B8'
  dark-border: '#334155'
typography:
  display:
    fontFamily: "'Poppins', sans-serif"
    fontSize: '2.5rem'
    fontWeight: '700'
    lineHeight: '1.2'
  heading-1:
    fontFamily: "'Poppins', sans-serif"
    fontSize: '1.75rem'
    fontWeight: '700'
    lineHeight: '1.3'
  heading-2:
    fontFamily: "'Poppins', sans-serif"
    fontSize: '1.5rem'
    fontWeight: '600'
    lineHeight: '1.4'
  heading-3:
    fontFamily: "'Poppins', sans-serif"
    fontSize: '1.25rem'
    fontWeight: '600'
    lineHeight: '1.5'
  body:
    fontFamily: "'Poppins', sans-serif"
    fontSize: '1rem'
    fontWeight: '400'
    lineHeight: '1.6'
  body-small:
    fontFamily: "'Poppins', sans-serif"
    fontSize: '0.875rem'
    fontWeight: '400'
    lineHeight: '1.5'
  caption:
    fontFamily: "'Poppins', sans-serif"
    fontSize: '0.75rem'
    fontWeight: '500'
    lineHeight: '1.4'
  mono:
    fontFamily: "'JetBrains Mono', 'Fira Code', monospace"
    fontSize: '0.8125rem'
    fontWeight: '400'
    lineHeight: '1.4'
rounded:
  sm: 4px
  md: 8px
  lg: 12px
  xl: 16px
  '2xl': 20px
  full: 9999px
spacing:
  '1': 4px
  '2': 8px
  '3': 12px
  '4': 16px
  '5': 20px
  '6': 24px
  '8': 32px
  '10': 40px
  '12': 48px
  sidebar-width: 256px
  topbar-height: 64px
elevation:
  '1': '0 1px 2px 0 rgb(0 0 0 / 0.05)'
  '2': '0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1)'
  '3': '0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1)'
  '4': '0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1)'
  '5': '0 25px 50px -12px rgb(0 0 0 / 0.25)'
motion:
  fast: 150ms
  normal: 300ms
  slow: 500ms
---

# ServiceSaaS — Design Spine

**Implementação:** Tailwind CSS com configuração customizada
**Última atualização:** 2026-07-27

## Brand & Style

ServiceSaaS é a plataforma que profissionais autônomos e pequenas empresas usam para transformar o caos de orçamentos manuais em propostas profissionais com 1 clique. A identidade visual traduz essa promessa: **crescimento profissional com solidez**.

A paleta é liderada pelo **Verde Esmeralda** — cor que comunica crescimento, confiança e prosperidade financeira. O contraste com o **Dark Slate (`#0F172A`)** da sidebar ancora o sistema com autoridade. Onde produtos financeiros tendem ao azul corporativo frio, ServiceSaaS escolhe o verde — mais humano, mais brasileiro, mais otimista.

**Mobile-first, profissional, acolhedor.** A UX prioriza ação sobre informação passiva. Cada tela tem um propósito claro e um próximo passo óbvio.

## Colors

### Hierarquia de Cor Primária

A paleta primária utiliza **duas variações de verde esmeralda** para criar profundidade visual:

| Token | Cor | Uso | Tailwind |
|:---|:---:|---|---|
| `primary-500` | `#10B981` 🟢 | **Ação principal** — Botões primários, links, indicadores de aprovação, status "ativo", destaques, hover states, CTA | `bg-primary` / `text-primary` |
| `primary-600` | `#059669` 🟢 | **Hover** — Interações de confirmação, active states de botões | `hover:bg-primary-600` |
| `primary-700` | `#006c49` 🟢 | **Variante escura** — Sidebar, headers escuros, contrastes profundos, badges de destaque, modo escuro | `bg-primary-700` |
| `primary-900` | `#064E3B` 🟢 | **Contraste máximo** — Texto de badges sobre fundo claro, backgrounds de alto contraste | `text-primary-900` |
| `primary-50` | `#ECFDF5` 🟢 | **Fundo sutil** — Cards destacados, badges de sucesso, backgrounds de seção | `bg-primary-50` |

### Neutras: Slate

| Token | Cor | Uso | Tailwind |
|:---|:---:|---|---|
| `surface-base` | `#F8FAFC` | Fundo geral da aplicação | `bg-surface` |
| `surface-raised` | `#FFFFFF` | Cards, tabelas, modais, inputs | `bg-white` |
| `surface-sidebar` | `#0F172A` | Sidebar fixa à esquerda | `bg-sidebar` |
| `ink-primary` | `#0F172A` | Títulos, labels, valores de KPI | `text-ink` |
| `ink-secondary` | `#64748B` | Textos de apoio, subtítulos | `text-ink-secondary` |
| `ink-muted` | `#94A3B8` | Placeholders, textos desabilitados | `text-ink-muted` |

### Status Semânticos

| Status | Cor | Tailwind | Uso |
|:---|:---:|:---|:---|
| ✅ **Sucesso** | `#16A34A` | `text-success` / `bg-success/10` | Proposta aprovada, pagamento confirmado |
| ⏳ **Alerta** | `#D97706` | `text-warning` / `bg-warning/10` | Pendente, aguardando aprovação |
| 🔄 **Info** | `#0284C7` | `text-info` / `bg-info/10` | Em andamento, processando |
| ❌ **Erro** | `#DC2626` | `text-danger` / `bg-danger/10` | Cancelado, rejeitado |
| 💬 **WhatsApp** | `#25D366` | `bg-whatsapp` / `text-whatsapp` | Botão exclusivo WhatsApp |

### Badges Semânticos

Implementados com Tailwind: `px-2 py-0.5 rounded text-xs font-bold uppercase tracking-wider`

| Badge | Classes |
|:---|---|
| 🟢 Success | `bg-success/10 text-success` |
| 🟡 Warning | `bg-warning/10 text-warning` |
| 🔵 Info | `bg-info/10 text-info` |
| 🔴 Danger | `bg-danger/10 text-danger` |

## Typography

**Poppins** é a voz tipográfica da plataforma — moderna, arredondada, legível em telas de alta e baixa densidade.

**Implementação Tailwind:**

```js
fontFamily: {
  sans: ['Poppins', '-apple-system', 'sans-serif'],
  mono: ['JetBrains Mono', 'Fira Code', 'monospace']
}
```

| Token | Tamanho | Peso | Tailwind | Uso |
|---|---|---|---|---|
| `display` | 30px | 800 | `text-display` | KPIs no dashboard |
| `heading-1` | 24px | 700 | `text-h1` | Títulos de página |
| `heading-2` | 20px | 700 | `text-h2` | Títulos de cards |
| `heading-3` | 18px | 600 | `text-h3` | Subtítulos |
| `body` | 16px | 400 | `text-base` | Texto corrido |
| `body-small` | 14px | 400 | `text-sm` | Tabelas, inputs |
| `caption` | 12px | 400 | `text-xs` | Badges, metadados |
| `mono` | 13px | 400 | `font-mono` | Código, IDs |

## Layout & Spacing

### Grid: Modular de 8px — Implementado via Tailwind spacing

```js
spacing: {
  '1': '4px', '2': '8px', '3': '12px', '4': '16px',
  '5': '20px', '6': '24px', '8': '32px', '10': '40px',
  '12': '48px'
}
```

### Componentes Principais (Tailwind)

| Componente | Classes Base |
|:---|---|
| **Botão Primário** | `bg-primary text-white px-4 py-2.5 rounded-lg font-bold hover:bg-primary-600 active:scale-95 disabled:opacity-50` |
| **Botão Secundário** | `bg-white border border-border text-ink-secondary px-4 py-2.5 rounded-lg font-bold hover:bg-surface` |
| **Botão WhatsApp** | `bg-whatsapp text-white px-4 py-2.5 rounded-lg font-bold flex items-center gap-2 hover:brightness-110` |
| **Card KPI** | `bg-white p-5 rounded-xl border border-border shadow-sm hover:shadow-md transition-all` |
| **Input** | `w-full bg-white border border-border rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-primary outline-none` |
| **Badge** | `px-2 py-0.5 rounded text-xs font-bold uppercase tracking-wider` |
| **Sidebar** | `w-64 bg-sidebar text-white fixed left-0 top-0 h-full` |
| **Topbar** | `h-16 bg-white border-b border-border flex items-center px-6 sticky top-0 z-30` |
| **Tabela** | `w-full border-collapse divide-y divide-border` + `hover:bg-surface` |
| **Modal** | `bg-white rounded-xl shadow-lg p-6 max-w-lg w-full` |

## Tailwind Config Template

O arquivo `web-frontend/public/js/tailwind.config.js` deve ser configurado com:

```js
tailwind.config = {
  theme: {
    extend: {
      colors: {
        primary: '#10B981',
        'primary-600': '#059669',
        'primary-700': '#006c49',
        'primary-900': '#064E3B',
        'primary-50': '#ECFDF5',
        sidebar: '#0F172A',
        surface: '#F8FAFC',
        ink: '#0F172A',
        'ink-secondary': '#64748B',
        'ink-muted': '#94A3B8',
        border: '#E2E8F0',
        success: '#16A34A',
        warning: '#D97706',
        info: '#0284C7',
        danger: '#DC2626',
        whatsapp: '#25D366'
      },
      fontFamily: {
        sans: ['Poppins', '-apple-system', 'sans-serif']
      },
      borderRadius: {
        DEFAULT: '4px',
        lg: '6px',
        xl: '12px',
        full: '9999px'
      }
    }
  }
}
```

## Responsivo

- **Desktop (>1024px):** Sidebar visível (260px), layout grid multi-coluna
- **Tablet (768-1024px):** Sidebar recolhida em ícones, layout 2 colunas
- **Mobile (<768px):** Sidebar oculta com hambúrguer, layout 1 coluna + bottom navigation

**Implementação:** Tailwind `hidden md:flex`, `grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3`

## Elevation & Depth

| Nível | Tailwind Shadow | Uso |
|:---|---|:---|
| `shadow-sm` | `shadow-sm` | Cards de KPI, inputs |
| `shadow-md` | `shadow-md` | Modais, dropdowns |
| `shadow-lg` | `shadow-lg` | Painéis flutuantes |

## Do's and Don'ts

| Do | Don't |
|---|---|
| Usar `#10B981` para ações principais | Usar verde para elementos decorativos |
| Usar `#006c49` para variantes escuras (sidebar) | Misturar ambas sem critério de hierarquia |
| Sidebar escura com contraste máximo | Sidebar clara que compete com o conteúdo |
| Cards com borda sutil e sombra leve | Cards com sombras dramáticas |
| Badges semânticos com fundo claro | Badges com cor sólida (difícil ler) |
| Padding generoso (`p-5` em cards) | Comprimir campos para caber na tela |
| Fonte Poppins em toda a interface | Misturar múltiplas fontes |
| Tailwind utility classes | CSS custom sem necessidade |
