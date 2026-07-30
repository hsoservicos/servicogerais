<div id="worker-modal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-ink/50 backdrop-blur-sm" onclick="closeModal()"></div>
    <div class="fixed inset-0 flex items-start justify-center p-4 pt-[5vh] overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-modal border border-border w-full max-w-lg animate-scale-in relative">
            <div class="px-6 py-5 border-b border-border flex items-center justify-between sticky top-0 bg-white rounded-t-2xl">
                <div>
                    <h2 class="text-h3 text-ink" id="modal-title">Novo Trabalhador</h2>
                    <p class="text-sm text-ink-muted mt-0.5">Cadastre um trabalhador doméstico</p>
                </div>
                <button onclick="closeModal()" class="w-8 h-8 rounded-lg hover:bg-surface transition-colors"><svg class="w-5 h-5 text-ink-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <form id="worker-form" class="p-6 space-y-4">
                <input type="hidden" id="worker-id">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-ink mb-1.5">Nome Completo <span class="text-danger">*</span></label>
                        <input type="text" id="worker-name" required class="w-full px-3.5 py-2.5 rounded-lg border border-border bg-white text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">CPF <span class="text-danger">*</span></label>
                        <input type="text" id="worker-cpf" required maxlength="14" class="w-full px-3.5 py-2.5 rounded-lg border border-border bg-white text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary" placeholder="000.000.000-00">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">RG</label>
                        <input type="text" id="worker-rg" class="w-full px-3.5 py-2.5 rounded-lg border border-border bg-white text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">Categoria <span class="text-danger">*</span></label>
                        <select id="worker-category" class="w-full px-3.5 py-2.5 rounded-lg border border-border bg-white text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            <option value="">Selecione...</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">CBO</label>
                        <input type="text" id="worker-cbo" class="w-full px-3.5 py-2.5 rounded-lg border border-border bg-white text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary" placeholder="Ex: 5121-05">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">Telefone</label>
                        <input type="tel" id="worker-phone" class="w-full px-3.5 py-2.5 rounded-lg border border-border bg-white text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">WhatsApp</label>
                        <input type="tel" id="worker-whatsapp" class="w-full px-3.5 py-2.5 rounded-lg border border-border bg-white text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-ink mb-1.5">Chave PIX</label>
                        <input type="text" id="worker-pix" class="w-full px-3.5 py-2.5 rounded-lg border border-border bg-white text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeModal()" class="px-5 py-2.5 rounded-lg border border-border text-sm font-medium text-ink-secondary hover:bg-surface">Cancelar</button>
                    <button type="submit" class="px-5 py-2.5 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-600 shadow-card">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
