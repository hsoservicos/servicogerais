<section class="min-h-screen bg-background">
    <?php
    $pageTitle = 'Agendamentos';
    $pageSubtitle = 'Gerencie os agendamentos de trabalhadores';
    $currentPage = 'schedules';
    require __DIR__ . '/partials/sidebar.php';
    require __DIR__ . '/partials/topbar.php';
    ?>

    <main class="md:ml-64 pt-16 p-6">
        <div class="max-w-7xl mx-auto">

            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Agendamentos</h1>
                    <p class="text-sm text-gray-500 mt-1">Gerencie diárias e serviços agendados</p>
                </div>
                <button onclick="openCreateModal()" class="btn-primary flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Novo Agendamento
                </button>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 border-b border-gray-100 flex flex-wrap gap-3">
                    <select id="filter-status" onchange="loadSchedules()" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                        <option value="">Todos os status</option>
                        <option value="scheduled">Agendado</option>
                        <option value="confirmed">Confirmado</option>
                        <option value="in_progress">Em Andamento</option>
                        <option value="completed">Concluído</option>
                        <option value="cancelled">Cancelado</option>
                    </select>
                    <input type="date" id="filter-date-from" onchange="loadSchedules()" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                    <input type="date" id="filter-date-to" onchange="loadSchedules()" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                </div>

                <div id="skeleton-loading" class="p-8 space-y-4">
                    <div class="animate-pulse flex gap-4"><div class="h-4 bg-gray-200 rounded w-1/4"></div><div class="h-4 bg-gray-200 rounded w-1/4"></div><div class="h-4 bg-gray-200 rounded w-1/6"></div></div>
                    <div class="animate-pulse flex gap-4"><div class="h-4 bg-gray-200 rounded w-1/3"></div><div class="h-4 bg-gray-200 rounded w-1/4"></div><div class="h-4 bg-gray-200 rounded w-1/6"></div></div>
                    <div class="animate-pulse flex gap-4"><div class="h-4 bg-gray-200 rounded w-1/4"></div><div class="h-4 bg-gray-200 rounded w-1/3"></div><div class="h-4 bg-gray-200 rounded w-1/6"></div></div>
                </div>

                <div id="empty-state" class="hidden p-12 text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum agendamento encontrado</h3>
                    <p class="text-gray-500 mb-4">Crie um novo agendamento para começar.</p>
                    <button onclick="openCreateModal()" class="btn-primary">Criar Agendamento</button>
                </div>

                <div id="error-state" class="hidden p-12 text-center">
                    <svg class="w-16 h-16 text-danger mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Erro ao carregar</h3>
                    <p id="error-message" class="text-gray-500 mb-4">Tente novamente.</p>
                    <button onclick="loadSchedules()" class="btn-secondary">Tentar Novamente</button>
                </div>

                <div id="table-container" class="hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50 text-left text-sm font-medium text-gray-500">
                                    <th class="px-6 py-3">Data</th>
                                    <th class="px-6 py-3">Trabalhador</th>
                                    <th class="px-6 py-3">Cliente</th>
                                    <th class="px-6 py-3">Categoria</th>
                                    <th class="px-6 py-3">Regime</th>
                                    <th class="px-6 py-3">Valor</th>
                                    <th class="px-6 py-3">Status</th>
                                    <th class="px-6 py-3 w-24">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="schedules-tbody" class="divide-y divide-gray-100 text-sm"></tbody>
                        </table>
                    </div>
                    <div id="pagination" class="flex items-center justify-between px-6 py-4 border-t border-gray-100"></div>
                </div>
            </div>
        </div>
    </main>

    <div id="create-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeCreateModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-lg font-bold">Novo Agendamento</h2>
                    <button onclick="closeCreateModal()" class="p-1 hover:bg-gray-100 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Trabalhador</label>
                        <select id="create-worker-id" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm"></select>
                        <p id="frequency-warning" class="hidden text-xs text-amber-600 mt-1 flex items-center gap-1">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>
                            <span id="frequency-warning-text"></span>
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
                        <select id="create-client-id" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm"></select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Categoria do Serviço</label>
                        <select id="create-service-category" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            <option value="EMPREGADO_DOMESTICO_GERAL">Empregada Doméstica Geral</option>
                            <option value="DIARISTA">Diarista (Autônoma)</option>
                            <option value="BABA">Babá / Cuidador Infantil</option>
                            <option value="CUIDADOR_IDOSOS">Cuidador de Idosos</option>
                            <option value="COZINHEIRO">Cozinheiro(a) Doméstico</option>
                            <option value="MOTORISTA">Motorista Particular</option>
                            <option value="JARDINEIRO">Jardineiro Residencial</option>
                            <option value="CASEIRO">Caseiro / Zelador</option>
                            <option value="GOVERNANTA">Governanta / Mordomo</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Regime</label>
                        <select id="create-regime" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            <option value="AUTONOMO_DIARISTA">Autônomo / Diarista</option>
                            <option value="LC_150_CLT">CLT (carteira assinada)</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Data</label>
                            <input type="date" id="create-date" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Valor (R$)</label>
                            <input type="number" step="0.01" id="create-amount" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Início</label>
                            <input type="time" id="create-start" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fim</label>
                            <input type="time" id="create-end" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Vale Transporte (R$)</label>
                        <input type="number" step="0.01" id="create-transport" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                        <textarea id="create-notes" rows="2" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm"></textarea>
                    </div>
                </div>
                <div class="p-6 border-t border-gray-100 flex justify-end gap-3">
                    <button onclick="closeCreateModal()" class="btn-secondary">Cancelar</button>
                    <button onclick="createSchedule()" id="create-btn" class="btn-primary">
                        <span id="create-btn-text">Criar Agendamento</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="status-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeStatusModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center">
                <svg class="w-16 h-16 mx-auto mb-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-lg font-bold mb-2" id="status-modal-title">Confirmar</h3>
                <p class="text-gray-500 mb-6" id="status-modal-text">Tem certeza?</p>
                <div class="flex justify-center gap-3">
                    <button onclick="closeStatusModal()" class="btn-secondary">Cancelar</button>
                    <button id="status-btn-confirm" onclick="confirmStatusAction()" class="btn-primary">Confirmar</button>
                </div>
            </div>
        </div>
    </div>

