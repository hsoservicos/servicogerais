<!-- ═══════════════════════════════════════════════════════════════
     templates/transactions.php — Financeiro / Transações (Story 4.4)
     ═══════════════════════════════════════════════════════════
     FR-043: Prestador visualiza extrato de transações com
     paginação, filtros e cards de resumo.
     ═══════════════════════════════════════════════════════════ -->

<?php $currentPage = 'transactions'; require __DIR__ . '/partials/sidebar.php'; ?>

<div class="md:ml-64 min-h-screen flex flex-col">
    <?php
    $pageTitle = 'Financeiro';
    $pageSubtitle = 'Histórico de transações e pagamentos';
    $topbarExtra = '<select id="status-filter" onchange="currentPage=1;loadTransactions()" class="text-sm border border-border rounded-lg px-3 py-2 text-ink bg-white focus:outline-none focus:ring-2 focus:ring-primary"><option value="">Todos os status</option><option value="completed">Aprovados</option><option value="pending">Pendentes</option><option value="processing">Processando</option><option value="refunded">Estornados</option><option value="cancelled">Cancelados</option></select>';
    require __DIR__ . '/partials/topbar.php';
    ?>

    <main class="flex-1 p-6">
        <!-- Summary Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6" id="summary-cards">
            <!-- Populated by JS -->
        </div>

        <!-- Loading -->
        <div id="loading-state" class="text-center py-20">
            <svg class="w-10 h-10 text-primary animate-spin mx-auto mb-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <p class="text-ink-muted">Carregando transações...</p>
        </div>

        <!-- Table -->
        <div id="table-content" class="hidden">
            <div class="bg-white rounded-xl shadow-card border border-border overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-surface/50 text-left text-ink-muted border-b border-border">
                                <th class="px-4 py-3 font-medium">Proposta</th>
                                <th class="px-4 py-3 font-medium">Cliente</th>
                                <th class="px-4 py-3 font-medium">Valor</th>
                                <th class="px-4 py-3 font-medium">Taxa</th>
                                <th class="px-4 py-3 font-medium">Líquido</th>
                                <th class="px-4 py-3 font-medium">Método</th>
                                <th class="px-4 py-3 font-medium">Status</th>
                                <th class="px-4 py-3 font-medium">Data</th>
                            </tr>
                        </thead>
                        <tbody id="transactions-table-body"></tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div id="pagination" class="flex items-center justify-between mt-4 text-sm text-ink-muted"></div>

            <!-- Empty State -->
            <div id="empty-state" class="hidden text-center py-20">
                <svg class="w-16 h-16 text-ink-muted/30 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-ink-secondary font-medium mb-1">Nenhuma transação encontrada</p>
                <p class="text-ink-muted text-sm">As transações aparecerão aqui quando seus clientes realizarem pagamentos.</p>
            </div>
        </div>
    </main>
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
        const token = '<?= getToken() ?>';
        if (!token) { window.location.href = '?page=login'; return; }

        const status = document.getElementById('status-filter').value;
        const params = new URLSearchParams({ page: currentPage, perPage: 20 });
        if (status) params.set('status', status);

        const response = await fetch(`/api/v1/transactions?${params}`, {
            headers: { 'Authorization': 'Bearer ' + token }
        });

        if (!response.ok) {
            if (response.status === 401) { window.location.href = '?page=login'; return; }
            throw new Error('Erro ao carregar');
        }

        const data = await response.json();
        renderSummary(data.summary, data.totals);
        renderTable(data);
    } catch (err) {
        console.error('[Transactions]', err.message);
        loading.classList.add('hidden');
    }
}

