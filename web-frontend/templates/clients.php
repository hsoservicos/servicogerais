<?php
/**
 * ═══════════════════════════════════════════════════════════════
 * templates/clients.php — Gestão de Clientes (CRUD + WhatsApp)
 * ═══════════════════════════════════════════════════════════════
 */

if (!isAuthenticated()) {
    header('Location: ?page=login');
    exit;
}
?>

<!-- ═══════════════════════════════════════════════════════════════
     templates/clients.php — Gestão de Clientes
     ═══════════════════════════════════════════════════════════════ -->
<?php $currentPage = 'clients'; require __DIR__ . '/partials/sidebar.php'; ?>

<!-- Main Content -->
<div class="md:ml-64 min-h-screen flex flex-col">
    <?php
    $pageTitle = 'Clientes';
    $pageSubtitle = 'Gerencie sua base de clientes';
    $topbarExtra = '<button onclick="openCreateModal()" class="bg-primary text-white font-medium px-4 py-2 rounded-lg hover:bg-primary-600 active:bg-primary-700 transition-all duration-200 flex items-center gap-2 shadow-card text-sm"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Novo Cliente</button>';
    require __DIR__ . '/partials/topbar.php';
    ?>

    <main class="flex-1 p-6">
        <!-- Search + Filters -->
        <div class="flex flex-col sm:flex-row gap-4 mb-6">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-ink-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" id="search-input" placeholder="Buscar cliente por nome..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
            </div>
            <select id="per-page" class="px-4 py-2.5 rounded-lg border border-border bg-surface text-ink focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all text-sm">
                <option value="10">10 por página</option>
                <option value="20" selected>20 por página</option>
                <option value="50">50 por página</option>
            </select>
        </div>

        <!-- Tabela de Clientes -->
        <div class="bg-white rounded-xl shadow-card border border-border overflow-hidden">
            <div id="clients-table">
                <!-- Loading State -->
                <div class="flex items-center justify-center py-16">
                    <div class="w-10 h-10 border-4 border-primary/30 border-t-primary rounded-full animate-spin"></div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div id="pagination" class="flex items-center justify-between mt-4 text-sm text-ink-secondary">
            <span id="pagination-info">Carregando...</span>
            <div class="flex gap-2">
                <button id="prev-page" onclick="goPage(-1)" disabled class="px-3 py-1.5 rounded-lg border border-border bg-white hover:bg-surface disabled:opacity-40 disabled:cursor-not-allowed transition-all">← Anterior</button>
                <button id="next-page" onclick="goPage(1)" disabled class="px-3 py-1.5 rounded-lg border border-border bg-white hover:bg-surface disabled:opacity-40 disabled:cursor-not-allowed transition-all">Próximo →</button>
            </div>
        </div>
    </main>
</div>

