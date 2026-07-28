// ═══════════════════════════════════════════════════════════════
// tailwind.config.js — ServiceSaaS (Serviços Flex)
// ═══════════════════════════════════════════════════════════════
// Uso: Carregado VIA CDN (cdn.tailwindcss.com) nos layouts PHP/HTML
// Config: Injetado via <script id="tailwind-config"> antes do CDN
// ═══════════════════════════════════════════════════════════════
// Paleta oficial (2026-07-27):
//   Primary:       #10B981 (ação — botões, links, destaques)
//   Primary-dark:  #006c49 (contraste — sidebar, headers)
// ═══════════════════════════════════════════════════════════════

tailwind.config = {
  darkMode: 'class',
  theme: {
    extend: {
      // ── CORES OFICIAIS ────────────────────────────────────
      colors: {
        // Escala primária
        primary: '#10B981',
        'primary-600': '#059669',
        'primary-700': '#006c49',   // variante escura
        'primary-900': '#064E3B',
        'primary-50': '#ECFDF5',

        // Sidebar
        sidebar: '#0F172A',

        // Superfícies
        surface: '#F8FAFC',

        // Textos
        ink: '#0F172A',
        'ink-secondary': '#64748B',
        'ink-muted': '#94A3B8',

        // Bordas
        border: '#E2E8F0',

        // Status semânticos
        success: '#16A34A',
        warning: '#D97706',
        info: '#0284C7',
        danger: '#DC2626',

        // WhatsApp (cor exclusiva)
        whatsapp: '#25D366',
      },

      // ── TIPOGRAFIA (Poppins via Google Fonts CDN) ────────
      fontFamily: {
        sans: ['Poppins', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'sans-serif'],
        mono: ['JetBrains Mono', 'Fira Code', 'monospace'],
      },
      fontSize: {
        display: ['30px', { lineHeight: '1.2', fontWeight: '800' }],
        h1: ['24px', { lineHeight: '1.33', fontWeight: '700' }],
        h2: ['20px', { lineHeight: '1.40', fontWeight: '700' }],
        h3: ['18px', { lineHeight: '1.55', fontWeight: '600' }],
      },

      // ── ESPAÇAMENTO (escala modular de 8px) ──────────────
      spacing: {
        '1': '4px',
        '2': '8px',
        '3': '12px',
        '4': '16px',
        '5': '20px',
        '6': '24px',
        '8': '32px',
        '10': '40px',
        '12': '48px',
      },

      // ── BORDAS ARREDONDADAS ───────────────────────────────
      borderRadius: {
        DEFAULT: '6px',     // md (botões, inputs, cards)
        sm: '4px',          // badges, tags
        xl: '12px',         // cards grandes, modais
        full: '9999px',     // avatares, indicadores
      },

      // ── SOMBRAS ───────────────────────────────────────────
      boxShadow: {
        card: '0 1px 2px 0 rgba(0,0,0,0.05)',
        modal: '0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -2px rgba(0,0,0,0.05)',
        panel: '0 10px 15px -3px rgba(0,0,0,0.1)',
      },
    },
  },
};
