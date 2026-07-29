<?php
/**
 * ═══════════════════════════════════════════════════════════════
 * templates/proposals.php — Gestão de Propostas (CRUD + Itens + Status)
 * ═══════════════════════════════════════════════════════════════
 */

if (!isAuthenticated()) {
    header('Location: ?page=login');
    exit;
}
?>

<?php $currentPage = 'proposals'; require __DIR__ . '/partials/sidebar.php'; ?>

<!-- Main Content -->
<div class="md:ml-64 min-h-screen flex flex-col">
    <?php
    $pageTitle = 'Propostas';
    $pageSubtitle = 'Gerencie orçamentos e propostas comerciais';
    $topbarExtra = '<button onclick="openCreateModal()" class="bg-primary text-white font-medium px-4 py-2 rounded-lg hover:bg-primary-600 active:bg-primary-700 transition-all duration-200 flex items-center gap-2 shadow-card text-sm"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Nova Proposta</button>';
    require __DIR__ . '/partials/topbar.php';
    ?>

    <main class="flex-1 p-6">

        <!-- ── Status Filter Tabs ── -->
        <div class="flex flex-wrap gap-2 mb-6">
            <?php
            $statusTabs = [
                ''          => ['label' => 'Todas', 'color' => ''],
                'draft'     => ['label' => 'Rascunho', 'color' => 'bg-gray-100 text-gray-700'],
                'sent'      => ['label' => 'Enviadas', 'color' => 'bg-blue-100 text-blue-700'],
                'viewed'    => ['label' => 'Visualizadas', 'color' => 'bg-purple-100 text-purple-700'],
                'accepted'  => ['label' => 'Aceitas', 'color' => 'bg-green-100 text-green-700'],
                'rejected'  => ['label' => 'Rejeitadas', 'color' => 'bg-red-100 text-red-700'],
                'cancelled' => ['label' => 'Canceladas', 'color' => 'bg-gray-100 text-gray-500'],
            ];
            $currentStatus = $_GET['status'] ?? '';
            ?>
            <?php foreach ($statusTabs as $key => $tab): ?>
                <a href="?page=proposals<?= $key ? '&status='.$key : '' ?>"
                    class="px-4 py-1.5 rounded-full text-xs font-medium transition-all duration-200
                    <?= ($currentStatus === $key) ? 'bg-primary text-white shadow-sm' : ($tab['color'] ? $tab['color'].' hover:opacity-80' : 'bg-surface border border-border text-ink-secondary hover:bg-border/30') ?>">
                    <?= $tab['label'] ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- ── Search + Filters ── -->
        <div class="flex flex-col sm:flex-row gap-4 mb-6">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-ink-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" id="search-input" placeholder="Buscar por título, número ou cliente..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
            </div>
            <select id="filter-client"
                class="px-4 py-2.5 rounded-lg border border-border bg-surface text-ink focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all text-sm min-w-[200px]">
                <option value="">Todos os clientes</option>
            </select>
        </div>

        <!-- ── Proposals Table ── -->
        <div class="bg-white rounded-xl shadow-card border border-border overflow-hidden">
            <div id="proposals-table">
                <div class="flex items-center justify-center py-16">
                    <div class="w-10 h-10 border-4 border-primary/30 border-t-primary rounded-full animate-spin"></div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div id="pagination" class="flex items-center justify-between mt-4 text-sm text-ink-secondary hidden">
            <span id="pagination-info">Carregando...</span>
            <div class="flex gap-2">
                <button id="prev-page" onclick="goPage(-1)" disabled class="px-3 py-1.5 rounded-lg border border-border bg-white hover:bg-surface disabled:opacity-40 disabled:cursor-not-allowed transition-all">← Anterior</button>
                <button id="next-page" onclick="goPage(1)" disabled class="px-3 py-1.5 rounded-lg border border-border bg-white hover:bg-surface disabled:opacity-40 disabled:cursor-not-allowed transition-all">Próximo →</button>
            </div>
        </div>
    </main>
</div>

<!-- ══════════════════════════════════════════════════════════
     MODAL: CRUD (Criar / Editar Proposta)
     ══════════════════════════════════════════════════════════ -->
