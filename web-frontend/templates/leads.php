<!-- ═══════════════════════════════════════════════════════════════
     templates/leads.php — Painel de Leads (Epic 6 — Story 6.4)
     ═══════════════════════════════════════════════════════════
     Listagem paginada com filtros, badges de status,
     detalhes do lead, ações WhatsApp e mudança de status.
     ═══════════════════════════════════════════════════════════ -->

<?php $currentPage = 'leads'; require __DIR__ . '/partials/sidebar.php'; ?>

<div class="md:ml-64 min-h-screen flex flex-col">
    <?php $pageTitle = 'Leads Recebidos'; $pageSubtitle = 'Clientes que solicitaram orçamento na Landing Page'; require __DIR__ . '/partials/topbar.php'; ?>

    <!-- Content -->
    <main class="flex-1 p-6">
        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-card border border-border p-4 sm:p-5 mb-6">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <svg class="w-5 h-5 text-ink-muted absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" id="search-input"
                            class="w-full pl-10 pr-4 py-2.5 rounded-lg border-2 border-border focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all text-sm"
                            placeholder="Buscar por nome, telefone ou serviço...">
                    </div>
                </div>
                <div class="w-full sm:w-48">
                    <select id="status-filter"
                        class="w-full px-4 py-2.5 rounded-lg border-2 border-border focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all text-sm bg-white">
                        <option value="">Todos os status</option>
                        <option value="new">🆕 Novo</option>
                        <option value="contacted">📞 Contactado</option>
                        <option value="converted">✅ Convertido</option>
                        <option value="archived">📦 Arquivado</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Leads Table -->
        <div class="bg-white rounded-xl shadow-card border border-border overflow-hidden">
            <div id="loading-state" class="flex items-center justify-center py-16">
                <div class="flex items-center gap-3 text-ink-muted">
                    <svg class="w-6 h-6 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span class="text-sm">Carregando leads...</span>
                </div>
            </div>

            <div id="table-content" class="hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-border bg-surface/50">
                                <th class="text-left px-4 sm:px-6 py-3 text-xs font-semibold text-ink-secondary uppercase tracking-wider">Cliente</th>
                                <th class="text-left px-4 sm:px-6 py-3 text-xs font-semibold text-ink-secondary uppercase tracking-wider">Serviço</th>
                                <th class="text-left px-4 sm:px-6 py-3 text-xs font-semibold text-ink-secondary uppercase tracking-wider hidden sm:table-cell">Data</th>
                                <th class="text-left px-4 sm:px-6 py-3 text-xs font-semibold text-ink-secondary uppercase tracking-wider">Status</th>
                                <th class="text-right px-4 sm:px-6 py-3 text-xs font-semibold text-ink-secondary uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody id="leads-tbody">
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div id="empty-state" class="hidden py-16 text-center">
                    <svg class="w-16 h-16 text-ink-muted/30 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <p class="text-ink-secondary font-medium">Nenhum lead recebido ainda</p>
                    <p class="text-ink-muted text-sm mt-1">Os clientes que solicitarem orçamento aparecerão aqui.</p>
                </div>

                <!-- Pagination -->
                <div id="pagination" class="hidden flex items-center justify-between px-4 sm:px-6 py-4 border-t border-border bg-surface/30">
                    <span class="text-sm text-ink-muted" id="pagination-info">—</span>
                    <div class="flex items-center gap-2">
                        <button id="prev-page"
                            class="px-3 py-1.5 text-sm font-medium rounded-lg border border-border hover:bg-white transition-colors disabled:opacity-30 disabled:cursor-not-allowed">
                            ← Anterior
                        </button>
                        <span class="text-sm text-ink-muted px-2" id="page-indicator">1</span>
                        <button id="next-page"
                            class="px-3 py-1.5 text-sm font-medium rounded-lg border border-border hover:bg-white transition-colors disabled:opacity-30 disabled:cursor-not-allowed">
                            Próxima →
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- ── Detail Modal ──────────────────────────────────── -->
<div id="lead-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeLeadModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-modal w-full max-w-lg max-h-[90vh] overflow-y-auto animate-fade-in">
            <div class="px-6 py-5 border-b border-border flex items-center justify-between">
                <h3 class="text-h3 text-ink">Detalhes do Lead</h3>
                <button onclick="closeLeadModal()" class="text-ink-muted hover:text-ink transition-colors p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="px-6 py-5 space-y-5" id="lead-detail-content">
                <!-- Populated by JS -->
            </div>
        </div>
    </div>
</div>