<!-- Modal CRUD (Create/Edit) -->
<div id="client-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" onclick="closeModal()"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-modal w-full max-w-lg animate-fade-in">
            <div class="flex items-center justify-between px-6 py-4 border-b border-border">
                <h3 id="modal-title" class="text-h3 text-ink">Novo Cliente</h3>
                <button onclick="closeModal()" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-surface transition-colors">
                    <svg class="w-5 h-5 text-ink-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="client-form" class="p-6 space-y-4" novalidate>
                <input type="hidden" id="client-id" value="">

                <!-- Nome -->
                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">Nome <span class="text-danger">*</span></label>
                    <input type="text" id="field-name" required
                        class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                        placeholder="Nome do cliente">
                    <span class="error-message text-xs text-danger mt-1 hidden">Nome é obrigatório</span>
                </div>

                <!-- E-mail + Telefone -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">E-mail</label>
                        <input type="email" id="field-email"
                            class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                            placeholder="cliente@email.com">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">Telefone</label>
                        <input type="tel" id="field-phone"
                            class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                            placeholder="(11) 99999-9999">
                    </div>
                </div>

                <!-- WhatsApp + Documentos -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">WhatsApp</label>
                        <div class="relative">
                            <input type="tel" id="field-whatsapp"
                                class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                                placeholder="(11) 99999-9999">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-whatsapp font-medium">💬</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">CPF</label>
                        <input type="text" id="field-cpf"
                            class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                            placeholder="000.000.000-00" maxlength="14">
                    </div>
                </div>

                <!-- Endereço -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-3">
                        <label class="block text-sm font-medium text-ink mb-1.5">Endereço</label>
                        <input type="text" id="field-address"
                            class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                            placeholder="Rua, número, bairro">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">Cidade</label>
                        <input type="text" id="field-city"
                            class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                            placeholder="São Paulo">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">Estado</label>
                        <input type="text" id="field-state" maxlength="2"
                            class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                            placeholder="SP">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">CNPJ</label>
                        <input type="text" id="field-cnpj"
                            class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                            placeholder="00.000.000/0001-00" maxlength="18">
                    </div>
                </div>

                <!-- Observações -->
                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">Observações</label>
                    <textarea id="field-notes" rows="2"
                        class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all resize-none"
                        placeholder="Informações adicionais..."></textarea>
                </div>

                <!-- Actions -->
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeModal()"
                        class="w-1/3 border-2 border-border text-ink font-medium py-2.5 px-4 rounded-lg hover:bg-border/30 transition-all text-sm">Cancelar</button>
                    <button type="submit" id="modal-submit-btn"
                        class="w-2/3 bg-primary text-white font-medium py-2.5 px-4 rounded-lg hover:bg-primary-600 active:bg-primary-700 transition-all duration-200 text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        Salvar Cliente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-modal w-full max-w-sm animate-fade-in p-6 text-center">
            <div class="w-14 h-14 bg-danger/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="text-h3 text-ink mb-2">Excluir Cliente?</h3>
            <p class="text-ink-secondary text-sm mb-6">Esta ação não pode ser desfeita. O cliente será desativado.</p>
            <div class="flex gap-3">
                <button onclick="closeDeleteModal()"
                    class="w-1/2 border-2 border-border text-ink font-medium py-2.5 px-4 rounded-lg hover:bg-border/30 transition-all text-sm">Cancelar</button>
                <button id="confirm-delete-btn"
                    class="w-1/2 bg-danger text-white font-medium py-2.5 px-4 rounded-lg hover:bg-danger/90 active:bg-danger/80 transition-all text-sm">Confirmar Exclusão</button>
            </div>
        </div>
    </div>
</div>

<script>
// ═══════════════════════════════════════════════════════════════
// Clients CRUD — JavaScript
// ═══════════════════════════════════════════════════════════════

const API_BASE = '<?= API_BASE_URL ?>';
let currentPage = 1;
let currentPerPage = 20;
let currentSearch = '';
let deleteClientId = null;

// ── Carregar Lista ───────────────────────────────────────
async function loadClients() {
    const table = document.getElementById('clients-table');
    table.innerHTML = '<div class="flex items-center justify-center py-16"><div class="w-10 h-10 border-4 border-primary/30 border-t-primary rounded-full animate-spin"></div></div>';

    try {
        const token = '<?= getToken() ?>';
        if (!token) { window.location.href = '?page=login'; return; }

        const params = new URLSearchParams({ page: currentPage, perPage: currentPerPage });
        if (currentSearch.length >= 2) params.set('search', currentSearch);

        const response = await fetch(`${API_BASE}/clients?${params}`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });

        if (response.status === 401) { window.location.href = '?page=login'; return; }
        if (!response.ok) throw new Error('Erro ao carregar clientes');

        const data = await response.json();
        renderTable(data.clients, data.pagination);
    } catch (err) {
        table.innerHTML = `<div class="text-center py-12"><p class="text-danger">❌ ${err.message}</p></div>`;
    }
}