<div id="proposal-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" onclick="closeModal()"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-modal w-full max-w-2xl animate-fade-in max-h-[90vh] flex flex-col">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-border flex-shrink-0">
                <h3 id="modal-title" class="text-h3 text-ink">Nova Proposta</h3>
                <button onclick="closeModal()" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-surface transition-colors">
                    <svg class="w-5 h-5 text-ink-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <!-- Body (scrollable) -->
            <form id="proposal-form" class="p-6 space-y-4 overflow-y-auto flex-1" novalidate>
                <input type="hidden" id="proposal-id" value="">

                <!-- Título -->
                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">Título <span class="text-danger">*</span></label>
                    <input type="text" id="field-title" required
                        class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                        placeholder="Ex: Orçamento para Casamento">
                </div>

                <!-- Cliente + Validade -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">Cliente</label>
                        <select id="field-client"
                            class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                            <option value="">Selecione um cliente</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">Validade</label>
                        <input type="date" id="field-valid-until"
                            class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                    </div>
                </div>

                <!-- Descrição -->
                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">Descrição</label>
                    <textarea id="field-description" rows="2"
                        class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all resize-none"
                        placeholder="Descrição geral da proposta"></textarea>
                </div>

                <!-- ── ITENS DA PROPOSTA ── -->
                <div class="border-t border-border pt-4">
                    <div class="flex items-center justify-between mb-3">
                        <label class="block text-sm font-semibold text-ink">Itens da Proposta</label>
                        <button type="button" onclick="addItemRow()"
                            class="text-xs text-primary font-medium flex items-center gap-1 hover:underline">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Adicionar item
                        </button>
                    </div>

                    <!-- Cabeçalho dos itens -->
                    <div class="hidden sm:grid grid-cols-12 gap-2 mb-2 text-xs font-semibold text-ink-muted uppercase tracking-wider px-1">
                        <div class="col-span-5">Descrição</div>
                        <div class="col-span-2 text-center">Qtd</div>
                        <div class="col-span-2 text-right">Valor Unit.</div>
                        <div class="col-span-2 text-right">Total</div>
                        <div class="col-span-1"></div>
                    </div>

                    <!-- Container dos itens -->
                    <div id="items-container" class="space-y-2">
                        <!-- Itens serão inseridos aqui via JS -->
                    </div>

                    <!-- Total -->
                    <div class="flex justify-end items-center gap-4 mt-3 pt-3 border-t border-border">
                        <span class="text-sm text-ink-secondary font-medium">Total:</span>
                        <span id="items-total" class="text-xl font-bold text-ink">R$ 0,00</span>
                    </div>
                </div>

                <!-- Condições de Pagamento + Observações -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">Condições de Pagamento</label>
                        <textarea id="field-payment-terms" rows="2"
                            class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all resize-none"
                            placeholder="Ex: Entrada de 50% + 2x de R$..."></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">Observações</label>
                        <textarea id="field-notes" rows="2"
                            class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all resize-none"
                            placeholder="Observações internas"></textarea>
                    </div>
                </div>

                <!-- Ações -->
                <div class="flex gap-3 pt-4 border-t border-border">
                    <button type="button" onclick="closeModal()"
                        class="w-1/4 border-2 border-border text-ink font-medium py-2.5 px-4 rounded-lg hover:bg-border/30 transition-all text-sm">Cancelar</button>
                    <button type="submit" id="modal-submit-btn"
                        class="w-3/4 bg-primary text-white font-medium py-2.5 px-4 rounded-lg hover:bg-primary-600 active:bg-primary-700 transition-all duration-200 text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        Salvar Proposta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════
     MODAL: Visualizar Proposta (Detalhes + Status)
     ══════════════════════════════════════════════════════════ -->
<div id="view-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" onclick="closeViewModal()"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-modal w-full max-w-3xl animate-fade-in max-h-[90vh] flex flex-col">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-border flex-shrink-0">
                <div class="flex items-center gap-3">
                    <h3 class="text-h3 text-ink" id="view-number">—</h3>
                    <span id="view-status-badge" class="px-3 py-0.5 rounded-full text-xs font-medium">—</span>
                </div>
                <button onclick="closeViewModal()" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-surface transition-colors">
                    <svg class="w-5 h-5 text-ink-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <!-- Body -->
            <div class="p-6 overflow-y-auto flex-1 space-y-6">
                <!-- Info -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-ink-muted block text-xs uppercase tracking-wider font-semibold mb-1">Cliente</span>
                        <span id="view-client" class="text-ink font-medium">—</span>                         <span id="view-client-whatsapp" class="text-whatsapp text-xs block mt-0.5 hidden">
                             <a id="view-whatsapp-link" href="#" target="_blank" class="hover:underline">💬 Enviar WhatsApp</a>
                         </span>
                          <span id="view-public-link" class="text-xs block mt-1 hidden">
                              <button onclick="copyPublicLink()"
                                  class="inline-flex items-center gap-1 text-primary hover:text-primary-600 font-medium transition-colors">
                                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                  Copiar Link Público
                              </button>
                          </span>
                          <span id="view-pdf-link" class="text-xs block mt-1 hidden">
                              <button onclick="downloadPdf()"
                                  class="inline-flex items-center gap-1 text-danger hover:text-danger/80 font-medium transition-colors">
                                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                  Baixar PDF
                              </button>
                          </span>
                    </div>
                    <div>
                        <span class="text-ink-muted block text-xs uppercase tracking-wider font-semibold mb-1">Validade</span>
                        <span id="view-valid-until" class="text-ink">—</span>
                    </div>
                </div>

                <div id="view-description" class="hidden">
                    <span class="text-ink-muted block text-xs uppercase tracking-wider font-semibold mb-1">Descrição</span>
                    <p id="view-description-text" class="text-ink text-sm">—</p>
                </div>

                <!-- Itens -->
                <div>
                    <span class="text-ink-muted block text-xs uppercase tracking-wider font-semibold mb-3">Itens</span>
                    <div class="overflow-x-auto border border-border rounded-xl">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-surface/80 border-b border-border">
                                    <th class="text-left px-4 py-2.5 text-xs font-semibold text-ink-muted uppercase">Descrição</th>
                                    <th class="text-center px-4 py-2.5 text-xs font-semibold text-ink-muted uppercase">Qtd</th>
                                    <th class="text-right px-4 py-2.5 text-xs font-semibold text-ink-muted uppercase">Valor Unit.</th>
                                    <th class="text-right px-4 py-2.5 text-xs font-semibold text-ink-muted uppercase">Total</th>
                                </tr>
                            </thead>
                            <tbody id="view-items-body" class="divide-y divide-border">
                            </tbody>
                            <tfoot>
                                <tr class="bg-surface/50">
                                    <td colspan="3" class="px-4 py-3 text-right text-sm font-semibold text-ink">Total</td>
                                    <td id="view-total" class="px-4 py-3 text-right text-sm font-bold text-ink">R$ 0,00</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Payment terms + Notes -->
                <div id="view-payment-terms-box" class="hidden">
                    <span class="text-ink-muted block text-xs uppercase tracking-wider font-semibold mb-1">Condições de Pagamento</span>
                    <p id="view-payment-terms" class="text-ink text-sm bg-surface rounded-xl p-3 border border-border">—</p>
                </div>

                <div id="view-notes-box" class="hidden">
                    <span class="text-ink-muted block text-xs uppercase tracking-wider font-semibold mb-1">Observações</span>
                    <p id="view-notes" class="text-ink text-sm bg-surface rounded-xl p-3 border border-border">—</p>
                </div>

                <!-- Timeline -->
                <div>
                    <span class="text-ink-muted block text-xs uppercase tracking-wider font-semibold mb-3">Timeline</span>
                    <div class="flex flex-wrap gap-6 text-xs text-ink-secondary">
                        <span>Criada: <strong id="view-created-at" class="text-ink font-medium">—</strong></span>
                        <span id="view-sent-at-box" class="hidden">Enviada: <strong id="view-sent-at" class="text-ink font-medium">—</strong></span>
                        <span id="view-accepted-at-box" class="hidden">Aceita: <strong id="view-accepted-at" class="text-ink font-medium">—</strong></span>
                    </div>
                </div>

                <!-- Status Actions -->
                <div id="view-actions" class="flex flex-wrap gap-3 pt-4 border-t border-border">
                    <!-- Botões serão inseridos dinamicamente -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════
     MODAL: Confirmar exclusão/cancelamento
     ══════════════════════════════════════════════════════════ -->
