<?php
if (!isAuthenticated()) {
    header('Location: ?page=login');
    exit;
}
?>
<?php $currentPage = 'services'; require __DIR__ . '/partials/sidebar.php'; ?>

<div class="md:ml-64 min-h-screen flex flex-col">
    <?php
    $pageTitle = 'Serviços';
    $pageSubtitle = 'Gerencie os serviços que você oferece';
    $topbarExtra = '<button onclick="openCreateModal()" class="bg-primary text-white font-medium px-4 py-2 rounded-lg hover:bg-primary-600 active:bg-primary-700 transition-all duration-200 flex items-center gap-2 shadow-card text-sm"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Novo Serviço</button>';
    require __DIR__ . '/partials/topbar.php';
    ?>

    <main class="flex-1 p-6">
        <div class="flex flex-col sm:flex-row gap-4 mb-6">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-ink-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" id="search-input" placeholder="Buscar serviço..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
            </div>
            <select id="category-filter" class="px-4 py-2.5 rounded-lg border border-border bg-surface text-ink focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all text-sm">
                <option value="">Todas as Categorias</option>
            </select>
            <select id="per-page" class="px-4 py-2.5 rounded-lg border border-border bg-surface text-ink focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all text-sm">
                <option value="10">10 por página</option>
                <option value="20" selected>20 por página</option>
                <option value="50">50 por página</option>
            </select>
        </div>

        <div class="bg-white rounded-xl shadow-card border border-border overflow-hidden">
            <div id="services-table">
                <div class="flex items-center justify-center py-16">
                    <div class="w-10 h-10 border-4 border-primary/30 border-t-primary rounded-full animate-spin"></div>
                </div>
            </div>
        </div>

        <div id="pagination" class="flex items-center justify-between mt-4 text-sm text-ink-secondary">
            <span id="pagination-info">Carregando...</span>
            <div class="flex gap-2">
                <button id="prev-page" onclick="goPage(-1)" disabled class="px-3 py-1.5 rounded-lg border border-border bg-white hover:bg-surface disabled:opacity-40 disabled:cursor-not-allowed transition-all">← Anterior</button>
                <button id="next-page" onclick="goPage(1)" disabled class="px-3 py-1.5 rounded-lg border border-border bg-white hover:bg-surface disabled:opacity-40 disabled:cursor-not-allowed transition-all">Próximo →</button>
            </div>
        </div>
    </main>
</div>

<div id="service-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" onclick="closeModal()"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-modal w-full max-w-md animate-fade-in">
            <div class="flex items-center justify-between px-6 py-4 border-b border-border">
                <h3 id="modal-title" class="text-h3 text-ink">Novo Serviço</h3>
                <button onclick="closeModal()" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-surface transition-colors">
                    <svg class="w-5 h-5 text-ink-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="service-form" class="p-6 space-y-4" novalidate>
                <input type="hidden" id="service-id" value="">

                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">Nome <span class="text-danger">*</span></label>
                    <input type="text" id="field-name" required
                        class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                        placeholder="Ex: Corte Feminino">
                </div>

                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">Descrição</label>
                    <textarea id="field-description" rows="2"
                        class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all resize-none"
                        placeholder="Breve descrição do serviço"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">Categoria <span class="text-danger">*</span></label>
                    <select id="field-category" required
                        class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                        <option value="">Selecione uma categoria...</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">Preço (R$) <span class="text-danger">*</span></label>
                        <input type="text" id="field-price" required
                            class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                            placeholder="0,00">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">Duração (min)</label>
                        <input type="number" id="field-duration" min="0" step="5"
                            class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                            placeholder="60">
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeModal()"
                        class="w-1/3 border-2 border-border text-ink font-medium py-2.5 px-4 rounded-lg hover:bg-border/30 transition-all text-sm">Cancelar</button>
                    <button type="submit" id="modal-submit-btn"
                        class="w-2/3 bg-primary text-white font-medium py-2.5 px-4 rounded-lg hover:bg-primary-600 active:bg-primary-700 transition-all duration-200 text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        Salvar Serviço
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="delete-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-modal w-full max-w-sm animate-fade-in p-6 text-center">
            <div class="w-14 h-14 bg-danger/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="text-h3 text-ink mb-2">Desativar Serviço?</h3>
            <p class="text-ink-secondary text-sm mb-6" id="delete-message">O serviço será desativado do catálogo.</p>
            <div class="flex gap-3">
                <button onclick="closeDeleteModal()"
                    class="w-1/2 border-2 border-border text-ink font-medium py-2.5 px-4 rounded-lg hover:bg-border/30 transition-all text-sm">Cancelar</button>
                <button id="confirm-delete-btn"
                    class="w-1/2 bg-danger text-white font-medium py-2.5 px-4 rounded-lg hover:bg-danger/90 active:bg-danger/80 transition-all text-sm">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<script>
