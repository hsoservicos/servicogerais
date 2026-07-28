<!-- ═══════════════════════════════════════════════════════════════
     templates/admin-tenants.php — Admin Tenants CRUD (Epic 7 — Story 7.2)
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
    <?php $currentPage = 'admin-tenants'; require_once __DIR__ . '/partials/admin-sidebar.php'; ?>

    <!-- Main -->
    <div class="ml-64 min-h-screen flex flex-col flex-1">
        <?php
        $pageTitle = 'Gestão de Tenants';
        $pageSubtitle = 'Gerencie todos os tenants da plataforma';
        $topbarExtra = '<select id="status-filter" onchange="loadTenants()" class="text-sm border border-border rounded-lg px-3 py-2 text-ink bg-white focus:outline-none focus:ring-2 focus:ring-primary"><option value="">Todos</option><option value="active">Ativos</option><option value="suspended">Suspensos</option></select><div class="relative"><input type="text" id="search-input" placeholder="Buscar tenant..." onkeyup="if(event.key==='Enter') loadTenants()" class="w-64 text-sm border border-border rounded-lg pl-9 pr-3 py-2 text-ink placeholder-ink-muted focus:outline-none focus:ring-2 focus:ring-primary"><svg class="absolute left-3 top-2.5 w-4 h-4 text-ink-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg></div>';
        require __DIR__ . '/partials/admin-topbar.php';
        ?>

        <main class="flex-1 p-6">
            <!-- Loading -->
            <div id="loading-state" class="text-center py-20">
                <svg class="w-10 h-10 text-primary animate-spin mx-auto mb-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <p class="text-ink-muted">Carregando tenants...</p>
            </div>

            <!-- Toast -->
            <div id="toast" class="hidden fixed bottom-6 right-6 z-50 px-5 py-3 rounded-lg shadow-modal text-white text-sm animate-fade-in"></div>

            <!-- Table -->
            <div id="table-content" class="hidden">
                <div class="bg-white rounded-xl shadow-card border border-border overflow-hidden hover:shadow-lg transition-shadow duration-200">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-surface/50 text-left text-ink-muted border-b border-border">
                                    <th class="px-4 py-3 font-medium">ID</th>
                                    <th class="px-4 py-3 font-medium">Nome</th>
                                    <th class="px-4 py-3 font-medium">Documento</th>
                                    <th class="px-4 py-3 font-medium">Plano</th>
                                    <th class="px-4 py-3 font-medium">Usuários</th>
                                    <th class="px-4 py-3 font-medium">Clientes</th>
                                    <th class="px-4 py-3 font-medium">Receita</th>
                                    <th class="px-4 py-3 font-medium">Status</th>
                                    <th class="px-4 py-3 font-medium">Cadastro</th>
                                    <th class="px-4 py-3 font-medium text-right">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="tenants-table-body"></tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <div id="pagination" class="flex items-center justify-between mt-4 text-sm text-ink-muted"></div>

                <!-- Empty State -->
                <div id="empty-state" class="hidden text-center py-20">
                    <svg class="w-16 h-16 text-ink-muted/30 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <p class="text-ink-secondary mb-2">Nenhum tenant encontrado</p>
                    <p class="text-ink-muted text-sm">Tente ajustar os filtros de busca.</p>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Edit Tenant Modal -->
<div id="edit-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-modal w-full max-w-md mx-4 p-6 animate-fade-in">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-h3 text-ink">Editar Tenant</h3>
            <button onclick="closeEditModal()" class="text-ink-muted hover:text-ink transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="edit-form" class="space-y-4">
            <input type="hidden" id="edit-id">
            <div>
                <label class="block text-sm font-medium text-ink mb-1">Nome</label>
                <input type="text" id="edit-name" class="w-full px-3 py-2 border border-border rounded-lg text-ink focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-ink mb-1">Telefone</label>
                <input type="text" id="edit-phone" class="w-full px-3 py-2 border border-border rounded-lg text-ink focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-ink mb-1">WhatsApp</label>
                <input type="text" id="edit-whatsapp" class="w-full px-3 py-2 border border-border rounded-lg text-ink focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-ink mb-1">Plano</label>
                <select id="edit-plan" class="w-full px-3 py-2 border border-border rounded-lg text-ink bg-white focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="free">Free</option>
                    <option value="basic">Basic</option>
                    <option value="pro">Pro</option>
                    <option value="enterprise">Enterprise</option>
                </select>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeEditModal()" class="flex-1 px-4 py-2 border border-border rounded-lg text-ink-secondary hover:bg-surface hover:scale-[1.02] active:scale-95 transition-all duration-200 text-sm font-medium">Cancelar</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-600 hover:scale-[1.02] active:scale-95 transition-all duration-200 text-sm font-medium">Salvar</button>
            </div>
        </form>
    </div>
</div>

