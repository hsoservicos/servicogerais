---
name: 'ServiceSaaS'
description: 'Plataforma SaaS de gestão de propostas e orçamentos para prestadores de serviço. Profissional, acolhedor, verde-esmeralda como identidade.'
implementation: 'tailwind-css'
colors:
  # PALETA OFICIAL — Resolução ADR-006 + Decisão de Design 2026-07-27
  # #10B981 (Esmeralda Claro) = Primary (ações, botões, links)
  # #006c49 (Esmeralda Escuro) = Primary-dark (sidebars, headers, contrast)
  
  primary-500: '#10B981'
  primary-600: '#059669'
  primary-700: '#006c49'
  primary-900: '#064E3B'
  primary-50: '#ECFDF5'
  secondary-500: '#0F172A'
  secondary-700: '#1E293B'
  surface-base: '#F8FAFC'
  surface-raised: '#FFFFFF'
  surface-sidebar: '#0F172A'
  border: '#E2E8F0'
  border-hairline: '#F1F5F9'
  ink-primary: '#0F172A'
  ink-secondary: '#64748B'
  ink-muted: '#94A3B8'
  ink-white: '#FFFFFF'
  status-success-bg: '#DCFCE7'
  status-success-text: '#15803D'
  status-success: '#16A34A'
  status-warning-bg: '#FEF3C7'
  status-warning-text: '#B45309'
  status-warning: '#D97706'
  status-info-bg: '#E0F2FE'
  status-info-text: '#0369A1'
  status-info: '#0284C7'
  status-danger-bg: '#FEE2E2'
  status-danger-text: '#B91C1C'
  status-danger: '#DC2626'
  whatsapp: '#25D366'
  surface-base-dark: '#0F172A'
  surface-raised-dark: '#1E293B'
  ink-primary-dark: '#F8FAFC'
  ink-secondary-dark: '#94A3B8'
  border-dark: '#334155'
typography:
  display:
    fontFamily: "'Poppins', sans-serif"
    fontSize: '30px'
    fontWeight: '800'
    lineHeight: '1.2'
  heading-1:
    fontFamily: "'Poppins', sans-serif"
    fontSize: '24px'
    fontWeight: '700'
    lineHeight: '1.33'
  heading-2:
    fontFamily: "'Poppins', sans-serif"
    fontSize: '20px'
    fontWeight: '700'
    lineHeight: '1.40'
  heading-3:
    fontFamily: "'Poppins', sans-serif"
    fontSize: '18px'
    fontWeight: '600'
    lineHeight: '1.55'
  body:
    fontFamily: "'Poppins', sans-serif"
    fontSize: '16px'
    fontWeight: '400'
    lineHeight: '1.50'
  body-small:
    fontFamily: "'Poppins', sans-serif"
    fontSize: '14px'
    fontWeight: '400'
    lineHeight: '1.43'
  caption:
    fontFamily: "'Poppins', sans-serif"
    fontSize: '12px'
    fontWeight: '400'
    lineHeight: '1.33'
  mono:
    fontFamily: "'JetBrains Mono', 'Fira Code', monospace"
    fontSize: '13px'
    fontWeight: '400'
    lineHeight: '1.40'
rounded:
  sm: 4px
  md: 6px
  lg: 12px
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
  sidebar-width: 260px
  topbar-height: 64px
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
