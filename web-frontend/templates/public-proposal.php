<!-- ═══════════════════════════════════════════════════════════════
     templates/public-proposal.php — Página Pública de Proposta
     ═══════════════════════════════════════════════════════════
     Story 6.3 — Cliente visualiza proposta e aprova/rejeita
     sem necessidade de login. Acessível via ?page=public-proposal&token=UUID
     ═══════════════════════════════════════════════════════════ -->

<?php
$publicToken = $_GET['token'] ?? '';
if (empty($publicToken)) {
    echo '<div class="min-h-screen bg-surface flex items-center justify-center p-6">';
    echo '<div class="text-center max-w-md">';
    echo '<div class="w-20 h-20 bg-danger/10 rounded-full flex items-center justify-center mx-auto mb-6">';
    echo '<svg class="w-10 h-10 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>';
    echo '</div>';
    echo '<h1 class="text-h1 text-ink mb-3">Link Inválido</h1>';
    echo '<p class="text-ink-secondary mb-6">O link que você acessou não é válido. Verifique o link enviado pelo profissional.</p>';
    echo '</div></div>';
    return;
}
?>

<div class="min-h-screen bg-gradient-to-b from-primary-50/20 via-white to-white">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-8 sm:py-12">
        <!-- Loading State -->
        <div id="loading-state" class="text-center py-20">
            <svg class="w-10 h-10 animate-spin text-primary mx-auto mb-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <p class="text-ink-secondary">Carregando proposta...</p>
        </div>

        <!-- Error State -->
        <div id="error-state" class="hidden text-center py-20">
            <div class="w-20 h-20 bg-danger/10 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </div>
            <h1 class="text-h1 text-ink mb-3">Proposta não encontrada</h1>
            <p class="text-ink-secondary mb-6">O link que você acessou pode estar expirado ou inválido.</p>
            <p class="text-sm text-ink-muted">Entre em contato com o profissional que enviou esta proposta para mais informações.</p>
        </div>

        <!-- Proposal Content -->
        <div id="proposal-content" class="hidden">
            <!-- Header -->
            <div class="bg-white rounded-2xl shadow-modal border border-border overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-primary-700 to-primary-600 px-6 sm:px-8 py-8 sm:py-10 text-white">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-primary-200 text-sm font-medium mb-1" id="prop-number">—</p>
                            <h1 class="text-h1 text-white mb-2" id="prop-title">—</h1>
                            <p class="text-primary-100 text-sm" id="prop-tenant">—</p>
                        </div>
                        <div id="prop-status-badge" class="px-3 py-1.5 rounded-full text-xs font-medium bg-white/20 text-white">
                            Carregando...
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="px-6 sm:px-8 py-6 border-b border-border" id="prop-description-section">
                    <p class="text-sm text-ink-secondary mb-2">Descrição</p>
                    <p class="text-ink" id="prop-description">—</p>
                </div>

                <!-- Items Table -->
                <div class="px-6 sm:px-8 py-6 border-b border-border">
                    <p class="text-sm font-semibold text-ink mb-4">Itens da Proposta</p>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-border">
                                    <th class="text-left py-2 text-xs font-semibold text-ink-secondary uppercase">Item</th>
                                    <th class="text-right py-2 text-xs font-semibold text-ink-secondary uppercase">Qtd</th>
                                    <th class="text-right py-2 text-xs font-semibold text-ink-secondary uppercase">Valor Unit.</th>
                                    <th class="text-right py-2 text-xs font-semibold text-ink-secondary uppercase">Total</th>
                                </tr>
                            </thead>
                            <tbody id="items-tbody">
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Total & Details -->
                <div class="px-6 sm:px-8 py-6 border-b border-border bg-surface/30">
                    <div class="flex justify-between items-center">
                        <span class="text-base font-semibold text-ink">Valor Total</span>
                        <span class="text-xl font-bold text-primary" id="prop-total">—</span>
                    </div>
                    <div id="prop-details" class="mt-4 space-y-2 text-sm">
                    </div>
                </div>

                <!-- Payment Terms -->
                <div class="px-6 sm:px-8 py-4 border-b border-border hidden" id="payment-terms-section">
                    <p class="text-xs text-ink-secondary mb-1">Condições de Pagamento</p>
                    <p class="text-sm text-ink" id="prop-payment-terms">—</p>
                </div>

                <!-- Notes -->
                <div class="px-6 sm:px-8 py-4 hidden" id="notes-section">
                    <p class="text-xs text-ink-secondary mb-1">Observações</p>
                    <p class="text-sm text-ink" id="prop-notes">—</p>
                </div>

                <!-- Actions -->
                <div id="actions-section" class="px-6 sm:px-8 py-6 border-t border-border bg-white">
                    <div id="actions-approve-reject" class="hidden">
                        <p class="text-center text-sm text-ink-secondary mb-4">
                            O que você achou desta proposta?
                        </p>
                        <div class="flex flex-col sm:flex-row gap-3 justify-center">
                            <button onclick="proposalAction('reject')"
                                class="flex-1 flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl border-2 border-danger/20 text-danger font-medium hover:bg-danger/5 hover:border-danger/40 transition-all active:scale-[0.98]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Recusar Proposta
                            </button>
                            <button onclick="proposalAction('approve')"
                                class="flex-1 flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-primary text-white font-medium hover:bg-primary-600 active:scale-[0.98] transition-all shadow-card">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Aprovar Proposta
                            </button>
                        </div>
                    </div>
                    <div id="actions-result" class="hidden text-center">
                    </div>
                    
                    <!-- ══════════════════════════════════════════════════════
                         Pix Payment Section (Story 5.4)
                         ══════════════════════════════════════════════════════ -->
                    <div id="payment-section" class="hidden pt-4">
                        <!-- Payment Method Selection -->
                        <div id="payment-method-select" class="border border-border rounded-xl p-4 sm:p-6 bg-surface/30">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-ink">Pagamento via Pix</h3>
                                    <p class="text-sm text-ink-secondary">Pague no Pix e receba confirmação na hora</p>
                                </div>
                            </div>
                            <button onclick="startPixPayment()"
                                class="w-full flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-primary text-white font-medium hover:bg-primary-600 active:scale-[0.98] transition-all shadow-card text-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 1.5-.5 2-1.5 2S9 12.5 9 11s.5-2 1.5-2 1.5.5 1.5 2zM12 8c.5 0 1-.5 1-1.5S12.5 5 12 5s-1 .5-1 1.5S11.5 8 12 8zM12 17c2.5 0 4-1.5 4-4s-1.5-4-4-4-4 1.5-4 4 1.5 4 4 4z"/>
                                </svg>
                                <span id="pix-btn-text">Pagar com Pix</span>
                                <svg class="w-4 h-4 animate-spin hidden" id="pix-btn-spinner" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                            </button>
                            <p class="text-xs text-ink-muted text-center mt-2">Pagamento processado com segurança pelo Mercado Pago</p>
                        </div>

                        <!-- Pix QR Code Display -->
                        <div id="pix-qr-display" class="hidden text-center py-4 animate-fade-in">
                            <div class="bg-white border-2 border-primary/20 rounded-2xl p-6 sm:p-8 max-w-sm mx-auto">
                                <div class="w-48 h-48 sm:w-56 sm:h-56 mx-auto mb-4 bg-surface rounded-xl flex items-center justify-center border border-border">
                                    <div id="pix-qr-placeholder" class="text-center p-4">
                                        <svg class="w-12 h-12 text-primary/40 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                        </svg>
                                        <p class="text-sm text-ink-muted">QR Code será gerado ao iniciar o pagamento</p>
                                    </div>
                                    <img id="pix-qr-image" class="hidden w-full h-full object-contain p-2" alt="QR Code Pix">
                                </div>
                                
                                <p class="text-sm font-medium text-ink mb-1">Pague com Pix</p>
                                <p class="text-xs text-ink-secondary mb-4">Escaneie o QR Code com o app do seu banco</p>

                                <div class="bg-surface rounded-xl p-3 border border-border mb-3">
                                    <p class="text-xs text-ink-muted mb-1">Código Pix Copia e Cola</p>
                                    <p id="pix-copy-code" class="text-xs font-mono text-ink break-all select-all">—</p>
                                </div>

                                <button onclick="copyPixCode()"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-primary/10 text-primary font-medium hover:bg-primary/20 transition-all text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                                    </svg>
                                    Copiar Código Pix
                                </button>

                                <div class="mt-4 flex items-center justify-center gap-2 text-xs text-ink-muted">
                                    <svg class="w-4 h-4 animate-spin" id="pix-timer-icon" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    <span id="pix-timer">Aguardando pagamento...</span>
                                </div>
                            </div>

                            <div id="pix-success" class="hidden mt-4 animate-fade-in">
                                <div class="bg-success/10 border border-success/20 rounded-2xl p-6 sm:p-8 max-w-sm mx-auto">
                                    <div class="w-16 h-16 bg-success rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-h3 text-ink mb-2">Pagamento Confirmado! 🎉</h3>
                                    <p class="text-sm text-ink-secondary mb-4">Seu pagamento foi processado com sucesso. O profissional será notificado.</p>
                                    <div class="text-xs text-ink-muted">
                                        <p>Valor pago: <strong class="text-ink" id="pix-paid-amount">—</strong></p>
                                        <p class="mt-1">Data: <strong class="text-ink" id="pix-paid-date">—</strong></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pix Error -->
                        <div id="pix-error" class="hidden text-center py-4">
                            <div class="bg-danger/5 border border-danger/20 rounded-xl p-6 max-w-sm mx-auto">
                                <p class="text-danger font-medium" id="pix-error-message">Erro ao processar pagamento</p>
                                <button onclick="resetPixPayment()"
                                    class="mt-3 text-sm text-primary hover:underline">Tentar novamente</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <p class="text-center text-xs text-ink-muted">
                Proposta gerada via ServiceSaaS — Plataforma de Gestão de Serviços
            </p>
        </div>
    </div>
