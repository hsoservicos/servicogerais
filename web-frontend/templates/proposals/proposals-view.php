<!-- View Proposal Modal -->
<div id="view-modal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-ink/50 backdrop-blur-sm" onclick="closeViewModal()"></div>
    <div class="fixed inset-0 flex items-start justify-center p-4 pt-[5vh] overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-modal border border-border w-full max-w-2xl animate-scale-in relative">
            <div class="px-6 py-5 border-b border-border flex items-center justify-between sticky top-0 bg-white rounded-t-2xl z-10">
                <div>
                    <h2 class="text-h3 text-ink" id="view-title">Detalhes da Proposta</h2>
                    <p class="text-sm text-ink-muted mt-0.5" id="view-number"></p>
                </div>
                <button onclick="closeViewModal()" class="w-8 h-8 rounded-lg hover:bg-surface transition-colors flex items-center justify-center">
                    <svg class="w-5 h-5 text-ink-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" id="view-info"></div>
                <div class="border-t border-border pt-4">
                    <h3 class="text-sm font-semibold text-ink mb-3">Itens</h3>
                    <div id="view-items" class="overflow-x-auto"></div>
                </div>
                <div class="flex justify-end pt-2 gap-3" id="view-actions"></div>
            </div>
        </div>
    </div>
</div>
