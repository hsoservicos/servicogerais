<?php
/**
 * ═══════════════════════════════════════════════════════════════
 * templates/workers.php — Gestão de Trabalhadores Domésticos
 * ═══════════════════════════════════════════════════════════════
 */

if (!isAuthenticated()) {
    header('Location: ?page=login');
    exit;
}
?>

<?php $currentPage = 'workers'; require __DIR__ . '/partials/sidebar.php'; ?>

<div class="md:ml-64 min-h-screen flex flex-col">
    <?php
    $pageTitle = 'Trabalhadores';
    $pageSubtitle = 'Gerencie seus trabalhadores domésticos';
    $topbarExtra = '<button onclick="openCreateModal()" class="bg-primary text-white font-medium px-4 py-2 rounded-lg hover:bg-primary-600 active:bg-primary-700 transition-all duration-200 flex items-center gap-2 shadow-card text-sm"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Novo Trabalhador</button>';
    require __DIR__ . '/partials/topbar.php';
    ?>

    <main class="flex-1 p-6">
        <div class="flex flex-col sm:flex-row gap-4 mb-6">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-ink-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" id="search-input" placeholder="Buscar por nome ou CPF..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
            </div>
            <select id="category-filter" class="px-4 py-2.5 rounded-lg border border-border bg-surface text-ink focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all text-sm">
                <option value="">Todas as Categorias</option>
                <option value="EMPREGADO_DOMESTICO_GERAL">Doméstica Geral</option>
                <option value="DIARISTA">Diarista</option>
                <option value="BABA">Babá</option>
                <option value="CUIDADOR_IDOSOS">Cuidador de Idosos</option>
                <option value="COZINHEIRO">Cozinheiro(a)</option>
                <option value="MOTORISTA">Motorista</option>
                <option value="JARDINEIRO">Jardineiro</option>
                <option value="CASEIRO">Caseiro</option>
                <option value="GOVERNANTA">Governanta</option>
            </select>
            <select id="per-page" class="px-4 py-2.5 rounded-lg border border-border bg-surface text-ink focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all text-sm">
                <option value="10">10 por página</option>
                <option value="20" selected>20 por página</option>
                <option value="50">50 por página</option>
            </select>
        </div>

        <div class="bg-white rounded-xl shadow-card border border-border overflow-hidden">
            <div id="workers-table">
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

<div id="worker-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" onclick="closeModal()"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-modal w-full max-w-lg animate-fade-in">
            <div class="flex items-center justify-between px-6 py-4 border-b border-border">
                <h3 id="modal-title" class="text-h3 text-ink">Novo Trabalhador</h3>
                <button onclick="closeModal()" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-surface transition-colors">
                    <svg class="w-5 h-5 text-ink-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="worker-form" class="p-6 space-y-4" novalidate>
                <input type="hidden" id="worker-id" value="">

                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">Nome <span class="text-danger">*</span></label>
                    <input type="text" id="worker-name" required
                        class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                        placeholder="Nome completo">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">CPF <span class="text-danger">*</span></label>
                        <input type="text" id="worker-cpf" required maxlength="14"
                            class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                            placeholder="000.000.000-00"
                            data-mask="cpf" data-validate="cpf">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">RG</label>
                        <input type="text" id="worker-rg"
                            class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                            placeholder="RG">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">Código CBO <span class="text-danger">*</span></label>
                        <input type="text" id="worker-cbo" required placeholder="Ex: 5121-05"
                            class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">Categoria <span class="text-danger">*</span></label>
                        <select id="worker-category" required
                            class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                            <option value="">Selecione...</option>
                            <option value="EMPREGADO_DOMESTICO_GERAL">Doméstica Geral</option>
                            <option value="DIARISTA">Diarista</option>
                            <option value="BABA">Babá</option>
                            <option value="CUIDADOR_IDOSOS">Cuidador de Idosos</option>
                            <option value="COZINHEIRO">Cozinheiro(a)</option>
                            <option value="MOTORISTA">Motorista</option>
                            <option value="JARDINEIRO">Jardineiro</option>
                            <option value="CASEIRO">Caseiro</option>
                            <option value="GOVERNANTA">Governanta</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">Telefone</label>
                        <input type="tel" id="worker-phone"
                            class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                            placeholder="(11) 99999-9999"
                            data-mask="phone">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">WhatsApp</label>
                        <input type="tel" id="worker-whatsapp"
                            class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                            placeholder="(11) 99999-9999"
                            data-mask="phone">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">E-mail</label>
                        <input type="email" id="worker-email"
                            class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                            placeholder="email@exemplo.com"
                            data-validate="email">
                </div>

                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">Chave PIX</label>
                    <input type="text" id="worker-pix"
                        class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                        placeholder="CPF, e-mail, telefone ou chave aleatória">
                </div>

                <div class="flex gap-3 justify-end pt-4 border-t border-border">
                    <button type="button" onclick="closeModal()"
                        class="px-4 py-2.5 rounded-lg border border-border text-ink-secondary hover:bg-surface transition-all text-sm font-medium">Cancelar</button>
                    <button type="submit" id="submit-btn"
                        class="px-6 py-2.5 rounded-lg bg-primary text-white hover:bg-primary-600 active:bg-primary-700 transition-all text-sm font-medium shadow-card flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const API_BASE = window.API_BASE || '/api/v1';
