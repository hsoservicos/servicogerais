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

<div id="view-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" onclick="closeViewModal()"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-modal w-full max-w-2xl animate-fade-in max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 border-b border-border flex-shrink-0">
                <h3 id="view-modal-title" class="text-h3 text-ink">Detalhes do Trabalhador</h3>
                <button onclick="closeViewModal()" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-surface transition-colors">
                    <svg class="w-5 h-5 text-ink-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="flex-shrink-0">
                <div class="flex border-b border-border px-6" id="view-tabs">
                    <button class="view-tab px-4 py-3 text-sm font-medium border-b-2 border-primary text-primary transition-colors" data-tab="info">Informações</button>
                    <button class="view-tab px-4 py-3 text-sm font-medium text-ink-secondary hover:text-ink border-b-2 border-transparent transition-colors" data-tab="certs">Certificações</button>
                    <button class="view-tab px-4 py-3 text-sm font-medium text-ink-secondary hover:text-ink border-b-2 border-transparent transition-colors" data-tab="bg">Background Check</button>
                    <button class="view-tab px-4 py-3 text-sm font-medium text-ink-secondary hover:text-ink border-b-2 border-transparent transition-colors" data-tab="clt">Transição CLT</button>
                </div>
            </div>
            <div class="flex-1 overflow-y-auto p-6" id="view-content"></div>
        </div>
    </div>
</div>

<div id="cert-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" onclick="closeCertModal()"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-modal w-full max-w-md animate-fade-in">
            <div class="flex items-center justify-between px-6 py-4 border-b border-border">
                <h3 class="text-h3 text-ink">Nova Certificação</h3>
                <button onclick="closeCertModal()" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-surface transition-colors">
                    <svg class="w-5 h-5 text-ink-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="cert-form" class="p-6 space-y-4" novalidate>
                <input type="hidden" id="cert-worker-id" value="">
                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">Tipo <span class="text-danger">*</span></label>
                    <select id="cert-type" required class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                        <option value="">Selecione...</option>
                        <option value="BABA">Babá</option>
                        <option value="CUIDADOR_IDOSOS">Cuidador de Idosos</option>
                        <option value="APH">APH (Atendimento Pré-Hospitalar)</option>
                        <option value="COZINHA">Cozinha</option>
                        <option value="JARDINAGEM">Jardinagem</option>
                        <option value="PRIMEIROS_SOCORROS">Primeiros Socorros</option>
                        <option value="OUTRO">Outro</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">Título <span class="text-danger">*</span></label>
                    <input type="text" id="cert-title" required class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink" placeholder="Ex: Curso de Babá">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">Emissor</label>
                    <input type="text" id="cert-issuer" class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink" placeholder="Instituição emissora">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">Data Emissão</label>
                        <input type="date" id="cert-issue-date" class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">Data Validade</label>
                        <input type="date" id="cert-expiry-date" class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">URL do Documento</label>
                    <input type="url" id="cert-document-url" class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink" placeholder="https://...">
                </div>
                <div class="flex gap-3 justify-end pt-4 border-t border-border">
                    <button type="button" onclick="closeCertModal()" class="px-4 py-2.5 rounded-lg border border-border text-ink-secondary hover:bg-surface transition-all text-sm font-medium">Cancelar</button>
                    <button type="submit" class="px-6 py-2.5 rounded-lg bg-primary text-white hover:bg-primary-600 transition-all text-sm font-medium shadow-card flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="clt-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" onclick="closeCltModal()"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-modal w-full max-w-lg animate-fade-in">
            <div class="flex items-center justify-between px-6 py-4 border-b border-border">
                <h3 class="text-h3 text-ink">Transição para CLT</h3>
                <button onclick="closeCltModal()" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-surface transition-colors">
                    <svg class="w-5 h-5 text-ink-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="clt-form" class="p-6 space-y-4" novalidate>
                <input type="hidden" id="clt-worker-id" value="">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">Salário (R$) <span class="text-danger">*</span></label>
                        <input type="number" id="clt-salary" required min="0" step="0.01" class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink" placeholder="1518.00">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">Data Início <span class="text-danger">*</span></label>
                        <input type="date" id="clt-start-date" required class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">Vale Transporte (R$)</label>
                        <input type="number" id="clt-transport" min="0" step="0.01" value="0" class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">Vale Alimentação (R$)</label>
                        <input type="number" id="clt-food" min="0" step="0.01" value="0" class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">Dias/Semana</label>
                    <select id="clt-frequency" class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink">
                        <option value="1">1 dia</option>
                        <option value="2">2 dias</option>
                        <option value="3">3 dias</option>
                        <option value="4">4 dias</option>
                        <option value="5" selected>5 dias</option>
                        <option value="6">6 dias</option>
                    </select>
                </div>
                <div id="clt-preview" class="bg-surface rounded-lg p-4 hidden">
                    <h4 class="text-sm font-semibold text-ink mb-3">Pré-visualização de Custos</h4>
                    <div class="space-y-2 text-sm" id="clt-costs"></div>
                </div>
                <div class="flex gap-3 justify-end pt-4 border-t border-border">
                    <button type="button" onclick="closeCltModal()" class="px-4 py-2.5 rounded-lg border border-border text-ink-secondary hover:bg-surface transition-all text-sm font-medium">Cancelar</button>
                    <button type="button" id="preview-btn" onclick="previewCltCosts()" class="px-4 py-2.5 rounded-lg border border-primary text-primary hover:bg-primary/5 transition-all text-sm font-medium">Calcular Custos</button>
                    <button type="submit" class="px-6 py-2.5 rounded-lg bg-success text-white hover:bg-success/90 transition-all text-sm font-medium shadow-card flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Iniciar CLT</button>
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
                                <button onclick="viewWorker(${w.id})" class="text-ink-secondary hover:text-primary transition-colors p-1" title="Detalhes">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
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

