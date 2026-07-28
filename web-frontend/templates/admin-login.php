<!-- ═══════════════════════════════════════════════════════════════
     templates/admin-login.php — Admin Login (Epic 7 — Story 7.1)
     ═══════════════════════════════════════════════════════════ -->
<div class="min-h-screen bg-sidebar flex">
    <!-- Left Panel: Branding -->
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-sidebar via-primary-900 to-primary-800 items-center justify-center p-12">
        <div class="max-w-md text-center">
            <div class="w-20 h-20 bg-white/10 rounded-2xl flex items-center justify-center mx-auto mb-8 backdrop-blur-sm">
                <svg class="w-10 h-10 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <h1 class="text-white text-display mb-4">Painel Administrativo</h1>
            <p class="text-primary-200 text-lg">Gerencie todos os tenants, transações e configurações da plataforma ServiceSaaS em um só lugar.</p>
            <div class="mt-12 flex items-center justify-center gap-8 text-primary-300 text-sm">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Gestão de Tenants</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Financeiro</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Auditoria</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Panel: Login Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-white">
        <div class="w-full max-w-sm animate-fade-in">
            <!-- Logo Mobile -->
            <div class="lg:hidden flex items-center gap-3 mb-8">
                <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <span class="font-bold text-lg text-ink"><?= APP_NAME ?></span>
                    <p class="text-xs text-ink-muted">Admin</p>
                </div>
            </div>

            <h2 class="text-h1 text-ink mb-2">Acesso Administrativo</h2>
            <p class="text-ink-secondary text-sm mb-8">Entre com suas credenciais de administrador da plataforma.</p>

            <!-- Error Message -->
            <div id="login-error" class="hidden bg-danger/10 border border-danger/20 text-danger text-sm rounded-lg p-3 mb-6">
            </div>

            <form id="admin-login-form" class="space-y-5">
                <div>
                    <label for="email" class="block text-sm font-medium text-ink mb-1.5">E-mail</label>
                    <input type="email" id="email" name="email" required autofocus
                        class="w-full px-4 py-2.5 border border-border rounded-lg text-ink placeholder-ink-muted
                               focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent
                               transition-all duration-200"
                        placeholder="admin@servicesaas.com" autocomplete="email">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-ink mb-1.5">Senha</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required
                            class="w-full px-4 py-2.5 pr-11 border border-border rounded-lg text-ink placeholder-ink-muted
                                   focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent
                                   transition-all duration-200"
                            placeholder="••••••••" autocomplete="current-password">
                        <button type="button" id="toggle-password"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-ink-muted hover:text-ink transition-colors duration-200 focus:outline-none"
                            tabindex="-1" aria-label="Mostrar senha">
                            <!-- Eye icon (visible) -->
                            <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <!-- Eye-off icon (hidden) -->
                            <svg id="eye-off-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <button type="submit"
                    class="w-full bg-primary hover:bg-primary-600 text-white font-medium py-2.5 px-4 rounded-lg
                           transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2
                           disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                    <span id="btn-text">Entrar</span>
                    <svg id="btn-spinner" class="hidden w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </button>
            </form>

            <p class="text-center mt-6 text-sm text-ink-muted">
                <a href="?page=home" class="text-primary hover:text-primary-600 transition-colors">← Voltar ao site</a>
            </p>
        </div>
    </div>
</div>

<script>
document.getElementById('admin-login-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const errorDiv = document.getElementById('login-error');
    const btn = this.querySelector('button[type="submit"]');
    const btnText = document.getElementById('btn-text');
    const spinner = document.getElementById('btn-spinner');

    errorDiv.classList.add('hidden');
    btn.disabled = true;
    btnText.textContent = 'Entrando...';
    spinner.classList.remove('hidden');

    try {
        const response = await fetch('/api/v1/auth/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
            }),
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Credenciais inválidas');
        }

        // Verificar se é super_admin
        if (data.user?.role !== 'super_admin') {
            throw new Error('Acesso restrito a administradores da plataforma');
        }

        // Armazenar token na sessão
        const storeResponse = await fetch('?action=store-token', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                token: data.token,
                user: {
                    ...data.user,
                    tenantId: data.user.tenantId || data.tenant?.id,
                }
            }),
        });

        if (!storeResponse.ok) {
            throw new Error('Erro ao iniciar sessão');
        }

                // Redirecionar para admin dashboard
        window.location.href = '?page=admin-dashboard';

    } catch (err) {
        errorDiv.textContent = err.message;
        errorDiv.classList.remove('hidden');
        btn.disabled = false;
        btnText.textContent = 'Entrar';
        spinner.classList.add('hidden');
    }
});

// ── Password Visibility Toggle ────────────────────────────
document.getElementById('toggle-password').addEventListener('click', function() {
    const password = document.getElementById('password');
    const eyeIcon = document.getElementById('eye-icon');
    const eyeOffIcon = document.getElementById('eye-off-icon');

    if (password.type === 'password') {
        password.type = 'text';
        eyeIcon.classList.add('hidden');
        eyeOffIcon.classList.remove('hidden');
        this.setAttribute('aria-label', 'Esconder senha');
    } else {
        password.type = 'password';
        eyeIcon.classList.remove('hidden');
        eyeOffIcon.classList.add('hidden');
        this.setAttribute('aria-label', 'Mostrar senha');
    }
});

</script>