// ── Renderizar Tabela ────────────────────────────────────
function renderTable(clients, pagination) {
    const table = document.getElementById('clients-table');

    if (!clients || clients.length === 0) {
        table.innerHTML = `
            <div class="text-center py-16 px-6">
                <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3 class="text-h3 text-ink mb-2">Nenhum cliente cadastrado</h3>
                <p class="text-ink-secondary text-sm mb-6">Adicione seu primeiro cliente para começar.</p>
                <button onclick="openCreateModal()" class="bg-primary text-white font-medium px-6 py-2.5 rounded-lg hover:bg-primary-600 transition-all">+ Adicionar Cliente</button>
            </div>`;
        document.getElementById('pagination').classList.add('hidden');
        return;
    }

    document.getElementById('pagination').classList.remove('hidden');

    table.innerHTML = `
        <table class="w-full">
            <thead>
                <tr class="bg-surface/80 border-b border-border">
                    <th class="text-left px-6 py-3 text-xs font-semibold text-ink-muted uppercase tracking-wider">Nome</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-ink-muted uppercase tracking-wider hidden md:table-cell">Documento</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-ink-muted uppercase tracking-wider hidden sm:table-cell">Telefone</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-ink-muted uppercase tracking-wider hidden lg:table-cell">Cidade</th>
                    <th class="text-right px-6 py-3 text-xs font-semibold text-ink-muted uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                ${clients.map(c => `
                    <tr class="hover:bg-surface/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-primary/10 text-primary rounded-full flex items-center justify-center font-semibold text-sm flex-shrink-0">
                                    ${c.name.charAt(0).toUpperCase()}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-ink">${escapeHtml(c.name)}</p>
                                    ${c.email ? `<p class="text-xs text-ink-muted">${escapeHtml(c.email)}</p>` : ''}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-ink-secondary hidden md:table-cell">${c.document_cpf || c.document_cnpj || '—'}</td>
                        <td class="px-6 py-4 text-sm text-ink-secondary hidden sm:table-cell">${c.phone || '—'}</td>
                        <td class="px-6 py-4 text-sm text-ink-secondary hidden lg:table-cell">${c.city || '—'}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1">
                                ${c.whatsapp ? `
                                    <a href="https://wa.me/55${c.whatsapp.replace(/\\D/g, '').replace(/^55/, '')}?text=Olá%20${encodeURIComponent(c.name)}!%20Tudo%20bem?"
                                       target="_blank" title="Enviar WhatsApp"
                                       class="w-8 h-8 rounded-lg flex items-center justify-center text-whatsapp hover:bg-whatsapp/10 transition-colors">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                    </a>` : ''}
                                <button onclick="openEditModal(${c.id})" title="Editar"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center text-ink-secondary hover:bg-primary/10 hover:text-primary transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button onclick="openDeleteModal(${c.id}, '${escapeHtml(c.name)}')" title="Excluir"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center text-ink-secondary hover:bg-danger/10 hover:text-danger transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                `).join('')}
            </tbody>
        </table>`;

    // Paginação
    document.getElementById('pagination-info').textContent =
        `Mostrando ${clients.length} de ${pagination.total} clientes (página ${pagination.page} de ${pagination.totalPages})`;
    document.getElementById('prev-page').disabled = pagination.page <= 1;
    document.getElementById('next-page').disabled = pagination.page >= pagination.totalPages;
}

// ── Ações de Navegação ──────────────────────────────────
function goPage(delta) {
    const btn = delta > 0 ? document.getElementById('next-page') : document.getElementById('prev-page');
    if (btn.disabled) return;
    currentPage += delta;
    loadClients();
}

// ── Search com Debounce ─────────────────────────────────
let searchTimeout;
document.getElementById('search-input').addEventListener('input', (e) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        currentSearch = e.target.value.trim();
        currentPage = 1;
        loadClients();
    }, 300);
});

document.getElementById('per-page').addEventListener('change', (e) => {
    currentPerPage = parseInt(e.target.value);
    currentPage = 1;
    loadClients();
});

// ── Modal CRUD ──────────────────────────────────────────
function openCreateModal() {
    document.getElementById('modal-title').textContent = 'Novo Cliente';
    document.getElementById('modal-submit-btn').textContent = 'Salvar Cliente';
    document.getElementById('client-form').reset();
    document.getElementById('client-id').value = '';
    document.getElementById('client-modal').classList.remove('hidden');
}