</div>

<!-- ── Inline Styles ──────────────────────────────────── -->
<style>
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in-up { animation: fadeInUp 0.4s ease-out; }
    @keyframes pulse-dot { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
    .animate-pulse-dot { animation: pulse-dot 1.5s ease-in-out infinite; }
</style>

<script>
// ═══════════════════════════════════════════════════════════════
// State
// ═══════════════════════════════════════════════════════════════
const TOKEN = '<?= htmlspecialchars($publicToken) ?>';
let proposalData = null;

// ═══════════════════════════════════════════════════════════════
// Load Proposal
// ═══════════════════════════════════════════════════════════════
async function loadProposal() {
    const loading = document.getElementById('loading-state');
    const content = document.getElementById('proposal-content');
    const error = document.getElementById('error-state');

    try {
        const resp = await fetch(`/api/v1/public/proposals/${encodeURIComponent(TOKEN)}`);
        if (!resp.ok) throw new Error('Not found');
        const data = await resp.json();
        proposalData = data.proposal;
        renderProposal(data.proposal);
        loading.classList.add('hidden');
        content.classList.remove('hidden');
    } catch (err) {
        loading.classList.add('hidden');
        error.classList.remove('hidden');
    }
}

// ═══════════════════════════════════════════════════════════════
// Render
// ═══════════════════════════════════════════════════════════════
function renderProposal(p) {
    document.getElementById('prop-number').textContent = p.number || 'Proposta';
    document.getElementById('prop-title').textContent = p.title;
    document.getElementById('prop-tenant').textContent = p.tenant?.name || '';

    // Status badge
    const statusConfig = {
        draft:     { label: 'Rascunho', class: 'bg-ink-muted/20 text-ink-secondary' },
        sent:      { label: '📨 Enviada', class: 'bg-info/20 text-info' },
        viewed:    { label: '👁️ Visualizada', class: 'bg-warning/20 text-warning' },
        accepted:  { label: '✅ Aprovada', class: 'bg-success/20 text-success' },
        rejected:  { label: '❌ Recusada', class: 'bg-danger/20 text-danger' },
        cancelled: { label: '🚫 Cancelada', class: 'bg-ink-muted/20 text-ink-secondary' },
        paid:      { label: '💰 Paga', class: 'bg-success/20 text-success' },
    };
    const cfg = statusConfig[p.status] || statusConfig.draft;
    document.getElementById('prop-status-badge').innerHTML =
        `<span class="px-3 py-1.5 rounded-full text-xs font-medium ${cfg.class}">${cfg.label}</span>`;

    // Description
    if (p.description) {
        document.getElementById('prop-description').textContent = p.description;
    } else {
        document.getElementById('prop-description-section').classList.add('hidden');
    }

    // Items
    const tbody = document.getElementById('items-tbody');
    if (p.items && p.items.length > 0) {
        p.items.forEach(item => {
            const tr = document.createElement('tr');
            tr.className = 'border-b border-border/50 last:border-b-0';
            const unitPrice = `R$ ${parseFloat(item.unit_price).toFixed(2).replace('.', ',')}`;
            const totalPrice = `R$ ${parseFloat(item.total_price || item.quantity * item.unit_price).toFixed(2).replace('.', ',')}`;
            tr.innerHTML = `
                <td class="py-3 text-sm text-ink">${escHtml(item.description)}</td>
                <td class="py-3 text-sm text-ink-secondary text-right">${item.quantity}</td>
                <td class="py-3 text-sm text-ink-secondary text-right">${unitPrice}</td>
                <td class="py-3 text-sm font-medium text-ink text-right">${totalPrice}</td>
            `;
            tbody.appendChild(tr);
        });
    }

    // Total
    const totalFormatted = `R$ ${parseFloat(p.totalAmount).toFixed(2).replace('.', ',')}`;
    document.getElementById('prop-total').textContent = totalFormatted;

    // Details: validity, date
    const details = document.getElementById('prop-details');
    const detailItems = [];
    if (p.validUntil) {
        const d = new Date(p.validUntil + 'T12:00:00');
        detailItems.push(`<span class="text-ink-secondary">Válida até: <strong class="text-ink">${d.toLocaleDateString('pt-BR')}</strong></span>`);
    }
    const created = new Date(p.createdAt);
    detailItems.push(`<span class="text-ink-secondary">Criada em: <strong class="text-ink">${created.toLocaleDateString('pt-BR')}</strong></span>`);
    details.innerHTML = detailItems.join('<br>');

    // Payment terms
    if (p.paymentTerms) {
        document.getElementById('payment-terms-section').classList.remove('hidden');
        document.getElementById('prop-payment-terms').textContent = p.paymentTerms;
    }

    // Notes
    if (p.notes) {
        document.getElementById('notes-section').classList.remove('hidden');
        document.getElementById('prop-notes').textContent = p.notes;
    }

    // Actions + Payment
    const approveSection = document.getElementById('actions-approve-reject');
    const resultSection = document.getElementById('actions-result');
    const paymentSection = document.getElementById('payment-section');

    if (p.canAct) {
        approveSection.classList.remove('hidden');
        resultSection.classList.add('hidden');
        paymentSection.classList.add('hidden');
    } else {
        approveSection.classList.add('hidden');

        if (p.status === 'accepted') {
            // Show payment section for accepted proposals
            resultSection.classList.add('hidden');
            paymentSection.classList.remove('hidden');
            checkPaymentStatus();
        } else if (p.status === 'paid') {
            resultSection.classList.remove('hidden');
            paymentSection.classList.add('hidden');
            resultSection.innerHTML = `
                <div class="py-4">
                    <div class="w-16 h-16 bg-success rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <p class="text-lg font-semibold text-ink mb-1">💰 Proposta Paga</p>
                    <p class="text-sm text-ink-secondary">Esta proposta já foi paga. O profissional será notificado.</p>
                </div>
            `;
        } else if (p.status === 'rejected') {
            resultSection.classList.remove('hidden');
            paymentSection.classList.add('hidden');
            resultSection.innerHTML = `
                <div class="py-4">
                    <div class="w-16 h-16 bg-danger/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <p class="text-lg font-semibold text-ink mb-1">Proposta Recusada</p>
                    <p class="text-sm text-ink-secondary">Esta proposta foi recusada. Se precisar de ajuda, entre em contato com o profissional.</p>
                </div>
            `;
        } else {
            resultSection.classList.remove('hidden');
            paymentSection.classList.add('hidden');
            resultSection.innerHTML = `
                <div class="py-4">
                    <p class="text-ink-secondary text-sm">Status: ${p.status}</p>
                </div>
            `;
        }
    }
}

// ═══════════════════════════════════════════════════════════════
// Actions
// ═══════════════════════════════════════════════════════════════
async function proposalAction(action) {
    const btnReject = document.querySelector('button[onclick*="reject"]');
    const btnApprove = document.querySelector('button[onclick*="approve"]');
    const buttons = [btnReject, btnApprove];

    buttons.forEach(btn => { if (btn) { btn.disabled = true; btn.style.opacity = '0.6'; } });

    try {
        const resp = await fetch(`/api/v1/public/proposals/${encodeURIComponent(TOKEN)}/status`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action }),
        });

        const data = await resp.json();

        if (!resp.ok) {
            throw new Error(data.message || 'Erro ao processar resposta');
        }

        // Reload proposal to show updated status
        showToast(data.message || 'Resposta registrada!', 'success');
        setTimeout(() => location.reload(), 1500);

    } catch (err) {
        showToast(err.message, 'error');
        buttons.forEach(btn => { if (btn) { btn.disabled = false; btn.style.opacity = '1'; } });
    }
}

