<!-- Create/Edit Proposal Modal -->
<div id="proposal-modal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-ink/50 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
    <div class="fixed inset-0 flex items-start justify-center p-4 pt-[5vh] overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-modal border border-border w-full max-w-2xl animate-scale-in relative">
            <div class="px-6 py-5 border-b border-border flex items-center justify-between sticky top-0 bg-white rounded-t-2xl z-10">
                <div>
                    <h2 class="text-h3 text-ink" id="modal-title">Nova Proposta</h2>
                    <p class="text-sm text-ink-muted mt-0.5" id="modal-subtitle">Preencha os dados da proposta</p>
                </div>
                <button onclick="closeModal()" class="w-8 h-8 rounded-lg hover:bg-surface transition-colors flex items-center justify-center">
                    <svg class="w-5 h-5 text-ink-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="proposal-form" class="p-6 space-y-5">
                <input type="hidden" id="proposal-id">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-ink mb-1.5">Título <span class="text-danger">*</span></label>
                        <input type="text" id="proposal-title" required class="w-full px-3.5 py-2.5 rounded-lg border border-border bg-white text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" placeholder="Ex: Orçamento para limpeza residencial">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-ink mb-1.5">Cliente <span class="text-danger">*</span></label>
                        <select id="proposal-client" required class="w-full px-3.5 py-2.5 rounded-lg border border-border bg-white text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                            <option value="">Selecione um cliente...</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-ink mb-1.5">Descrição</label>
                        <textarea id="proposal-description" rows="3" class="w-full px-3.5 py-2.5 rounded-lg border border-border bg-white text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all resize-none" placeholder="Descreva o escopo do serviço..."></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">Validade</label>
                        <input type="date" id="proposal-valid-until" class="w-full px-3.5 py-2.5 rounded-lg border border-border bg-white text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">Condições de Pagamento</label>
                        <input type="text" id="proposal-payment-terms" class="w-full px-3.5 py-2.5 rounded-lg border border-border bg-white text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" placeholder="Ex: Pix, 2x cartão">
                    </div>
                </div>

                <!-- Items Section -->
                <div class="border-t border-border pt-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold text-ink">Itens da Proposta</h3>
                        <button type="button" onclick="addItemRow()" class="text-xs text-primary hover:text-primary-600 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Adicionar Item
                        </button>
                    </div>
                    <div id="items-container">
                        <div class="text-center py-6 text-sm text-ink-muted" id="items-empty">Nenhum item adicionado. Clique em "Adicionar Item" acima.</div>
                    </div>
                    <div class="flex justify-end mt-3 pt-3 border-t border-border">
                        <div class="text-right">
                            <span class="text-xs text-ink-muted">Valor Total</span>
                            <p class="text-h2 text-ink" id="proposal-total">R$ 0,00</p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeModal()" class="px-5 py-2.5 rounded-lg border border-border text-sm font-medium text-ink-secondary hover:bg-surface transition-colors">Cancelar</button>
                    <button type="submit" class="px-5 py-2.5 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-600 transition-all shadow-card flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Salvar Proposta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let itemsCount = 0;
function addItemRow(data) {
    const container = document.getElementById('items-container');
    document.getElementById('items-empty')?.remove();
    const idx = itemsCount++;
    const row = document.createElement('div');
    row.className = 'item-row flex gap-2 items-start mb-2';
    row.innerHTML = `
        <input type="text" value="${data?.description || ''}" placeholder="Descrição do item" class="item-desc flex-1 px-3 py-2 rounded-lg border border-border text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
        <input type="number" value="${data?.quantity || 1}" min="1" placeholder="Qtd" class="item-qty w-16 px-2 py-2 rounded-lg border border-border text-sm text-center focus:ring-2 focus:ring-primary/20 focus:border-primary">
        <input type="number" value="${data?.unit_price || ''}" step="0.01" min="0" placeholder="R$" class="item-price w-24 px-2 py-2 rounded-lg border border-border text-sm text-right focus:ring-2 focus:ring-primary/20 focus:border-primary" oninput="recalcTotal()">
        <button type="button" onclick="this.closest('.item-row').remove(); recalcTotal();" class="px-2 py-2 text-danger/60 hover:text-danger transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
    `;
    container.appendChild(row);
    recalcTotal();
}
function recalcTotal() {
    let total = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
        const price = parseFloat(row.querySelector('.item-price').value) || 0;
        total += qty * price;
    });
    document.getElementById('proposal-total').textContent = 'R$ ' + total.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}
function getItems() {
    const items = [];
    document.querySelectorAll('.item-row').forEach(row => {
        items.push({
            description: row.querySelector('.item-desc').value,
            quantity: parseFloat(row.querySelector('.item-qty').value) || 1,
            unit_price: parseFloat(row.querySelector('.item-price').value) || 0,
        });
    });
    return items;
}
</script>
