<section class="min-h-screen bg-background">
    <?php
    $pageTitle = 'Privacidade e Dados';
    $pageSubtitle = 'Gerencie seus dados pessoais e consentimentos LGPD';
    $currentPage = 'privacy';
    require __DIR__ . '/partials/sidebar.php';
    require __DIR__ . '/partials/topbar.php';
    ?>

    <main class="md:ml-64 pt-16 p-6">
        <div class="max-w-4xl mx-auto space-y-6">

            <div>
                <h1 class="text-2xl font-bold text-gray-900">Privacidade e Dados Pessoais</h1>
                <p class="text-sm text-gray-500 mt-1">Lei Geral de Proteção de Dados (LGPD) — Lei nº 13.709/2018</p>
            </div>

            <div id="loading-state" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center">
                <div class="animate-spin w-8 h-8 border-2 border-primary border-t-transparent rounded-full mx-auto mb-4"></div>
                <p class="text-gray-500">Carregando...</p>
            </div>

            <div id="error-state" class="hidden bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center">
                <p class="text-danger mb-4" id="error-message">Erro ao carregar dados.</p>
                <button onclick="loadData()" class="btn-secondary">Tentar Novamente</button>
            </div>

            <div id="content-area" class="hidden space-y-6">

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-bold mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Portabilidade de Dados
                    </h2>
                    <p class="text-sm text-gray-500 mb-4">Baixe todos os seus dados pessoais armazenados na plataforma em formato JSON.</p>
                    <button onclick="exportData()" id="export-btn" class="btn-primary">
                        <span id="export-btn-text">Exportar Meus Dados</span>
                    </button>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-bold mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Solicitar Eliminação de Dados
                    </h2>
                    <p class="text-sm text-gray-500 mb-4">Solicite a anonimização dos seus dados pessoais. Registros fiscais e operacionais serão mantidos anonimizados conforme legislação.</p>
                    <button onclick="requestDeletion()" id="delete-btn" class="btn-danger">
                        <span id="delete-btn-text">Solicitar Eliminação</span>
                    </button>
                    <p id="deletion-result" class="hidden text-sm text-success mt-3"></p>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-bold mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Consentimentos LGPD
                    </h2>
                    <p class="text-sm text-gray-500 mb-4">Gerencie seus consentimentos para tratamento de dados pessoais.</p>
                    <div id="consents-list" class="space-y-3"></div>
                </div>

            </div>
        </div>
    </main>

</section>

<script>
document.addEventListener('DOMContentLoaded', loadData);

async function loadData() {
    document.getElementById('loading-state').classList.remove('hidden');
    document.getElementById('error-state').classList.add('hidden');
    document.getElementById('content-area').classList.add('hidden');

    try {
        const token = getToken();
        const resp = await fetch('/api/v1/data/consent', { headers: { 'Authorization': `Bearer ${token}` } });
        if (resp.status === 401) { window.location.href = '?page=login'; return; }
        const data = await resp.json();

        document.getElementById('loading-state').classList.add('hidden');
        document.getElementById('content-area').classList.remove('hidden');

        renderConsents(data.consents || []);
    } catch (e) {
        document.getElementById('loading-state').classList.add('hidden');
        document.getElementById('error-state').classList.remove('hidden');
        document.getElementById('error-message').textContent = e.message;
    }
}

