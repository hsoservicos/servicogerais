<!-- ═══════════════════════════════════════════════════════════════
     templates/admin-audit.php — Admin Auditoria (Epic 7 — Story 7.4)
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
    <?php $currentPage = 'admin-audit'; require_once __DIR__ . '/partials/admin-sidebar.php'; ?>

    <!-- Main -->
    <div class="ml-64 min-h-screen flex flex-col flex-1">
        <?php
        $pageTitle = 'Auditoria';
        $pageSubtitle = 'Registro de todas as ações administrativas';
        $topbarExtra = '<select id="action-filter" onchange="loadAudit()" class="text-sm border border-border rounded-lg px-3 py-2 text-ink bg-white focus:outline-none focus:ring-2 focus:ring-primary"><option value="">Todas as ações</option></select><input type="date" id="start-date" onchange="loadAudit()" class="text-sm border border-border rounded-lg px-3 py-2 text-ink bg-white focus:outline-none focus:ring-2 focus:ring-primary"><span class="text-ink-muted text-xs">até</span><input type="date" id="end-date" onchange="loadAudit()" class="text-sm border border-border rounded-lg px-3 py-2 text-ink bg-white focus:outline-none focus:ring-2 focus:ring-primary">';
        require __DIR__ . '/partials/admin-topbar.php';
        ?>

        <main class="flex-1 p-6">
            <!-- Loading -->
            <div id="loading-state" class="text-center py-20">
                <svg class="w-10 h-10 text-primary animate-spin mx-auto mb-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <p class="text-ink-muted">Carregando logs de auditoria...</p>
            </div>

            <!-- Table -->
            <div id="table-content" class="hidden">
                <div class="bg-white rounded-xl shadow-card border border-border overflow-hidden hover:shadow-lg transition-shadow duration-200">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-surface/50 text-left text-ink-muted border-b border-border">
                                    <th class="px-4 py-3 font-medium">Data/Hora</th>
                                    <th class="px-4 py-3 font-medium">Admin</th>
                                    <th class="px-4 py-3 font-medium">Ação</th>
                                    <th class="px-4 py-3 font-medium">Alvo</th>
                                    <th class="px-4 py-3 font-medium">Detalhes</th>
                                    <th class="px-4 py-3 font-medium">IP</th>
                                </tr>
                            </thead>
                            <tbody id="audit-table-body"></tbody>
                        </table>
                    </div>
                </div>
                <div id="pagination" class="flex items-center justify-between mt-4 text-sm text-ink-muted"></div>
                <div id="empty-state" class="hidden text-center py-20">
                    <svg class="w-16 h-16 text-ink-muted/30 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-ink-secondary mb-2">Nenhum registro de auditoria encontrado</p>
                    <p class="text-ink-muted text-sm">As ações administrativas aparecerão aqui conforme forem executadas.</p>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
var currentPage = 1;
var filterActions = [];

async function loadAudit() {
    const loading = document.getElementById('loading-state');
    const content = document.getElementById('table-content');
    const empty = document.getElementById('empty-state');

    loading.classList.remove('hidden');
    content.classList.add('hidden');
    empty.classList.add('hidden');

    try {
        const token = '<?= $token ?>';
        const params = new URLSearchParams({ page: currentPage, perPage: 50 });

        const action = document.getElementById('action-filter').value;
        const startDate = document.getElementById('start-date').value;
        const endDate = document.getElementById('end-date').value;

        if (action) params.set('action', action);
        if (startDate) params.set('start_date', startDate);
        if (endDate) params.set('end_date', endDate);

        const response = await fetch(`/api/v1/admin/audit?${params}`, {
            headers: { 'Authorization': 'Bearer ' + token }
        });

        if (!response.ok) { if (response.status === 401) { window.location.href = '?page=admin-login'; return; } throw new Error('Erro'); }
        const data = await response.json();
        populateFilters(data.filters);
        renderTable(data);
    } catch (err) {
        console.error('[Audit]', err.message);
    } finally {
        loading.classList.add('hidden');
    }
}

function populateFilters(filters) {
    if (!filters?.actions) return;
    const select = document.getElementById('action-filter');
    if (select.options.length > 1) return; // Already populated

    filters.actions.forEach(action => {
        const opt = document.createElement('option');
        opt.value = action;
        opt.textContent = action.replace(/_/g, ' ');
        select.appendChild(opt);
    });
}

function renderTable(data) {
    const tbody = document.getElementById('audit-table-body');
    const content = document.getElementById('table-content');
    const empty = document.getElementById('empty-state');
    const pag = document.getElementById('pagination');

    if (!data.logs || data.logs.length === 0) {
        content.classList.add('hidden');
        empty.classList.remove('hidden');
        return;
    }

    content.classList.remove('hidden');
    empty.classList.add('hidden');

    const actionColors = {
        suspend: 'bg-warning/10 text-warning',
        activate: 'bg-success/10 text-success',
        refund: 'bg-danger/10 text-danger',
        update: 'bg-info/10 text-info',
        create: 'bg-primary/10 text-primary',
    };

    tbody.innerHTML = data.logs.map(log => {
        const actionClass = Object.entries(actionColors).find(([key]) => log.action.includes(key))?.[1] || 'bg-ink-muted/10 text-ink-muted';
        let details = '';
        try {
            const parsed = typeof log.details === 'string' ? JSON.parse(log.details) : log.details;
            details = parsed?.tenant_name || parsed?.proposal_number || JSON.stringify(parsed).substring(0, 60) || '—';
        } catch { details = '—'; }

        return `
            <tr class="border-b border-border/50 hover:bg-surface/30 hover:-translate-x-0.5 transition-all duration-200">
                <td class="px-4 py-3 text-ink-muted text-xs whitespace-nowrap">${log.created_at}</td>
                <td class="px-4 py-3">
                    <div>
                        <p class="text-ink font-medium text-sm">${log.admin}</p>
                        <p class="text-ink-muted text-xs">${log.admin_email || ''}</p>
                    </div>
                </td>
                <td class="px-4 py-3">
                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium ${actionClass}">${log.action}</span>
                </td>
                <td class="px-4 py-3 text-ink-secondary text-xs">
                    ${log.target.type}#${log.target.id || '—'}
                </td>
                <td class="px-4 py-3 text-ink-muted text-xs max-w-[200px] truncate" title="${details}">${details}</td>
                <td class="px-4 py-3 text-ink-muted text-xs font-mono">${log.ip || '—'}</td>
            </tr>
        `;
    }).join('');

    const { page, totalPages, total } = data.pagination;
    pag.innerHTML = `
        <span>${total} registros</span>
        <div class="flex items-center gap-2">
            <button onclick="goToPage(${page - 1})" class="px-3 py-1 border border-border rounded-lg hover:bg-surface active:scale-95 transition-all duration-200 ${page <= 1 ? 'opacity-50 cursor-not-allowed' : ''}" ${page <= 1 ? 'disabled' : ''}>Anterior</button>
            <span class="text-ink-muted">Página ${page} de ${totalPages}</span>
            <button onclick="goToPage(${page + 1})" class="px-3 py-1 border border-border rounded-lg hover:bg-surface active:scale-95 transition-all duration-200 ${page >= totalPages ? 'opacity-50 cursor-not-allowed' : ''}" ${page >= totalPages ? 'disabled' : ''}>Próxima</button>
        </div>
    `;
}

function goToPage(p) { currentPage = p; loadAudit(); }

document.addEventListener('DOMContentLoaded', loadAudit);
</script>
