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
        // ── Mostrar erro nos skeletons ──────────────────────
        // Remove skeleton-pulse class and show error
        showKpiError('skel-clients');
        showKpiError('skel-proposals');
        showKpiError('skel-revenue');
        showKpiError('skel-pending');
    }
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
