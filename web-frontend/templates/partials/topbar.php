<!-- ═══════════════════════════════════════════════════════════════
     partials/topbar.php — Topbar Reutilizável (Prestador)
     ═══════════════════════════════════════════════════════════════
     Usage:
       &lt;?php $pageTitle = 'Clientes'; $pageSubtitle = 'Gerencie seus clientes'; require __DIR__ . '/partials/topbar.php'; ?&gt;
       &lt;?php $topbarExtra = '<button class="...">Ação</button>'; ?&gt;
     ═══════════════════════════════════════════════════════════ -->

<header class="h-16 bg-white/80 backdrop-blur-md border-b border-border flex items-center justify-between px-6 sticky top-0 z-20">
    <div class="flex items-center gap-3">
        <!-- Hamburger Button (mobile) -->
        <button onclick="toggleSidebar()"
            class="md:hidden w-10 h-10 rounded-lg flex items-center justify-center hover:bg-surface transition-all duration-200 hover:scale-105 active:scale-95"
            aria-label="Abrir menu" title="Abrir menu">
            <svg class="w-5 h-5 text-ink-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        <div>
            <h2 class="text-h3 text-ink" id="greeting-header"><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></h2>
            <p class="text-ink-muted text-sm"><?= htmlspecialchars($pageSubtitle ?? '') ?></p>
        </div>
    </div>
    <div class="flex items-center gap-4">
        <!-- Extra actions slot (e.g., "Nova Proposta" button) -->
        <?php if (!empty($topbarExtra)): ?>
            <?= $topbarExtra ?>
        <?php endif; ?>

        <!-- Avatar -->
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-primary/20 text-primary rounded-full flex items-center justify-center font-semibold text-sm">
                <?= strtoupper(substr(getUser()['name'] ?? 'U', 0, 1)) ?>
            </div>
            <div class="hidden md:block">
                <p class="text-sm font-medium text-ink"><?= htmlspecialchars(getUser()['name'] ?? 'Usuário') ?></p>
                <p class="text-xs text-ink-muted"><?= htmlspecialchars(getUser()['email'] ?? '') ?></p>
            </div>
        </div>
    </div>
</header>
