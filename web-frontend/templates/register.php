<!-- ═══════════════════════════════════════════════════════════════
     templates/register.php — Cadastro de Prestador (Multi-Tenant)
     ═══════════════════════════════════════════════════════════ -->
<section class="min-h-screen flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-2xl animate-fade-in">
        <!-- Logo / Brand -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-primary/10 rounded-2xl mb-4">
                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <h1 class="text-h2 text-ink">Cadastre sua Empresa</h1>
            <p class="text-ink-secondary mt-2">Comece a gerenciar seus serviços e propostas em minutos.</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-xl shadow-card border border-border p-8">
            <form id="register-form" class="space-y-6" novalidate>
                <!-- Step Indicator -->
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-2">
                        <span class="step-indicator w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-sm font-semibold">1</span>
                        <span class="text-sm font-medium text-ink">Dados da Empresa</span>
                    </div>
                    <div class="flex-1 h-px bg-border mx-4"></div>
                    <div class="flex items-center gap-2">
                        <span class="step-indicator w-8 h-8 rounded-full bg-ink-muted/20 text-ink-muted flex items-center justify-center text-sm font-semibold">2</span>
                        <span class="text-sm font-medium text-ink-muted">Acesso</span>
                    </div>
                </div>

                <!-- Step 1: Dados da Empresa -->
                <div id="step-1" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Nome da Empresa -->
                        <div class="md:col-span-2">
                            <label for="companyName" class="block text-sm font-medium text-ink mb-1.5">
                                Nome da Empresa <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="companyName" name="companyName" required
                                class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                                placeholder="Ex: Maria Beleza Estética">
                            <span class="error-message text-xs text-danger mt-1 hidden">Nome é obrigatório</span>
                        </div>

                        <!-- CPF -->
                        <div>
                            <label for="documentCpf" class="block text-sm font-medium text-ink mb-1.5">CPF</label>
                            <input type="text" id="documentCpf" name="documentCpf"
                                class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                                placeholder="000.000.000-00" maxlength="14">
                        </div>

                        <!-- CNPJ -->
                        <div>
                            <label for="documentCnpj" class="block text-sm font-medium text-ink mb-1.5">CNPJ</label>
                            <input type="text" id="documentCnpj" name="documentCnpj"
                                class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                                placeholder="00.000.000/0001-00" maxlength="18">
                        </div>
                    </div>

                    <!-- Telefone -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="phone" class="block text-sm font-medium text-ink mb-1.5">Telefone</label>
                            <input type="tel" id="phone" name="phone"
                                class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                                placeholder="(11) 99999-9999">
                        </div>
                        <div>
                            <label for="whatsapp" class="block text-sm font-medium text-ink mb-1.5">WhatsApp</label>
                            <input type="tel" id="whatsapp" name="whatsapp"
                                class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                                placeholder="(11) 99999-9999">
                        </div>
                    </div>

                    <!-- Endereço -->
                    <div class="border-t border-border pt-4 mt-2">
                        <p class="text-sm font-medium text-ink mb-3">📍 Endereço do Prestador</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="zipcode" class="block text-sm font-medium text-ink mb-1.5">CEP</label>
                                <input type="text" id="zipcode" name="zipcode"
                                    class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                                    placeholder="00000-000" maxlength="9">
                            </div>
                            <div>
                                <label for="neighborhood" class="block text-sm font-medium text-ink mb-1.5">Bairro</label>
                                <input type="text" id="neighborhood" name="neighborhood"
                                    class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                                    placeholder="Centro">
                            </div>
                        </div>
                        <div class="mt-3">
                            <label for="address" class="block text-sm font-medium text-ink mb-1.5">Endereço</label>
                            <input type="text" id="address" name="address"
                                class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                                placeholder="Rua, número, complemento">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                            <div>
                                <label for="city" class="block text-sm font-medium text-ink mb-1.5">Cidade <span class="text-danger">*</span></label>
                                <input type="text" id="city" name="city" required
                                    class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                                    placeholder="São Paulo">
                            </div>
                            <div>
                                <label for="state" class="block text-sm font-medium text-ink mb-1.5">Estado <span class="text-danger">*</span></label>
                                <select id="state" name="state" required
                                    class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                                    <option value="">Selecione...</option>
                                    <option value="AC">AC</option><option value="AL">AL</option><option value="AP">AP</option>
                                    <option value="AM">AM</option><option value="BA">BA</option><option value="CE">CE</option>
                                    <option value="DF">DF</option><option value="ES">ES</option><option value="GO">GO</option>
                                    <option value="MA">MA</option><option value="MT">MT</option><option value="MS">MS</option>
                                    <option value="MG">MG</option><option value="PA">PA</option><option value="PB">PB</option>
                                    <option value="PR">PR</option><option value="PE">PE</option><option value="PI">PI</option>
                                    <option value="RJ">RJ</option><option value="RN">RN</option><option value="RS">RS</option>
                                    <option value="RO">RO</option><option value="RR">RR</option><option value="SC">SC</option>
                                    <option value="SP">SP</option><option value="SE">SE</option><option value="TO">TO</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <button type="button" onclick="nextStep()"
                        class="w-full bg-primary text-white font-medium py-2.5 px-4 rounded-lg hover:bg-primary-600 active:bg-primary-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary/50">
                        Continuar
                    </button>
                </div>

                <!-- Step 2: Dados de Acesso -->
                <div id="step-2" class="space-y-4 hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- E-mail -->
                        <div class="md:col-span-2">
                            <label for="email" class="block text-sm font-medium text-ink mb-1.5">
                                E-mail <span class="text-danger">*</span>
                            </label>
                            <input type="email" id="email" name="email" required
                                class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                                placeholder="maria@exemplo.com">
                            <span class="error-message text-xs text-danger mt-1 hidden">E-mail é obrigatório</span>
                        </div>

                        <!-- Senha -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-ink mb-1.5">
                                Senha <span class="text-danger">*</span>
                            </label>
                            <input type="password" id="password" name="password" required minlength="8"
                                class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                                placeholder="Mínimo 8 caracteres">
                            <span class="error-message text-xs text-danger mt-1 hidden">Senha deve ter no mínimo 8 caracteres</span>
                        </div>

                        <!-- Confirmar Senha -->
                        <div>
                            <label for="confirmPassword" class="block text-sm font-medium text-ink mb-1.5">
                                Confirmar Senha <span class="text-danger">*</span>
                            </label>
                            <input type="password" id="confirmPassword" name="confirmPassword" required
                                class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                                placeholder="Repita a senha">
                            <span class="error-message text-xs text-danger mt-1 hidden">Senhas não conferem</span>
                        </div>
                    </div>

                    <!-- LGPD Consentimento -->
                    <div class="bg-primary-50 rounded-lg p-4 space-y-3 border border-primary/10">
                        <p class="text-sm font-medium text-primary-900">📋 LGPD — Consentimento</p>

                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" id="lgpd-opt-in" required
                                class="mt-0.5 w-4 h-4 rounded border-border text-primary focus:ring-primary/30">
                            <span class="text-sm text-ink-secondary">
                                <strong class="text-ink">Opt-in explícito:</strong> Autorizo o tratamento dos meus dados pessoais para criação da conta e prestação dos serviços contratados. (Art. 7°, V - LGPD)
                            </span>
                        </label>

                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" id="lgpd-communications"
                                class="mt-0.5 w-4 h-4 rounded border-border text-primary focus:ring-primary/30">
                            <span class="text-sm text-ink-secondary">
                                <strong class="text-ink">Comunicações:</strong> Autorizo o envio de comunicações sobre novos serviços e ofertas por e-mail e WhatsApp. (Art. 7°, I - LGPD)
                            </span>
                        </label>

                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" id="lgpd-terms" required
                                class="mt-0.5 w-4 h-4 rounded border-border text-primary focus:ring-primary/30">
                            <span class="text-sm text-ink-secondary">
                                <strong class="text-ink">Aceito os Termos:</strong> Declaro que li e concordo com os
                                <a href="#" class="text-primary hover:text-primary-600 underline">Termos de Uso</a> e a
                                <a href="#" class="text-primary hover:text-primary-600 underline">Política de Privacidade</a>.
                            </span>
                        </label>
                    </div>

                    <div class="flex gap-3">
                        <button type="button" onclick="prevStep()"
                            class="w-1/3 border-2 border-border text-ink font-medium py-2.5 px-4 rounded-lg hover:bg-border/30 transition-all">
                            Voltar
                        </button>
                        <button type="submit"
                            class="w-2/3 bg-primary text-white font-medium py-2.5 px-4 rounded-lg hover:bg-primary-600 active:bg-primary-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary/50 disabled:opacity-50 disabled:cursor-not-allowed"
                            id="submit-btn">
                            Criar Conta
                        </button>
                    </div>
                </div>

                <!-- Loading Overlay -->
                <div id="loading-overlay" class="hidden fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50">
                    <div class="bg-white rounded-xl shadow-modal p-8 flex flex-col items-center gap-4">
                        <div class="w-12 h-12 border-4 border-primary/30 border-t-primary rounded-full animate-spin"></div>
                        <p class="text-ink-secondary text-sm">Criando sua conta...</p>
                    </div>
                </div>
            </form>

            <!-- Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-border"></div></div>
                <div class="relative flex justify-center text-sm"><span class="bg-white px-4 text-ink-muted">Já tem conta?</span></div>
            </div>

            <a href="?page=login"
                class="block text-center text-primary hover:text-primary-600 font-medium transition-colors">
                Fazer login →
            </a>
        </div>
    </div>