</section>

<script>
let currentPage = 1;
let pendingStatusAction = null;

document.addEventListener('DOMContentLoaded', () => {
    loadSchedules();
    loadWorkers();
    loadClients();
});

async function loadWorkers() {
    try {
        const token = getToken();
        const resp = await fetch('/api/v1/workers?perPage=200', { headers: { 'Authorization': `Bearer ${token}` } });
        const data = await resp.json();
        const select = document.getElementById('create-worker-id');
        select.innerHTML = '<option value="">Selecione um trabalhador</option>';
        (data.workers || []).forEach(w => {
            select.innerHTML += `<option value="${w.id}" data-category="${w.worker_category}">${w.name} (${w.worker_category})</option>`;
        });
        select.onchange = function() {
            const opt = this.options[this.selectedIndex];
            const cat = opt?.dataset?.category;
            const warning = document.getElementById('frequency-warning');
            if (cat === 'DIARISTA') {
                warning.classList.remove('hidden');
                document.getElementById('frequency-warning-text').textContent = 'Diarista: limite de 2 dias/semana no mesmo tomador.';
            } else {
                warning.classList.add('hidden');
            }
        };
    } catch (e) { showToast('Erro ao carregar trabalhadores', 'error'); }
}

async function loadClients() {
    try {
        const token = getToken();
        const resp = await fetch('/api/v1/clients?perPage=200', { headers: { 'Authorization': `Bearer ${token}` } });
        const data = await resp.json();
        const select = document.getElementById('create-client-id');
        select.innerHTML = '<option value="">Selecione um cliente</option>';
        (data.clients || []).forEach(c => {
            select.innerHTML += `<option value="${c.id}">${c.name}</option>`;
        });
    } catch (e) { showToast('Erro ao carregar clientes', 'error'); }
}

async function loadSchedules() {
    const token = getToken();
    const status = document.getElementById('filter-status').value;
    const dateFrom = document.getElementById('filter-date-from').value;
    const dateTo = document.getElementById('filter-date-to').value;

    document.getElementById('skeleton-loading').classList.remove('hidden');
    document.getElementById('table-container').classList.add('hidden');
    document.getElementById('empty-state').classList.add('hidden');
    document.getElementById('error-state').classList.add('hidden');

    try {
        let url = `/api/v1/schedules?page=${currentPage}&perPage=20`;
        if (status) url += `&status=${status}`;
        if (dateFrom) url += `&dateFrom=${dateFrom}`;
        if (dateTo) url += `&dateTo=${dateTo}`;

        const resp = await fetch(url, { headers: { 'Authorization': `Bearer ${token}` } });
        if (resp.status === 401) { window.location.href = '?page=login'; return; }
        const data = await resp.json();

        document.getElementById('skeleton-loading').classList.add('hidden');

        if (!data.schedules || data.schedules.length === 0) {
            document.getElementById('empty-state').classList.remove('hidden');
            return;
        }

        document.getElementById('table-container').classList.remove('hidden');
        renderTable(data.schedules);
        renderPagination(data.pagination);
    } catch (e) {
        document.getElementById('skeleton-loading').classList.add('hidden');
        document.getElementById('error-state').classList.remove('hidden');
        document.getElementById('error-message').textContent = e.message || 'Erro de conexão';
    }
}