const API_BASE = '<?= API_BASE_URL ?>';
let currentPage = 1;
let currentSearch = '';
let currentCategory = '';
let deleteServiceId = null;

function formatPrice(value) {
    const num = parseFloat(value);
    if (isNaN(num)) return 'R$ 0,00';
    return 'R$ ' + num.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function parsePriceInput(value) {
    let cleaned = value.replace(/[^\d,]/g, '').replace(',', '.');
    return parseFloat(cleaned) || 0;
}

async function loadCategoriesForFilter() {
    try {
        const token = '<?= getToken() ?>';
        if (!token) return;
        const response = await fetch(`${API_BASE}/categories?perPage=200&active=true`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        if (!response.ok) return;
        const data = await response.json();
        const filterSelect = document.getElementById('category-filter');
        const modalSelect = document.getElementById('field-category');
        data.categories.forEach(c => {
            const opt1 = document.createElement('option');
            opt1.value = c.id;
            opt1.textContent = c.name;
            filterSelect.appendChild(opt1);
            const opt2 = document.createElement('option');
            opt2.value = c.id;
            opt2.textContent = c.name;
            modalSelect.appendChild(opt2);
        });
    } catch (err) {
        // silently fail, categories will just not be filtered
    }
}

async function loadServices() {
    const table = document.getElementById('services-table');
    table.innerHTML = '<div class="flex items-center justify-center py-16"><div class="w-10 h-10 border-4 border-primary/30 border-t-primary rounded-full animate-spin"></div></div>';

    try {
        const token = '<?= getToken() ?>';
        if (!token) { window.location.href = '?page=login'; return; }

        const params = new URLSearchParams({
            page: currentPage,
            perPage: document.getElementById('per-page').value
        });
        if (currentSearch.length >= 2) params.set('search', currentSearch);
        if (currentCategory) params.set('category_id', currentCategory);

        const response = await fetch(`${API_BASE}/services?${params}`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });

        if (response.status === 401) { window.location.href = '?page=login'; return; }
        if (!response.ok) throw new Error('Erro ao carregar serviços');

        const data = await response.json();
        renderTable(data.services, data.pagination);
    } catch (err) {
        table.innerHTML = `<div class="text-center py-12"><p class="text-danger">❌ ${err.message}</p></div>`;
    }
}

function renderTable(services, pagination) {
    const table = document.getElementById('services-table');
    document.getElementById('pagination').classList.toggle('hidden', !services || services.length === 0);

    if (!services || services.length === 0) {
        table.innerHTML = `
            <div class="text-center py-16 px-6">
                <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-h3 text-ink mb-2">Nenhum serviço cadastrado</h3>
                <p class="text-ink-secondary text-sm mb-6">Crie serviços para incluir em suas propostas.</p>
                <button onclick="openCreateModal()" class="bg-primary text-white font-medium px-6 py-2.5 rounded-lg hover:bg-primary-600 transition-all">+ Novo Serviço</button>
            </div>`;
        return;
    }

    table.innerHTML = `
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-surface/80 border-b border-border">
                        <th class="text-left px-6 py-3 text-xs font-semibold text-ink-muted uppercase tracking-wider">Serviço</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-ink-muted uppercase tracking-wider hidden md:table-cell">Categoria</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold text-ink-muted uppercase tracking-wider">Preço</th>
                        <th class="text-center px-6 py-3 text-xs font-semibold text-ink-muted uppercase tracking-wider hidden md:table-cell">Duração</th>
                        <th class="text-center px-6 py-3 text-xs font-semibold text-ink-muted uppercase tracking-wider hidden sm:table-cell">Status</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold text-ink-muted uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    ${services.map(s => `
                        <tr class="hover:bg-surface/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-sm font-bold" style="background: ${s.category_color || '#10B981'}">
                                        ${(s.name.charAt(0) || '?').toUpperCase()}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-ink">${escapeHtml(s.name)}</p>
                                        <p class="text-xs text-ink-muted">${escapeHtml(s.description || '')}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" style="background: ${s.category_color || '#10B981'}15; color: ${s.category_color || '#10B981'}">
                                    ${escapeHtml(s.category_name || '—')}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-semibold text-ink">${formatPrice(s.price)}</td>
                            <td class="px-6 py-4 text-center text-sm text-ink-secondary hidden md:table-cell">${s.duration_minutes ? s.duration_minutes + ' min' : '—'}</td>
                            <td class="px-6 py-4 text-center hidden sm:table-cell">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium ${s.active ? 'bg-success/10 text-success' : 'bg-ink-muted/10 text-ink-muted'}">
                                    ${s.active ? 'Ativo' : 'Inativo'}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button onclick="openEditModal(${s.id})" title="Editar"
                                        class="w-8 h-8 rounded-lg flex items-center justify-center text-ink-secondary hover:bg-primary/10 hover:text-primary transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button onclick="toggleActive(${s.id}, ${s.active ? 0 : 1})" title="${s.active ? 'Desativar' : 'Ativar'}"
                                        class="w-8 h-8 rounded-lg flex items-center justify-center text-ink-secondary hover:bg-primary/10 hover:text-primary transition-colors">
                                        ${s.active
                                            ? '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                                            : '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'}
                                    </button>
                                    <button onclick="openDeleteModal(${s.id})" title="Desativar"
                                        class="w-8 h-8 rounded-lg flex items-center justify-center text-ink-secondary hover:bg-danger/10 hover:text-danger transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>`;

    document.getElementById('pagination-info').textContent =
        `Mostrando ${services.length} de ${pagination.total} serviços (página ${pagination.page} de ${pagination.totalPages})`;
    document.getElementById('prev-page').disabled = pagination.page <= 1;
    document.getElementById('next-page').disabled = pagination.page >= pagination.totalPages;
}

function goPage(delta) {
    const btn = delta > 0 ? document.getElementById('next-page') : document.getElementById('prev-page');
    if (btn.disabled) return;
    currentPage += delta;
    loadServices();
}

async function toggleActive(serviceId, newActive) {
    try {
        const token = '<?= getToken() ?>';
        const response = await fetch(`${API_BASE}/services/${serviceId}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` },
            body: JSON.stringify({ active: !!newActive }),
        });
        const data = await response.json();
        if (!response.ok) throw new Error(data.message || 'Erro ao atualizar');
        showToast(data.message, 'success');
        loadServices();
    } catch (err) {
        showToast(err.message, 'error');
    }
}

