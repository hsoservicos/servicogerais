<?php $currentStatus = $_GET['status'] ?? ''; ?>
<!-- Status Filter Tabs -->
<div class="flex flex-wrap gap-2 mb-6">
    <?php
    $statusTabs = [
        ''          => ['label' => 'Todas', 'color' => ''],
        'draft'     => ['label' => 'Rascunho', 'color' => 'bg-gray-100 text-gray-700'],
        'sent'      => ['label' => 'Enviadas', 'color' => 'bg-blue-100 text-blue-700'],
        'viewed'    => ['label' => 'Visualizadas', 'color' => 'bg-purple-100 text-purple-700'],
        'accepted'  => ['label' => 'Aceitas', 'color' => 'bg-green-100 text-green-700'],
        'rejected'  => ['label' => 'Rejeitadas', 'color' => 'bg-red-100 text-red-700'],
        'cancelled' => ['label' => 'Canceladas', 'color' => 'bg-gray-100 text-gray-500'],
    ];
    ?>
    <?php foreach ($statusTabs as $key => $tab): ?>
        <a href="?page=proposals<?= $key ? '&status='.$key : '' ?>"
            class="px-4 py-1.5 rounded-full text-xs font-medium transition-all duration-200
            <?= ($currentStatus === $key) ? 'bg-primary text-white shadow-sm' : ($tab['color'] ? $tab['color'].' hover:opacity-80' : 'bg-surface border border-border text-ink-secondary hover:bg-border/30') ?>">
            <?= $tab['label'] ?>
        </a>
    <?php endforeach; ?>
</div>

<!-- Search + Filters -->
<div class="flex flex-col sm:flex-row gap-3 mb-6">
    <div class="relative flex-1">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-ink-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input type="text" id="search-input" placeholder="Buscar por título, número ou cliente..." class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-border bg-white text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
    </div>
    <button onclick="openCreateModal()" class="sm:hidden bg-primary text-white font-medium px-4 py-2.5 rounded-lg hover:bg-primary-600 transition-all text-sm flex items-center justify-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Nova
    </button>
</div>

<!-- Loading State -->
<div id="loading-state" class="text-center py-16 hidden">
    <svg class="w-10 h-10 animate-spin text-primary mx-auto mb-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
    <p class="text-ink-secondary text-sm">Carregando propostas...</p>
</div>

<!-- Empty State -->
<div id="empty-state" class="text-center py-16">
    <div class="w-16 h-16 bg-surface rounded-2xl flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-ink-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
    </div>
    <h3 class="text-h3 text-ink mb-2">Nenhuma proposta encontrada</h3>
    <p class="text-ink-secondary mb-6">Crie sua primeira proposta para começar.</p>
    <button onclick="openCreateModal()" class="bg-primary text-white font-medium px-6 py-2.5 rounded-lg hover:bg-primary-600 transition-all shadow-card text-sm inline-flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Criar Proposta
    </button>
</div>

<!-- Proposals Table -->
<div id="proposals-table" class="hidden overflow-x-auto">
    <table class="w-full">
        <thead>
            <tr class="text-left text-xs font-semibold text-ink-muted uppercase tracking-wider">
                <th class="pb-3 pr-4">Proposta</th>
                <th class="pb-3 pr-4">Cliente</th>
                <th class="pb-3 pr-4">Valor</th>
                <th class="pb-3 pr-4">Status</th>
                <th class="pb-3 pr-4">Data</th>
                <th class="pb-3 text-right">Ações</th>
            </tr>
        </thead>
        <tbody id="proposals-tbody" class="divide-y divide-border"></tbody>
    </table>
</div>

<!-- Pagination -->
<div id="pagination" class="hidden flex items-center justify-between mt-6 pt-4 border-t border-border">
    <p class="text-sm text-ink-muted" id="pagination-info"></p>
    <div class="flex gap-2">
        <button id="prev-page" class="px-3 py-1.5 rounded-lg text-sm border border-border hover:bg-surface transition-colors disabled:opacity-40" disabled>Anterior</button>
        <button id="next-page" class="px-3 py-1.5 rounded-lg text-sm border border-border hover:bg-surface transition-colors disabled:opacity-40" disabled>Próximo</button>
    </div>
</div>
