---
name: Serviços Flex Growth
status: aligned-with-official-design-2026-07-27
implementation: 'tailwind-css'
colors:
  # PALETA ALINHADA COM DESIGN.MD OFICIAL (2026-07-27)
  # #10B981 (Esmeralda Claro) = Primary — ações, botões, links
  # #006c49 (Esmeralda Escuro) = Primary-dark — sidebars, headers, contrastes
  
  primary: '#10B981'
  primary-dark: '#006c49'
  primary-600: '#059669'
  primary-900: '#064E3B'
  primary-50: '#ECFDF5'
  primary-fixed: '#6ffbbe'
  primary-fixed-dim: '#4edea3'
  
  surface: '#faf8ff'
  surface-dim: '#d2d9f4'
  surface-bright: '#faf8ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f2f3ff'
  surface-container: '#eaedff'
  surface-container-high: '#e2e7ff'
  surface-container-highest: '#dae2fd'
  on-surface: '#131b2e'
  on-surface-variant: '#3c4a42'
  inverse-surface: '#283044'
  inverse-on-surface: '#eef0ff'
  outline: '#6c7a71'
  outline-variant: '#bbcabf'
  surface-tint: '#006c49'
  
  on-primary: '#ffffff'
  on-primary-fixed: '#002113'
  on-primary-fixed-variant: '#005236'
  
  secondary: '#585f66'
  on-secondary: '#ffffff'
  secondary-container: '#dce3eb'
  on-secondary-container: '#5e656c'
  secondary-fixed: '#dce3eb'
  secondary-fixed-dim: '#c0c7cf'
  on-secondary-fixed: '#151c22'
  on-secondary-fixed-variant: '#40484e'
  
  tertiary: '#006d2f'
  on-tertiary: '#ffffff'
  tertiary-container: '#00bb56'
  on-tertiary-container: '#00431a'
  tertiary-fixed: '#66ff8e'
  tertiary-fixed-dim: '#3de273'
  on-tertiary-fixed: '#002109'
  on-tertiary-fixed-variant: '#005322'
  
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  
  background: '#f0f4f8'
  on-background: '#131b2e'
  surface-variant: '#dae2fd'
  surface-light: '#F3F4F6'
  text-main: '#1F2937'
  
  # Status Tokens (alinhados com oficial)
  status-draft: '#94A3B8'
  status-sent: '#3B82F6'
  status-viewed: '#8B5CF6'
  status-approved: '#10B981'
  status-rejected: '#EF4444'
  status-paid: '#25D366'
  status-cancelled: '#64748B'

typography:
  headline-lg:
    fontFamily: Poppins
    fontSize: 32px
    fontWeight: '600'
    lineHeight: '1.2'
    letterSpacing: -0.02em
  headline-lg-mobile:
    fontFamily: Poppins
    fontSize: 24px
    fontWeight: '600'
    lineHeight: '1.2'
  headline-md:
    fontFamily: Poppins
    fontSize: 24px
    fontWeight: '600'
    lineHeight: '1.3'
  headline-sm:
    fontFamily: Poppins
    fontSize: 20px
    fontWeight: '500'
    lineHeight: '1.4'
  body-lg:
    fontFamily: Poppins
    fontSize: 18px
    fontWeight: '400'
    lineHeight: '1.6'
  body-md:
    fontFamily: Poppins
    fontSize: 16px
    fontWeight: '400'
    lineHeight: '1.6'
  body-sm:
    fontFamily: Poppins
    fontSize: 14px
    fontWeight: '400'
    lineHeight: '1.5'
  label-md:
    fontFamily: Poppins
    fontSize: 14px
    fontWeight: '600'
    lineHeight: '1'
    letterSpacing: 0.05em
  label-sm:
    fontFamily: Poppins
    fontSize: 12px
    fontWeight: '500'
    lineHeight: '1'
  code-table:
    fontFamily: monospace
    fontSize: 14px
    fontWeight: '400'
    lineHeight: '1.4'

rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px

spacing:
  unit: 4px
  container-margin: 24px
  gutter: 16px
  card-padding: 20px
  section-gap: 64px
  stack-sm: 8px
  stack-md: 16px
---

# ServiceSaaS — Growth Design Variant

**Status:** ✅ Alinhado com DESIGN.md Oficial (2026-07-27)
**Implementação:** Tailwind CSS
**Última atualização:** 2026-07-27

## Brand & Style

Design system construído com estética **Corporativa / Moderna** e ênfase em **Tecnologia Orientada ao Crescimento**. Projetado para preencher a lacuna entre a confiabilidade de fintechs de alto nível e a acessibilidade necessária para profissionais autônomos e pequenas empresas no mercado brasileiro.

A personalidade da marca é **Profissional, Eficiente e Otimista**.

## Color Hierarchy (Alinhada)

| Token | Cor | Uso |
|:---|:---:|:---|
| **Primary** | `#10B981` | Ações principais, botões primários, links, indicadores de sucesso |
| **Primary-dark** | `#006c49` | Elementos escuros, headers, sidebar, contrastes profundos |
| **Primary-600** | `#059669` | Hover states, active states |
| **Primary-900** | `#064E3B` | Contraste máximo, texto sobre fundo claro |

## Status Tokens

| Status | Cor | Tailwind |
|:---|:---:|:---|
| **Draft** | `#94A3B8` | `bg-status-draft/10 text-status-draft` |
| **Sent** | `#3B82F6` | `bg-status-sent/10 text-status-sent` |
| **Viewed** | `#8B5CF6` | `bg-status-viewed/10 text-status-viewed` |
| **Approved** | `#10B981` | `bg-status-approved/10 text-status-approved` |
| **Rejected** | `#EF4444` | `bg-status-rejected/10 text-status-rejected` |
| **Paid** | `#25D366` | `bg-status-paid/10 text-status-paid` |
| **Cancelled** | `#64748B` | `bg-status-cancelled/10 text-status-cancelled` |

## Components (Tailwind)

### Buttons
- **Primary:** `bg-primary text-white rounded-lg font-bold hover:brightness-110 active:scale-95 transition-all`
- **Secondary:** `border border-outline-variant text-on-surface-variant rounded-lg font-bold hover:bg-surface-container-low transition-colors`
- **WhatsApp/Action:** `bg-[#25D366] text-white rounded-lg font-bold hover:brightness-110 transition-all`

### Dashboard Cards (KPI)
```html
<div class="bg-surface-container-lowest p-card-padding rounded-xl border border-outline-variant card-hover">
  <div class="flex justify-between items-start mb-4">
    <span class="material-symbols-outlined text-primary p-2 bg-primary/10 rounded-lg">icon</span>
    <span class="text-status-approved text-xs">+12%</span>
  </div>
  <p class="text-xs text-on-surface-variant mb-1">Label</p>
  <h3 class="text-2xl font-bold">1.234</h3>
</div>
```

### Sidebar
```html
<aside class="hidden md:flex flex-col fixed left-0 top-0 h-full z-40 py-6 bg-inverse-surface w-64 shadow-xl">
  <!-- Logo + Nav items -->
</aside>
```

### Table
```html
<div class="overflow-x-auto">
  <table class="w-full">
    <thead>
      <tr class="bg-surface-container-low text-left text-xs uppercase tracking-wider text-on-surface-variant">
        <th class="px-5 py-3 font-semibold">Coluna</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-outline-variant/50 text-sm">
      <tr class="hover:bg-surface-container-low transition-colors">
        <td class="px-5 py-4">Dados</td>
      </tr>
    </tbody>
  </table>
</div>
```

### Status Badge
```html
<span class="px-2 py-0.5 rounded text-xs font-bold uppercase tracking-wider bg-status-approved/10 text-status-approved">
  Approved
</span>
```
