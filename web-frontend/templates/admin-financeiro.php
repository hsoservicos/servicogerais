<!-- ═══════════════════════════════════════════════════════════════
     templates/admin-financeiro.php — Admin Financeiro (Epic 7 — Story 7.3)
     ═══════════════════════════════════════════════════════════ -->
<?php
$token = getToken();
$user = getUser();
if (!$token || ($user['role'] ?? '') !== 'super_admin') {
    header('Location: ?page=admin-login');
    exit;
}
?>
<div class="min-h-screen bg-surface flex">
    <?php $currentPage = 'admin-financeiro'; require_once __DIR__ . '/partials/admin-sidebar.php'; ?>

    <!-- Main -->
    <div class="ml-64 min-h-screen flex flex-col flex-1">
        <?php
        $pageTitle = 'Financeiro';
        $pageSubtitle = 'Transações, estornos e relatórios financeiros';
        $topbarExtra = '<select id="status-filter" onchange="loadTransactions()" class="text-sm border border-border rounded-lg px-3 py-2 text-ink bg-white focus:outline-none focus:ring-2 focus:ring-primary"><option value="">Todos os status</option><option value="completed">Completas</option><option value="pending">Pendentes</option><option value="refunded">Estornadas</option><option value="cancelled">Canceladas</option></select>';
        require __DIR__ . '/partials/admin-topbar.php';
        ?>

        <main class="flex-1 p-6">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6" id="summary-cards">
                <!-- Skeleton Loading (4 cards) -->
                <div class="bg-white rounded-xl p-4 shadow-card border border-border animate-pulse">
                    <div class="h-4 w-24 bg-ink-muted/10 rounded mb-3"></div>
                    <div class="h-8 w-32 bg-ink-muted/10 rounded mb-2"></div>
                    <div class="h-3 w-20 bg-ink-muted/10 rounded"></div>
                </div>
                <div class="bg-white rounded-xl p-4 shadow-card border border-border animate-pulse">
                    <div class="h-4 w-24 bg-ink-muted/10 rounded mb-3"></div>
                    <div class="h-8 w-32 bg-ink-muted/10 rounded mb-2"></div>
                    <div class="h-3 w-20 bg-ink-muted/10 rounded"></div>
                </div>
                <div class="bg-white rounded-xl p-4 shadow-card border border-border animate-pulse">
                    <div class="h-4 w-24 bg-ink-muted/10 rounded mb-3"></div>
                    <div class="h-8 w-32 bg-ink-muted/10 rounded mb-2"></div>
                    <div class="h-3 w-20 bg-ink-muted/10 rounded"></div>
                </div>
                <div class="bg-white rounded-xl p-4 shadow-card border border-border animate-pulse">
                    <div class="h-4 w-24 bg-ink-muted/10 rounded mb-3"></div>
                    <div class="h-8 w-32 bg-ink-muted/10 rounded mb-2"></div>
                    <div class="h-3 w-20 bg-ink-muted/10 rounded"></div>
                </div>
            </div>

            <!-- Loading -->
            <div id="loading-state" class="text-center py-20">
                <svg class="w-10 h-10 text-primary animate-spin mx-auto mb-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <p class="text-ink-muted">Carregando transações...</p>
            </div>

            <!-- Toast -->
            <div id="toast" class="hidden fixed bottom-6 right-6 z-50 px-5 py-3 rounded-lg shadow-modal text-white text-sm animate-fade-in"></div>

            <!-- Table -->
            <div id="table-content" class="hidden">
                <div class="bg-white rounded-xl shadow-card border border-border overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-surface/50 text-left text-ink-muted border-b border-border">
                                    <th class="px-4 py-3 font-medium">ID</th>
                                    <th class="px-4 py-3 font-medium">Tenant</th>
                                    <th class="px-4 py-3 font-medium">Proposta</th>
                                    <th class="px-4 py-3 font-medium">Valor</th>
                                    <th class="px-4 py-3 font-medium">Taxa</th>
                                    <th class="px-4 py-3 font-medium">Líquido</th>
                                    <th class="px-4 py-3 font-medium">Método</th>
                                    <th class="px-4 py-3 font-medium">Status</th>
                                    <th class="px-4 py-3 font-medium">Data</th>
                                    <th class="px-4 py-3 font-medium text-right">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="transactions-table-body"></tbody>
                        </table>
                    </div>
                </div>
                <div id="pagination" class="flex items-center justify-between mt-4 text-sm text-ink-muted"></div>
                <div id="empty-state" class="hidden text-center py-20">
                    <svg class="w-16 h-16 text-ink-muted/30 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-ink-secondary mb-2">Nenhuma transação encontrada</p>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Refund Modal -->
