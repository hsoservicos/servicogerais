<?php
$publicToken = $_GET['token'] ?? '';
if (empty($publicToken)) {
    echo '<div class="min-h-screen bg-surface flex items-center justify-center p-6">';
    echo '<div class="text-center max-w-md">';
    echo '<div class="w-20 h-20 bg-danger/10 rounded-full flex items-center justify-center mx-auto mb-6">';
    echo '<svg class="w-10 h-10 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>';
    echo '</div>';
    echo '<h1 class="text-h1 text-ink mb-3">Link Inválido</h1>';
    echo '<p class="text-ink-secondary mb-6">O link que você acessou não é válido. Verifique o link enviado pelo profissional.</p>';
    echo '</div></div>';
    return;
}
?>
<div class="min-h-screen bg-gradient-to-b from-primary-50/20 via-white to-white">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-8 sm:py-12">
        <div id="loading-state" class="text-center py-20">
            <svg class="w-10 h-10 animate-spin text-primary mx-auto mb-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            <p class="text-ink-secondary">Carregando proposta...</p>
        </div>
        <div id="error-state" class="hidden text-center py-20">
            <div class="w-20 h-20 bg-danger/10 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            </div>
            <h1 class="text-h1 text-ink mb-3">Proposta não encontrada</h1>
            <p class="text-ink-secondary mb-6">O link que você acessou pode estar expirado ou inválido.</p>
            <p class="text-sm text-ink-muted">Entre em contato com o profissional que enviou esta proposta para mais informações.</p>
        </div>
        <div id="proposal-content" class="hidden">
            <?php require __DIR__ . '/public-proposal/header.php'; ?>
            <?php require __DIR__ . '/public-proposal/items.php'; ?>
            <?php require __DIR__ . '/public-proposal/actions.php'; ?>
            <?php require __DIR__ . '/public-proposal/payment.php'; ?>
        </div>
    </div>
</div>
