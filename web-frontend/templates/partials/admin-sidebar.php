<!-- ═══════════════════════════════════════════════════════════════
     partials/admin-sidebar.php — Admin Sidebar (Epic 7)
     ═══════════════════════════════════════════════════════════
     Sidebar compartilhada entre todas as páginas admin.
     O parâmetro $currentPage define o item ativo.
     ═══════════════════════════════════════════════════════════ -->
<aside class="fixed left-0 top-0 h-screen w-64 bg-sidebar text-white z-30 flex flex-col">
    <div class="h-16 flex items-center gap-3 px-6 border-b border-white/10">
        <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>
        <div>
            <span class="font-bold text-sm"><?= APP_NAME ?></span>
            <span class="text-xs text-primary-300 block">Admin</span>
        </div>
    </div>
    <nav class="flex-1 py-4 overflow-y-auto sidebar-scroll">
        <?php
        $adminNav = [
            ['icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'label' => 'Dashboard', 'page' => 'admin-dashboard'],
            ['icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'label' => 'Tenants', 'page' => 'admin-tenants'],
            ['icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'label' => 'Financeiro', 'page' => 'admin-financeiro'],
            ['icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'label' => 'Auditoria', 'page' => 'admin-audit'],
        ];
        foreach ($adminNav as $item):
            $active = ($currentPage ?? '') === $item['page'] ? 'bg-white/10 text-white' : 'text-white/60 hover:bg-white/5 hover:text-white';
        ?>
            <a href="?page=<?= $item['page'] ?>"
                class="flex items-center gap-3 px-6 py-2.5 mx-2 rounded-lg transition-all duration-200 <?= $active ?>">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="<?= $item['icon'] ?>"/>
                </svg>
                <span class="text-sm font-medium"><?= $item['label'] ?></span>
            </a>
        <?php endforeach; ?>
    </nav>
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