let searchTimeout;
document.getElementById('search-input').addEventListener('input', (e) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        currentSearch = e.target.value.trim();
        currentPage = 1;
        loadServices();
    }, 300);
});

document.getElementById('category-filter').addEventListener('change', (e) => {
    currentCategory = e.target.value;
    currentPage = 1;
    loadServices();
});

document.getElementById('per-page').addEventListener('change', () => {
    currentPage = 1;
    loadServices();
});

document.getElementById('field-price').addEventListener('input', (e) => {
    let value = e.target.value.replace(/[^\d,]/g, '');
    const commaCount = (value.match(/,/g) || []).length;
    if (commaCount > 1) value = value.replace(/,/g, '');
    e.target.value = value;
});

function openCreateModal() {
    document.getElementById('modal-title').textContent = 'Novo Serviço';
    document.getElementById('modal-submit-btn').textContent = 'Salvar Serviço';
    document.getElementById('service-form').reset();
    document.getElementById('service-id').value = '';
    document.getElementById('service-modal').classList.remove('hidden');
}

async function openEditModal(serviceId) {
    try {
        const token = '<?= getToken() ?>';
        const response = await fetch(`${API_BASE}/services/${serviceId}`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        if (!response.ok) throw new Error('Erro ao carregar serviço');
        const data = await response.json();
        const s = data.service;

        document.getElementById('modal-title').textContent = 'Editar Serviço';
        document.getElementById('modal-submit-btn').textContent = 'Atualizar';
        document.getElementById('service-id').value = s.id;
        document.getElementById('field-name').value = s.name || '';
        document.getElementById('field-description').value = s.description || '';
        document.getElementById('field-category').value = s.category_id || '';
        document.getElementById('field-price').value = (s.price || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('field-duration').value = s.duration_minutes || '';

        document.getElementById('service-modal').classList.remove('hidden');
    } catch (err) {
        showToast(err.message, 'error');
    }
}

function closeModal() {
    document.getElementById('service-modal').classList.add('hidden');
}

document.getElementById('service-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('modal-submit-btn');
    const serviceId = document.getElementById('service-id').value;
    const isEdit = !!serviceId;
    const name = document.getElementById('field-name').value.trim();

    if (!name) {
        showToast('Nome é obrigatório', 'error');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="inline-block w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin mr-2"></span> Salvando...';

    try {
        const token = '<?= getToken() ?>';
        const payload = {
            name,
            description: document.getElementById('field-description').value.trim() || null,
            category_id: parseInt(document.getElementById('field-category').value) || null,
            price: parsePriceInput(document.getElementById('field-price').value),
            duration_minutes: parseInt(document.getElementById('field-duration').value) || null,
        };

        const url = isEdit ? `${API_BASE}/services/${serviceId}` : `${API_BASE}/services`;
        const method = isEdit ? 'PUT' : 'POST';

        const response = await fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` },
            body: JSON.stringify(payload),
        });

        const data = await response.json();
        if (!response.ok) throw new Error(data.message || 'Erro ao salvar');

        showToast(data.message, 'success');
        closeModal();
        loadServices();
    } catch (err) {
        showToast(err.message, 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = isEdit ? 'Atualizar' : 'Salvar Serviço';
    }
});

function openDeleteModal(serviceId) {
    deleteServiceId = serviceId;
    document.getElementById('delete-modal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('delete-modal').classList.add('hidden');
    deleteServiceId = null;
}

document.getElementById('confirm-delete-btn').addEventListener('click', async () => {
    if (!deleteServiceId) return;
    const btn = document.getElementById('confirm-delete-btn');
    btn.disabled = true;
    btn.innerHTML = '<span class="inline-block w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin mr-2"></span> Desativando...';

    try {
        const token = '<?= getToken() ?>';
        const response = await fetch(`${API_BASE}/services/${deleteServiceId}`, {
            method: 'DELETE',
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const data = await response.json();
        if (!response.ok) throw new Error(data.message || 'Erro ao desativar');

        showToast('✅ ' + data.message, 'success');
        closeDeleteModal();
        loadServices();
    } catch (err) {
        showToast(err.message, 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = 'Confirmar';
    }
});

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str || '';
    return div.innerHTML;
}

loadCategoriesForFilter();
document.addEventListener('DOMContentLoaded', loadServices);
</script>