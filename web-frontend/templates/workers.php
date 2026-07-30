<?php if (!isAuthenticated()) { header('Location: ?page=login'); exit; } ?>
<?php $currentPage = 'workers'; require __DIR__ . '/partials/sidebar.php'; ?>
<div class="md:ml-64 min-h-screen flex flex-col">
    <?php
    $pageTitle = 'Trabalhadores';
    $pageSubtitle = 'Gerencie trabalhadores domésticos e certificações';
    $topbarExtra = '<button onclick="openCreateModal()" class="bg-primary text-white font-medium px-4 py-2 rounded-lg hover:bg-primary-600 transition-all flex items-center gap-2 shadow-card text-sm"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Novo Trabalhador</button>';
    require __DIR__ . '/partials/topbar.php';
    ?>
    <main class="flex-1 p-6">
        <?php require __DIR__ . '/workers/workers-list.php'; ?>
    </main>
</div>
<?php require __DIR__ . '/workers/workers-form.php'; ?>
<?php require __DIR__ . '/workers/workers-cert.php'; ?>
