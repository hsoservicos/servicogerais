<!-- ═══════════════════════════════════════════════════════════════
     templates/login.php — Login Page
     ═══════════════════════════════════════════════════════════ -->
<section class="min-h-screen flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md animate-fade-in">
        <!-- Logo / Brand -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-primary/10 rounded-2xl mb-4">
                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <h1 class="text-h2 text-ink">Bem-vindo de volta</h1>
            <p class="text-ink-secondary mt-2">Acesse sua conta para continuar.</p>
        </div>

        <!-- Login Form -->
        <div class="bg-white rounded-xl shadow-card border border-border p-8">
            <form id="login-form" class="space-y-5" novalidate>
                <div>
                    <label for="email" class="block text-sm font-medium text-ink mb-1.5">E-mail</label>
                    <input type="email" id="email" name="email" required
                        class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                        placeholder="seu@email.com">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-ink mb-1.5">Senha</label>
                    <input type="password" id="password" name="password" required
                        class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                        placeholder="Sua senha">
                </div>
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" class="w-4 h-4 rounded border-border text-primary focus:ring-primary/30">
                        <span class="text-ink-secondary">Lembrar-me</span>
                    </label>
                    <a href="?page=forgot-password" class="text-primary hover:text-primary-600 transition-colors">Esqueceu a senha?</a>
                </div>
                <button type="submit" id="login-btn"
                    class="w-full bg-primary text-white font-medium py-2.5 px-4 rounded-lg hover:bg-primary-600 active:bg-primary-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary/50 disabled:opacity-50 disabled:cursor-not-allowed">
                    Entrar
                </button>
            </form>

            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-border"></div></div>
                <div class="relative flex justify-center text-sm"><span class="bg-white px-4 text-ink-muted">Ainda não tem conta?</span></div>
            </div>

            <a href="?page=register"
                class="block text-center text-primary hover:text-primary-600 font-medium transition-colors">
                Criar conta gratuita →
            </a>
        </div>
    </div>
</section>

<script>
    document.getElementById('login-form').addEventListener('submit', async (e) => {
        e.preventDefault();

        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        const btn = document.getElementById('login-btn');

        if (!email || !password) {
            showToast('Preencha todos os campos', 'error');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="inline-block w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin mr-2"></span> Entrando...';

        try {
            const response = await fetch('<?= API_BASE_URL ?>/auth/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, password }),
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Erro ao fazer login');
            }

            // Store token via fetch to server
            await fetch('?page=login&action=store-token', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ token: data.token, user: data.user }),
            });

            showToast('✅ ' + data.message, 'success');
            setTimeout(() => { window.location.href = '?page=dashboard'; }, 1000);

        } catch (err) {
            showToast(err.message, 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = 'Entrar';
        }
    });
</script>