function renderSummary(summary, totals) {
    const container = document.getElementById('summary-cards');

    if (!totals || totals.count === 0) {
        container.innerHTML = `
            <div class="bg-white rounded-xl p-4 sm:p-5 shadow-card border border-border col-span-2 lg:col-span-4">
                <p class="text-ink-muted text-sm text-center">Nenhuma transação registrada</p>
            </div>
        `;
        return;
    }

    const colors = { completed: 'success', pending: 'warning', processing: 'info', refunded: 'danger' };

    container.innerHTML = `
        <div class="bg-white rounded-xl p-4 sm:p-5 shadow-card border border-border">
            <p class="text-xs text-ink-muted font-medium uppercase tracking-wide">Total</p>
            <p class="text-h2 text-ink mt-1">${totals.amount}</p>
            <p class="text-xs text-ink-muted">${totals.count} transações</p>
        </div>
        <div class="bg-white rounded-xl p-4 sm:p-5 shadow-card border border-border">
            <p class="text-xs text-ink-muted font-medium uppercase tracking-wide">Taxas</p>
            <p class="text-h2 text-ink mt-1">${totals.fees}</p>
            <p class="text-xs text-ink-muted">Total descontado</p>
        </div>
        <div class="bg-white rounded-xl p-4 sm:p-5 shadow-card border border-border">
            <p class="text-xs text-ink-muted font-medium uppercase tracking-wide">Líquido</p>
            <p class="text-h2 text-success mt-1">${totals.net}</p>
            <p class="text-xs text-ink-muted">Valor recebido</p>
        </div>
        <div class="bg-white rounded-xl p-4 sm:p-5 shadow-card border border-border">
            <p class="text-xs text-ink-muted font-medium uppercase tracking-wide">Por Status</p>
            <div class="mt-2 space-y-1">
                ${summary.map(s => `
                    <div class="flex items-center justify-between text-xs">
                        <span class="${s.info.class} inline-flex px-2 py-0.5 rounded-full font-medium">${s.info.label}</span>
                        <span class="text-ink">${s.count}x ${s.total}</span>
                    </div>
                `).join('')}
            </div>
        </div>
    `;
}

function renderTable(data) {
    const tbody = document.getElementById('transactions-table-body');
    const content = document.getElementById('table-content');
    const empty = document.getElementById('empty-state');
    const pag = document.getElementById('pagination');

    document.getElementById('loading-state').classList.add('hidden');

    if (!data.transactions || data.transactions.length === 0) {
        content.classList.add('hidden');
        empty.classList.remove('hidden');
        return;
    }

    content.classList.remove('hidden');
    empty.classList.add('hidden');

    tbody.innerHTML = data.transactions.map(tx => `
        <tr class="border-b border-border/50 hover:bg-surface/30 transition-colors">
            <td class="px-4 py-3">
                <span class="text-ink font-medium text-sm">${tx.proposalNumber || '—'}</span>
            </td>
            <td class="px-4 py-3 text-ink-secondary">${tx.clientName || '—'}</td>
            <td class="px-4 py-3 text-ink font-medium">${tx.amount}</td>
            <td class="px-4 py-3 text-ink-muted text-xs">${tx.fee}</td>
            <td class="px-4 py-3 text-success font-medium">${tx.netAmount}</td>
            <td class="px-4 py-3 text-ink-secondary text-xs">${tx.paymentMethod}</td>
            <td class="px-4 py-3">
                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium ${tx.statusInfo.class}">
                    ${tx.statusInfo.label}
                </span>
            </td>
            <td class="px-4 py-3 text-ink-muted text-xs whitespace-nowrap">${tx.paidAt !== '—' ? tx.paidAt : tx.createdAt}</td>
        </tr>
    `).join('');

    const { page, totalPages, total } = data.pagination;
    pag.innerHTML = `
        <span>${total} transações</span>
        <div class="flex items-center gap-2">
            <button onclick="goToPage(${page - 1})" class="px-3 py-1 border border-border rounded-lg hover:bg-surface transition-colors text-sm ${page <= 1 ? 'opacity-50 cursor-not-allowed' : ''}" ${page <= 1 ? 'disabled' : ''}>Anterior</button>
            <span class="text-ink-muted">Página ${page} de ${totalPages || 1}</span>
            <button onclick="goToPage(${page + 1})" class="px-3 py-1 border border-border rounded-lg hover:bg-surface transition-colors text-sm ${page >= totalPages ? 'opacity-50 cursor-not-allowed' : ''}" ${page >= totalPages ? 'disabled' : ''}>Próxima</button>
        </div>
    `;
}

function goToPage(p) { currentPage = p; loadTransactions(); }

document.addEventListener('DOMContentLoaded', loadTransactions);
</script>