function renderTable(schedules) {
    const tbody = document.getElementById('schedules-tbody');
    tbody.innerHTML = schedules.map(s => {
        const statusLabels = { scheduled: 'Agendado', confirmed: 'Confirmado', in_progress: 'Em Andamento', completed: 'Concluído', cancelled: 'Cancelado' };
        const statusColors = { scheduled: 'bg-blue-100 text-blue-800', confirmed: 'bg-amber-100 text-amber-800', in_progress: 'bg-purple-100 text-purple-800', completed: 'bg-green-100 text-green-800', cancelled: 'bg-red-100 text-red-800' };
        return `
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4">${formatDate(s.scheduled_date)}</td>
                <td class="px-6 py-4 font-medium">${escHtml(s.worker_name || '—')}</td>
                <td class="px-6 py-4">${escHtml(s.client_name || '—')}</td>
                <td class="px-6 py-4 text-xs">${s.service_category}</td>
                <td class="px-6 py-4 text-xs">${s.regime === 'AUTONOMO_DIARISTA' ? 'Autônomo' : 'CLT'}</td>
                <td class="px-6 py-4">${formatMoney(s.total_amount)}</td>
                <td class="px-6 py-4"><span class="px-2.5 py-1 rounded-full text-xs font-medium ${statusColors[s.status] || 'bg-gray-100'}">${statusLabels[s.status] || s.status}</span></td>
                <td class="px-6 py-4">
                    <div class="flex gap-1">
                        ${s.status === 'scheduled' ? `<button onclick="updateScheduleStatus(${s.id},'confirmed')" class="p-1.5 hover:bg-amber-50 rounded text-amber-600" title="Confirmar"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></button>` : ''}
                        ${s.status === 'confirmed' ? `<button onclick="updateScheduleStatus(${s.id},'in_progress')" class="p-1.5 hover:bg-purple-50 rounded text-purple-600" title="Iniciar"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/></svg></button>` : ''}
                        ${s.status === 'in_progress' ? `<button onclick="updateScheduleStatus(${s.id},'completed')" class="p-1.5 hover:bg-green-50 rounded text-green-600" title="Concluir"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></button>` : ''}
                        ${!['completed','cancelled'].includes(s.status) ? `<button onclick="updateScheduleStatus(${s.id},'cancelled')" class="p-1.5 hover:bg-red-50 rounded text-red-500" title="Cancelar"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>` : ''}
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

function renderPagination(p) {
    const div = document.getElementById('pagination');
    if (!p || p.totalPages <= 1) { div.innerHTML = ''; return; }
    div.innerHTML = `
        <span class="text-sm text-gray-500">Mostrando página ${p.page} de ${p.totalPages} (${p.total} registros)</span>
        <div class="flex gap-2">
            <button onclick="goToPage(${p.page - 1})" class="px-3 py-1.5 border border-gray-200 rounded-lg text-sm ${p.page <= 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'}" ${p.page <= 1 ? 'disabled' : ''}>Anterior</button>
            <button onclick="goToPage(${p.page + 1})" class="px-3 py-1.5 border border-gray-200 rounded-lg text-sm ${p.page >= p.totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'}" ${p.page >= p.totalPages ? 'disabled' : ''}>Próxima</button>
        </div>
    `;
}

function goToPage(page) { currentPage = page; loadSchedules(); }

function openCreateModal() {
    document.getElementById('create-modal').classList.remove('hidden');
    document.getElementById('create-date').value = new Date().toISOString().split('T')[0];
}

function closeCreateModal() {
    document.getElementById('create-modal').classList.add('hidden');
}

async function createSchedule() {
    const btn = document.getElementById('create-btn');
    const text = document.getElementById('create-btn-text');
    btn.disabled = true;
    text.textContent = 'Criando...';

    try {
        const token = getToken();
        const body = {
            workerId: parseInt(document.getElementById('create-worker-id').value),
            clientId: parseInt(document.getElementById('create-client-id').value),
            serviceCategory: document.getElementById('create-service-category').value,
            regime: document.getElementById('create-regime').value,
            scheduledDate: document.getElementById('create-date').value,
            startTime: document.getElementById('create-start').value || null,
            endTime: document.getElementById('create-end').value || null,
            totalAmount: parseFloat(document.getElementById('create-amount').value) || null,
            transportVoucher: parseFloat(document.getElementById('create-transport').value) || 0,
            notes: document.getElementById('create-notes').value || null,
        };

        const resp = await fetch('/api/v1/schedules', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` },
            body: JSON.stringify(body),
        });

        const data = await resp.json();

        if (!resp.ok) {
            if (data.error === 'ERR_FREQUENCY_LIMIT') {
                showToast('⚠️ ' + data.message, 'warning');
                if (data.details?.transitionUrl) {
                    showToast('Considere contratar como CLT: Transição disponível', 'info');
                }
                return;
            }
            throw new Error(data.message || 'Erro ao criar');
        }

        showToast('✅ ' + data.message, 'success');
        if (data.alert) {
            setTimeout(() => showToast('⚠️ ' + data.alert, 'warning'), 500);
        }
        closeCreateModal();
        loadSchedules();
    } catch (e) {
        showToast(e.message, 'error');
    } finally {
        btn.disabled = false;
        text.textContent = 'Criar Agendamento';
    }
}

