<!-- ═══════════════════════════════════════════════════════════════
     templates/admin-dashboard.php — Admin Dashboard (Epic 7 — Story 7.1)
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
    <?php $currentPage = 'admin-dashboard'; require_once __DIR__ . '/partials/admin-sidebar.php'; ?>

    <!-- Main Content -->
    <div class="ml-64 min-h-screen flex flex-col flex-1">
        <?php $pageTitle = 'Dashboard Administrativo'; $pageSubtitle = 'Visão geral da plataforma'; require __DIR__ . '/partials/admin-topbar.php'; ?>

        <!-- Dashboard Content -->
        <main class="flex-1 p-6">
            <!-- Loading State -->
            <div id="loading-state" class="text-center py-20">
                <svg class="w-10 h-10 text-primary animate-spin mx-auto mb-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <p class="text-ink-muted">Carregando KPIs...</p>
            </div>

            <!-- Dashboard Data (hidden until loaded) -->
            <div id="dashboard-content" class="hidden">
                <!-- KPI Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8" id="kpi-grid">
                    <!-- Populated by JS -->
                </div>

                <!-- Two Column: Chart + Recent Transactions -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Plan Distribution & Monthly Growth -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Revenue Chart -->
                        <div class="bg-white rounded-xl shadow-card border border-border p-6 hover:shadow-lg transition-shadow duration-200 hover:border-primary/20">
                            <h3 class="text-h3 text-ink mb-4">Receita Mensal (últimos 6 meses)</h3>
                            <div class="relative">
                                <canvas id="revenueChart" height="200"></canvas>
                                <!-- Skeleton -->
                                <div id="skel-revenueChart" class="absolute inset-0 flex items-end gap-1.5 p-4 animate-pulse">
                                    <div class="flex-1 h-3/4 bg-ink-muted/10 rounded-t"></div>
                                    <div class="flex-1 h-1/2 bg-ink-muted/10 rounded-t"></div>
                                    <div class="flex-1 h-2/3 bg-ink-muted/10 rounded-t"></div>
                                    <div class="flex-1 h-4/5 bg-ink-muted/10 rounded-t"></div>
                                    <div class="flex-1 h-3/5 bg-ink-muted/10 rounded-t"></div>
                                    <div class="flex-1 h-7/10 bg-ink-muted/10 rounded-t"></div>
                                </div>
                            </div>
                        </div>
                        <!-- Monthly Growth -->
                        <div class="bg-white rounded-xl shadow-card border border-border p-6 hover:shadow-lg transition-shadow duration-200 hover:border-primary/20">
                            <h3 class="text-h3 text-ink mb-4">Novos Tenants por Mês</h3>
                            <div class="relative">
                                <canvas id="growthChart" height="150"></canvas>
                                <!-- Skeleton -->
                                <div id="skel-growthChart" class="absolute inset-0 flex items-end gap-1.5 p-4 animate-pulse">
                                    <div class="flex-1 h-2/5 bg-ink-muted/10 rounded-t"></div>
                                    <div class="flex-1 h-3/5 bg-ink-muted/10 rounded-t"></div>
                                    <div class="flex-1 h-1/2 bg-ink-muted/10 rounded-t"></div>
                                    <div class="flex-1 h-4/5 bg-ink-muted/10 rounded-t"></div>
                                    <div class="flex-1 h-3/5 bg-ink-muted/10 rounded-t"></div>
                                    <div class="flex-1 h-7/10 bg-ink-muted/10 rounded-t"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Right Column -->
                    <div class="space-y-6">
                        <!-- Plan Distribution -->
                        <div class="bg-white rounded-xl shadow-card border border-border p-6 hover:shadow-lg transition-shadow duration-200 hover:border-primary/20">
                            <h3 class="text-h3 text-ink mb-4">Planos</h3>
                            <div class="relative flex items-center justify-center">
                                <canvas id="planChart" height="200"></canvas>
                                <!-- Skeleton -->
                                <div id="skel-planChart" class="absolute inset-0 flex items-center justify-center p-4 animate-pulse">
                                    <div class="w-32 h-32 rounded-full bg-ink-muted/10"></div>
                                </div>
                            </div>
                        </div>
                        <!-- Proposals by Status -->
                        <div class="bg-white rounded-xl shadow-card border border-border p-6 hover:shadow-lg transition-shadow duration-200 hover:border-primary/20">
                            <h3 class="text-h3 text-ink mb-4">Propostas</h3>
                            <div class="relative flex items-center justify-center">
                                <canvas id="proposalChart" height="200"></canvas>
                                <!-- Skeleton -->
                                <div id="skel-proposalChart" class="absolute inset-0 flex items-center justify-center p-4 animate-pulse">
                                    <div class="w-32 h-32 rounded-full bg-ink-muted/10"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions Table -->
                <div class="bg-white rounded-xl shadow-card border border-border p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-h3 text-ink">Transações Recentes</h3>
                        <a href="?page=admin-financeiro" class="group inline-flex items-center gap-1 text-sm text-primary hover:text-primary-600 transition-colors">Ver todas <span class="group-hover:translate-x-0.5 transition-transform duration-200">→</span></a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-ink-muted border-b border-border">
                                    <th class="pb-3 font-medium">Tenant</th>
                                    <th class="pb-3 font-medium">Proposta</th>
                                    <th class="pb-3 font-medium">Valor</th>
                                    <th class="pb-3 font-medium">Status</th>
                                    <th class="pb-3 font-medium">Data</th>
                                </tr>
                            </thead>
                            <tbody id="transactions-table-body">
                                <!-- Populated by JS -->
                            </tbody>
                        </table>
                    </div>
                    <div id="transactions-empty" class="hidden text-center py-8 text-ink-muted">
                        Nenhuma transação recente.
                    </div>
                </div>
            </div>

            <!-- Error State -->
            <div id="error-state" class="hidden text-center py-20">
                <svg class="w-16 h-16 text-danger/30 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <p class="text-ink-secondary mb-2">Erro ao carregar dashboard</p>
                <button onclick="loadDashboard()" class="text-primary hover:text-primary-600 text-sm transition-all duration-150 hover:scale-105 active:scale-95">Tentar novamente</button>
            </div>
        </main>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

