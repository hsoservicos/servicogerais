<!-- templates/admin-relatorios.php — Admin Relatórios (Epic 7 — Story 7.3) -->
<?php
$token = getToken();
$user = getUser();
if (!$token || ($user['role'] ?? '') !== 'super_admin') {
    header('Location: ?page=admin-login');
    exit;
}
?>
<div class="min-h-screen bg-surface flex">
    <?php $currentPage = 'admin-relatorios'; require_once __DIR__ . '/partials/admin-sidebar.php'; ?>
    <div class="ml-64 min-h-screen flex flex-col flex-1">
        <?php
        $pageTitle = 'Relatórios';
        $pageSubtitle = 'Relatório financeiro da plataforma';
        require __DIR__ . '/partials/admin-topbar.php';
        ?>
        <main class="flex-1 p-6">
            <!-- Filters -->
            <div class="bg-white rounded-xl shadow-card border border-border p-4 mb-6 flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-xs font-medium text-ink-muted mb-1">Data Início</label>
                    <input type="date" id="filter-start" class="px-3 py-2 rounded-lg border border-border bg-surface text-ink text-sm focus:outline-none focus:ring-2 focus:ring-primary/30">
                </div>
                <div>
                    <label class="block text-xs font-medium text-ink-muted mb-1">Data Fim</label>
                    <input type="date" id="filter-end" class="px-3 py-2 rounded-lg border border-border bg-surface text-ink text-sm focus:outline-none focus:ring-2 focus:ring-primary/30">
                </div>
                <button onclick="loadReport()" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-600 transition-colors text-sm font-medium flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Buscar
                </button>
                <button onclick="exportCSV()" class="px-4 py-2 border border-border rounded-lg text-ink-secondary hover:bg-surface transition-colors text-sm font-medium flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Exportar CSV
                </button>
            </div>

            <div id="loading-state" class="text-center py-20">
                <svg class="w-10 h-10 text-primary animate-spin mx-auto mb-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                <p class="text-ink-muted">Selecione um período e clique em "Buscar"</p>
            </div>
            <div id="report-content" class="hidden space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4" id="summary-cards"></div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white rounded-xl shadow-card border border-border p-6">
                        <h3 class="text-h3 text-ink mb-4">Receita por Plano</h3>
                        <div id="by-plan-list" class="space-y-3"></div>
                    </div>
                    <div class="bg-white rounded-xl shadow-card border border-border p-6">
                        <h3 class="text-h3 text-ink mb-4">Total por Dia</h3>
                        <div id="daily-list" class="space-y-1 max-h-[400px] overflow-y-auto"></div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
let currentReportData = null;

async function loadReport() {
    const loading = document.getElementById('loading-state');
    const content = document.getElementById('report-content');
    loading.classList.remove('hidden'); content.classList.add('hidden');

    const start = document.getElementById('filter-start').value;
    const end = document.getElementById('filter-end').value;
    const params = new URLSearchParams();
    if (start) params.set('start_date', start);
    if (end) params.set('end_date', end);

    try {
        const resp = await fetch('/api/v1/admin/reports/financial?' + params, {
            headers: { 'Authorization': 'Bearer <?= $token ?>' }
        });
        if (!resp.ok) { if (resp.status === 401) { window.location.href = '?page=admin-login'; return; } throw new Error('Erro'); }
        const data = await resp.json();
        currentReportData = data;
        renderReport(data);
    } catch (err) { console.error('[Relatorios]', err.message); }
    finally { loading.classList.add('hidden'); }
}

function renderReport(data) {
    const content = document.getElementById('report-content');
    content.classList.remove('hidden');

    const t = data.totals;
    document.getElementById('summary-cards').innerHTML = `
        <div class="bg-white rounded-xl p-4 shadow-card border border-border">
            <p class="text-sm text-ink-secondary">Transações</p>
            <p class="text-h3 text-ink mt-1">${t.transactions}</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-card border border-border">
            <p class="text-sm text-ink-secondary">Receita Total</p>
            <p class="text-h3 text-success mt-1">${t.formatted.revenue}</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-card border border-border">
            <p class="text-sm text-ink-secondary">Taxas</p>
            <p class="text-h3 text-warning mt-1">${t.formatted.fees}</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-card border border-border">
            <p class="text-sm text-ink-secondary">Líquido</p>
            <p class="text-h3 text-primary mt-1">${t.formatted.net}</p>
        </div>
    `;

    document.getElementById('by-plan-list').innerHTML = data.byPlan?.length > 0
        ? data.byPlan.map(p => `
            <div class="flex items-center justify-between p-3 rounded-lg bg-surface/50 border border-border/50">
                <div>
                    <p class="text-sm font-medium text-ink">${p.plan}</p>
                    <p class="text-xs text-ink-muted">${p.count} transações</p>
                </div>
                <p class="text-sm font-bold text-ink">${p.formatted}</p>
            </div>
        `).join('')
        : '<p class="text-sm text-ink-muted text-center py-4">Nenhum dado no período</p>';

    document.getElementById('daily-list').innerHTML = data.daily?.length > 0
        ? data.daily.map(d => {
            const date = new Date(d.day + 'T12:00:00');
            return `
            <div class="flex items-center justify-between p-2 rounded hover:bg-surface/30 transition-colors text-sm">
                <span class="text-ink-secondary">${date.toLocaleDateString('pt-BR')}</span>
                <span class="text-ink font-medium">R$ ${d.revenue.toFixed(2).replace('.', ',')}</span>
                <span class="text-ink-muted text-xs">${d.transactions} tx</span>
            </div>`;
        }).join('')
        : '<p class="text-sm text-ink-muted text-center py-4">Nenhuma transação no período</p>';
}

function exportCSV() {
    const start = document.getElementById('filter-start').value;
    const end = document.getElementById('filter-end').value;
    const params = new URLSearchParams({ format: 'csv' });
    if (start) params.set('start_date', start);
    if (end) params.set('end_date', end);
    const url = '/api/v1/admin/reports/financial?' + params;
    const a = document.createElement('a');
    a.href = url;
    a.setAttribute('Authorization', 'Bearer <?= $token ?>');
    // Use download via fetch for auth headers
    fetch(url, { headers: { 'Authorization': 'Bearer <?= $token ?>' } })
        .then(r => r.blob())
        .then(blob => {
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'relatorio-financeiro.csv';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(link.href);
        })
        .catch(err => alert('Erro ao exportar CSV'));
}

// Set default dates (last 30 days)
(function() {
    const end = new Date();
    const start = new Date();
    start.setDate(start.getDate() - 30);
    document.getElementById('filter-end').value = end.toISOString().split('T')[0];
    document.getElementById('filter-start').value = start.toISOString().split('T')[0];
    loadReport();
})();
</script>