<script>
// ═══════════════════════════════════════════════════════════════
// State
// ═══════════════════════════════════════════════════════════════
let currentPage = 1;
let totalPages = 1;
let leadsData = [];

// ═══════════════════════════════════════════════════════════════
// Load Leads
// ═══════════════════════════════════════════════════════════════
async function loadLeads() {
    const loading = document.getElementById('loading-state');
    const content = document.getElementById('table-content');
    const empty = document.getElementById('empty-state');
    const tbody = document.getElementById('leads-tbody');
    const pagination = document.getElementById('pagination');

    loading.classList.remove('hidden');
    content.classList.add('hidden');
    empty.classList.add('hidden');
    pagination.classList.add('hidden');

    try {
        const token = '<?= getToken() ?>';
        if (!token) { window.location.href = '?page=login'; return; }

        const status = document.getElementById('status-filter').value;
        const search = document.getElementById('search-input').value.trim();

        let url = `/api/v1/leads?page=${currentPage}&limit=20`;
        if (status) url += `&status=${status}`;
        if (search.length >= 2) url += `&search=${encodeURIComponent(search)}`;

        const resp = await fetch(url, {
            headers: { 'Authorization': 'Bearer ' + token }
        });

        if (!resp.ok) {
            if (resp.status === 401) { window.location.href = '?page=login'; return; }
            throw new Error('Erro ao carregar leads');
        }

        const data = await resp.json();
        leadsData = data.leads;
        totalPages = data.pagination.totalPages;

        loading.classList.add('hidden');

        if (leadsData.length === 0) {
            content.classList.remove('hidden');
            empty.classList.remove('hidden');
            tbody.innerHTML = '';
            pagination.classList.add('hidden');
            return;
        }

        content.classList.remove('hidden');
        empty.classList.add('hidden');

        tbody.innerHTML = leadsData.map(l => {
            const statusBadge = getStatusBadge(l.status);
            const phoneClean = l.customerPhone.replace(/\D/g, '').replace(/^55/, '');
            const whatsappLink = phoneClean.length >= 10
                ? `https://wa.me/55${phoneClean}?text=Olá%20${encodeURIComponent(l.customerName)}!%20Recebemos%20sua%20solicitação%20de%20orçamento%20para%20${encodeURIComponent(l.service)}.%20Vamos%20analisar!`
                : '#';

            return `
                <tr class="border-b border-border/50 hover:bg-surface/30 transition-colors cursor-pointer" onclick="openLeadModal(${l.id})">
                    <td class="px-4 sm:px-6 py-4">
                        <p class="text-sm font-medium text-ink">${escHtml(l.customerName)}</p>
                        <p class="text-xs text-ink-muted">${escHtml(l.customerPhone)}</p>
                    </td>
                    <td class="px-4 sm:px-6 py-4">
                        <p class="text-sm text-ink">${escHtml(l.service)}</p>
                        ${l.hasPhotos > 0 ? `<span class="inline-flex items-center gap-1 text-xs text-primary"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg> ${l.hasPhotos} foto(s)</span>` : ''}
                    </td>
                    <td class="px-4 sm:px-6 py-4 hidden sm:table-cell">
                        <p class="text-sm text-ink-secondary">${formatDate(l.createdAt)}</p>
                        <p class="text-xs text-ink-muted">${timeAgo(l.createdAt)}</p>
                    </td>
                    <td class="px-4 sm:px-6 py-4">
                        ${statusBadge}
                    </td>
                    <td class="px-4 sm:px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            ${whatsappLink !== '#' ? `
                                <a href="${whatsappLink}" target="_blank" rel="noopener"
                                    class="p-2 rounded-lg hover:bg-green-50 text-whatsapp transition-colors"
                                    title="Abrir WhatsApp" onclick="event.stopPropagation()">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                </a>
                            ` : ''}
                            ${l.status === 'new' ? `
                                <button onclick="event.stopPropagation(); updateLeadStatus(${l.id}, 'contacted')"
                                    class="px-3 py-1.5 text-xs font-medium rounded-lg bg-info/10 text-info hover:bg-info/20 transition-colors whitespace-nowrap">
                                    📞 Contactar
                                </button>
                            ` : ''}
                            ${l.status === 'contacted' ? `
                                <button onclick="event.stopPropagation(); updateLeadStatus(${l.id}, 'converted')"
                                    class="px-3 py-1.5 text-xs font-medium rounded-lg bg-success/10 text-success hover:bg-success/20 transition-colors whitespace-nowrap">
                                    ✅ Converter
                                </button>
                            ` : ''}
                        </div>
                    </td>
                </tr>
            `;
        }).join('');

        // Pagination
        pagination.classList.remove('hidden');
        document.getElementById('pagination-info').textContent =
            `Mostrando ${leadsData.length} de ${data.pagination.total} leads`;
        document.getElementById('page-indicator').textContent = `${currentPage} de ${totalPages}`;
        document.getElementById('prev-page').disabled = currentPage <= 1;
        document.getElementById('next-page').disabled = currentPage >= totalPages;

    } catch (err) {
        loading.classList.add('hidden');
        content.classList.remove('hidden');
        document.getElementById('leads-tbody').innerHTML = `
            <tr><td colspan="5" class="px-6 py-12 text-center">
                <p class="text-danger text-sm">${escHtml(err.message)}</p>
            </td></tr>
        `;
    }
}

