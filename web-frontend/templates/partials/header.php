<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? APP_NAME) ?></title>
    <meta name="description" content="<?= APP_NAME ?> — Plataforma completa para gerenciar seus serviços e propostas.">

    <!-- Tailwind CSS (Play CDN v3) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Tailwind Config (Design System Tokens) -->
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#3B82F6',
                        'primary-50': '#EFF6FF',
                        'primary-100': '#DBEAFE',
                        'primary-200': '#BFDBFE',
                        'primary-300': '#93C5FD',
                        'primary-400': '#60A5FA',
                        'primary-500': '#3B82F6',
                        'primary-600': '#2563EB',
                        'primary-700': '#1D4ED8',
                        'primary-800': '#1E40AF',
                        'primary-900': '#1E3A8A',
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
                        whatsapp: '#25D366',
                    },
                    fontFamily: {
                        sans: ['Poppins', 'system-ui', 'sans-serif'],
                    },
                    fontSize: {
                        display: ['2.5rem', { lineHeight: '1.2', fontWeight: '700' }],
                        h1: ['1.875rem', { lineHeight: '1.3', fontWeight: '600' }],
                        h2: ['1.5rem', { lineHeight: '1.35', fontWeight: '600' }],
                        h3: ['1.25rem', { lineHeight: '1.4', fontWeight: '600' }],
                    },
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
                    borderRadius: {
                        sm: '4px',
                        md: '8px',
                        lg: '12px',
                        xl: '16px',
                        '2xl': '20px',
                    },
                    boxShadow: {
                        card: '0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1)',
                        modal: '0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1)',
                        panel: '0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1)',
                    },
                },
            },
        };
    </script>

    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Poppins', system-ui, sans-serif; }
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: #1E293B; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #475569; border-radius: 2px; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeIn 0.3s ease-out; }
        @keyframes slideIn { from { opacity: 0; transform: translateX(-16px); } to { opacity: 1; transform: translateX(0); } }
        .animate-slide-in { animation: slideIn 0.3s ease-out; }
        @keyframes pulse-dot { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
        .animate-pulse-dot { animation: pulse-dot 1.5s ease-in-out infinite; }
    </style>
</head>
<body class="bg-surface text-ink antialiased">