// ═══════════════════════════════════════════════════════════════
// Helpers
// ═══════════════════════════════════════════════════════════════
function escHtml(str) {
    if (!str) return '';
    return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
}

// ═══════════════════════════════════════════════════════════════
// Story 5.4 — Checkout Pix
// ═══════════════════════════════════════════════════════════════
// Inicia pagamento, exibe QR Code, polling de status,
// e tela de sucesso.
// ═══════════════════════════════════════════════════════════════

// ── State ────────────────────────────────────────────────
let pixPollingInterval = null;
let pixStartTime = null;
const PIX_POLLING_INTERVAL = 5000; // 5 segundos
const PIX_TIMEOUT = 30 * 60 * 1000; // 30 minutos

// ── Toast ────────────────────────────────────────────────
function showToast(message, type) {
    // Remove existing toast
    const old = document.getElementById('toast');
    if (old) old.remove();

    const toast = document.createElement('div');
    toast.id = 'toast';
    const bg = type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-ink';
    toast.className = `fixed top-4 right-4 z-[999] ${bg} text-white px-5 py-3 rounded-xl shadow-modal text-sm font-medium animate-fade-in-up max-w-sm`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; toast.style.transition = 'opacity 0.3s'; setTimeout(() => toast.remove(), 300); }, 4000);
}