// ═══════════════════════════════════════════════════════════════
// Helpers
// ═══════════════════════════════════════════════════════════════
function getStatusBadge(status) {
    const config = {
        new:       { label: '🆕 Novo',       class: 'bg-info/10 text-info border-info/20' },
        contacted: { label: '📞 Contactado',  class: 'bg-warning/10 text-warning border-warning/20' },
        converted: { label: '✅ Convertido',  class: 'bg-success/10 text-success border-success/20' },
        archived:  { label: '📦 Arquivado',  class: 'bg-ink-muted/10 text-ink-secondary border-ink-muted/20' },
    };
    const c = config[status] || config.new;
    return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border ${c.class}">${c.label}</span>`;
}

function formatDate(dateStr) {
    if (!dateStr) return '—';
    const d = new Date(dateStr);
    return d.toLocaleDateString('pt-BR');
}

function timeAgo(dateStr) {
    if (!dateStr) return '';
    const now = new Date();
    const d = new Date(dateStr);
    const diff = Math.floor((now - d) / 1000);
    if (diff < 60) return 'agora';
    if (diff < 3600) return `${Math.floor(diff / 60)} min atrás`;
    if (diff < 86400) return `${Math.floor(diff / 3600)}h atrás`;
    if (diff < 2592000) return `${Math.floor(diff / 86400)} dias atrás`;
    return formatDate(dateStr);
}

function escHtml(str) {
    if (!str) return '—';
    return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
}