let currentViewWorkerId = null;

function closeViewModal() { document.getElementById('view-modal').classList.add('hidden'); }

function closeCertModal() { document.getElementById('cert-modal').classList.add('hidden'); }

function closeCltModal() { document.getElementById('clt-modal').classList.add('hidden'); }

document.querySelectorAll('.view-tab').forEach(tab => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('.view-tab').forEach(t => {
            t.classList.remove('border-primary', 'text-primary');
            t.classList.add('border-transparent', 'text-ink-secondary');
        });
        tab.classList.remove('border-transparent', 'text-ink-secondary');
        tab.classList.add('border-primary', 'text-primary');
        renderViewTab(tab.dataset.tab);
    });
});

async function viewWorker(id) {
    currentViewWorkerId = id;
    document.getElementById('view-modal').classList.remove('hidden');
    document.getElementById('view-content').innerHTML = '<div class="flex items-center justify-center py-16"><div class="w-10 h-10 border-4 border-primary/30 border-t-primary rounded-full animate-spin"></div></div>';
    try {
        const res = await fetch(`${API_BASE}/workers/${id}`, {
            headers: { 'Authorization': `Bearer ${window.AUTH_TOKEN || ''}` }
        });
        const data = await res.json();
        window.currentWorker = data.worker;
        document.getElementById('view-modal-title').textContent = data.worker.name;
        renderViewTab('info');
    } catch (err) {
        document.getElementById('view-content').innerHTML = '<p class="text-danger">Erro ao carregar dados</p>';
    }
}

