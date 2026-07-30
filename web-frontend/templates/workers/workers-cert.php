<div id="cert-modal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-ink/50 backdrop-blur-sm" onclick="closeCertModal()"></div>
    <div class="fixed inset-0 flex items-start justify-center p-4 pt-[5vh] overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-modal border border-border w-full max-w-md animate-scale-in relative">
            <div class="px-6 py-5 border-b border-border flex items-center justify-between sticky top-0 bg-white rounded-t-2xl">
                <div><h2 class="text-h3 text-ink">Certificações</h2><p class="text-sm text-ink-muted mt-0.5" id="cert-worker-name"></p></div>
                <button onclick="closeCertModal()" class="w-8 h-8 rounded-lg hover:bg-surface transition-colors"><svg class="w-5 h-5 text-ink-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <div class="p-6" id="cert-list"></div>
        </div>
    </div>
</div>