// ═══════════════════════════════════════════════════════════════
// Detail Modal
// ═══════════════════════════════════════════════════════════════
function openLeadModal(id) {
    const lead = leadsData.find(l => l.id === id);
    if (!lead) return;

    const phoneClean = (lead.customerPhone?.replace(/\D/g, '') || '').replace(/^55/, '');
    const whatsappLink = phoneClean.length >= 10
        ? `https://wa.me/55${phoneClean}?text=Olá%20${encodeURIComponent(lead.customerName)}!%20Recebemos%20sua%20solicitação%20de%20orçamento%20para%20${encodeURIComponent(lead.service)}.%20Vamos%20analisar!`
        : '#';
    const telLink = phoneClean.length >= 10 ? `tel:+55${phoneClean}` : '#';

    // Parse photos
    let photos = [];
    if (lead.photoUrls) {
        try {
            photos = typeof lead.photoUrls === 'string' ? JSON.parse(lead.photoUrls) : lead.photoUrls;
        } catch(e) { photos = []; }
    }

    document.getElementById('lead-detail-content').innerHTML = `
        <div class="flex items-center justify-between">
            <div>
                <p class="text-lg font-semibold text-ink">${escHtml(lead.customerName)}</p>
                <p class="text-sm text-ink-muted">${getStatusBadge(lead.status)}</p>
            </div>
            <p class="text-xs text-ink-muted">#${lead.id}</p>
        </div>

        <div class="grid grid-cols-2 gap-4 p-4 bg-surface rounded-xl">
            <div>
                <p class="text-xs text-ink-muted mb-0.5">📞 Telefone</p>
                <p class="text-sm font-medium text-ink">${escHtml(lead.customerPhone)}</p>
            </div>
            <div>
                <p class="text-xs text-ink-muted mb-0.5">✉️ E-mail</p>
                <p class="text-sm font-medium text-ink">${lead.customerEmail ? escHtml(lead.customerEmail) : '—'}</p>
            </div>
        </div>

        <div>
            <p class="text-xs text-ink-muted mb-1">📋 Serviço solicitado</p>
            <p class="text-sm font-medium text-ink">${escHtml(lead.service)}</p>
        </div>

        ${lead.description ? `
        <div>
            <p class="text-xs text-ink-muted mb-1">📝 Descrição</p>
            <p class="text-sm text-ink-secondary bg-surface rounded-lg p-3">${escHtml(lead.description)}</p>
        </div>` : ''}

        ${lead.desiredDate || lead.desiredTime ? `
        <div>
            <p class="text-xs text-ink-muted mb-1">📅 Data/Horário</p>
            <p class="text-sm font-medium text-ink">
                ${lead.desiredDate ? new Date(lead.desiredDate + 'T12:00:00').toLocaleDateString('pt-BR') : '—'}
                ${lead.desiredTime ? `às ${lead.desiredTime}h` : ''}
            </p>
        </div>` : ''}

        ${lead.address ? `
        <div>
            <p class="text-xs text-ink-muted mb-1">📍 Local</p>
            <p class="text-sm font-medium text-ink">${escHtml(lead.address)}</p>
        </div>` : ''}

        ${photos.length > 0 ? `
        <div>
            <p class="text-xs text-ink-muted mb-2">📸 Fotos (${photos.length})</p>
            <div class="grid grid-cols-3 gap-2">
                ${photos.map(url => `
                    <a href="${url}" target="_blank" rel="noopener"
                        class="aspect-square rounded-lg overflow-hidden border border-border bg-surface hover:opacity-80 transition-opacity">
                        <img src="${url}" alt="Foto do serviço" class="w-full h-full object-cover">
                    </a>
                `).join('')}
            </div>
        </div>` : ''}

        <div class="text-xs text-ink-muted">
            Recebido em ${formatDate(lead.createdAt)} (${timeAgo(lead.createdAt)})
        </div>

        <div class="flex flex-col sm:flex-row gap-3 pt-2 border-t border-border">
            ${whatsappLink !== '#' ? `
                <a href="${whatsappLink}" target="_blank" rel="noopener"
                    class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-whatsapp text-white font-medium text-sm hover:opacity-90 transition-opacity">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    WhatsApp
                </a>
            ` : ''}
            ${telLink !== '#' ? `
                <a href="${telLink}"
                    class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-primary/10 text-primary font-medium text-sm hover:bg-primary/20 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    Ligar
                </a>
            ` : ''}
            ${lead.status === 'new' ? `
                <button onclick="updateLeadStatus(${lead.id}, 'contacted'); closeLeadModal();"
                    class="flex-1 px-4 py-2.5 rounded-lg bg-info/10 text-info font-medium text-sm hover:bg-info/20 transition-colors">
                    📞 Marcar Contactado
                </button>
            ` : ''}
            ${lead.status === 'contacted' ? `
                <button onclick="updateLeadStatus(${lead.id}, 'converted'); closeLeadModal();"
                    class="flex-1 px-4 py-2.5 rounded-lg bg-success/10 text-success font-medium text-sm hover:bg-success/20 transition-colors">
                    ✅ Marcar Convertido
                </button>
            ` : ''}
            ${lead.status !== 'archived' ? `
                <button onclick="updateLeadStatus(${lead.id}, 'archived'); closeLeadModal();"
                    class="px-4 py-2.5 rounded-lg bg-ink-muted/10 text-ink-secondary font-medium text-sm hover:bg-ink-muted/20 transition-colors">
                    📦 Arquivar
                </button>
            ` : ''}
        </div>
    `;

    document.getElementById('lead-modal').classList.remove('hidden');
}

function closeLeadModal() {
    document.getElementById('lead-modal').classList.add('hidden');
}

// ═══════════════════════════════════════════════════════════════
// Update Lead Status
// ═══════════════════════════════════════════════════════════════
async function updateLeadStatus(id, newStatus) {
    try {
        const token = '<?= getToken() ?>';
        const resp = await fetch(`/api/v1/leads/${id}`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
            body: JSON.stringify({ status: newStatus }),
        });

        if (!resp.ok) throw new Error('Erro ao atualizar lead');

        showToast('Lead atualizado com sucesso!', 'success');
        loadLeads();
    } catch (err) {
        showToast(err.message, 'error');
    }
}

// ═══════════════════════════════════════════════════════════════
// Event Listeners
// ═══════════════════════════════════════════════════════════════
let searchTimeout;
document.getElementById('search-input').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        currentPage = 1;
        loadLeads();
    }, 400);
});

document.getElementById('status-filter').addEventListener('change', () => {
    currentPage = 1;
    loadLeads();
});

document.getElementById('prev-page').addEventListener('click', () => {
    if (currentPage > 1) { currentPage--; loadLeads(); }
});

document.getElementById('next-page').addEventListener('click', () => {
    if (currentPage < totalPages) { currentPage++; loadLeads(); }
});

// Keyboard: Escape fecha modal
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeLeadModal();
});

// ── Init ────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', loadLeads);
</script>