<div id="delete-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-modal w-full max-w-sm animate-fade-in p-6 text-center">
            <div class="w-14 h-14 bg-danger/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="text-h3 text-ink mb-2">Cancelar Proposta?</h3>
            <p class="text-ink-secondary text-sm mb-6" id="delete-message">A proposta será movida para "Cancelada".</p>
            <div class="flex gap-3">
                <button onclick="closeDeleteModal()"
                    class="w-1/2 border-2 border-border text-ink font-medium py-2.5 px-4 rounded-lg hover:bg-border/30 transition-all text-sm">Voltar</button>
                <button id="confirm-delete-btn"
                    class="w-1/2 bg-danger text-white font-medium py-2.5 px-4 rounded-lg hover:bg-danger/90 active:bg-danger/80 transition-all text-sm">Cancelar Proposta</button>
            </div>
        </div>
    </div>
</div>

<script>
const API_BASE = '<?= API_BASE_URL ?>';
let currentPage = 1;
let currentSearch = '';
let currentStatus = '<?= htmlspecialchars($currentStatus) ?>';
let currentClient = '';
let deleteProposalId = null;
let clientsCache = [];
let itemCounter = 0;
let viewProposalData = null;

// ── Helpers ──────────────────────────────────────────────
function formatMoney(value) {
    return 'R$ ' + parseFloat(value || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str || '';
    return div.innerHTML;
}

function formatDate(dateStr) {
    if (!dateStr) return '—';
    const d = new Date(dateStr);
    return d.toLocaleDateString('pt-BR');
}

function formatDateTime(dateStr) {
    if (!dateStr) return '—';
    const d = new Date(dateStr);
    return d.toLocaleDateString('pt-BR') + ' ' + d.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
}

const STATUS_LABELS = {
    draft: 'Rascunho',
    sent: 'Enviada',
    viewed: 'Visualizada',
    accepted: 'Aceita',
    rejected: 'Rejeitada',
    cancelled: 'Cancelada',
};

const STATUS_COLORS = {
    draft: { bg: 'bg-gray-100', text: 'text-gray-700' },
    sent: { bg: 'bg-blue-100', text: 'text-blue-700' },
    viewed: { bg: 'bg-purple-100', text: 'text-purple-700' },
    accepted: { bg: 'bg-green-100', text: 'text-green-700' },
    rejected: { bg: 'bg-red-100', text: 'text-red-700' },
    cancelled: { bg: 'bg-gray-100', text: 'text-gray-500' },
};

// ── Load Clients for Selects ──────────────────────────
async function loadClients() {
    try {
        const token = '<?= getToken() ?>';
        const response = await fetch(`${API_BASE}/clients?perPage=100`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        if (!response.ok) return;
        const data = await response.json();
        clientsCache = data.clients || [];

        const filterSelect = document.getElementById('filter-client');
        const modalSelect = document.getElementById('field-client');

        const options = clientsCache.map(c =>
            `<option value="${c.id}">${escapeHtml(c.name)}</option>`
        ).join('');

        filterSelect.innerHTML = '<option value="">Todos os clientes</option>' + options;
        modalSelect.innerHTML = '<option value="">Selecione um cliente</option>' + options;
    } catch (e) {
        console.warn('Erro ao carregar clientes:', e);
    }
}

// ── Load Proposals ─────────────────────────────────────
async function loadProposals() {
    const table = document.getElementById('proposals-table');
    table.innerHTML = '<div class="flex items-center justify-center py-16"><div class="w-10 h-10 border-4 border-primary/30 border-t-primary rounded-full animate-spin"></div></div>';

    try {
        const token = '<?= getToken() ?>';
        if (!token) { window.location.href = '?page=login'; return; }

        const params = new URLSearchParams({ page: currentPage, perPage: 20 });
        if (currentStatus) params.set('status', currentStatus);
        if (currentSearch.length >= 2) params.set('search', currentSearch);
        if (currentClient) params.set('client_id', currentClient);

        const response = await fetch(`${API_BASE}/proposals?${params}`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });

        if (response.status === 401) { window.location.href = '?page=login'; return; }
        if (!response.ok) throw new Error('Erro ao carregar propostas');

        const data = await response.json();
        renderTable(data.proposals, data.pagination);
    } catch (err) {
        table.innerHTML = `<div class="text-center py-12"><p class="text-danger">❌ ${err.message}</p></div>`;
    }
}

function renderTable(proposals, pagination) {
    const table = document.getElementById('proposals-table');
    document.getElementById('pagination').classList.toggle('hidden', !proposals || proposals.length === 0);

    if (!proposals || proposals.length === 0) {
        table.innerHTML = `
            <div class="text-center py-16 px-6">
                <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <h3 class="text-h3 text-ink mb-2">Nenhuma proposta encontrada</h3>
                <p class="text-ink-secondary text-sm mb-6">${currentStatus ? 'Nenhuma proposta com este status.' : 'Crie sua primeira proposta comercial.'}</p>
                <button onclick="openCreateModal()" class="bg-primary text-white font-medium px-6 py-2.5 rounded-lg hover:bg-primary-600 transition-all">+ Nova Proposta</button>
            </div>`;
        return;
    }

    table.innerHTML = `
        <table class="w-full">
            <thead>
                <tr class="bg-surface/80 border-b border-border">
                    <th class="text-left px-6 py-3 text-xs font-semibold text-ink-muted uppercase tracking-wider">Nº</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-ink-muted uppercase tracking-wider">Título</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-ink-muted uppercase tracking-wider hidden md:table-cell">Cliente</th>
                    <th class="text-right px-6 py-3 text-xs font-semibold text-ink-muted uppercase tracking-wider">Valor</th>
                    <th class="text-center px-6 py-3 text-xs font-semibold text-ink-muted uppercase tracking-wider">Status</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-ink-muted uppercase tracking-wider hidden lg:table-cell">Data</th>
                    <th class="text-right px-6 py-3 text-xs font-semibold text-ink-muted uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                ${proposals.map(p => {
                    const sc = STATUS_COLORS[p.status] || STATUS_COLORS.draft;
                    return `
                    <tr class="hover:bg-surface/50 transition-colors cursor-pointer" onclick="openViewModal(${p.id})">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-mono font-medium text-ink">${escapeHtml(p.number)}</span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-ink">${escapeHtml(p.title)}</p>
                            ${p.description ? `<p class="text-xs text-ink-muted truncate max-w-[200px]">${escapeHtml(p.description)}</p>` : ''}
                        </td>
                        <td class="px-6 py-4 hidden md:table-cell">
                            <span class="text-sm text-ink-secondary">${escapeHtml(p.client_name || '—')}</span>
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <span class="text-sm font-semibold text-ink">${formatMoney(p.total_amount)}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium ${sc.bg} ${sc.text}">
                                <span class="w-1.5 h-1.5 rounded-full animate-pulse-dot" style="background: currentColor"></span>
                                ${STATUS_LABELS[p.status] || p.status}
                            </span>
                        </td>
                        <td class="px-6 py-4 hidden lg:table-cell">
                            <span class="text-sm text-ink-secondary">${formatDate(p.created_at)}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1" onclick="event.stopPropagation()">
                                <button onclick="openViewModal(${p.id})" title="Visualizar"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center text-ink-secondary hover:bg-info/10 hover:text-info transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                                ${['draft', 'rejected'].includes(p.status) ? `
                                <button onclick="openEditModal(${p.id})" title="Editar"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center text-ink-secondary hover:bg-primary/10 hover:text-primary transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                ` : ''}
                                ${p.status !== 'cancelled' ? `
                                <button onclick="openDeleteModal(${p.id}, '${STATUS_LABELS[p.status] || p.status}')" title="Cancelar"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center text-ink-secondary hover:bg-danger/10 hover:text-danger transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                                ` : ''}
                            </div>
                        </td>
                    </tr>`;
                }).join('')}
            </tbody>
        </table>`;

    document.getElementById('pagination-info').textContent =
        `Mostrando ${proposals.length} de ${pagination.total} propostas (página ${pagination.page} de ${pagination.totalPages})`;
    document.getElementById('prev-page').disabled = pagination.page <= 1;
    document.getElementById('next-page').disabled = pagination.page >= pagination.totalPages;
}

function goPage(delta) {
    const btn = delta > 0 ? document.getElementById('next-page') : document.getElementById('prev-page');
    if (btn.disabled) return;
    currentPage += delta;
    loadProposals();
}

// ── Search debounce ────────────────────────────────────
let searchTimeout;
document.getElementById('search-input').addEventListener('input', (e) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        currentSearch = e.target.value.trim();
        currentPage = 1;
        loadProposals();
    }, 300);
});