let currentPage = 1;
let currentSearch = '';
let currentCategory = '';
let editingId = null;

async function loadWorkers(page = 1) {
    currentPage = page;
    const params = new URLSearchParams({ page, perPage: document.getElementById('per-page').value });
    if (currentSearch) params.set('search', currentSearch);
    if (currentCategory) params.set('category', currentCategory);

    document.getElementById('workers-table').innerHTML = '<div class="flex items-center justify-center py-16"><div class="w-10 h-10 border-4 border-primary/30 border-t-primary rounded-full animate-spin"></div></div>';

    try {
        const res = await fetch(`${API_BASE}/workers?${params}`, {
            headers: { 'Authorization': `Bearer ${window.AUTH_TOKEN || ''}` }
        });
        if (!res.ok) throw new Error('Erro ao carregar');

        const data = await res.json();
        renderTable(data.workers || []);
        renderPagination(data.pagination);
    } catch (err) {
        document.getElementById('workers-table').innerHTML = `<div class="flex items-center justify-center py-16 text-danger"><p>Erro ao carregar trabalhadores.</p></div>`;
    }
}

function renderTable(workers) {
    if (workers.length === 0) {
        document.getElementById('workers-table').innerHTML = `
            <div class="flex flex-col items-center justify-center py-16 text-ink-muted">
                <svg class="w-16 h-16 mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <p class="text-lg font-medium mb-1">Nenhum trabalhador encontrado</p>
                <p class="text-sm">Cadastre seu primeiro trabalhador clicando em "Novo Trabalhador"</p>
            </div>`;
        return;
    }

    const categoryLabels = {
        'EMPREGADO_DOMESTICO_GERAL': 'Doméstica Geral',
        'DIARISTA': 'Diarista',
        'BABA': 'Babá',
        'CUIDADOR_IDOSOS': 'Cuidador de Idosos',
        'COZINHEIRO': 'Cozinheiro(a)',
        'MOTORISTA': 'Motorista',
        'JARDINEIRO': 'Jardineiro',
        'CASEIRO': 'Caseiro',
        'GOVERNANTA': 'Governanta',
    };

    const bgStatus = {
        'PENDING': 'bg-warning/10 text-warning',
        'APPROVED': 'bg-success/10 text-success',
        'REJECTED': 'bg-danger/10 text-danger',
    };

    document.getElementById('workers-table').innerHTML = `
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-border bg-surface/50">
                        <th class="text-left px-6 py-3 text-xs font-semibold text-ink-secondary uppercase tracking-wider">Nome</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-ink-secondary uppercase tracking-wider">CPF</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-ink-secondary uppercase tracking-wider">Categoria</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-ink-secondary uppercase tracking-wider">CBO</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-ink-secondary uppercase tracking-wider">Background</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-ink-secondary uppercase tracking-wider">WhatsApp</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold text-ink-secondary uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    ${workers.map(w => `
                        <tr class="hover:bg-surface/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-medium text-ink">${w.name}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-ink-secondary">${w.cpf}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-primary/10 text-primary">${categoryLabels[w.worker_category] || w.worker_category}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-ink-secondary">${w.cbo_code}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium ${bgStatus[w.background_check_status] || 'bg-ink-muted/10 text-ink-muted'}">${w.background_check_status}</span>
                            </td>
                            <td class="px-6 py-4">
                                ${w.whatsapp ? `<a href="https://wa.me/55${w.whatsapp.replace(/\D/g,'').replace(/^55/,'')}" target="_blank" class="inline-flex items-center gap-1 text-sm text-[#25D366] hover:underline"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg> WhatsApp</a>` : '<span class="text-sm text-ink-muted">—</span>'}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button onclick="editWorker(${w.id})" class="text-ink-secondary hover:text-primary transition-colors p-1" title="Editar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button onclick="deleteWorker(${w.id})" class="text-ink-secondary hover:text-danger transition-colors p-1" title="Excluir">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;
}