// ── Start Pix Payment ───────────────────────────────────
async function startPixPayment() {
    const btn = document.querySelector('button[onclick*="startPixPayment"]');
    const btnText = document.getElementById('pix-btn-text');
    const spinner = document.getElementById('pix-btn-spinner');
    const errorEl = document.getElementById('pix-error');
    const errorMsg = document.getElementById('pix-error-message');
    const methodSelect = document.getElementById('payment-method-select');
    const qrDisplay = document.getElementById('pix-qr-display');

    errorEl.classList.add('hidden');
    btn.disabled = true;
    btnText.textContent = 'Gerando Pix...';
    spinner.classList.remove('hidden');

    try {
        // Pegar dados do cliente do proposalData
        const payerName = proposalData?.clientName || 'Cliente';
        const payerEmail = proposalData?.clientEmail || '';

        if (!payerEmail) {
            // Se não temos email, pedir para o usuário
            const email = prompt('Informe seu e-mail para gerar o pagamento Pix:');
            if (!email || !email.includes('@')) {
                throw new Error('E-mail inválido. O pagamento Pix requer um e-mail válido.');
            }
            payerEmail = email;
        }

        const resp = await fetch(
            `/api/v1/public/proposals/${encodeURIComponent(TOKEN)}/pay`,
            {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    name: payerName,
                    email: payerEmail,
                }),
            }
        );

        const data = await resp.json();

        if (!resp.ok) {
            throw new Error(data.message || 'Erro ao gerar pagamento Pix');
        }

        // ── Exibir QR Code ────────────────────────────────
        methodSelect.classList.add('hidden');
        qrDisplay.classList.remove('hidden');

        const pix = data.data.pix;

        // QR Code image (base64)
        const qrImg = document.getElementById('pix-qr-image');
        const qrPlaceholder = document.getElementById('pix-qr-placeholder');

        if (pix.qrCodeBase64) {
            qrImg.src = `data:image/png;base64,${pix.qrCodeBase64}`;
            qrImg.classList.remove('hidden');
            qrPlaceholder.classList.add('hidden');
        } else {
            // Fallback: mostrar apenas o código
            qrPlaceholder.innerHTML = `<p class="text-sm text-ink-muted">QR Code temporariamente indisponível</p>`;
        }

        // Copy-paste code
        document.getElementById('pix-copy-code').textContent = pix.copyPaste || pix.qrCode || '—';

        // Iniciar timer e polling
        pixStartTime = Date.now();
        startPixPolling();

        showToast('✅ Pagamento Pix gerado! Escaneie o QR Code com seu banco.', 'success');

    } catch (err) {
        errorMsg.textContent = err.message;
        errorEl.classList.remove('hidden');
        showToast('❌ ' + err.message, 'error');
    } finally {
        btn.disabled = false;
        btnText.textContent = 'Pagar com Pix';
        spinner.classList.add('hidden');
    }
}