document.getElementById('filter-client').addEventListener('change', (e) => {
    currentClient = e.target.value;
    currentPage = 1;
    loadProposals();
});

// ══════════════════════════════════════════════════════════
//  ITEMS: Dynamic Row Management
// ══════════════════════════════════════════════════════════

function addItemRow(data) {
    itemCounter++;
    const id = `item_${itemCounter}`;
    const container = document.getElementById('items-container');

    const div = document.createElement('div');
    div.id = id;
    div.className = 'grid grid-cols-1 sm:grid-cols-12 gap-2 items-end p-2 rounded-lg hover:bg-surface/50 transition-colors border border-transparent hover:border-border';

    div.innerHTML = `
        <div class="sm:col-span-5">
            <input type="text" class="item-desc w-full px-3 py-2 rounded-lg border border-border bg-surface text-ink text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all" placeholder="Descrição do item" value="${escapeHtml(data?.description || '')}">
        </div>
        <div class="sm:col-span-2">
            <input type="number" class="item-qty w-full px-3 py-2 rounded-lg border border-border bg-surface text-ink text-sm text-center focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all" placeholder="1" min="0.01" step="1" value="${data?.quantity || 1}">
        </div>
        <div class="sm:col-span-2">
            <div class="relative">
                <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-ink-muted text-xs">R$</span>
                <input type="number" class="item-price w-full pl-7 pr-3 py-2 rounded-lg border border-border bg-surface text-ink text-sm text-right focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all" placeholder="0,00" min="0" step="0.01" value="${data?.unit_price || ''}">
            </div>
        </div>
        <div class="sm:col-span-2 flex items-center justify-end">
            <span class="item-total text-sm font-semibold text-ink">${data ? formatMoney((data.quantity || 1) * (data.unit_price || 0)) : 'R$ 0,00'}</span>
        </div>
        <div class="sm:col-span-1 flex justify-end">
            <button type="button" onclick="removeItemRow('${id}')" class="w-8 h-8 rounded-lg flex items-center justify-center text-ink-muted hover:bg-danger/10 hover:text-danger transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        </div>
    `;

    container.appendChild(div);

    // Event listeners for recalc
    const qtyInput = div.querySelector('.item-qty');
    const priceInput = div.querySelector('.item-price');
    const totalSpan = div.querySelector('.item-total');

    function recalcItem() {
        const qty = parseFloat(qtyInput.value) || 0;
        const price = parseFloat(priceInput.value) || 0;
        totalSpan.textContent = formatMoney(qty * price);
        recalcTotal();
    }

    qtyInput.addEventListener('input', recalcItem);
    priceInput.addEventListener('input', recalcItem);
}

