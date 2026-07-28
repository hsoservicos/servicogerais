<!-- ═══════════════════════════════════════════════════════════════
     templates/reset-password.php — Redefinir Senha
     ═══════════════════════════════════════════════════════════ -->
<section class="min-h-screen flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md animate-fade-in">
        <!-- Logo / Brand -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-primary/10 rounded-2xl mb-4">
                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h1 class="text-h2 text-ink">Redefinir Senha</h1>
            <p class="text-ink-secondary mt-2">Escolha uma nova senha para sua conta.</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-xl shadow-card border border-border p-8">
            <form id="reset-form" class="space-y-5" novalidate>
                <input type="hidden" id="token" value="<?= htmlspecialchars($_GET['token'] ?? '') ?>">

                <div>
                    <label for="password" class="block text-sm font-medium text-ink mb-1.5">
                        Nova Senha <span class="text-danger">*</span>
                    </label>
                    <input type="password" id="password" name="password" required minlength="8"
                        class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                        placeholder="Mínimo 8 caracteres">
                    <span class="error-message text-xs text-danger mt-1 hidden">Senha deve ter no mínimo 8 caracteres</span>
                </div>

                <div>
                    <label for="confirmPassword" class="block text-sm font-medium text-ink mb-1.5">
                        Confirmar Senha <span class="text-danger">*</span>
                    </label>
                    <input type="password" id="confirmPassword" name="confirmPassword" required
                        class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                        placeholder="Repita a senha">
                    <span class="error-message text-xs text-danger mt-1 hidden">Senhas não conferem</span>
                </div>

                <button type="submit" id="submit-btn"
                    class="w-full bg-primary text-white font-medium py-2.5 px-4 rounded-lg hover:bg-primary-600 active:bg-primary-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary/50 disabled:opacity-50 disabled:cursor-not-allowed">
                    Redefinir Senha
                </button>
            </form>
        </div>
    </div>
</section>

<!-- Success Modal -->
<div id="success-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-modal w-full max-w-sm animate-fade-in p-6 text-center">
            <div class="w-14 h-14 bg-success/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <h3 class="text-h3 text-ink mb-2">Senha redefinida!</h3>
            <p class="text-ink-secondary text-sm mb-6">Sua senha foi atualizada com sucesso.</p>
            <a href="?page=login"
                class="inline-block bg-primary text-white font-medium px-6 py-2.5 rounded-lg hover:bg-primary-600 transition-all">
                Fazer login
            </a>
        </div>
    </div>
</div>

<script>
    document.getElementById('reset-form').addEventListener('submit', async (e) => {
        e.preventDefault();

        const token = document.getElementById('token').value;
        const password = document.getElementById('password').value;
        const confirm = document.getElementById('confirmPassword').value;
        const btn = document.getElementById('submit-btn');

        // Validação frontend
        if (!token) {
            showToast('Link inválido ou expirado. Solicite um novo.', 'error');
            return;
        }

        if (password.length < 8) {
            document.getElementById('password').classList.add('border-danger');
            showToast('Senha deve ter no mínimo 8 caracteres', 'error');
            return;
        }

        if (password !== confirm) {
            document.getElementById('confirmPassword').classList.add('border-danger');
            showToast('Senhas não conferem', 'error');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="inline-block w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin mr-2"></span> Redefinindo...';

        try {
            const response = await fetch('<?= API_BASE_URL ?>/auth/reset-password', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ token, password }),
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Erro ao redefinir senha');
            }

            document.getElementById('success-modal').classList.remove('hidden');

        } catch (err) {
            showToast(err.message, 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = 'Redefinir Senha';
        }
    });
</script>
