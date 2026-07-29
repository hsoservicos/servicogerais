<?php
if (!isAuthenticated()) {
    header('Location: ?page=login');
    exit;
}
$currentPage = 'tenant-profile';
require __DIR__ . '/partials/sidebar.php';
?>

<div class="md:ml-64 min-h-screen flex flex-col">
    <?php
    $pageTitle = 'Meu Perfil';
    $pageSubtitle = 'Gerencie as informações do seu negócio';
    require __DIR__ . '/partials/topbar.php';
    ?>

    <main class="flex-1 p-6">
        <div class="max-w-2xl mx-auto">
            <div id="profile-loading" class="flex items-center justify-center py-16">
                <div class="w-10 h-10 border-4 border-primary/30 border-t-primary rounded-full animate-spin"></div>
            </div>

            <div id="profile-form" class="hidden">
                <div class="bg-white rounded-xl shadow-card border border-border overflow-hidden">
                    <div class="px-6 py-4 border-b border-border bg-surface/30">
                        <h3 class="text-h3 text-ink">Dados da Empresa</h3>
                    </div>

                    <form id="tenant-profile-form" class="p-6 space-y-4" novalidate>
                        <div>
                            <label class="block text-sm font-medium text-ink mb-1.5">Nome da Empresa</label>
                            <input type="text" id="profile-name"
                                class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-ink mb-1.5">CPF</label>
                                <input type="text" id="profile-cpf" readonly
                                    class="w-full px-4 py-2.5 rounded-lg border border-border bg-gray-50 text-ink-muted cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-ink mb-1.5">CNPJ</label>
                                <input type="text" id="profile-cnpj" readonly
                                    class="w-full px-4 py-2.5 rounded-lg border border-border bg-gray-50 text-ink-muted cursor-not-allowed">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-ink mb-1.5">Telefone</label>
                                    <input type="tel" id="profile-phone"
                                        class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                                        placeholder="(11) 99999-9999"
                                        data-mask="phone">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-ink mb-1.5">WhatsApp</label>
                                    <input type="tel" id="profile-whatsapp"
                                        class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                                        placeholder="(11) 99999-9999"
                                        data-mask="phone">
                            </div>
                        </div>

                        <div class="border-t border-border pt-4">
                            <p class="text-sm font-medium text-ink mb-3">📍 Endereço</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-ink mb-1.5">CEP</label>
                                    <input type="text" id="profile-zipcode"
                                        class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                                        placeholder="00000-000" maxlength="9"
                                        data-mask="cep" data-cep-target="profile-">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-ink mb-1.5">Bairro</label>
                                    <input type="text" id="profile-neighborhood"
                                        class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                                        placeholder="Centro">
                                </div>
                            </div>
                            <div class="mt-3">
                                <label class="block text-sm font-medium text-ink mb-1.5">Endereço</label>
                                <input type="text" id="profile-address"
                                    class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                                    placeholder="Rua, número, complemento">
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                                <div>
                                    <label class="block text-sm font-medium text-ink mb-1.5">Cidade <span class="text-danger">*</span></label>
                                    <input type="text" id="profile-city"
                                        class="w-full px-4 py-2.5 rounded-lg border border-border bg-surface text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                                        placeholder="São Paulo">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-ink mb-1.5">Estado <span class="text-danger">*</span></label>
                                    <select id="profile-state"
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

                        <div class="flex justify-end pt-4 border-t border-border">
                            <button type="submit" id="profile-submit"
                                class="px-6 py-2.5 rounded-lg bg-primary text-white hover:bg-primary-600 active:bg-primary-700 transition-all text-sm font-medium shadow-card flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>

                <div class="mt-6 bg-white rounded-xl shadow-card border border-border overflow-hidden">
                    <div class="px-6 py-4 border-b border-border bg-surface/30">
                        <h3 class="text-h3 text-ink">Informações da Conta</h3>
                    </div>
                    <div class="p-6 space-y-3 text-sm">
                        <div class="flex justify-between"><span class="text-ink-secondary">Plano</span><span id="profile-plan" class="font-medium text-ink"></span></div>
                        <div class="flex justify-between"><span class="text-ink-secondary">Cadastrado em</span><span id="profile-created" class="font-medium text-ink"></span></div>
                        <div class="flex justify-between"><span class="text-ink-secondary">Status</span><span id="profile-status" class="font-medium text-success"></span></div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
const API = window.API_BASE || '/api/v1';

async function loadProfile() {
    try {
        const res = await fetch(`${API}/tenants/me`, {
            headers: { 'Authorization': `Bearer ${window.AUTH_TOKEN || ''}` }
        });
        if (!res.ok) throw new Error('Erro ao carregar perfil');
        const data = await res.json();
        const t = data.tenant;

        document.getElementById('profile-name').value = t.name || '';
        document.getElementById('profile-cpf').value = t.document_cpf || '';
        document.getElementById('profile-cnpj').value = t.document_cnpj || '';
        document.getElementById('profile-phone').value = t.phone || '';
        document.getElementById('profile-whatsapp').value = t.whatsapp || '';
        document.getElementById('profile-zipcode').value = t.zipcode || '';
        document.getElementById('profile-address').value = t.address || '';
        document.getElementById('profile-neighborhood').value = t.neighborhood || '';
        document.getElementById('profile-city').value = t.city || '';
        if (t.state) document.getElementById('profile-state').value = t.state;
        document.getElementById('profile-plan').textContent = t.plan || '—';
        document.getElementById('profile-created').textContent = t.created_at ? new Date(t.created_at).toLocaleDateString('pt-BR') : '—';
        document.getElementById('profile-status').textContent = t.active ? 'Ativo' : 'Inativo';

        document.getElementById('profile-loading').classList.add('hidden');
        document.getElementById('profile-form').classList.remove('hidden');
    } catch (err) {
        document.getElementById('profile-loading').innerHTML = '<p class="text-danger">Erro ao carregar perfil.</p>';
    }
}

document.getElementById('tenant-profile-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('profile-submit');
    btn.disabled = true;
    btn.innerHTML = '<span class="inline-block w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin mr-2"></span> Salvando...';

    try {
        const body = {
            name: document.getElementById('profile-name').value.trim(),
            phone: document.getElementById('profile-phone').value.trim() || null,
            whatsapp: document.getElementById('profile-whatsapp').value.trim() || null,
            zipcode: document.getElementById('profile-zipcode').value.trim() || null,
            address: document.getElementById('profile-address').value.trim() || null,
            neighborhood: document.getElementById('profile-neighborhood').value.trim() || null,
            city: document.getElementById('profile-city').value.trim(),
            state: document.getElementById('profile-state').value,
        };

        const res = await fetch(`${API}/tenants/me`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${window.AUTH_TOKEN || ''}`
            },
            body: JSON.stringify(body),
        });

        const data = await res.json();
        if (!res.ok) throw new Error(data.message || 'Erro ao salvar');
        showToast('✅ ' + data.message, 'success');
    } catch (err) {
        showToast(err.message, 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Salvar Alterações';
    }
});

loadProfile();
</script>