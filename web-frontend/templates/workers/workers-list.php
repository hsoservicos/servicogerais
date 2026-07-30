<div class="flex flex-col sm:flex-row gap-3 mb-6">
    <div class="relative flex-1">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-ink-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input type="text" id="search-input" placeholder="Buscar por nome ou CPF..." class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-border bg-white text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
    </div>
    <button onclick="openCreateModal()" class="sm:hidden bg-primary text-white font-medium px-4 py-2.5 rounded-lg hover:bg-primary-600 transition-all text-sm">Novo Trabalhador</button>
</div>

<div id="loading-state" class="text-center py-16 hidden">
    <svg class="w-10 h-10 animate-spin text-primary mx-auto mb-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
    <p class="text-ink-secondary text-sm">Carregando trabalhadores...</p>
</div>

<div id="empty-state" class="text-center py-16">
    <div class="w-16 h-16 bg-surface rounded-2xl flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-ink-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
    </div>
    <h3 class="text-h3 text-ink mb-2">Nenhum trabalhador encontrado</h3>
    <p class="text-ink-secondary mb-6">Cadastre trabalhadores para gerenciar serviços domésticos.</p>
    <button onclick="openCreateModal()" class="bg-primary text-white font-medium px-6 py-2.5 rounded-lg hover:bg-primary-600 transition-all shadow-card text-sm">Cadastrar Trabalhador</button>
</div>

<div id="workers-table" class="hidden overflow-x-auto">
    <table class="w-full">
        <thead><tr class="text-left text-xs font-semibold text-ink-muted uppercase tracking-wider">
            <th class="pb-3 pr-4">Nome</th><th class="pb-3 pr-4">CPF</th><th class="pb-3 pr-4">Categoria</th>
            <th class="pb-3 pr-4">Status</th><th class="pb-3 pr-4">Background</th><th class="pb-3 text-right">Ações</th>
        </tr></thead>
        <tbody id="workers-tbody" class="divide-y divide-border"></tbody>
    </table>
</div>
<div id="pagination" class="hidden flex items-center justify-between mt-6 pt-4 border-t border-border">
    <p class="text-sm text-ink-muted" id="pagination-info"></p>
    <div class="flex gap-2">
        <button id="prev-page" class="px-3 py-1.5 rounded-lg text-sm border border-border hover:bg-surface transition-colors disabled:opacity-40" disabled>Anterior</button>
        <button id="next-page" class="px-3 py-1.5 rounded-lg text-sm border border-border hover:bg-surface transition-colors disabled:opacity-40" disabled>Próximo</button>
    </div>
</div>
