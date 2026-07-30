<div id="prop-actions" class="hidden bg-white rounded-2xl shadow-modal border border-border overflow-hidden mb-6">
    <div class="px-6 sm:px-8 py-6 text-center">
        <div class="w-16 h-16 bg-success/10 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h2 class="text-h2 text-ink mb-2">Aprovar Proposta?</h2>
        <p class="text-ink-secondary mb-6">Ao aprovar, você concorda com os termos e valores apresentados.</p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <button onclick="respondProposal('approve')" class="px-8 py-3 rounded-xl bg-success text-white font-medium hover:bg-success-600 transition-all shadow-card flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Aprovar Proposta
            </button>
            <button onclick="respondProposal('reject')" class="px-8 py-3 rounded-xl border-2 border-danger/20 text-danger font-medium hover:bg-danger/5 transition-all flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg> Recusar Proposta
            </button>
        </div>
    </div>
</div>
