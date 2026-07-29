<!-- templates/admin-planos.php — Admin Planos (Epic 7 — Story 7.3) -->
<?php
$token = getToken();
$user = getUser();
if (!$token || ($user['role'] ?? '') !== 'super_admin') {
    header('Location: ?page=admin-login');
    exit;
}
?>
<div class="min-h-screen bg-surface flex">
    <?php $currentPage = 'admin-planos'; require_once __DIR__ . '/partials/admin-sidebar.php'; ?>
    <div class="ml-64 min-h-screen flex flex-col flex-1">
        <?php
        $pageTitle = 'Planos';
        $pageSubtitle = 'Gerenciar planos de assinatura';
        $topbarExtra = '<button onclick="openPlanModal()" class="bg-primary text-white font-medium px-4 py-2 rounded-lg hover:bg-primary-600 transition-all text-sm flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Novo Plano</button>';
        require __DIR__ . '/partials/admin-topbar.php';
        ?>
        <main class="flex-1 p-6">
            <div id="loading-state" class="text-center py-20">
                <svg class="w-10 h-10 text-primary animate-spin mx-auto mb-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                <p class="text-ink-muted">Carregando planos...</p>
            </div>
            <div id="table-content" class="hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6" id="plans-grid"></div>
            </div>
        </main>
    </div>
</div>

<!-- Modal Plan -->
<div id="plan-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-modal w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto p-6 animate-fade-in">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-h3 text-ink" id="modal-title">Novo Plano</h3>
            <button onclick="closePlanModal()" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-surface transition-colors">
                <svg class="w-5 h-5 text-ink-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="plan-form" class="space-y-4">
            <input type="hidden" id="plan-id">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-ink mb-1">Slug <span class="text-danger">*</span></label>
                    <input type="text" id="field-slug" required class="w-full px-3 py-2 rounded-lg border border-border bg-surface text-ink focus:outline-none focus:ring-2 focus:ring-primary/30 text-sm" placeholder="ex: premium">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink mb-1">Nome <span class="text-danger">*</span></label>
                    <input type="text" id="field-name" required class="w-full px-3 py-2 rounded-lg border border-border bg-surface text-ink focus:outline-none focus:ring-2 focus:ring-primary/30 text-sm" placeholder="ex: Premium">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-ink mb-1">Descrição</label>
                <textarea id="field-description" rows="2" class="w-full px-3 py-2 rounded-lg border border-border bg-surface text-ink focus:outline-none focus:ring-2 focus:ring-primary/30 text-sm resize-none"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-ink mb-1">Preço Mensal (R$)</label>
                    <input type="number" id="field-price" step="0.01" min="0" class="w-full px-3 py-2 rounded-lg border border-border bg-surface text-ink focus:outline-none focus:ring-2 focus:ring-primary/30 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink mb-1">Ordem</label>
                    <input type="number" id="field-sort" min="0" class="w-full px-3 py-2 rounded-lg border border-border bg-surface text-ink focus:outline-none focus:ring-2 focus:ring-primary/30 text-sm">
                </div>
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" id="field-active" checked class="w-4 h-4 rounded border-border text-primary focus:ring-primary">
                <label for="field-active" class="text-sm text-ink">Ativo</label>
            </div>
            <div class="flex gap-3 pt-4 border-t border-border">
                <button type="button" onclick="closePlanModal()" class="flex-1 px-4 py-2 border border-border rounded-lg text-ink-secondary hover:bg-surface transition-colors text-sm font-medium">Cancelar</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-600 transition-colors text-sm font-medium">Salvar</button>
            </div>
        </form>
    </div>
</div>

<script>
async function loadPlans() {
    const loading = document.getElementById('loading-state');
    const content = document.getElementById('table-content');
    loading.classList.remove('hidden'); content.classList.add('hidden');
    try {
        const resp = await fetch('/api/v1/admin/plans', { headers: { 'Authorization': 'Bearer <?= $token ?>' } });
        if (!resp.ok) { if (resp.status === 401) { window.location.href = '?page=admin-login'; return; } throw new Error('Erro'); }
        const data = await resp.json();
        renderPlans(data.plans);
    } catch (err) { console.error('[Planos]', err.message); }
    finally { loading.classList.add('hidden'); }
}

