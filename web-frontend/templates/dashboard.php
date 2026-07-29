<!-- ═══════════════════════════════════════════════════════════════
     templates/dashboard.php — Dashboard (Prestador)
     ═══════════════════════════════════════════════════════════ -->
<?php $currentPage = 'dashboard'; require __DIR__ . '/partials/sidebar.php'; ?>

<!-- Main Content Area -->
<div class="md:ml-64 min-h-screen flex flex-col bg-gradient-to-b from-white to-surface/50">
    <?php $pageTitle = 'Dashboard'; $pageSubtitle = 'Visão geral do seu negócio'; require __DIR__ . '/partials/topbar.php'; ?>

    <!-- Dashboard Content -->
    <main class="flex-1 p-6">
        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Clientes -->
            <div class="bg-white rounded-xl p-6 shadow-card border border-border animate-fade-in hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200 group">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-medium text-ink-secondary">Clientes</span>
                    <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center group-hover:scale-110 group-hover:bg-primary/15 transition-all duration-200">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                </div>
                <!-- Skeleton -->
                <div id="skel-clients" class="skeleton-value">
                    <div class="h-8 w-20 bg-surface animate-pulse rounded-lg"></div>
                </div>
                <!-- Value -->
                <p class="text-h2 text-ink hidden" id="kpi-clients">—</p>
                <p class="text-xs text-ink-muted mt-1">Total cadastrados</p>
            </div>

            <!-- Propostas -->
            <div class="bg-white rounded-xl p-6 shadow-card border border-border animate-fade-in hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200 group" style="animation-delay: 0.05s">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-medium text-ink-secondary">Propostas</span>
                    <div class="w-10 h-10 bg-info/10 rounded-xl flex items-center justify-center group-hover:scale-110 group-hover:bg-info/15 transition-all duration-200">
                        <svg class="w-5 h-5 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
                <!-- Skeleton -->
                <div id="skel-proposals" class="skeleton-value">
                    <div class="h-8 w-16 bg-surface animate-pulse rounded-lg"></div>
                </div>
                <!-- Value -->
                <p class="text-h2 text-ink hidden" id="kpi-proposals">—</p>
                <p class="text-xs text-ink-muted mt-1">Este mês</p>
            </div>

            <!-- Faturamento -->
            <div class="bg-white rounded-xl p-6 shadow-card border border-border animate-fade-in hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200 group" style="animation-delay: 0.1s">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-medium text-ink-secondary">Faturamento</span>
                    <div class="w-10 h-10 bg-success/10 rounded-xl flex items-center justify-center group-hover:scale-110 group-hover:bg-success/15 transition-all duration-200">
                        <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <!-- Skeleton -->
                <div id="skel-revenue" class="skeleton-value">
                    <div class="h-8 w-24 bg-surface animate-pulse rounded-lg"></div>
                </div>
                <!-- Value -->
                <p class="text-h2 text-ink hidden" id="kpi-revenue">—</p>
                <p class="text-xs text-ink-muted mt-1">Este mês</p>
            </div>

            <!-- Pendentes -->
            <div class="bg-white rounded-xl p-6 shadow-card border border-border animate-fade-in hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200 group" style="animation-delay: 0.15s">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-medium text-ink-secondary">Pendentes</span>
                    <div class="w-10 h-10 bg-warning/10 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                        <svg class="w-5 h-5 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <!-- Skeleton -->
                <div id="skel-pending" class="skeleton-value">
                    <div class="h-8 w-16 bg-surface animate-pulse rounded-lg"></div>
                </div>
                <!-- Value -->
                <p class="text-h2 text-ink hidden" id="kpi-pending">—</p>
                <p class="text-xs text-ink-muted mt-1">Aguardando ação</p>
            </div>
        </div>

        <!-- Two Columns: Chart + Follow-up -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Revenue Chart -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-card border border-border p-6 animate-fade-in">
                <h3 class="text-h3 text-ink mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Receita Mensal
                </h3>
                <div class="relative">
                    <canvas id="revenueChart" height="180"></canvas>
                    <div id="skel-revenueChart" class="absolute inset-0 flex items-end gap-1.5 p-4 animate-pulse">
                        <div class="flex-1 h-2/5 bg-ink-muted/10 rounded-t"></div>
                        <div class="flex-1 h-3/5 bg-ink-muted/10 rounded-t"></div>
                        <div class="flex-1 h-1/2 bg-ink-muted/10 rounded-t"></div>
                        <div class="flex-1 h-4/5 bg-ink-muted/10 rounded-t"></div>
                        <div class="flex-1 h-3/5 bg-ink-muted/10 rounded-t"></div>
                        <div class="flex-1 h-2/3 bg-ink-muted/10 rounded-t"></div>
                    </div>
                </div>
            </div>

            <!-- Follow-up -->
            <div class="bg-white rounded-xl shadow-card border border-border p-6 animate-fade-in">
                <h3 class="text-h3 text-ink mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Follow-up
                    <span id="followup-count" class="ml-auto text-xs bg-warning/10 text-warning px-2 py-0.5 rounded-full font-medium">0</span>
                </h3>
                <div id="followup-list" class="space-y-2 max-h-[360px] overflow-y-auto">
                    <div id="followup-empty" class="text-center py-8">
                        <svg class="w-10 h-10 text-ink-muted/20 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-xs text-ink-muted">Nenhum follow-up pendente</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Feed -->
        <div class="bg-white rounded-xl shadow-card border border-border p-6 animate-fade-in">
            <h3 class="text-h3 text-ink mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
                Atividades Recentes
            </h3>
            <div id="activity-feed" class="space-y-3">
                <!-- Empty State (default, shown when no activities) -->
                <div id="activity-empty" class="text-center py-10">
                    <svg class="w-16 h-16 text-ink-muted/20 mx-auto mb-4 animate-float" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                    <p class="text-ink-muted mb-1">Nenhuma atividade recente</p>
                    <p class="text-xs text-ink-muted/60">Crie sua primeira proposta ou cadastre um cliente para começar.</p>
                </div>
            </div>
        </div>
    </main>