function renderViewTab(tab) {
    const w = window.currentWorker;
    if (!w) return;
    const container = document.getElementById('view-content');

    if (tab === 'info') {
        const categoryLabels = {
            'EMPREGADO_DOMESTICO_GERAL': 'Doméstica Geral', 'DIARISTA': 'Diarista', 'BABA': 'Babá',
            'CUIDADOR_IDOSOS': 'Cuidador de Idosos', 'COZINHEIRO': 'Cozinheiro(a)', 'MOTORISTA': 'Motorista',
            'JARDINEIRO': 'Jardineiro', 'CASEIRO': 'Caseiro', 'GOVERNANTA': 'Governanta',
        };
        container.innerHTML = `
            <div class="grid grid-cols-2 gap-6">
                <div><span class="text-xs text-ink-muted uppercase tracking-wider">Nome</span><p class="text-ink font-medium mt-1">${w.name}</p></div>
                <div><span class="text-xs text-ink-muted uppercase tracking-wider">CPF</span><p class="text-ink font-medium mt-1">${w.cpf}</p></div>
                <div><span class="text-xs text-ink-muted uppercase tracking-wider">RG</span><p class="text-ink mt-1">${w.rg || '—'}</p></div>
                <div><span class="text-xs text-ink-muted uppercase tracking-wider">Categoria</span><p class="text-ink mt-1">${categoryLabels[w.worker_category] || w.worker_category}</p></div>
                <div><span class="text-xs text-ink-muted uppercase tracking-wider">CBO</span><p class="text-ink mt-1">${w.cbo_code}</p></div>
                <div><span class="text-xs text-ink-muted uppercase tracking-wider">E-mail</span><p class="text-ink mt-1">${w.email || '—'}</p></div>
                <div><span class="text-xs text-ink-muted uppercase tracking-wider">Telefone</span><p class="text-ink mt-1">${w.phone || '—'}</p></div>
                <div><span class="text-xs text-ink-muted uppercase tracking-wider">WhatsApp</span><p class="text-ink mt-1">${w.whatsapp ? `<a href="https://wa.me/55${w.whatsapp.replace(/\D/g,'')}" target="_blank" class="text-[#25D366] hover:underline">${w.whatsapp}</a>` : '—'}</p></div>
                <div><span class="text-xs text-ink-muted uppercase tracking-wider">Chave PIX</span><p class="text-ink mt-1">${w.pix_key || '—'}</p></div>
                <div><span class="text-xs text-ink-muted uppercase tracking-wider">Background Check</span><p class="text-ink mt-1"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium ${w.background_check_status === 'APPROVED' ? 'bg-success/10 text-success' : w.background_check_status === 'REJECTED' ? 'bg-danger/10 text-danger' : 'bg-warning/10 text-warning'}">${w.background_check_status || 'PENDING'}</span></p></div>
            </div>`;
    } else if (tab === 'certs') {
        container.innerHTML = '<div class="flex items-center justify-center py-8"><div class="w-8 h-8 border-4 border-primary/30 border-t-primary rounded-full animate-spin"></div></div>';
        fetch(`${API_BASE}/workers/${currentViewWorkerId}/certifications`, {
            headers: { 'Authorization': `Bearer ${window.AUTH_TOKEN || ''}` }
        }).then(r => r.json()).then(data => {
            const certs = data.certifications || [];
            const certLabels = { 'BABA': 'Babá', 'CUIDADOR_IDOSOS': 'Cuidador de Idosos', 'APH': 'APH', 'COZINHA': 'Cozinha', 'JARDINAGEM': 'Jardinagem', 'PRIMEIROS_SOCORROS': 'Primeiros Socorros', 'OUTRO': 'Outro' };
            container.innerHTML = `
                <div class="flex justify-between items-center mb-4">
                    <h4 class="text-sm font-semibold text-ink">Certificações (${certs.length})</h4>
                    <button onclick="openCertModal(${currentViewWorkerId})" class="text-sm text-primary hover:underline font-medium">+ Adicionar</button>
                </div>
                ${certs.length === 0 ? '<p class="text-ink-muted text-sm">Nenhuma certificação cadastrada.</p>' :
                    certs.map(c => `
                        <div class="flex items-center justify-between p-3 bg-surface rounded-lg mb-2">
                            <div>
                                <span class="font-medium text-ink text-sm">${c.title}</span>
                                <span class="text-xs text-ink-muted ml-2">${certLabels[c.certification_type] || c.certification_type}</span>
                                ${c.verified ? '<span class="inline-flex items-center ml-2 px-1.5 py-0.5 rounded text-xs bg-success/10 text-success">Verificada</span>' : '<span class="inline-flex items-center ml-2 px-1.5 py-0.5 rounded text-xs bg-warning/10 text-warning">Pendente</span>'}
                                ${c.expiry_date ? `<span class="text-xs text-ink-muted ml-2">Válida até ${c.expiry_date}</span>` : ''}
                            </div>
                            <button onclick="deleteCert(${currentViewWorkerId}, ${c.id})" class="text-danger hover:text-danger/80 text-sm">Excluir</button>
                        </div>
                    `).join('')}`;
        }).catch(() => { container.innerHTML = '<p class="text-danger text-sm">Erro ao carregar certificações</p>'; });
    } else if (tab === 'bg') {
        container.innerHTML = `
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-surface rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-ink">Status atual: <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium ${w.background_check_status === 'APPROVED' ? 'bg-success/10 text-success' : w.background_check_status === 'REJECTED' ? 'bg-danger/10 text-danger' : 'bg-warning/10 text-warning'}">${w.background_check_status || 'PENDING'}</span></p>
                        ${w.background_check_date ? `<p class="text-xs text-ink-muted mt-1">Verificado em: ${new Date(w.background_check_date).toLocaleDateString('pt-BR')}</p>` : ''}
                        ${w.background_check_provider ? `<p class="text-xs text-ink-muted">Provedor: ${w.background_check_provider}</p>` : ''}
                    </div>
                    ${w.background_check_status !== 'APPROVED' ? `<button onclick="runBackgroundCheck(${currentViewWorkerId})" class="px-4 py-2 rounded-lg bg-primary text-white text-sm hover:bg-primary-600 transition-all">Aprovar Background Check</button>` : ''}
                </div>
                <p class="text-xs text-ink-muted">O background check verifica antecedentes criminais e documentação do trabalhador.</p>
            </div>`;
    } else if (tab === 'clt') {
        container.innerHTML = `
            <div class="space-y-4">
                <div class="p-4 bg-surface rounded-lg">
                    <p class="text-sm text-ink">Deseja formalizar <strong>${w.name}</strong> como empregado(a) doméstico(a) CLT?</p>
                    <p class="text-xs text-ink-muted mt-2">A transição para CLT segue a Lei Complementar 150/2015 e inclui registro em carteira, FGTS, INSS, 13º e férias.</p>
                </div>
                <button onclick="openCltModal(${currentViewWorkerId})" class="w-full px-4 py-3 rounded-lg bg-success text-white font-medium hover:bg-success/90 transition-all flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Iniciar Transição para CLT
                </button>
            </div>`;
    }
}

