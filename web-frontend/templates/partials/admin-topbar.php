<!-- ═══════════════════════════════════════════════════════════════
     partials/admin-topbar.php — Admin Topbar Reutilizável
     ═══════════════════════════════════════════════════════════════
     Usage:
       &lt;?php $pageTitle = 'Painel Admin'; $pageSubtitle = 'Visão geral'; require __DIR__ . '/partials/admin-topbar.php'; ?&gt;
       &lt;?php $topbarExtra = '<select>...</select>'; ?&gt;
     ═══════════════════════════════════════════════════════════ -->

<header class="h-16 bg-white border-b border-border flex items-center justify-between px-6 sticky top-0 z-20">
    <div>
        <h2 class="text-h3 text-ink"><?= htmlspecialchars($pageTitle ?? 'Admin') ?></h2>
        <p class="text-ink-muted text-sm"><?= htmlspecialchars($pageSubtitle ?? '') ?></p>
    </div>
    <div class="flex items-center gap-3">
        <!-- Extra actions/filters slot -->
        <?php if (!empty($topbarExtra)): ?>
            <?= $topbarExtra ?>
        <?php endif; ?>
        <!-- Admin Avatar -->
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-danger/20 text-danger rounded-full flex items-center justify-center font-semibold text-sm">
                A
            </div>
            <div class="hidden md:block">
                <p class="text-sm font-medium text-ink">Admin</p>
                <p class="text-xs text-ink-muted">super_admin</p>
            </div>
        </div>
    </div>
</header>
