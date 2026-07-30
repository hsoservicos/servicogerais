<div class="min-h-screen bg-gradient-to-b from-primary-50/30 via-white to-white">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-8 sm:py-12">
        <a href="?page=home" class="inline-flex items-center gap-2 text-ink-secondary hover:text-primary transition-colors mb-6 sm:mb-8 group">
            <svg class="w-5 h-5 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span class="text-sm font-medium">Voltar para página inicial</span>
        </a>
        <div class="bg-white rounded-2xl shadow-modal border border-border overflow-hidden">
            <div class="px-6 sm:px-8 pt-6 sm:pt-8 pb-4 sm:pb-6 border-b border-border bg-gradient-to-r from-primary-50/50 to-white">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h1 class="text-h2 text-ink">Solicitar Orçamento</h1>
                        <p class="text-sm text-ink-secondary">Preencha os dados abaixo para receber propostas de profissionais</p>
                    </div>
                </div>
                <div class="flex items-center gap-1 sm:gap-2" id="progress-bar">
                    <?php for ($i = 1; $i <= 3; $i++): ?>
                        <div class="flex items-center gap-1 sm:gap-2 flex-1">
                            <div class="step-dot <?= $i === 1 ? 'active' : '' ?>" data-step="<?= $i ?>"><span class="step-number"><?= $i ?></span></div>
                            <?php if ($i < 3): ?><div class="step-line <?= $i === 1 ? 'active' : '' ?>" data-step="<?= $i ?>"></div><?php endif; ?>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

            <?php require __DIR__ . '/solicitar/step1-service.php'; ?>
            <?php require __DIR__ . '/solicitar/step2-details.php'; ?>
            <?php require __DIR__ . '/solicitar/step3-personal.php'; ?>

            <div class="px-6 sm:px-8 py-4 border-t border-border bg-surface/50 flex items-center justify-between">
                <button id="prev-btn" onclick="prevStep()" class="px-4 py-2 rounded-lg border border-border text-sm font-medium text-ink-secondary hover:bg-white transition-colors hidden">
                    <span class="flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg> Anterior</span>
                </button>
                <div class="flex-1"></div>
                <button id="next-btn" onclick="nextStep()" class="px-6 py-2.5 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-600 transition-all shadow-card flex items-center gap-2">
                    Próximo <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
                <button id="submit-btn" onclick="submitLead()" class="px-6 py-2.5 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-600 transition-all shadow-card items-center gap-2 hidden">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Solicitar Orçamento
                </button>
            </div>
        </div>
    </div>
</div>
