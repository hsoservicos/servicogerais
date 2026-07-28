<!-- ═══════════════════════════════════════════════════════════════
     templates/forgot-password.php — Recuperação de Senha
     ═══════════════════════════════════════════════════════════ -->
<section class="min-h-screen flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md animate-fade-in">
        <!-- Logo / Brand -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-primary/10 rounded-2xl mb-4">
                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </div>
            <h1 class="text-h2 text-ink">Recuperar Senha</h1>
            <p class="text-ink-secondary mt-2">Digite seu e-mail para receber o link de recuperação.</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-xl shadow-card border border-border p-8">
            <form id="forgot-form" class="space-y-5" novalidate>
                <div>
                    <label for="email" class="block text-sm font-medium text-ink mb-1.5">E-mail</label>
                    <input type="email" id="email" name="email" required
                        class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                        placeholder="seu@email.com">
                </div>
                <button type="submit" id="submit-btn"
                    class="w-full bg-primary text-white font-medium py-2.5 px-4 rounded-lg hover:bg-primary-600 active:bg-primary-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary/50 disabled:opacity-50 disabled:cursor-not-allowed">
                    Enviar link de recuperação
                </button>
            </form>

            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-border"></div></div>
                <div class="relative flex justify-center text-sm"><span class="bg-white px-4 text-ink-muted">Lembrou sua senha?</span></div>
            </div>

            <a href="?page=login"
                class="block text-center text-primary hover:text-primary-600 font-medium transition-colors">
                Voltar ao login →
            </a>
        </div>
    </div>
</section>

<!-- Success Modal -->
<div id="success-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-modal w-full max-w-sm animate-fade-in p-6 text-center">
            <div class="w-14 h-14 bg-success/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <h3 class="text-h3 text-ink mb-2">E-mail enviado!</h3>
            <p class="text-ink-secondary text-sm mb-6">
                Se o e-mail existir, você receberá um link de recuperação em instantes.
                <br><br>
                <strong class="text-ink">⚠️ MVP:</strong> O link aparece no console do servidor (docker-compose logs api).
            </p>
            <a href="?page=login"
                class="inline-block bg-primary text-white font-medium px-6 py-2.5 rounded-lg hover:bg-primary-600 transition-all">
                Voltar ao login
            </a>
        </div>
    </div>
</div>

<script>
    document.getElementById('forgot-form').addEventListener('submit', async (e) => {
        e.preventDefault();

        const email = document.getElementById('email').value.trim();
        const btn = document.getElementById('submit-btn');

        if (!email) {
            showToast('Informe seu e-mail', 'error');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="inline-block w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin mr-2"></span> Enviando...';

        try {
            const response = await fetch('<?= API_BASE_URL ?>/auth/forgot-password', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email }),
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Erro ao solicitar recuperação');
            }

            // Mostrar modal de sucesso (mensagem genérica de segurança)
            document.getElementById('success-modal').classList.remove('hidden');

        } catch (err) {
            showToast(err.message, 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = 'Enviar link de recuperação';
        }
    });
</script>