// ── Copy Pix Code ───────────────────────────────────────
async function copyPixCode() {
    const code = document.getElementById('pix-copy-code').textContent;
    if (!code || code === '—') return;

    try {
        await navigator.clipboard.writeText(code);
        showToast('📋 Código Pix copiado!', 'success');
    } catch {
        // Fallback
        const textarea = document.createElement('textarea');
        textarea.value = code;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        showToast('📋 Código Pix copiado!', 'success');
    }
}

// ── Polling de Status ────────────────────────────────────
function startPixPolling() {
    // Limpar polling anterior
    if (pixPollingInterval) {
        clearInterval(pixPollingInterval);
    }

    updatePixTimer();
    pixPollingInterval = setInterval(async () => {
        try {
            // Verificar timeout
            if (pixStartTime && (Date.now() - pixStartTime > PIX_TIMEOUT)) {
                clearInterval(pixPollingInterval);
                pixPollingInterval = null;
                document.getElementById('pix-timer').textContent = '⏰ Tempo expirado. Gere um novo pagamento.';
                return;
            }

            const resp = await fetch(
                `/api/v1/public/proposals/${encodeURIComponent(TOKEN)}/payment`
            );
            const data = await resp.json();

            if (data.isPaid) {
                // Pagamento confirmado!
                clearInterval(pixPollingInterval);
                pixPollingInterval = null;
                showPixSuccess(data.payment);
            }

            updatePixTimer();

        } catch (err) {
            console.warn('[Pix] Polling error:', err);
        }
    }, PIX_POLLING_INTERVAL);
}