<script>
// ── KPI Card Renderer ────────────────────────────────────
function renderKPICards(kpis) {
    const grid = document.getElementById('kpi-grid');
    const cards = [
        { label: 'Tenants Ativos', value: kpis.tenants.active, icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', color: 'primary', subtitle: `${kpis.tenants.suspended} suspensos` },
        { label: 'Usuários', value: kpis.users, icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857', color: 'info', subtitle: 'Total ativos' },
        { label: 'Receita (mês)', value: kpis.revenue.current, icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', color: 'success', subtitle: `📈 ${kpis.revenue.growth} vs mês anterior` },
        { label: 'Clientes', value: kpis.clients, icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', color: 'warning', subtitle: `${kpis.pendingProposals} propostas pendentes` },
    ];

    grid.innerHTML = cards.map((card, i) => `
        <div class="bg-white rounded-xl p-6 shadow-card border border-border animate-fade-in group hover:shadow-xl hover:-translate-y-1 transition-all duration-200 cursor-default" style="animation-delay: ${i * 0.05}s">
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-medium text-ink-secondary">${card.label}</span>
                <div class="w-10 h-10 bg-${card.color}/10 rounded-xl flex items-center justify-center group-hover:scale-110 group-hover:bg-${card.color}/20 transition-all duration-200">
                    <svg class="w-5 h-5 text-${card.color}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="${card.icon}"/>
                    </svg>
                </div>
            </div>
            <p class="text-h2 text-ink">${card.value}</p>
            <p class="text-xs text-ink-muted mt-1">${card.subtitle}</p>
        </div>
    `).join('');
}

// ── Charts ────────────────────────────────────────────────
let charts = {};

function renderCharts(data) {
    // ── Hide skeletons ────────────────────────────────────
    ['skel-revenueChart', 'skel-growthChart', 'skel-planChart', 'skel-proposalChart'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.classList.add('hidden');
    });

    const commonOpts = {
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { display: false } },
    };

    // Revenue Chart
    const revCtx = document.getElementById('revenueChart').getContext('2d');
    if (charts.revenue) charts.revenue.destroy();
    charts.revenue = new Chart(revCtx, {
        type: 'bar',
        data: {
            labels: data.monthlyRevenue.map(m => m.month),
            datasets: [
                { label: 'Receita', data: data.monthlyRevenue.map(m => m.revenue), backgroundColor: '#10B981', borderRadius: 6 },
                { label: 'Taxas', data: data.monthlyRevenue.map(m => m.fees), backgroundColor: '#F59E0B', borderRadius: 6 },
            ],
        },
        options: {
            ...commonOpts,
            plugins: { legend: { display: true, position: 'top' } },
            scales: { y: { beginAtZero: true, ticks: { callback: v => 'R$ ' + v.toLocaleString('pt-BR') } } },
        },
    });

    // Growth Chart
    const growCtx = document.getElementById('growthChart').getContext('2d');
    if (charts.growth) charts.growth.destroy();
    charts.growth = new Chart(growCtx, {
        type: 'line',
        data: {
            labels: data.monthlyGrowth.map(m => m.month),
            datasets: [{ label: 'Novos Tenants', data: data.monthlyGrowth.map(m => m.newTenants), borderColor: '#0284C7', backgroundColor: '#0284C720', fill: true, tension: 0.4 }],
        },
        options: { ...commonOpts, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } },
    });

    // Plan Chart (Doughnut)
    const planCtx = document.getElementById('planChart').getContext('2d');
    if (charts.plans) charts.plans.destroy();
    const planColors = { free: '#94A3B8', basic: '#3B82F6', pro: '#8B5CF6', enterprise: '#F59E0B' };
    charts.plans = new Chart(planCtx, {
        type: 'doughnut',
        data: {
            labels: data.planDistribution.map(p => p.label),
            datasets: [{ data: data.planDistribution.map(p => p.value), backgroundColor: data.planDistribution.map(p => planColors[p.label] || '#94A3B8') }],
        },
        options: { ...commonOpts, plugins: { legend: { display: true, position: 'bottom' } }, cutout: '65%' },
    });

    // Proposal Chart
    const propCtx = document.getElementById('proposalChart').getContext('2d');
    if (charts.proposals) charts.proposals.destroy();
    const statusColors = { draft: '#94A3B8', sent: '#3B82F6', viewed: '#F59E0B', accepted: '#10B981', rejected: '#EF4444', cancelled: '#6B7280' };
    charts.proposals = new Chart(propCtx, {
        type: 'doughnut',
        data: {
            labels: data.proposalsByStatus.map(p => p.status),
            datasets: [{ data: data.proposalsByStatus.map(p => p.count), backgroundColor: data.proposalsByStatus.map(p => statusColors[p.status] || '#94A3B8') }],
        },
        options: { ...commonOpts, plugins: { legend: { display: true, position: 'bottom' } }, cutout: '65%' },
    });
}

// ── Transactions Table ────────────────────────────────────
function renderTransactions(transactions) {
    const tbody = document.getElementById('transactions-table-body');
    const empty = document.getElementById('transactions-empty');

    if (!transactions || transactions.length === 0) {
        tbody.innerHTML = '';
        empty.classList.remove('hidden');
        return;
    }

    empty.classList.add('hidden');
    const statusBadge = {
        completed: 'bg-success/10 text-success',
        pending: 'bg-warning/10 text-warning',
        processing: 'bg-info/10 text-info',
        refunded: 'bg-danger/10 text-danger',
        cancelled: 'bg-ink-muted/10 text-ink-muted',
    };

    tbody.innerHTML = transactions.map(tx => `
        <tr class="border-b border-border/50 hover:bg-surface/30 transition-colors">
            <td class="py-3 pr-4 text-ink font-medium">${tx.tenant}</td>
            <td class="py-3 pr-4 text-ink-secondary">${tx.proposal || '—'}</td>
            <td class="py-3 pr-4 text-ink font-medium">${tx.amount}</td>
            <td class="py-3 pr-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusBadge[tx.status] || 'bg-ink-muted/10 text-ink-muted'}">
                    ${tx.status}
                </span>
            </td>
            <td class="py-3 text-ink-muted text-xs">${tx.paid_at || tx.created_at}</td>
        </tr>
    `).join('');
}

// ── Load Dashboard ────────────────────────────────────────
async function loadDashboard() {
    const loading = document.getElementById('loading-state');
    const content = document.getElementById('dashboard-content');
    const error = document.getElementById('error-state');

    loading.classList.remove('hidden');
    content.classList.add('hidden');
    error.classList.add('hidden');

    try {
        const token = '<?= $token ?>';
        const response = await fetch('/api/v1/admin/dashboard', {
            headers: { 'Authorization': 'Bearer ' + token }
        });

        if (!response.ok) {
            if (response.status === 401) { window.location.href = '?page=admin-login'; return; }
            throw new Error('Erro ao carregar dashboard');
        }

        const data = await response.json();

        renderKPICards(data.kpis);
        renderCharts(data.charts);
        renderTransactions(data.recentTransactions);

        loading.classList.add('hidden');
        content.classList.remove('hidden');
    } catch (err) {
        loading.classList.add('hidden');
        error.classList.remove('hidden');
        console.error('[Admin Dashboard]', err.message);
    }
}

// ── Init ──────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', loadDashboard);
</script>
