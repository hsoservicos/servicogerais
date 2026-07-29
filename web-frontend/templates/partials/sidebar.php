<!-- ═══════════════════════════════════════════════════════════════
     partials/sidebar.php — Sidebar Reutilizável (com hamburger mobile)
     ═══════════════════════════════════════════════════════════════
     Uso: &lt;?php $currentPage = 'dashboard'; require __DIR__ . '/partials/sidebar.php'; ?&gt;
     ═══════════════════════════════════════════════════════════════ -->

<!-- Sidebar Overlay (mobile) -->
<div id="sidebar-overlay"
     class="fixed inset-0 bg-black/40 backdrop-blur-sm z-20 hidden md:hidden opacity-0 transition-opacity duration-300"
     onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<aside id="sidebar"
       class="fixed left-0 top-0 h-screen w-64 bg-sidebar text-white z-30 flex flex-col
              -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">

    <!-- Logo -->
    <div class="h-16 flex items-center gap-3 px-6 border-b border-white/10">
        <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
        </div>
        <span class="font-bold text-lg"><?= APP_NAME ?></span>
    </div>

    <!-- Nav Items -->
    <nav class="flex-1 py-4 overflow-y-auto sidebar-scroll">
        <?php
        $navItems = [
            ['icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'label' => 'Dashboard', 'page' => 'dashboard'],
            ['icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'label' => 'Clientes', 'page' => 'clients'],
            ['icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6', 'label' => 'Propostas', 'page' => 'proposals'],
            ['icon' => 'M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4', 'label' => 'Leads', 'page' => 'leads'],
            ['icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'label' => 'Categorias', 'page' => 'categories'],
            ['icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6', 'label' => 'Serviços', 'page' => 'services'],
            ['icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'label' => 'Trabalhadores', 'page' => 'workers'],
            ['icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'label' => 'Agendamentos', 'page' => 'schedules'],
            ['icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'label' => 'Financeiro', 'page' => 'transactions'],
            ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'label' => 'Privacidade', 'page' => 'privacy'],
            ['icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z', 'label' => 'Meu Perfil', 'page' => 'tenant-profile'],
        ];
        foreach ($navItems as $item):
            $active = ($_GET['page'] ?? '') === $item['page'] ? 'bg-white/10 text-white' : 'text-white/60 hover:bg-white/5 hover:text-white';
        ?>
            <a href="?page=<?= $item['page'] ?>"
                class="flex items-center gap-3 px-6 py-2.5 mx-2 rounded-lg transition-all duration-200 <?= $active ?>"
                onclick="closeSidebarMobile()">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="<?= $item['icon'] ?>"/>
                </svg>
                <span class="text-sm font-medium"><?= $item['label'] ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <!-- Logout -->
    <div class="p-4 border-t border-white/10">
        <a href="?page=logout"
            class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-white/60 hover:bg-white/5 hover:text-white transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            <span class="text-sm font-medium">Sair</span>
        </a>
    </div>
</aside>

<!-- ── Sidebar Toggle Script ──────────────────────────── -->
<style>
    /* Smooth sidebar transitions */
    #sidebar {
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    #sidebar-overlay {
        transition: opacity 0.3s ease;
    }
    /* When sidebar is open */
    body.sidebar-open #sidebar-overlay {
        opacity: 1;
    }
    body.sidebar-open #sidebar {
        transform: translateX(0);
    }
</style>
<script>
function toggleSidebar() {
    const body = document.body;
    const overlay = document.getElementById('sidebar-overlay');
    const sidebar = document.getElementById('sidebar');
    const isOpen = body.classList.contains('sidebar-open');

    if (isOpen) {
        body.classList.remove('sidebar-open');
        sidebar.classList.remove('translate-x-0');
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
        document.body.style.overflow = '';
    } else {
        body.classList.add('sidebar-open');
        sidebar.classList.remove('-translate-x-full');
        sidebar.classList.add('translate-x-0');
        overlay.classList.remove('hidden');
        overlay.setAttribute('tabindex', '-1');
        overlay.focus();
        document.body.style.overflow = 'hidden';
    }
}

function closeSidebarMobile() {
    // Only close on mobile (< 768px)
    if (window.innerWidth < 768) {
        const body = document.body;
        const overlay = document.getElementById('sidebar-overlay');
        const sidebar = document.getElementById('sidebar');
        body.classList.remove('sidebar-open');
        sidebar.classList.remove('translate-x-0');
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
        overlay.removeAttribute('tabindex');
        document.body.style.overflow = '';
    }
}

// ── Close sidebar on Escape key ──────────────────────────
function handleSidebarEscape(e) {
    if (e.key === 'Escape' || e.keyCode === 27) {
        const sidebar = document.getElementById('sidebar');
        if (sidebar && !sidebar.classList.contains('-translate-x-full')) {
            e.preventDefault();
            closeSidebarMobile();
        }
    }
}

document.addEventListener('keydown', handleSidebarEscape);
</script>
