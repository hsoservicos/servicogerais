<div id="step-2" class="step-content hidden">
    <div class="px-6 sm:px-8 py-6">
        <h2 class="text-h3 text-ink mb-1">Detalhes do Serviço</h2>
        <p class="text-sm text-ink-secondary mb-6">Descreva o que precisa e informe o endereço</p>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-ink mb-1.5">Descrição do Serviço <span class="text-danger">*</span></label>
                <textarea id="step2-description" rows="4" class="w-full px-3.5 py-2.5 rounded-lg border border-border bg-white text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all resize-none" placeholder="Descreva detalhadamente o serviço que precisa..."></textarea>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">Data Preferencial</label>
                    <input type="date" id="step2-date" class="w-full px-3.5 py-2.5 rounded-lg border border-border bg-white text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">Horário Preferencial</label>
                    <input type="time" id="step2-time" class="w-full px-3.5 py-2.5 rounded-lg border border-border bg-white text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-ink mb-1.5">CEP</label>
                <input type="text" id="step2-cep" maxlength="9" class="w-full sm:w-64 px-3.5 py-2.5 rounded-lg border border-border bg-white text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" placeholder="00000-000">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-ink mb-1.5">Endereço</label>
                    <input type="text" id="step2-address" class="w-full px-3.5 py-2.5 rounded-lg border border-border bg-white text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" placeholder="Rua, número">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">Estado</label>
                    <input type="text" id="step2-state" maxlength="2" class="w-full px-3.5 py-2.5 rounded-lg border border-border bg-white text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" placeholder="SP">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">Cidade</label>
                    <input type="text" id="step2-city" class="w-full px-3.5 py-2.5 rounded-lg border border-border bg-white text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" placeholder="São Paulo">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">Foto (opcional)</label>
                    <input type="file" id="step2-photo" accept="image/*" class="w-full text-sm text-ink-secondary file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary/10 file:text-primary hover:file:bg-primary/20 transition-all">
                </div>
            </div>
        </div>
    </div>
</div>