</section>

<script>
    // ── Step Navigation ─────────────────────────────────────
    let currentStep = 1;

    function nextStep() {
        if (currentStep === 1) {
            const companyName = document.getElementById('companyName').value.trim();
            const error = document.querySelector('#step-1 .error-message');
            if (!companyName) {
                document.getElementById('companyName').classList.add('border-danger');
                error.classList.remove('hidden');
                return;
            }
            error.classList.add('hidden');
            document.getElementById('companyName').classList.remove('border-danger');
            document.getElementById('step-1').classList.add('hidden');
            document.getElementById('step-2').classList.remove('hidden');
            document.querySelectorAll('.step-indicator')[0].classList.remove('bg-primary');
            document.querySelectorAll('.step-indicator')[0].classList.add('bg-primary-700');
            document.querySelectorAll('.step-indicator')[1].classList.remove('bg-ink-muted/20', 'text-ink-muted');
            document.querySelectorAll('.step-indicator')[1].classList.add('bg-primary', 'text-white');
            currentStep = 2;
        }
    }

    function prevStep() {
        if (currentStep === 2) {
            document.getElementById('step-2').classList.add('hidden');
            document.getElementById('step-1').classList.remove('hidden');
            document.querySelectorAll('.step-indicator')[1].classList.remove('bg-primary', 'text-white');
            document.querySelectorAll('.step-indicator')[1].classList.add('bg-ink-muted/20', 'text-ink-muted');
            document.querySelectorAll('.step-indicator')[0].classList.remove('bg-primary-700');
            document.querySelectorAll('.step-indicator')[0].classList.add('bg-primary', 'text-white');
            currentStep = 1;
        }
    }

    // ── Input Cleanup on Focus ──────────────────────────────
    document.querySelectorAll('input').forEach(input => {
        input.addEventListener('focus', () => {
            input.classList.remove('border-danger');
            const error = input.parentElement.querySelector('.error-message');
            if (error) error.classList.add('hidden');
        });
    });

    // ── Form Submit ─────────────────────────────────────────
    document.getElementById('register-form').addEventListener('submit', async (e) => {
        e.preventDefault();

        // Validação inline
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        const confirm = document.getElementById('confirmPassword').value;
        const lgpdOptIn = document.getElementById('lgpd-opt-in').checked;
        const lgpdTerms = document.getElementById('lgpd-terms').checked;

        let hasError = false;

        if (!email) { showFieldError('email', 'E-mail é obrigatório'); hasError = true; }
        if (password.length < 8) { showFieldError('password', 'Senha deve ter no mínimo 8 caracteres'); hasError = true; }
        if (password !== confirm) { showFieldError('confirmPassword', 'Senhas não conferem'); hasError = true; }
        if (!lgpdOptIn) { showToast('Você precisa autorizar o tratamento de dados (LGPD)', 'warning'); hasError = true; }
        if (!lgpdTerms) { showToast('Você precisa aceitar os Termos de Uso', 'warning'); hasError = true; }

        if (hasError) return;

        // Enviar requisição
        const btn = document.getElementById('submit-btn');
        btn.disabled = true;
        btn.innerHTML = '<span class="inline-block w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin mr-2"></span> Criando...';
        document.getElementById('loading-overlay').classList.remove('hidden');

        try {
            const companyName = document.getElementById('companyName').value.trim();
            const documentCpf = document.getElementById('documentCpf').value.trim() || null;
            const documentCnpj = document.getElementById('documentCnpj').value.trim() || null;
            const phone = document.getElementById('phone').value.trim() || null;
            const whatsapp = document.getElementById('whatsapp').value.trim() || null;
            const zipcode = document.getElementById('zipcode').value.trim() || null;
            const address = document.getElementById('address').value.trim() || null;
            const neighborhood = document.getElementById('neighborhood').value.trim() || null;
            const city = document.getElementById('city').value.trim() || null;
            const state = document.getElementById('state').value || null;

            const response = await fetch('<?= API_BASE_URL ?>/auth/register', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    companyName,
                    email,
                    password,
                    documentCpf,
                    documentCnpj,
                    phone,
                    whatsapp,
                    zipcode,
                    address,
                    neighborhood,
                    city,
                    state,
                }),
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Erro ao cadastrar');
            }

            // Salvar token na sessão
            await fetch('?page=register&action=store-token', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ token: data.token, user: data.user }),
            });

            showToast('✅ ' + data.message, 'success');
            setTimeout(() => { window.location.href = '?page=dashboard'; }, 1500);

        } catch (err) {
            showToast(err.message, 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = 'Criar Conta';
            document.getElementById('loading-overlay').classList.add('hidden');
        }
    });

    function showFieldError(fieldId, message) {
        const input = document.getElementById(fieldId);
        input.classList.add('border-danger');
        const error = input.parentElement.querySelector('.error-message');
        if (error) { error.textContent = message; error.classList.remove('hidden'); }
        showToast(message, 'error');
    }
</script>