function renderConsents(consents) {
    const labels = { 'opt-in': 'Opt-in voluntário', 'communications': 'Comunicações de marketing', 'terms': 'Termos de uso e serviços' };
    const descriptions = { 'opt-in': 'Consentimento para tratamento de dados pessoais.', 'communications': 'Autorização para envio de comunicações promocionais e novidades.', 'terms': 'Aceitação dos termos de uso e política de privacidade.' };
    const revocable = { 'opt-in': true, 'communications': true, 'terms': false };

    const list = document.getElementById('consents-list');
    list.innerHTML = '';

    const types = ['opt-in', 'communications', 'terms'];
    types.forEach(type => {
        const consent = consents.find(c => c.consent_type === type);
        const granted = consent ? !!consent.granted : (type === 'terms');
        const div = document.createElement('div');
        div.className = 'flex items-start gap-4 p-4 rounded-xl border border-gray-100';
        div.innerHTML = `
            <div class="flex-1">
                <p class="font-medium text-sm">${labels[type] || type}</p>
                <p class="text-xs text-gray-500 mt-0.5">${descriptions[type] || ''}</p>
            </div>
            ${revocable[type] ? `
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" class="sr-only peer" ${granted ? 'checked' : ''} onchange="toggleConsent('${type}', this.checked, this)">
                    <div class="w-10 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                </label>
            ` : `
                <span class="text-xs text-gray-400 font-medium">${granted ? 'Aceito' : 'Não aceito'}</span>
            `}
        `;
        list.appendChild(div);
    });
}

async function toggleConsent(type, granted, checkbox) {
    try {
        const token = getToken();
        const resp = await fetch('/api/v1/data/consent', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` },
            body: JSON.stringify({ consentType: type, granted }),
        });
        if (!resp.ok) {
            checkbox.checked = !granted;
            const data = await resp.json();
            throw new Error(data.message || 'Erro ao atualizar consentimento');
        }
        showToast(granted ? 'Consentimento registrado!' : 'Consentimento revogado!', 'success');
    } catch (e) {
        showToast(e.message, 'error');
    }
}

async function exportData() {
    const btn = document.getElementById('export-btn');
    const text = document.getElementById('export-btn-text');
    btn.disabled = true;
    text.textContent = 'Exportando...';

    try {
        const token = getToken();
        const resp = await fetch('/api/v1/data/export', { headers: { 'Authorization': `Bearer ${token}` } });
        if (!resp.ok) throw new Error('Erro ao exportar');
        const data = await resp.json();

        const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `meus-dados-${new Date().toISOString().slice(0, 10)}.json`;
        a.click();
        URL.revokeObjectURL(url);

        showToast('✅ Dados exportados com sucesso!', 'success');
    } catch (e) {
        showToast(e.message, 'error');
    } finally {
        btn.disabled = false;
        text.textContent = 'Exportar Meus Dados';
    }
}

async function requestDeletion() {
    if (!confirm('Tem certeza? Esta ação solicitará a anonimização dos seus dados pessoais. O processo leva até 15 dias úteis.')) return;

    const btn = document.getElementById('delete-btn');
    const text = document.getElementById('delete-btn-text');
    btn.disabled = true;
    text.textContent = 'Solicitando...';

    try {
        const token = getToken();
        const resp = await fetch('/api/v1/data/delete-request', {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${token}` },
        });
        if (!resp.ok) throw new Error('Erro ao solicitar');
        const data = await resp.json();

        document.getElementById('deletion-result').classList.remove('hidden');
        document.getElementById('deletion-result').textContent = data.message;
    } catch (e) {
        showToast(e.message, 'error');
    } finally {
        btn.disabled = false;
        text.textContent = 'Solicitar Eliminação';
    }
}

function getToken() {
    return <?= json_encode(getToken()) ?> || '';
}

function showToast(msg, type) {
    const container = document.getElementById('toast-container') || (() => {
        const c = document.createElement('div');
        c.id = 'toast-container';
        c.className = 'fixed top-4 right-4 z-[100] flex flex-col gap-2';
        document.body.appendChild(c);
        return c;
    })();
    const colors = { success: 'bg-success/10 text-success border-success/20', error: 'bg-danger/10 text-danger border-danger/20' };
    const toast = document.createElement('div');
    toast.className = `px-4 py-3 rounded-lg border shadow-lg text-sm font-medium animate-slide-in ${colors[type] || colors.success} max-w-sm`;
    toast.textContent = msg;
    container.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; toast.style.transition = 'opacity 0.3s'; setTimeout(() => toast.remove(), 300); }, 4000);
}
</script>
<style>
.animate-slide-in { animation: slideIn 0.3s ease-out; }
@keyframes slideIn { from { opacity: 0; transform: translateX(100px); } to { opacity: 1; transform: translateX(0); } }
</style>