async function openEditModal(clientId) {
    try {
        const token = '<?= getToken() ?>';
        const response = await fetch(`${API_BASE}/clients/${clientId}`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        if (!response.ok) throw new Error('Erro ao carregar cliente');
        const data = await response.json();
        const c = data.client;

        document.getElementById('modal-title').textContent = 'Editar Cliente';
        document.getElementById('modal-submit-btn').textContent = 'Atualizar';
        document.getElementById('client-id').value = c.id;
        document.getElementById('field-name').value = c.name || '';
        document.getElementById('field-email').value = c.email || '';
        document.getElementById('field-phone').value = c.phone || '';
        document.getElementById('field-whatsapp').value = c.whatsapp || '';
        document.getElementById('field-cpf').value = c.document_cpf || '';
        document.getElementById('field-cnpj').value = c.document_cnpj || '';
        document.getElementById('field-address').value = c.address || '';
        document.getElementById('field-city').value = c.city || '';
        document.getElementById('field-state').value = c.state || '';
        document.getElementById('field-notes').value = c.notes || '';

        document.getElementById('client-modal').classList.remove('hidden');
    } catch (err) {
        showToast(err.message, 'error');
    }
}

function closeModal() {
    document.getElementById('client-modal').classList.add('hidden');
}

// ── Submit do Formulário ────────────────────────────────
document.getElementById('client-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('modal-submit-btn');
    const clientId = document.getElementById('client-id').value;
    const isEdit = !!clientId;
    const name = document.getElementById('field-name').value.trim();

    if (!name) {
        document.getElementById('field-name').classList.add('border-danger');
        showToast('Nome é obrigatório', 'error');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="inline-block w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin mr-2"></span> Salvando...';

    try {
        const token = '<?= getToken() ?>';
        const payload = {
            name,
            email: document.getElementById('field-email').value.trim() || null,
            phone: document.getElementById('field-phone').value.trim() || null,
            whatsapp: document.getElementById('field-whatsapp').value.trim() || null,
            documentCpf: document.getElementById('field-cpf').value.trim() || null,
            documentCnpj: document.getElementById('field-cnpj').value.trim() || null,
            address: document.getElementById('field-address').value.trim() || null,
            city: document.getElementById('field-city').value.trim() || null,
            state: document.getElementById('field-state').value.trim().toUpperCase() || null,
            notes: document.getElementById('field-notes').value.trim() || null,
        };

        const url = isEdit ? `${API_BASE}/clients/${clientId}` : `${API_BASE}/clients`;
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
        loadClients();
    } catch (err) {
        showToast(err.message, 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = isEdit ? 'Atualizar' : 'Salvar Cliente';
    }
});

// ── Exclusão ────────────────────────────────────────────
function openDeleteModal(clientId, clientName) {
    deleteClientId = clientId;
    document.querySelector('#delete-modal p').textContent = `Tem certeza que deseja excluir "${clientName}"?`;
    document.getElementById('delete-modal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('delete-modal').classList.add('hidden');
    deleteClientId = null;
}

document.getElementById('confirm-delete-btn').addEventListener('click', async () => {
    if (!deleteClientId) return;
    const btn = document.getElementById('confirm-delete-btn');
    btn.disabled = true;
    btn.innerHTML = '<span class="inline-block w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin mr-2"></span> Excluindo...';

    try {
        const token = '<?= getToken() ?>';
        const response = await fetch(`${API_BASE}/clients/${deleteClientId}`, {
            method: 'DELETE',
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const data = await response.json();
        if (!response.ok) throw new Error(data.message || 'Erro ao excluir');

        showToast('✅ ' + data.message, 'success');
        closeDeleteModal();
        loadClients();
    } catch (err) {
        showToast(err.message, 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = 'Confirmar Exclusão';
    }
});

// ── Helpers ─────────────────────────────────────────────
function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str || '';
    return div.innerHTML;
}

// ── Init ────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', loadClients);
</script>