function renderPlans(plans) {
    const grid = document.getElementById('plans-grid');
    const content = document.getElementById('table-content');
    if (!plans || plans.length === 0) {
        grid.innerHTML = '<div class="col-span-full text-center py-20 text-ink-muted">Nenhum plano cadastrado</div>';
        content.classList.remove('hidden');
        return;
    }
    content.classList.remove('hidden');
    grid.innerHTML = plans.map(p => `
        <div class="bg-white rounded-xl shadow-card border border-border p-6 hover:shadow-lg transition-all duration-200 ${!p.active ? 'opacity-60' : ''}">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-mono text-ink-muted bg-surface px-2 py-0.5 rounded">${p.slug}</span>
                <span class="w-2.5 h-2.5 rounded-full ${p.active ? 'bg-success' : 'bg-ink-muted'}"></span>
            </div>
            <h3 class="text-h3 text-ink mb-1">${escHtml(p.name)}</h3>
            <p class="text-2xl font-bold text-primary mb-1">${p.price}</p>
            ${p.description ? `<p class="text-xs text-ink-muted mb-4">${escHtml(p.description)}</p>` : ''}
            <div class="text-xs text-ink-secondary space-y-1 mb-4">
                ${p.limits ? `
                    <div class="flex justify-between"><span>Clientes:</span><span class="font-medium text-ink">${p.limits.max_clients === -1 ? 'Ilimitado' : p.limits.max_clients}</span></div>
                    <div class="flex justify-between"><span>Propostas:</span><span class="font-medium text-ink">${p.limits.max_proposals === -1 ? 'Ilimitado' : p.limits.max_proposals}</span></div>
                    <div class="flex justify-between"><span>Usuários:</span><span class="font-medium text-ink">${p.limits.max_users || 1}</span></div>
                ` : ''}
            </div>
            ${p.features?.length ? `<div class="flex flex-wrap gap-1 mb-4">${p.features.map(f => `<span class="px-2 py-0.5 bg-primary/5 text-primary rounded text-xs">${f}</span>`).join('')}</div>` : ''}
            <div class="flex gap-2 pt-3 border-t border-border">
                <button onclick="editPlan(${p.id})" class="flex-1 px-3 py-1.5 border border-border rounded-lg text-ink-secondary hover:bg-surface text-xs font-medium transition-colors">Editar</button>
                <button onclick="togglePlan(${p.id}, ${p.active})" class="flex-1 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors ${p.active ? 'bg-warning/10 text-warning hover:bg-warning/20' : 'bg-success/10 text-success hover:bg-success/20'}">${p.active ? 'Desativar' : 'Ativar'}</button>
            </div>
        </div>
    `).join('');
}

function escHtml(s) { if (!s) return ''; return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

// Modal
function openPlanModal() {
    document.getElementById('modal-title').textContent = 'Novo Plano';
    document.getElementById('plan-form').reset();
    document.getElementById('plan-id').value = '';
    document.getElementById('field-slug').disabled = false;
    document.getElementById('plan-modal').classList.remove('hidden');
}
function closePlanModal() { document.getElementById('plan-modal').classList.add('hidden'); }

async function editPlan(id) {
    try {
        const resp = await fetch('/api/v1/admin/plans/' + id, { headers: { 'Authorization': 'Bearer <?= $token ?>' } });
        if (!resp.ok) throw new Error('Erro');
        const data = await resp.json();
        const p = data.plan;
        document.getElementById('modal-title').textContent = 'Editar: ' + p.name;
        document.getElementById('plan-id').value = p.id;
        document.getElementById('field-slug').value = p.slug;
        document.getElementById('field-slug').disabled = true;
        document.getElementById('field-name').value = p.name;
        document.getElementById('field-description').value = p.description || '';
        document.getElementById('field-price').value = p.price_raw || p.price || 0;
        document.getElementById('field-sort').value = p.sort_order || 0;
        document.getElementById('field-active').checked = p.active;
        document.getElementById('plan-modal').classList.remove('hidden');
    } catch (err) { alert('Erro ao carregar plano'); }
}

document.getElementById('plan-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('plan-id').value;
    const payload = {
        slug: document.getElementById('field-slug').value.trim(),
        name: document.getElementById('field-name').value.trim(),
        description: document.getElementById('field-description').value.trim() || null,
        price: parseFloat(document.getElementById('field-price').value) || 0,
        sort_order: parseInt(document.getElementById('field-sort').value) || 0,
        active: document.getElementById('field-active').checked,
    };
    const url = id ? '/api/v1/admin/plans/' + id : '/api/v1/admin/plans';
    const method = id ? 'PUT' : 'POST';
    try {
        const resp = await fetch(url, {
            method, headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer <?= $token ?>' },
            body: JSON.stringify(payload),
        });
        if (!resp.ok) { const d = await resp.json(); throw new Error(d.message || 'Erro'); }
        closePlanModal(); loadPlans();
    } catch (err) { alert(err.message); }
});

async function togglePlan(id, currentActive) {
    if (!confirm(currentActive ? 'Desativar este plano?' : 'Ativar este plano?')) return;
    try {
        const resp = await fetch('/api/v1/admin/plans/' + id, {
            method: 'PUT', headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer <?= $token ?>' },
            body: JSON.stringify({ active: !currentActive }),
        });
        if (!resp.ok) throw new Error('Erro');
        loadPlans();
    } catch (err) { alert('Erro ao alterar status'); }
}

document.addEventListener('DOMContentLoaded', loadPlans);
</script>