<div id="refund-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-modal w-full max-w-sm mx-4 p-6 animate-fade-in">
        <div class="w-12 h-12 bg-danger/10 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-6 h-6 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
        </div>
        <h3 class="text-center text-h3 text-ink mb-2">Estornar Transação</h3>
        <p class="text-center text-ink-secondary text-sm mb-2">Confirma o estorno da transação <span id="refund-amount" class="font-semibold text-ink"></span>?</p>
        <p class="text-center text-ink-muted text-xs mb-6">Esta ação não pode ser desfeita. O valor será reembolsado ao cliente.</p>
        <input type="hidden" id="refund-id">
        <div class="flex gap-3">
            <button onclick="closeRefundModal()" class="flex-1 px-4 py-2 border border-border rounded-lg text-ink-secondary hover:bg-surface transition-colors text-sm font-medium">Cancelar</button>
            <button onclick="confirmRefund()" class="flex-1 px-4 py-2 bg-danger text-white rounded-lg hover:bg-red-600 transition-colors text-sm font-medium">Estornar</button>
        </div>
    </div>
</div>

<script>
var currentPage = 1;

async function loadTransactions() {
    const loading = document.getElementById('loading-state');
    const content = document.getElementById('table-content');
    const empty = document.getElementById('empty-state');

    loading.classList.remove('hidden');
    content.classList.add('hidden');
    empty.classList.add('hidden');

    try {
        const status = document.getElementById('status-filter').value;
        const token = '<?= $token ?>';
        const params = new URLSearchParams({ page: currentPage, perPage: 20 });
        if (status) params.set('status', status);

        const response = await fetch(`/api/v1/admin/transactions?${params}`, {
            headers: { 'Authorization': 'Bearer ' + token }
        });

        if (!response.ok) { if (response.status === 401) { window.location.href = '?page=admin-login'; return; } throw new Error('Erro'); }
        const data = await response.json();
        renderSummary(data.summary);
        renderTable(data);
    } catch (err) {
        console.error('[Financeiro]', err.message);
    } finally {
        loading.classList.add('hidden');
    }
}

function renderSummary(summary) {
    const container = document.getElementById('summary-cards');
    if (!summary || summary.length === 0) {
        container.innerHTML = `
            <div class="bg-white rounded-xl p-4 shadow-card border border-border">
                <p class="text-sm text-ink-secondary">Total (completas)</p>
                <p class="text-h3 text-ink mt-1">R$ 0,00</p>
            </div>
        `;
        return;
    }

    const colors = { completed: 'success', pending: 'warning', refunded: 'danger', cancelled: 'ink-muted', processing: 'info' };
    container.innerHTML = summary.map(s => `
        <div class="bg-white rounded-xl p-4 shadow-card border border-border">
            <p class="text-sm text-ink-secondary">${s.status} (${s.count})</p>
            <p class="text-h3 text-${colors[s.status] || 'ink'} mt-1">${s.total}</p>
        </div>
    `).join('');
}