</div>

<style>
    /* ── Empty state animation ────────────────────────────── */
    .animate-float {
        animation: float 3s ease-in-out infinite;
    }
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-6px); }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
// ── Helper: Atualizar KPI com skeleton ──────────────────
function setKpiValue(elementId, skeletonId, value) {
    const el = document.getElementById(elementId);
    const skel = document.getElementById(skeletonId);
    if (el) {
        el.textContent = value ?? '—';
        el.classList.remove('hidden');
    }
    if (skel) {
        skel.classList.add('hidden');
    }
}

// ── Helper: Mostrar erro no KPI (falha na API) ──────────
function showKpiError(skeletonId) {
    const skel = document.getElementById(skeletonId);
    if (skel) {
        skel.innerHTML = '<span class="text-xs text-danger inline-flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg> Erro</span>';
    }
}

let revenueChart = null;

function formatMoney(value) {
    return 'R$ ' + parseFloat(value || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

// ── Carregar KPIs via API ────────────────────────────────
(async function loadDashboard() {
    try {
        const token = '<?= getToken() ?>';
        if (!token) return;

        const response = await fetch('/api/v1/dashboard', {
            headers: { 'Authorization': 'Bearer ' + token }
        });

        if (!response.ok) {
            if (response.status === 401) {
                window.location.href = '?page=login';
                return;
            }
            throw new Error('Erro ao carregar dashboard');
        }

        const data = await response.json();

        // ── Substituir skeletons pelos valores reais ────────
        setKpiValue('kpi-clients', 'skel-clients', data.clients);
        setKpiValue('kpi-proposals', 'skel-proposals', data.proposals);
        setKpiValue('kpi-revenue', 'skel-revenue', data.revenue);
        setKpiValue('kpi-pending', 'skel-pending', data.pending);

        // ── Atividades Recentes ─────────────────────────────
        const feed = document.getElementById('activity-feed');
        const empty = document.getElementById('activity-empty');

        if (data.activities?.length > 0) {
            if (empty) empty.remove();
            feed.innerHTML = data.activities.map(a => `
                <div class="flex items-center gap-4 p-3 rounded-lg hover:bg-surface/50 hover:-translate-x-0.5 transition-all duration-200 cursor-default border border-transparent hover:border-border/50">
                    <div class="w-2 h-2 rounded-full ${a.type === 'proposal' ? 'bg-info' : a.type === 'client' ? 'bg-success' : 'bg-warning'}"></div>
                    <div class="flex-1">
                        <p class="text-sm text-ink">${escHtml(a.description)}</p>
                        <p class="text-xs text-ink-muted">${a.time}</p>
                    </div>
                </div>
            `).join('');
        }
    } catch (err) {
        console.warn('[Dashboard] Erro:', err.message);
        showKpiError('skel-clients');
        showKpiError('skel-proposals');
        showKpiError('skel-revenue');
        showKpiError('skel-pending');
    }
})();

// ── Carregar Gráfico + Follow-up ─────────────────────────
(async function loadChartAndFollowup() {
    const token = '<?= getToken() ?>';
    if (!token) return;

    try {
        // Chart
        const chartResp = await fetch('/api/v1/dashboard/chart', {
            headers: { 'Authorization': 'Bearer ' + token }
        });
        if (chartResp.ok) {
            const chartData = await chartResp.json();
            document.getElementById('skel-revenueChart').classList.add('hidden');

            if (chartData.months?.length > 0) {
                const labels = chartData.months.map(m => {
                    const [y, mo] = m.month.split('-');
                    const months = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];
                    return months[parseInt(mo) - 1] + '/' + y.slice(2);
                });
                const values = chartData.months.map(m => m.revenue);

                if (revenueChart) revenueChart.destroy();
                const ctx = document.getElementById('revenueChart').getContext('2d');
                revenueChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Receita',
                            data: values,
                            backgroundColor: 'rgba(16, 185, 129, 0.2)',
                            borderColor: '#10B981',
                            borderWidth: 2,
                            borderRadius: 4,
                            borderSkipped: false,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => formatMoney(ctx.raw),
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: (v) => 'R$' + v.toFixed(0),
                                },
                                grid: { color: 'rgba(0,0,0,0.05)' },
                            },
                            x: {
                                grid: { display: false },
                            }
                        }
                    }
                });
            }
        }
    } catch (err) { console.warn('[Chart]', err.message); }

    try {
        // Follow-up
        const followResp = await fetch('/api/v1/dashboard/followup', {
            headers: { 'Authorization': 'Bearer ' + token }
        });
        if (followResp.ok) {
            const followData = await followResp.json();
            const list = document.getElementById('followup-list');
            const empty = document.getElementById('followup-empty');
            const count = document.getElementById('followup-count');

            if (followData.proposals?.length > 0) {
                if (empty) empty.remove();
                count.textContent = followData.proposals.length;
                list.innerHTML = followData.proposals.map(p => {
                    const waLink = p.client_whatsapp
                        ? `https://wa.me/55${p.client_whatsapp.replace(/\D/g, '').replace(/^55/, '')}?text=${encodeURIComponent('Olá! Vim do ServiceSaaS para dar um feedback sobre a proposta ' + p.number + ' - ' + p.title)}`
                        : null;
                    return `
                    <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-surface/50 transition-colors border border-transparent hover:border-border/50">
                        <div class="w-2 h-2 rounded-full mt-1.5 ${p.status === 'sent' ? 'bg-blue-400' : 'bg-purple-400'} flex-shrink-0"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-ink font-medium truncate">${escHtml(p.title)}</p>
                            <p class="text-xs text-ink-muted">${escHtml(p.client_name)} • ${p.total_amount} • ${p.hours_ago}</p>
                        </div>
                        ${waLink ? `<a href="${waLink}" target="_blank" class="flex-shrink-0 w-7 h-7 rounded-lg flex items-center justify-center text-whatsapp hover:bg-whatsapp/10 transition-colors" title="WhatsApp"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg></a>` : ''}
                    </div>`;
                }).join('');
            }
        }
    } catch (err) { console.warn('[Follow-up]', err.message); }
})();

// ── Helper: Escape HTML ──────────────────────────────────
function escHtml(str) {
    if (!str) return '';
    return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
}

// ── Saudação temporal ────────────────────────────────────
(function setGreeting() {
    const h = new Date().getHours();
    let greeting = 'Dashboard';
    if (h >= 5 && h < 12) greeting = '🌅 Bom dia';
    else if (h >= 12 && h < 18) greeting = '☀️ Boa tarde';
    else greeting = '🌙 Boa noite';
    const el = document.getElementById('greeting-header');
    if (el) {
        const name = '<?= htmlspecialchars(getUser()['name'] ?? '') ?>';
        el.textContent = name ? `${greeting}, ${name.split(' ')[0]}!` : greeting;
    }
})();
</script>