function updateScheduleStatus(id, newStatus) {
    const labels = { confirmed: 'Confirmar agendamento?', in_progress: 'Iniciar serviço?', completed: 'Concluir serviço?', cancelled: 'Cancelar agendamento?' };
    const texts = { confirmed: 'O status será alterado para Confirmado.', in_progress: 'O serviço será marcado como Em Andamento.', completed: 'O serviço será marcado como Concluído.', cancelled: 'Esta ação não pode ser desfeita.' };

    document.getElementById('status-modal-title').textContent = labels[newStatus] || 'Alterar status?';
    document.getElementById('status-modal-text').textContent = texts[newStatus] || 'Confirma a alteração?';
    document.getElementById('status-btn-confirm').textContent = labels[newStatus] || 'Confirmar';
    pendingStatusAction = { id, status: newStatus };
    document.getElementById('status-modal').classList.remove('hidden');
}

function closeStatusModal() {
    document.getElementById('status-modal').classList.add('hidden');
    pendingStatusAction = null;
}

async function confirmStatusAction() {
    if (!pendingStatusAction) return;
    const { id, status } = pendingStatusAction;
    closeStatusModal();

    try {
        const token = getToken();
        const resp = await fetch(`/api/v1/schedules/${id}/status`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` },
            body: JSON.stringify({ status }),
        });

        if (!resp.ok) {
            const data = await resp.json();
            throw new Error(data.message || 'Erro ao atualizar');
        }

        showToast('✅ Status atualizado!', 'success');
        loadSchedules();
    } catch (e) {
        showToast(e.message, 'error');
    }
}

function getToken() {
    return <?= json_encode(getToken()) ?> || '';
}

function escHtml(str) {
    if (!str) return '—';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

function formatDate(d) {
    if (!d) return '—';
    const date = new Date(d);
    return date.toLocaleDateString('pt-BR');
}

function formatMoney(v) {
    if (!v || v === 0) return '—';
    return 'R$ ' + parseFloat(v).toFixed(2).replace('.', ',');
}

function showToast(msg, type) {
    const container = document.getElementById('toast-container') || (() => {
        const c = document.createElement('div');
        c.id = 'toast-container';
        c.className = 'fixed top-4 right-4 z-[100] flex flex-col gap-2';
        document.body.appendChild(c);
        return c;
    })();

    const colors = { success: 'bg-success/10 text-success border-success/20', error: 'bg-danger/10 text-danger border-danger/20', warning: 'bg-amber-50 text-amber-800 border-amber-200', info: 'bg-blue-50 text-blue-800 border-blue-200' };
    const toast = document.createElement('div');
    toast.className = `px-4 py-3 rounded-lg border shadow-lg text-sm font-medium animate-slide-in ${colors[type] || colors.info} max-w-sm`;
    toast.textContent = msg;
    container.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; toast.style.transition = 'opacity 0.3s'; setTimeout(() => toast.remove(), 300); }, 4000);
}
</script>
<style>
.animate-slide-in { animation: slideIn 0.3s ease-out; }
@keyframes slideIn { from { opacity: 0; transform: translateX(100px); } to { opacity: 1; transform: translateX(0); } }
</style>