function renderPagination(pagination) {
    const { page, totalPages, total, perPage } = pagination;
    document.getElementById('pagination-info').textContent = `Mostrando página ${page} de ${totalPages} (${total} trabalhadores)`;
    document.getElementById('prev-page').disabled = page <= 1;
    document.getElementById('next-page').disabled = page >= totalPages;
}

function goPage(delta) {
    loadWorkers(currentPage + delta);
}

function openCreateModal() {
    editingId = null;
    document.getElementById('modal-title').textContent = 'Novo Trabalhador';
    document.getElementById('worker-form').reset();
    document.getElementById('worker-id').value = '';
    document.getElementById('submit-btn').innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Salvar';
    document.getElementById('worker-modal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('worker-modal').classList.add('hidden');
}

async function editWorker(id) {
    try {
        const res = await fetch(`${API_BASE}/workers/${id}`, {
            headers: { 'Authorization': `Bearer ${window.AUTH_TOKEN || ''}` }
        });
        if (!res.ok) throw new Error('Erro ao carregar');
        const data = await res.json();
        const w = data.worker;

        editingId = id;
        document.getElementById('modal-title').textContent = 'Editar Trabalhador';
        document.getElementById('worker-id').value = id;
        document.getElementById('worker-name').value = w.name;
        document.getElementById('worker-cpf').value = w.cpf;
        document.getElementById('worker-rg').value = w.rg || '';
        document.getElementById('worker-cbo').value = w.cbo_code;
        document.getElementById('worker-category').value = w.worker_category;
        document.getElementById('worker-phone').value = w.phone || '';
        document.getElementById('worker-whatsapp').value = w.whatsapp || '';
        document.getElementById('worker-email').value = w.email || '';
        document.getElementById('worker-pix').value = w.pix_key || '';
        document.getElementById('submit-btn').innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Atualizar';
        document.getElementById('worker-modal').classList.remove('hidden');
    } catch (err) {
        showToast('Erro ao carregar dados do trabalhador', 'error');
    }
}

async function deleteWorker(id) {
    if (!confirm('Tem certeza que deseja excluir este trabalhador?')) return;
    try {
        const res = await fetch(`${API_BASE}/workers/${id}`, {
            method: 'DELETE',
            headers: { 'Authorization': `Bearer ${window.AUTH_TOKEN || ''}` }
        });
        if (!res.ok) throw new Error('Erro ao excluir');
        showToast('Trabalhador excluído com sucesso!', 'success');
        loadWorkers(currentPage);
    } catch (err) {
        showToast('Erro ao excluir trabalhador', 'error');
    }
}

document.getElementById('worker-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('worker-id').value;
    const isEdit = !!id;

    // Validar CPF e email
    const cpf = document.getElementById('worker-cpf').value.trim();
    const email = document.getElementById('worker-email').value.trim();

    if (!AppValidation.validateCPF(cpf)) {
        showToast('CPF inválido', 'error');
        document.getElementById('worker-cpf').classList.add('border-danger');
        return;
    }
    if (email && !AppValidation.validateEmail(email)) {
        showToast('E-mail inválido', 'error');
        document.getElementById('worker-email').classList.add('border-danger');
        return;
    }

    const body = {
        name: document.getElementById('worker-name').value,
        cpf: document.getElementById('worker-cpf').value,
        rg: document.getElementById('worker-rg').value,
        cboCode: document.getElementById('worker-cbo').value,
        workerCategory: document.getElementById('worker-category').value,
        phone: document.getElementById('worker-phone').value,
        whatsapp: document.getElementById('worker-whatsapp').value,
        email: document.getElementById('worker-email').value,
        pixKey: document.getElementById('worker-pix').value,
    };

    try {
        const res = await fetch(`${API_BASE}/workers${isEdit ? '/' + id : ''}`, {
            method: isEdit ? 'PUT' : 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${window.AUTH_TOKEN || ''}`
            },
            body: JSON.stringify(body),
        });

        const data = await res.json();

        if (!res.ok) {
            throw new Error(data.message || 'Erro ao salvar');
        }

        showToast(data.message, 'success');
        closeModal();
        loadWorkers(1);
    } catch (err) {
        showToast(err.message, 'error');
    }
});

document.getElementById('search-input').addEventListener('input', (e) => {
    currentSearch = e.target.value;
    loadWorkers(1);
});

document.getElementById('category-filter').addEventListener('change', (e) => {
    currentCategory = e.target.value;
    loadWorkers(1);
});

document.getElementById('per-page').addEventListener('change', () => {
    loadWorkers(1);
});

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeModal();
});

loadWorkers();
</script>