function updatePixTimer() {
    const timerEl = document.getElementById('pix-timer');
    if (!pixStartTime) return;

    const elapsed = Date.now() - pixStartTime;
    const remaining = Math.max(0, PIX_TIMEOUT - elapsed);

    if (remaining <= 0) {
        timerEl.textContent = '⏰ Tempo expirado';
        return;
    }

    const min = Math.floor(remaining / 60000);
    const sec = Math.floor((remaining % 60000) / 1000);
    timerEl.textContent = `⏱️ ${min}:${sec.toString().padStart(2, '0')} para pagar`;
}

// ── Sucesso ──────────────────────────────────────────────
function showPixSuccess(payment) {
    const qrDisplay = document.getElementById('pix-qr-display');
    const success = document.getElementById('pix-success');
    const timerEl = document.getElementById('pix-timer');
    const timerIcon = document.getElementById('pix-timer-icon');

    // Esconder QR, mostrar sucesso
    document.querySelector('#pix-qr-display > .bg-white')?.classList.add('hidden');
    success.classList.remove('hidden');
    timerEl.textContent = '✅ Pagamento confirmado!';
    timerIcon.classList.add('hidden');

    if (payment) {
        const amount = parseFloat(payment.amount || 0);
        document.getElementById('pix-paid-amount').textContent =
            `R$ ${amount.toFixed(2).replace('.', ',')}`;
        if (payment.paidAt) {
            const d = new Date(payment.paidAt);
            document.getElementById('pix-paid-date').textContent =
                d.toLocaleDateString('pt-BR') + ' ' + d.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
        }
    }
}

// ── Reset ────────────────────────────────────────────────
function resetPixPayment() {
    if (pixPollingInterval) {
        clearInterval(pixPollingInterval);
        pixPollingInterval = null;
    }
    pixStartTime = null;

    document.getElementById('payment-method-select').classList.remove('hidden');
    document.getElementById('pix-qr-display').classList.add('hidden');
    document.getElementById('pix-error').classList.add('hidden');
    document.getElementById('pix-success').classList.add('hidden');

    const qrImg = document.getElementById('pix-qr-image');
    qrImg.classList.add('hidden');
    document.getElementById('pix-qr-placeholder').classList.remove('hidden');
    document.getElementById('pix-timer').textContent = 'Aguardando pagamento...';
    document.getElementById('pix-timer-icon').classList.remove('hidden');
}

// ── Check Payment Status on Load (for accepted proposals) ──
async function checkPaymentStatus() {
    try {
        const resp = await fetch(
            `/api/v1/public/proposals/${encodeURIComponent(TOKEN)}/payment`
        );
        const data = await resp.json();

        if (data.isPaid) {
            // Já está pago — esconder botão Pagar, mostrar sucesso
            document.getElementById('payment-method-select').classList.add('hidden');
            const qrDisplay = document.getElementById('pix-qr-display');
            qrDisplay.classList.remove('hidden');
            showPixSuccess(data.payment);
        } else if (data.canPay) {
            // Pode pagar — mostrar botão Pagar
            document.getElementById('payment-method-select').classList.remove('hidden');
        }
    } catch (err) {
        console.warn('[Pix] Status check error:', err);
    }
}

// ── Init ────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', loadProposal);
</script>