function removeItemRow(id) {
    const el = document.getElementById(id);
    if (el) {
        el.remove();
        recalcTotal();
    }
}

function recalcTotal() {
    let total = 0;
    document.querySelectorAll('#items-container .grid').forEach(row => {
        const qty = parseFloat(row.querySelector('.item-qty')?.value) || 0;
        const price = parseFloat(row.querySelector('.item-price')?.value) || 0;
        total += qty * price;
    });
    document.getElementById('items-total').textContent = formatMoney(total);
}

function getItemsData() {
    const items = [];
    document.querySelectorAll('#items-container .grid').forEach(row => {
        const desc = row.querySelector('.item-desc')?.value?.trim();
        if (!desc) return;
        const qty = parseFloat(row.querySelector('.item-qty')?.value) || 1;
        const price = parseFloat(row.querySelector('.item-price')?.value) || 0;
        items.push({ description: desc, quantity: qty, unit_price: price });
    });
    return items;
}

function setItemsData(items) {
    // Clear existing rows
    document.getElementById('items-container').innerHTML = '';
    itemCounter = 0;
    (items || []).forEach(item => addItemRow(item));
    recalcTotal();
}

// ══════════════════════════════════════════════════════════
//  MODAL: Create / Edit
// ══════════════════════════════════════════════════════════

function openCreateModal() {
    document.getElementById('modal-title').textContent = 'Nova Proposta';
    document.getElementById('modal-submit-btn').textContent = 'Salvar Proposta';
    document.getElementById('proposal-form').reset();
    document.getElementById('proposal-id').value = '';
    document.getElementById('items-container').innerHTML = '';
    itemCounter = 0;
    recalcTotal();
    document.getElementById('proposal-modal').classList.remove('hidden');
}