<!-- Suspend Modal -->
<div id="suspend-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-modal w-full max-w-sm mx-4 p-6 animate-fade-in">
        <div class="w-12 h-12 bg-warning/10 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-6 h-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
        </div>
        <h3 class="text-center text-h3 text-ink mb-2" id="suspend-modal-title">Suspender Tenant</h3>
        <p class="text-center text-ink-secondary text-sm mb-6" id="suspend-modal-desc">Tem certeza que deseja suspender este tenant?</p>
        <input type="hidden" id="suspend-id">
        <input type="hidden" id="suspend-current-status">
        <div class="flex gap-3">
            <button onclick="closeSuspendModal()" class="flex-1 px-4 py-2 border border-border rounded-lg text-ink-secondary hover:bg-surface hover:scale-[1.02] active:scale-95 transition-all duration-200 text-sm font-medium">Cancelar</button>
            <button id="suspend-confirm-btn" onclick="confirmSuspend()" class="flex-1 px-4 py-2 bg-warning text-white rounded-lg hover:bg-amber-600 hover:scale-[1.02] active:scale-95 transition-all duration-200 text-sm font-medium">Confirmar</button>
        </div>
    </div>
</div>

<script>
var currentPage = 1;

async function loadTenants() {
    const loading = document.getElementById('loading-state');
    const content = document.getElementById('table-content');
    const empty = document.getElementById('empty-state');

    loading.classList.remove('hidden');
    content.classList.add('hidden');
    empty.classList.add('hidden');

    try {
        const search = document.getElementById('search-input').value;
        const status = document.getElementById('status-filter').value;
        const token = '<?= $token ?>';

        const params = new URLSearchParams({ page: currentPage, perPage: 20 });
        if (search) params.set('search', search);
        if (status) params.set('status', status);

        const response = await fetch(`/api/v1/admin/tenants?${params}`, {
            headers: { 'Authorization': 'Bearer ' + token }
        });

        if (!response.ok) { if (response.status === 401) { window.location.href = '?page=admin-login'; return; } throw new Error('Erro'); }
        const data = await response.json();
        renderTable(data);
    } catch (err) {
        console.error('[Tenants]', err.message);
    } finally {
        loading.classList.add('hidden');
    }
}