function openCertModal(workerId) {
    document.getElementById('cert-worker-id').value = workerId;
    document.getElementById('cert-form').reset();
    document.getElementById('cert-modal').classList.remove('hidden');
}

async function deleteCert(workerId, certId) {
    if (!confirm('Excluir certificação?')) return;
    try {
        const res = await fetch(`${API_BASE}/workers/${workerId}/certifications/${certId}`, {
            method: 'DELETE',
            headers: { 'Authorization': `Bearer ${window.AUTH_TOKEN || ''}` }
        });
        if (!res.ok) throw new Error('Erro');
        showToast('Certificação excluída!', 'success');
        renderViewTab('certs');
    } catch (err) {
        showToast('Erro ao excluir certificação', 'error');
    }
}

document.getElementById('cert-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const workerId = document.getElementById('cert-worker-id').value;
    const body = {
        certificationType: document.getElementById('cert-type').value,
        title: document.getElementById('cert-title').value,
        issuer: document.getElementById('cert-issuer').value,
        issueDate: document.getElementById('cert-issue-date').value || null,
        expiryDate: document.getElementById('cert-expiry-date').value || null,
        documentUrl: document.getElementById('cert-document-url').value || null,
    };
    try {
        const res = await fetch(`${API_BASE}/workers/${workerId}/certifications`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${window.AUTH_TOKEN || ''}` },
            body: JSON.stringify(body),
        });
        if (!res.ok) { const d = await res.json(); throw new Error(d.message); }
        showToast('Certificação cadastrada!', 'success');
        closeCertModal();
        renderViewTab('certs');
    } catch (err) {
        showToast(err.message, 'error');
    }
});

async function runBackgroundCheck(workerId) {
    if (!confirm('Aprovar background check para este trabalhador?')) return;
    try {
        const res = await fetch(`${API_BASE}/workers/${workerId}/background-check`, {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${window.AUTH_TOKEN || ''}` }
        });
        if (!res.ok) throw new Error('Erro');
        showToast('Background check aprovado!', 'success');
        viewWorker(workerId);
        loadWorkers(currentPage);
    } catch (err) {
        showToast('Erro ao executar background check', 'error');
    }
}