function renderTable(data) {
    const tbody = document.getElementById('transactions-table-body');
    const content = document.getElementById('table-content');
    const empty = document.getElementById('empty-state');
    const pag = document.getElementById('pagination');

    if (!data.transactions || data.transactions.length === 0) {
        content.classList.add('hidden');
        empty.classList.remove('hidden');
        return;
    }

    content.classList.remove('hidden');
    empty.classList.add('hidden');

    const statusBadge = {
        completed: 'bg-success/10 text-success',
        pending: 'bg-warning/10 text-warning',
        processing: 'bg-info/10 text-info',
        refunded: 'bg-danger/10 text-danger',
        cancelled: 'bg-ink-muted/10 text-ink-muted',
    };

    tbody.innerHTML = data.transactions.map(tx => `
        <tr class="border-b border-border/50 hover:bg-surface/30 transition-colors">
            <td class="px-4 py-3 text-ink-muted text-xs">#${tx.id}</td>
            <td class="px-4 py-3 text-ink font-medium">${tx.tenant_name}</td>
            <td class="px-4 py-3 text-ink-secondary">${tx.proposal_number || '—'}</td>
            <td class="px-4 py-3 text-ink font-medium">${tx.amount}</td>
            <td class="px-4 py-3 text-ink-secondary">${tx.fee}</td>
            <td class="px-4 py-3 text-ink font-medium">${tx.net_amount}</td>
            <td class="px-4 py-3 text-ink-secondary text-xs">${tx.payment_method || '—'}</td>
            <td class="px-4 py-3">
                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium ${statusBadge[tx.status] || 'bg-ink-muted/10 text-ink-muted'}">${tx.status}</span>
            </td>
            <td class="px-4 py-3 text-ink-muted text-xs">${tx.paid_at || tx.created_at}</td>
            <td class="px-4 py-3 text-right">
                ${tx.status === 'completed' ? `
                    <button onclick="openRefundModal(${tx.id}, '${tx.amount}')" class="p-1.5 rounded-lg text-ink-muted hover:text-danger hover:bg-danger/10 transition-colors" title="Estornar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </button>
                ` : '<span class="text-ink-muted text-xs">—</span>'}
            </td>
        </tr>
    `).join('');

    const { page, totalPages, total } = data.pagination;
    pag.innerHTML = `
        <span>${total} transações</span>
        <div class="flex items-center gap-2">
            <button onclick="goToPage(${page - 1})" class="px-3 py-1 border border-border rounded-lg hover:bg-surface transition-colors ${page <= 1 ? 'opacity-50 cursor-not-allowed' : ''}" ${page <= 1 ? 'disabled' : ''}>Anterior</button>
            <span class="text-ink-muted">Página ${page} de ${totalPages}</span>
            <button onclick="goToPage(${page + 1})" class="px-3 py-1 border border-border rounded-lg hover:bg-surface transition-colors ${page >= totalPages ? 'opacity-50 cursor-not-allowed' : ''}" ${page >= totalPages ? 'disabled' : ''}>Próxima</button>
        </div>
    `;
}

function goToPage(p) { currentPage = p; loadTransactions(); }

// ── Refund Modal ──────────────────────────────────────────
function openRefundModal(id, amount) {
    document.getElementById('refund-id').value = id;
    document.getElementById('refund-amount').textContent = amount;
    document.getElementById('refund-modal').classList.remove('hidden');
}

function closeRefundModal() { document.getElementById('refund-modal').classList.add('hidden'); }

async function confirmRefund() {
    const id = document.getElementById('refund-id').value;
    const btn = document.querySelector('#refund-modal button:last-child');
    btn.disabled = true; btn.textContent = 'Estornando...';

    try {
        const response = await fetch('/api/v1/admin/transactions/' + id + '/refund', {
            method: 'POST',
            headers: { 'Authorization': 'Bearer <?= $token ?>' },
        });
        if (!response.ok) throw new Error('Erro ao estornar');
        closeRefundModal();
        showToast('Estorno processado com sucesso!', 'bg-success');
        loadTransactions();
    } catch (err) {
        showToast('Erro ao processar estorno', 'bg-danger');
    } finally {
        btn.disabled = false; btn.textContent = 'Estornar';
    }
}

function showToast(msg, bgClass) {
    const toast = document.getElementById('toast');
    toast.textContent = msg;
    toast.className = 'fixed bottom-6 right-6 z-50 px-5 py-3 rounded-lg shadow-modal text-white text-sm animate-fade-in ' + bgClass;
    toast.classList.remove('hidden');
    setTimeout(() => { toast.classList.add('hidden'); }, 3000);
}

document.addEventListener('DOMContentLoaded', loadTransactions);
</script>