function renderTable(data) {
    const tbody = document.getElementById('tenants-table-body');
    const content = document.getElementById('table-content');
    const empty = document.getElementById('empty-state');

    if (!data.tenants || data.tenants.length === 0) {
        content.classList.add('hidden');
        empty.classList.remove('hidden');
        return;
    }

    content.classList.remove('hidden');
    empty.classList.add('hidden');

    const planColors = {
        free: 'bg-gray-100 text-gray-800',
        basic: 'bg-blue-100 text-blue-600',
        pro: 'bg-purple-100 text-purple-600',
        enterprise: 'bg-amber-100 text-amber-600',
    };

    tbody.innerHTML = data.tenants.map(t => `
        <tr class="border-b border-border/50 hover:bg-surface/30 hover:-translate-x-0.5 transition-all duration-200 group">
            <td class="px-4 py-3 text-ink-muted text-xs">#${t.id}</td>
            <td class="px-4 py-3">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-primary/10 text-primary rounded-lg flex items-center justify-center font-semibold text-xs group-hover:scale-110 group-hover:bg-primary/20 transition-all duration-200">${t.name.charAt(0).toUpperCase()}</div>
                    <div>
                        <p class="text-ink font-medium">${t.name}</p>
                        <p class="text-xs text-ink-muted">${t.slug || ''}</p>
                    </div>
                </div>
            </td>
            <td class="px-4 py-3 text-ink-secondary text-xs">${t.document_cpf || t.document_cnpj || '—'}</td>
            <td class="px-4 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium ${planColors[t.plan] || 'bg-gray-100 text-gray-800'}">${t.plan}</span></td>
            <td class="px-4 py-3 text-ink-secondary">${t.user_count}</td>
            <td class="px-4 py-3 text-ink-secondary">${t.client_count}</td>
            <td class="px-4 py-3 text-ink font-medium">${t.total_revenue}</td>
            <td class="px-4 py-3">
                <span class="inline-flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full ${t.active ? 'bg-success' : 'bg-ink-muted'}"></span>
                    <span class="text-xs ${t.active ? 'text-success' : 'text-ink-muted'}">${t.active ? 'Ativo' : 'Suspenso'}</span>
                </span>
            </td>
            <td class="px-4 py-3 text-ink-muted text-xs">${t.created_at}</td>
            <td class="px-4 py-3 text-right">
                <div class="flex items-center justify-end gap-2">
                    <button onclick="openEditModal(${t.id}, '${t.name.replace(/'/g, "\\'")}', '${t.phone || ''}', '${t.whatsapp || ''}', '${t.plan}')"
                        class="p-1.5 rounded-lg text-ink-muted hover:text-info hover:bg-info/10 hover:scale-110 active:scale-90 transition-all duration-200" title="Editar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <button onclick="openSuspendModal(${t.id}, ${t.active}, '${t.name.replace(/'/g, "\\'")}')"
                        class="p-1.5 rounded-lg text-ink-muted hover:text-danger hover:bg-danger/10 hover:scale-110 active:scale-90 transition-all duration-200" title="${t.active ? 'Suspender' : 'Reativar'}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="${t.active ? 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636' : 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'}"/></svg>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');

    // Pagination
    const pag = document.getElementById('pagination');
    const { page, totalPages, total } = data.pagination;
    pag.innerHTML = `
        <span>${total} tenants encontrados</span>
        <div class="flex items-center gap-2">
            <button onclick="goToPage(${page - 1})" class="px-3 py-1 border border-border rounded-lg hover:bg-surface active:scale-95 transition-all duration-200 ${page <= 1 ? 'opacity-50 cursor-not-allowed' : ''}" ${page <= 1 ? 'disabled' : ''}>Anterior</button>
            <span class="text-ink-muted">Página ${page} de ${totalPages}</span>
            <button onclick="goToPage(${page + 1})" class="px-3 py-1 border border-border rounded-lg hover:bg-surface active:scale-95 transition-all duration-200 ${page >= totalPages ? 'opacity-50 cursor-not-allowed' : ''}" ${page >= totalPages ? 'disabled' : ''}>Próxima</button>
        </div>
    `;
}

function goToPage(p) { currentPage = p; loadTenants(); }

// ── Edit Modal ────────────────────────────────────────────
function openEditModal(id, name, phone, whatsapp, plan) {
    document.getElementById('edit-id').value = id;
    document.getElementById('edit-name').value = name;
    document.getElementById('edit-phone').value = phone;
    document.getElementById('edit-whatsapp').value = whatsapp;
    document.getElementById('edit-plan').value = plan;
    document.getElementById('edit-modal').classList.remove('hidden');
}

function closeEditModal() { document.getElementById('edit-modal').classList.add('hidden'); }

document.getElementById('edit-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const id = document.getElementById('edit-id').value;
    const btn = this.querySelector('button[type="submit"]');
    btn.disabled = true; btn.textContent = 'Salvando...';

    try {
        const response = await fetch('/api/v1/admin/tenants/' + id, {
            method: 'PUT',
            headers: { 'Authorization': 'Bearer <?= $token ?>', 'Content-Type': 'application/json' },
            body: JSON.stringify({
                name: document.getElementById('edit-name').value,
                phone: document.getElementById('edit-phone').value,
                whatsapp: document.getElementById('edit-whatsapp').value,
                plan: document.getElementById('edit-plan').value,
            }),
        });
        if (!response.ok) throw new Error('Erro ao atualizar');
        closeEditModal();
        showToast('Tenant atualizado com sucesso!', 'bg-success');
        loadTenants();
    } catch (err) {
        showToast('Erro ao atualizar tenant', 'bg-danger');
    } finally {
        btn.disabled = false; btn.textContent = 'Salvar';
    }
});

// ── Suspend Modal ─────────────────────────────────────────
function openSuspendModal(id, active, name) {
    document.getElementById('suspend-id').value = id;
    document.getElementById('suspend-current-status').value = active;
    document.getElementById('suspend-modal-title').textContent = active ? 'Suspender Tenant' : 'Reativar Tenant';
    document.getElementById('suspend-modal-desc').textContent = active ? `Tem certeza que deseja suspender "${name}"? O acesso será bloqueado.` : `Tem certeza que deseja reativar "${name}"?`;
    document.getElementById('suspend-confirm-btn').textContent = active ? 'Suspender' : 'Reativar';
    document.getElementById('suspend-confirm-btn').className = active ? 'flex-1 px-4 py-2 bg-warning text-white rounded-lg hover:bg-amber-600 hover:scale-[1.02] active:scale-95 transition-all duration-200 text-sm font-medium' : 'flex-1 px-4 py-2 bg-success text-white rounded-lg hover:bg-green-600 hover:scale-[1.02] active:scale-95 transition-all duration-200 text-sm font-medium';
    document.getElementById('suspend-modal').classList.remove('hidden');
}

function closeSuspendModal() { document.getElementById('suspend-modal').classList.add('hidden'); }

async function confirmSuspend() {
    const id = document.getElementById('suspend-id').value;
    const btn = document.getElementById('suspend-confirm-btn');
    btn.disabled = true; btn.textContent = 'Processando...';

    try {
        const response = await fetch('/api/v1/admin/tenants/' + id, {
            method: 'DELETE',
            headers: { 'Authorization': 'Bearer <?= $token ?>' },
        });
        if (!response.ok) throw new Error('Erro');
        closeSuspendModal();
        showToast('Status do tenant alterado com sucesso!', 'bg-success');
        loadTenants();
    } catch (err) {
        showToast('Erro ao alterar status', 'bg-danger');
    } finally {
        btn.disabled = false;
    }
}

// ── Toast ─────────────────────────────────────────────────
function showToast(msg, bgClass) {
    const toast = document.getElementById('toast');
    toast.textContent = msg;
    toast.className = 'fixed bottom-6 right-6 z-50 px-5 py-3 rounded-lg shadow-modal text-white text-sm animate-fade-in ' + bgClass;
    toast.classList.remove('hidden');
    setTimeout(() => { toast.classList.add('hidden'); }, 3000);
}

document.addEventListener('DOMContentLoaded', loadTenants);
</script>
