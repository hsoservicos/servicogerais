<div id="step-3" class="step-content hidden">
    <div class="px-6 sm:px-8 py-6">
        <h2 class="text-h3 text-ink mb-1">Dados Pessoais</h2>
        <p class="text-sm text-ink-secondary mb-6">Informe seus dados para receber as propostas</p>
        <div class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">Nome Completo <span class="text-danger">*</span></label>
                    <input type="text" id="step3-name" required class="w-full px-3.5 py-2.5 rounded-lg border border-border bg-white text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" placeholder="Seu nome">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">Telefone <span class="text-danger">*</span></label>
                    <input type="tel" id="step3-phone" required class="w-full px-3.5 py-2.5 rounded-lg border border-border bg-white text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" placeholder="(11) 99999-9999">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-ink mb-1.5">E-mail (opcional)</label>
                <input type="email" id="step3-email" class="w-full px-3.5 py-2.5 rounded-lg border border-border bg-white text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" placeholder="seu@email.com">
            </div>
            <div class="bg-surface rounded-xl p-4 space-y-3">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" id="step3-consent-1" class="mt-1 rounded border-border text-primary focus:ring-primary/20">
                    <span class="text-sm text-ink-secondary">Aceito ser contactado por profissionais sobre este orçamento</span>
                </label>
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" id="step3-consent-2" class="mt-1 rounded border-border text-primary focus:ring-primary/20">
                    <span class="text-sm text-ink-secondary">Aceito receber comunicações de marketing</span>
                </label>
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" id="step3-consent-3" class="mt-1 rounded border-border text-primary focus:ring-primary/20">
                    <span class="text-sm text-ink-secondary">Li e aceito os <a href="?page=privacy" class="text-primary hover:underline" target="_blank">Termos de Uso e Política de Privacidade</a></span>
                </label>
            </div>
        </div>
    </div>
</div>
