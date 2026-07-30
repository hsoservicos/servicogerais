<?php
if (!isAuthenticated()) {
    header('Location: ?page=login');
    exit;
}
$currentPage = 'proposals';
?>
<?php require __DIR__ . '/partials/sidebar.php'; ?>
<div class="md:ml-64 min-h-screen flex flex-col">
    <?php
    $pageTitle = 'Propostas';
    $pageSubtitle = 'Gerencie orçamentos e propostas comerciais';
    $topbarExtra = '<button onclick="openCreateModal()" class="bg-primary text-white font-medium px-4 py-2 rounded-lg hover:bg-primary-600 active:bg-primary-700 transition-all duration-200 flex items-center gap-2 shadow-card text-sm"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Nova Proposta</button>';
    require __DIR__ . '/partials/topbar.php';
    ?>
    <main class="flex-1 p-6">
        <?php require __DIR__ . '/proposals/proposals-list.php'; ?>
    </main>
</div>

<?php require __DIR__ . '/proposals/proposals-form.php'; ?>
<?php require __DIR__ . '/proposals/proposals-view.php'; ?>

<script>
const API = '/api/v1';
let currentPage = 1;
let currentStatus = '<?= $_GET['status'] ?? '' ?>';

async function loadProposals(page = 1) {
    // ... (the existing JS logic for loading proposals)
}

function openCreateModal() { /* ... */ }
function closeModal() { /* ... */ }
function closeViewModal() { /* ... */ }
</script>