async function openEditModal(proposalId) {
    try {
        const token = '<?= getToken() ?>';
        const response = await fetch(`${API_BASE}/proposals/${proposalId}`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        if (!response.ok) throw new Error('Erro ao carregar proposta');
        const data = await response.json();
        const p = data.proposal;

        document.getElementById('modal-title').textContent = `Editar: ${p.number}`;
        document.getElementById('modal-submit-btn').textContent = 'Atualizar Proposta';
        document.getElementById('proposal-id').value = p.id;
        document.getElementById('field-title').value = p.title || '';
        document.getElementById('field-client').value = p.client_id || '';
        document.getElementById('field-valid-until').value = p.valid_until ? p.valid_until.split('T')[0] : '';
        document.getElementById('field-description').value = p.description || '';
        document.getElementById('field-payment-terms').value = p.payment_terms || '';
        document.getElementById('field-notes').value = p.notes || '';

        setItemsData(p.items);

        document.getElementById('proposal-modal').classList.remove('hidden');
    } catch (err) {
        showToast(err.message, 'error');
    }
}

function closeModal() {
    document.getElementById('proposal-modal').classList.add('hidden');
}

document.getElementById('proposal-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('modal-submit-btn');
    const proposalId = document.getElementById('proposal-id').value;
    const isEdit = !!proposalId;
    const title = document.getElementById('field-title').value.trim();

    if (!title) {
        showToast('Título é obrigatório', 'error');
        document.getElementById('field-title').classList.add('border-danger');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="inline-block w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin mr-2"></span> Salvando...';

    try {
        const token = '<?= getToken() ?>';
        const payload = {
            title,
            client_id: document.getElementById('field-client').value || null,
            valid_until: document.getElementById('field-valid-until').value || null,
            description: document.getElementById('field-description').value.trim() || null,
            payment_terms: document.getElementById('field-payment-terms').value.trim() || null,
            notes: document.getElementById('field-notes').value.trim() || null,
        };

        if (!isEdit) {
            payload.items = getItemsData();
        }

        const url = isEdit ? `${API_BASE}/proposals/${proposalId}` : `${API_BASE}/proposals`;
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
        loadProposals();
    } catch (err) {
        showToast(err.message, 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = isEdit ? 'Atualizar Proposta' : 'Salvar Proposta';
    }
});

// ══════════════════════════════════════════════════════════
//  MODAL: View + Status Actions
// ══════════════════════════════════════════════════════════

async function openViewModal(proposalId) {
    try {
        const token = '<?= getToken() ?>';
        const response = await fetch(`${API_BASE}/proposals/${proposalId}`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        if (!response.ok) throw new Error('Erro ao carregar proposta');
        const data = await response.json();
        viewProposalData = data.proposal;
        renderViewModal(data.proposal);
        document.getElementById('view-modal').classList.remove('hidden');
    } catch (err) {
        showToast(err.message, 'error');
    }
}

function renderViewModal(p) {
    const sc = STATUS_COLORS[p.status] || STATUS_COLORS.draft;

    document.getElementById('view-number').textContent = p.number;
    document.getElementById('view-status-badge').className = `inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-medium ${sc.bg} ${sc.text}`;
    document.getElementById('view-status-badge').textContent = STATUS_LABELS[p.status] || p.status;

    document.getElementById('view-client').textContent = p.client_name || '—';
    document.getElementById('view-valid-until').textContent = p.valid_until ? formatDate(p.valid_until) : '—';

    // WhatsApp + Link Público + PDF
    const whatsappEl = document.getElementById('view-client-whatsapp');
    const whatsappLink = document.getElementById('view-whatsapp-link');
    const publicLinkEl = document.getElementById('view-public-link');
    const pdfLinkEl = document.getElementById('view-pdf-link');

    // Public link + PDF
    if (p.public_token) {
        publicLinkEl.classList.remove('hidden');
        pdfLinkEl.classList.remove('hidden');
    } else {
        publicLinkEl.classList.add('hidden');
        pdfLinkEl.classList.add('hidden');
    }

    if (p.client_whatsapp) {
        const publicUrl = p.public_token ? `${window.location.origin}/?page=public-proposal&token=${p.public_token}` : '';
        const proposalMsg = `Olá! Tenho uma proposta do ServiceSaaS para você: ${p.number} - ${p.title}`;
        const fullMsg = publicUrl ? `${proposalMsg}\n\n📎 Link para visualizar e responder: ${publicUrl}` : proposalMsg;
        whatsappLink.href = `https://wa.me/55${p.client_whatsapp.replace(/\D/g, '').replace(/^55/, '')}?text=${encodeURIComponent(fullMsg)}`;
        whatsappEl.classList.remove('hidden');
    } else {
        whatsappEl.classList.add('hidden');
    }

    // Description
    const descBox = document.getElementById('view-description');
    if (p.description) {
        document.getElementById('view-description-text').textContent = p.description;
        descBox.classList.remove('hidden');
    } else {
        descBox.classList.add('hidden');
    }

    // Items
    const itemsBody = document.getElementById('view-items-body');
    if (p.items && p.items.length > 0) {
        itemsBody.innerHTML = p.items.map(item => `
            <tr class="hover:bg-surface/30 transition-colors">
                <td class="px-4 py-3 text-sm text-ink">${escapeHtml(item.description)}</td>
                <td class="px-4 py-3 text-sm text-center text-ink-secondary">${item.quantity}</td>
                <td class="px-4 py-3 text-sm text-right text-ink-secondary">${formatMoney(item.unit_price)}</td>
                <td class="px-4 py-3 text-sm text-right font-medium text-ink">${formatMoney(item.total_price)}</td>
            </tr>
        `).join('');
    } else {
        itemsBody.innerHTML = '<tr><td colspan="4" class="px-4 py-8 text-center text-sm text-ink-muted">Nenhum item cadastrado</td></tr>';
    }
    document.getElementById('view-total').textContent = formatMoney(p.total_amount);

    // Payment terms
    const ptBox = document.getElementById('view-payment-terms-box');
    if (p.payment_terms) {
        document.getElementById('view-payment-terms').textContent = p.payment_terms;
        ptBox.classList.remove('hidden');
    } else {
        ptBox.classList.add('hidden');
    }

    // Notes
    const notesBox = document.getElementById('view-notes-box');
    if (p.notes) {
        document.getElementById('view-notes').textContent = p.notes;
        notesBox.classList.remove('hidden');
    } else {
        notesBox.classList.add('hidden');
    }

    // Timeline
    document.getElementById('view-created-at').textContent = formatDateTime(p.created_at);
    const sentBox = document.getElementById('view-sent-at-box');
    const acceptedBox = document.getElementById('view-accepted-at-box');
    if (p.sent_at) {
        document.getElementById('view-sent-at').textContent = formatDateTime(p.sent_at);
        sentBox.classList.remove('hidden');
    } else {
        sentBox.classList.add('hidden');
    }
    if (p.accepted_at) {
        document.getElementById('view-accepted-at').textContent = formatDateTime(p.accepted_at);
        acceptedBox.classList.remove('hidden');
    } else {
        acceptedBox.classList.add('hidden');
    }

    // Status Action buttons
    const actionsEl = document.getElementById('view-actions');
    const transitions = {
        draft: [
            { status: 'sent', label: '💌 Enviar Proposta', color: 'bg-primary text-white hover:bg-primary-600' },
        ],
        sent: [
            { status: 'accepted', label: '✅ Aceitar', color: 'bg-success text-white hover:bg-success/90' },
            { status: 'rejected', label: '❌ Rejeitar', color: 'bg-danger text-white hover:bg-danger/90' },
        ],
        viewed: [
            { status: 'accepted', label: '✅ Aceitar', color: 'bg-success text-white hover:bg-success/90' },
            { status: 'rejected', label: '❌ Rejeitar', color: 'bg-danger text-white hover:bg-danger/90' },
        ],
        accepted: [
            { action: 'cobrar', status: null, label: '💰 Cobrar Cliente', color: 'bg-primary text-white hover:bg-primary-600' },
        ],
        rejected: [
            { status: 'draft', label: '✏️ Revisar (voltar p/ Rascunho)', color: 'bg-warning text-white hover:bg-warning/90' },
        ],
        cancelled: [
            { status: 'draft', label: '📝 Reabrir Proposta', color: 'bg-primary text-white hover:bg-primary-600' },
        ],
    };

    const available = transitions[p.status] || [];
    if (available.length > 0) {
        actionsEl.innerHTML = available.map(t => {
            if (t.action === 'cobrar') {
                return `<button onclick="cobrarProposta(${p.id})"
                    class="px-5 py-2.5 rounded-lg font-medium text-sm transition-all duration-200 bg-primary text-white hover:bg-primary-600 active:scale-95 shadow-card">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Cobrar
                    </span>
                </button>`;
            }
            return `<button onclick="updateProposalStatus(${p.id}, '${t.status}')"
                class="px-4 py-2 rounded-lg font-medium text-sm transition-all duration-200 ${t.color} active:scale-95">
                ${t.label}
            </button>`;
        }).join('');

        // Always add cancel button (if not already cancelled)
        if (p.status !== 'cancelled') {
            actionsEl.innerHTML += `
                <button onclick="openDeleteModal(${p.id}, '${STATUS_LABELS[p.status] || p.status}')"
                    class="px-4 py-2 rounded-lg font-medium text-sm transition-all duration-200 border-2 border-border text-ink-secondary hover:bg-danger/10 hover:text-danger hover:border-danger/30 active:scale-95">
                    🗑️ Cancelar Proposta
                </button>
            `;
        }
    } else {
        actionsEl.innerHTML = '';
    }
}

function closeViewModal() {
    document.getElementById('view-modal').classList.add('hidden');
    viewProposalData = null;
}

// ── Download PDF (Story 3.5) ───────────────────────
function downloadPdf() {
    if (!viewProposalData?.id) return;
    const token = '<?= getToken() ?>';
    const url = `${API_BASE}/proposals/${viewProposalData.id}/pdf`;
    const xhr = new XMLHttpRequest();
    xhr.open('GET', url, true);
    xhr.setRequestHeader('Authorization', `Bearer ${token}`);
    xhr.responseType = 'blob';
    xhr.onload = function () {
        if (xhr.status === 200) {
            const blob = xhr.response;
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = `proposta-${viewProposalData.number || viewProposalData.id}.pdf`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(link.href);
            showToast('📄 PDF baixado com sucesso!', 'success');
        } else {
            showToast('Erro ao baixar PDF', 'error');
        }
    };
    xhr.onerror = () => showToast('Erro ao baixar PDF', 'error');
    xhr.send();
}

// ── Copiar Link Público ──────────────────────────────
function copyPublicLink() {
    if (!viewProposalData?.public_token) {
        showToast('Link público não disponível para esta proposta.', 'warning');
        return;
    }
    const publicUrl = `${window.location.origin}/?page=public-proposal&token=${viewProposalData.public_token}`;
    navigator.clipboard.writeText(publicUrl).then(() => {
        showToast('🔗 Link público copiado! Compartilhe com seu cliente.', 'success');
    }).catch(() => {
        // Fallback para navegadores sem clipboard API
        const textarea = document.createElement('textarea');
        textarea.value = publicUrl;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        showToast('🔗 Link público copiado!', 'success');
    });
}

// ── Status Update ──────────────────────────────────────
async function updateProposalStatus(proposalId, newStatus) {
    try {
        const token = '<?= getToken() ?>';
        const response = await fetch(`${API_BASE}/proposals/${proposalId}/status`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` },
            body: JSON.stringify({ status: newStatus }),
        });

        const data = await response.json();
        if (!response.ok) throw new Error(data.message || 'Erro ao atualizar status');

        showToast(data.message, 'success');
        closeViewModal();
        loadProposals();
    } catch (err) {
        showToast(err.message, 'error');
    }
}

// ══════════════════════════════════════════════════════════
//  MODAL: Delete / Cancel
// ══════════════════════════════════════════════════════════

function openDeleteModal(proposalId, currentStatusLabel) {
    deleteProposalId = proposalId;
    document.getElementById('delete-message').textContent =
        `Esta proposta está como "${currentStatusLabel}". Ela será cancelada.`;
    document.getElementById('delete-modal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('delete-modal').classList.add('hidden');
    deleteProposalId = null;
}

document.getElementById('confirm-delete-btn').addEventListener('click', async () => {
    if (!deleteProposalId) return;
    const btn = document.getElementById('confirm-delete-btn');
    btn.disabled = true;
    btn.innerHTML = '<span class="inline-block w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin mr-2"></span> Cancelando...';

    try {
        const token = '<?= getToken() ?>';
        const response = await fetch(`${API_BASE}/proposals/${deleteProposalId}`, {
            method: 'DELETE',
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const data = await response.json();
        if (!response.ok) throw new Error(data.message || 'Erro ao cancelar');

        showToast('✅ ' + data.message, 'success');
        closeDeleteModal();
        closeViewModal();
        loadProposals();
    } catch (err) {
        showToast(err.message, 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = 'Cancelar Proposta';
    }
});

// ══════════════════════════════════════════════════════════
//  STORY 5.2 — Cobrar (Criar Preferência MP)
// ══════════════════════════════════════════════════════════

async function cobrarProposta(proposalId) {
    try {
        const token = '<?= getToken() ?>';
        if (!token) { window.location.href = '?page=login'; return; }

        // Buscar dados atualizados da proposta
        const propResponse = await fetch(`${API_BASE}/proposals/${proposalId}`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        if (!propResponse.ok) throw new Error('Erro ao carregar proposta');
        const propData = await propResponse.json();
        const p = propData.proposal;

        if (!p.client_email) {
            showToast('Cliente sem e-mail cadastrado. Informe um e-mail para cobrança.', 'warning');
            return;
        }

        if (!p.items || p.items.length === 0) {
            showToast('Proposta sem itens. Adicione itens antes de cobrar.', 'warning');
            return;
        }

        // Mostrar loading
        showToast('🔄 Gerando link de pagamento...', 'info');

        // Criar preferência de pagamento
        const response = await fetch(`${API_BASE}/payments/preference`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` },
            body: JSON.stringify({
                proposalId: p.id,
                items: p.items.map(item => ({
                    id: String(item.id),
                    title: item.description,
                    description: item.description,
                    quantity: item.quantity,
                    unit_price: item.unit_price,
                })),
                payer: {
                    name: p.client_name || 'Cliente',
                    email: p.client_email,
                },
                metadata: { tenantId: '<?= getTenantId() ?>', proposalId: p.id },
            }),
        });

        const data = await response.json();

        if (!response.ok) {
            if (response.status === 503) {
                // MP não configurado (modo degradado)
                showToast('⚠️ Mercado Pago não configurado. Configure as credenciais MP_ACCESS_TOKEN no .env para ativar cobranças.', 'warning');
                return;
            }
            throw new Error(data.message || 'Erro ao criar cobrança');
        }

        // Exibir modal com link de pagamento
        const initPoint = data.data?.init_point || data.data?.sandbox_init_point;
        if (initPoint) {
            showPaymentModal(initPoint, data.data);
        } else {
            showToast('Link de pagamento gerado!', 'success');
        }

    } catch (err) {
        console.error('[Cobrar]', err);
        showToast('❌ ' + (err.message || 'Erro ao processar cobrança'), 'error');
    }
}

// ── Modal de Pagamento ──────────────────────────────────
function showPaymentModal(url, data) {
    // Remove modal anterior se existir
    const oldModal = document.getElementById('payment-modal');
    if (oldModal) oldModal.remove();

    const modal = document.createElement('div');
    modal.id = 'payment-modal';
    modal.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm';
    modal.innerHTML = `
        <div class="bg-white rounded-xl shadow-modal w-full max-w-md mx-4 animate-fade-in p-6 text-center">
            <div class="w-16 h-16 bg-success/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="text-h3 text-ink mb-2">✅ Cobrança Gerada!</h3>
            <p class="text-ink-secondary text-sm mb-6">
                O link de pagamento foi gerado com sucesso. Compartilhe com o cliente.
            </p>
            <div class="bg-surface rounded-xl p-4 mb-6 border border-border">
                <p class="text-xs text-ink-muted mb-2 text-left font-medium uppercase tracking-wider">Link de Pagamento</p>
                <div class="flex items-center gap-2">
                    <input type="text" id="payment-link" value="${url}" readonly
                        class="flex-1 px-3 py-2 rounded-lg border border-border bg-white text-ink text-sm focus:outline-none"
                        onclick="this.select()">
                    <button onclick="copyPaymentLink()"
                        class="px-3 py-2 bg-primary text-white rounded-lg hover:bg-primary-600 transition-colors text-sm whitespace-nowrap flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Copiar
                    </button>
                </div>
            </div>
            <div class="flex gap-3">
                <button onclick="closePaymentModal()"
                    class="flex-1 px-4 py-2 border border-border rounded-lg text-ink-secondary hover:bg-surface transition-colors text-sm font-medium">
                    Fechar
                </button>
                <a href="${url}" target="_blank"
                    class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-600 transition-colors text-sm font-medium flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    Abrir Pagamento
                </a>
            </div>
        </div>
    `;

    document.body.appendChild(modal);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closePaymentModal();
    });
}

function closePaymentModal() {
    const modal = document.getElementById('payment-modal');
    if (modal) modal.remove();
}

function copyPaymentLink() {
    const input = document.getElementById('payment-link');
    if (!input) return;
    input.select();
    navigator.clipboard?.writeText(input.value).then(() => {
        showToast('✅ Link copiado!', 'success');
    }).catch(() => {
        document.execCommand('copy');
        showToast('✅ Link copiado!', 'success');
    });
}

// ══════════════════════════════════════════════════════════
//  INIT
// ══════════════════════════════════════════════════════════

document.addEventListener('DOMContentLoaded', () => {
    loadClients();
    loadProposals();
});
</script>
