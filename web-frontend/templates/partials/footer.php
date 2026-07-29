    <!-- FOOTER -->
    <footer class="bg-white border-t border-border mt-auto">
        <div class="max-w-7xl mx-auto px-6 py-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-primary font-bold"><?= APP_NAME ?></span>
                    <span class="text-ink-muted text-sm">© <?= date('Y') ?> — Todos os direitos reservados</span>
                </div>
                <div class="flex items-center gap-6 text-sm">
                    <a href="#" class="text-ink-secondary hover:text-primary transition-colors">Termos de Uso</a>
                    <a href="#" class="text-ink-secondary hover:text-primary transition-colors">Privacidade</a>
                    <a href="#" class="text-ink-secondary hover:text-primary transition-colors">LGPD</a>
                    <a href="#" class="text-ink-secondary hover:text-primary transition-colors">Suporte</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 flex flex-col gap-3"></div>

    <script>
        // ── Toast Notification Helper ─────────────────────────
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const colors = {
                success: 'bg-success/10 text-success border-success/20',
                error: 'bg-danger/10 text-danger border-danger/20',
                warning: 'bg-warning/10 text-warning border-warning/20',
                info: 'bg-info/10 text-info border-info/20',
            };
            const icons = {
                success: '✅',
                error: '❌',
                warning: '⚠️',
                info: 'ℹ️',
            };
            const toast = document.createElement('div');
            toast.className = `animate-fade-in px-4 py-3 rounded-lg border ${colors[type] || colors.info} shadow-panel flex items-center gap-3 min-w-[320px] max-w-[480px]`;
            toast.innerHTML = `<span>${icons[type] || 'ℹ️'}</span><span class="text-sm font-medium">${message}</span>`;
            container.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.3s ease-out';
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        // ── Close Toast on Click ─────────────────────────────
        document.getElementById('toast-container')?.addEventListener('click', (e) => {
            if (e.target.closest('.animate-fade-in')) {
                e.target.closest('.animate-fade-in').remove();
            }
        });
    </script>

    <!-- Validation + Mask + CEP (deve vir após showToast) -->
    <script src="/js/validation.js"></script>
</body>
</html>