function openCltModal(workerId) {
    document.getElementById('clt-worker-id').value = workerId;
    document.getElementById('clt-form').reset();
    document.getElementById('clt-preview').classList.add('hidden');
    document.getElementById('clt-modal').classList.remove('hidden');
    document.getElementById('clt-start-date').value = new Date().toISOString().split('T')[0];
}

async function previewCltCosts() {
    const salary = parseFloat(document.getElementById('clt-salary').value);
    if (!salary || salary <= 0) { showToast('Informe o salário', 'error'); return; }
    try {
        const res = await fetch(`${API_BASE}/domestic/calculate-costs`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${window.AUTH_TOKEN || ''}` },
            body: JSON.stringify({
                salary,
                transportVoucher: parseFloat(document.getElementById('clt-transport').value) || 0,
                foodAllowance: parseFloat(document.getElementById('clt-food').value) || 0,
                regime: 'LC_150_CLT',
            }),
        });
        if (!res.ok) throw new Error('Erro ao calcular');
        const data = await res.json();
        document.getElementById('clt-preview').classList.remove('hidden');
        document.getElementById('clt-costs').innerHTML = `
            <div class="flex justify-between"><span>Salário Mensal</span><span class="font-medium">R$ ${data.monthlySalary.toFixed(2)}</span></div>
            <div class="flex justify-between"><span>INSS Patronal</span><span class="font-medium">R$ ${data.inssEmployer.toFixed(2)}</span></div>
            <div class="flex justify-between"><span>FGTS (8%)</span><span class="font-medium">R$ ${data.fgts.toFixed(2)}</span></div>
            <div class="flex justify-between"><span>13º (provisionado)</span><span class="font-medium">R$ ${data.thirteenth.toFixed(2)}</span></div>
            <div class="flex justify-between"><span>Férias + 1/3 (provisionado)</span><span class="font-medium">R$ ${data.vacation.toFixed(2)}</span></div>
            <hr class="border-border">
            <div class="flex justify-between text-success font-semibold"><span>Total Mensal</span><span>R$ ${data.monthlyTotal.toFixed(2)}</span></div>
            <div class="flex justify-between text-ink-muted text-xs"><span>Total Anual</span><span>R$ ${data.annualTotal.toFixed(2)}</span></div>`;
    } catch (err) {
        showToast('Erro ao calcular custos', 'error');
    }
}

document.getElementById('clt-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const workerId = document.getElementById('clt-worker-id').value;
    const body = {
        salary: parseFloat(document.getElementById('clt-salary').value),
        startDate: document.getElementById('clt-start-date').value,
        transportVoucher: parseFloat(document.getElementById('clt-transport').value) || 0,
        foodAllowance: parseFloat(document.getElementById('clt-food').value) || 0,
        weeklyFrequencyDays: parseInt(document.getElementById('clt-frequency').value),
    };
    try {
        const res = await fetch(`${API_BASE}/domestic/transition-to-clt/${workerId}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${window.AUTH_TOKEN || ''}` },
            body: JSON.stringify(body),
        });
        if (!res.ok) { const d = await res.json(); throw new Error(d.message); }
        const data = await res.json();
        showToast(data.message, 'success');
        closeCltModal();
        closeViewModal();
        loadWorkers(currentPage);
    } catch (err) {
        showToast(err.message, 'error');
    }
});

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') { closeModal(); closeViewModal(); closeCertModal(); closeCltModal(); }
});

loadWorkers();
</script>