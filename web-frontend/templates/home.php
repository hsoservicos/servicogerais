<!-- ═══════════════════════════════════════════════════════════════
     templates/home.php — Landing Page (Epic 6 — Story 6.1)
     ═══════════════════════════════════════════════════════════
     Hero com busca AJAX + Categorias + Como Funciona + CTA
     ═══════════════════════════════════════════════════════════ -->

<?php if (!isset($_SESSION['jwt'])): ?>

<!-- ── Navbar ──────────────────────────────────────────── -->
<nav class="bg-white/80 backdrop-blur-md border-b border-border sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <span class="font-bold text-lg text-ink"><?= APP_NAME ?></span>
        </div>
        <div class="flex items-center gap-2 sm:gap-3">
            <a href="?page=login" class="text-ink-secondary hover:text-ink font-medium px-3 sm:px-4 py-2 transition-colors text-sm sm:text-base">Entrar</a>
            <a href="?page=register" class="bg-primary text-white font-medium px-4 sm:px-5 py-2 rounded-lg hover:bg-primary-600 active:bg-primary-700 transition-all shadow-card text-sm sm:text-base whitespace-nowrap">Cadastre-se</a>
        </div>
    </div>
</nav>

<!-- ── Hero Section ────────────────────────────────────── -->
<section class="relative overflow-hidden bg-gradient-to-b from-primary-50 via-white to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 pt-16 sm:pt-20 md:pt-24 pb-20 sm:pb-24 md:pb-32">
        <div class="max-w-3xl mx-auto text-center animate-fade-in">
            <!-- Badge -->
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary/10 text-primary text-xs sm:text-sm font-medium mb-4 sm:mb-6">
                <span class="w-2 h-2 bg-primary rounded-full animate-pulse-dot"></span>
                Encontre o profissional ideal para você
            </span>

            <!-- Headline -->
            <h1 class="text-display text-ink mb-4 sm:mb-6 leading-tight">
                Serviços profissionais<br>
                <span class="text-primary">perto de você</span>
            </h1>
            <p class="text-base sm:text-lg text-ink-secondary mb-6 sm:mb-8 max-w-xl mx-auto">
                Encontre cabeleireiros, manicures, maquiadores e muito mais. 
                Solicite orçamentos sem compromisso.
            </p>

            <!-- Search Bar -->
            <div class="max-w-2xl mx-auto relative" id="search-container">
                <div class="flex items-center bg-white border-2 border-border focus-within:border-primary rounded-xl shadow-card transition-all duration-200 overflow-hidden">
                    <svg class="w-5 h-5 text-ink-muted ml-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" id="search-input"
                        class="w-full px-3 sm:px-4 py-3 sm:py-4 text-ink placeholder-ink-muted outline-none bg-transparent text-sm sm:text-base"
                        placeholder="O que você procura? Ex: Corte de cabelo, Manicure..."
                        autocomplete="off" minlength="2">
                    <button id="search-btn"
                        class="bg-primary hover:bg-primary-600 text-white font-medium px-4 sm:px-6 py-3 sm:py-4 transition-all text-sm sm:text-base flex-shrink-0">
                        Buscar
                    </button>
                </div>

                <!-- Autocomplete Results -->
                <div id="search-results" class="hidden absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-modal border border-border overflow-hidden z-50 max-h-80 overflow-y-auto animate-fade-in">
                </div>
            </div>

            <!-- Category Tags -->
            <div class="mt-6 sm:mt-8 flex flex-wrap justify-center gap-2" id="category-tags">
                <span class="text-xs sm:text-sm text-ink-muted mr-1">Categorias:</span>
                <!-- Populated by JS -->
            </div>
        </div>
    </div>
    <!-- Decorative Elements -->
    <div class="absolute -top-24 -right-24 w-96 h-96 bg-primary/5 rounded-full blur-3xl"></div>
    <div class="absolute -bottom-24 -left-24 w-80 h-80 bg-primary/10 rounded-full blur-3xl"></div>
    <div class="absolute top-1/3 left-1/4 w-4 h-4 bg-primary/20 rounded-full"></div>
    <div class="absolute bottom-1/4 right-1/3 w-6 h-6 bg-primary/15 rounded-full"></div>
</section>

<!-- ── Category Grid: Serviços ──────────────────────────── -->
<section id="categorias" class="py-12 sm:py-16 md:py-20 bg-surface">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-8 sm:mb-12">
            <div class="w-12 h-12 sm:w-14 sm:h-14 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 sm:w-7 sm:h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <h2 class="text-h1 text-ink mb-3 sm:mb-4">Serviços Profissionais</h2>
            <p class="text-ink-secondary max-w-2xl mx-auto text-sm sm:text-base">Encontre cabeleireiros, manicures, maquiadores e profissionais de estética perto de você.</p>
        </div>
        <div id="categories-grid" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 md:gap-6">
            <!-- Populated by JS -->
        </div>
    </div>
</section>

<!-- ── Category Grid: Trabalhadores Domésticos ─────────── -->
<section id="categorias-domesticas" class="py-12 sm:py-16 md:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-8 sm:mb-12">
            <div class="w-12 h-12 sm:w-14 sm:h-14 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 sm:w-7 sm:h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
            <h2 class="text-h1 text-ink mb-3 sm:mb-4">Profissionais Domésticos</h2>
            <p class="text-ink-secondary max-w-2xl mx-auto text-sm sm:text-base">Contrate profissionais para sua casa com toda segurança — diaristas, babás, cuidadores, cozinheiros e muito mais.</p>
        </div>
        <div id="worker-categories-grid" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 md:gap-6">
            <!-- Populated by JS -->
        </div>
    </div>
</section>

<!-- ── Como Funciona ──────────────────────────────────── -->
<section id="como-funciona" class="py-12 sm:py-16 md:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-8 sm:mb-12">
            <h2 class="text-h1 text-ink mb-3 sm:mb-4">Como funciona</h2>
            <p class="text-ink-secondary max-w-lg mx-auto text-sm sm:text-base">Em três passos simples, você encontra o profissional ideal.</p>
        </div>
        <div class="grid sm:grid-cols-3 gap-6 sm:gap-8">
            <div class="bg-surface rounded-xl p-6 sm:p-8 shadow-card border border-border text-center animate-fade-in">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-4 sm:mb-5">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <h3 class="text-h3 text-ink mb-2 sm:mb-3">1. Busque</h3>
                <p class="text-ink-secondary text-sm sm:text-base">Encontre o serviço que precisa na nossa plataforma. São diversas categorias disponíveis.</p>
            </div>
            <div class="bg-surface rounded-xl p-6 sm:p-8 shadow-card border border-border text-center animate-fade-in" style="animation-delay: 0.1s">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-4 sm:mb-5">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <h3 class="text-h3 text-ink mb-2 sm:mb-3">2. Solicite</h3>
                <p class="text-ink-secondary text-sm sm:text-base">Descreva o que precisa e receba orçamentos personalizados dos profissionais.</p>
            </div>
            <div class="bg-surface rounded-xl p-6 sm:p-8 shadow-card border border-border text-center animate-fade-in" style="animation-delay: 0.2s">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-4 sm:mb-5">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-h3 text-ink mb-2 sm:mb-3">3. Contrate</h3>
                <p class="text-ink-secondary text-sm sm:text-base">Escolha o melhor profissional, aprove o orçamento e pague com segurança.</p>
            </div>
        </div>
    </div>
</section>

<!-- ── CTA Profissional ───────────────────────────────── -->
<section class="bg-gradient-to-br from-primary-900 to-primary-800 py-12 sm:py-16 md:py-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 text-center">
        <div class="w-16 h-16 sm:w-20 sm:h-20 bg-white/10 rounded-2xl flex items-center justify-center mx-auto mb-6 sm:mb-8 backdrop-blur-sm">
            <svg class="w-8 h-8 sm:w-10 sm:h-10 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>
        <h2 class="text-h1 text-white mb-3 sm:mb-4">Seja um Profissional Parceiro</h2>
        <p class="text-primary-200 mb-6 sm:mb-8 text-sm sm:text-lg max-w-xl mx-auto">
            Cadastre-se gratuitamente e gerencie seus serviços, clientes e propostas em um só lugar. Alcance mais clientes na sua região.
        </p>
        <div class="grid sm:grid-cols-3 gap-4 sm:gap-6 max-w-2xl mx-auto mb-8 sm:mb-10">
            <div class="bg-white/5 rounded-xl p-4 sm:p-5 text-center backdrop-blur-sm">
                <p class="text-2xl sm:text-3xl font-bold text-white">Grátis</p>
                <p class="text-primary-200 text-xs sm:text-sm mt-1">Plano inicial sem custos</p>
            </div>
            <div class="bg-white/5 rounded-xl p-4 sm:p-5 text-center backdrop-blur-sm">
                <p class="text-2xl sm:text-3xl font-bold text-white">Clientes</p>
                <p class="text-primary-200 text-xs sm:text-sm mt-1">Gestão completa</p>
            </div>
            <div class="bg-white/5 rounded-xl p-4 sm:p-5 text-center backdrop-blur-sm">
                <p class="text-2xl sm:text-3xl font-bold text-white">Propostas</p>
                <p class="text-primary-200 text-xs sm:text-sm mt-1">Profissionais via WhatsApp</p>
            </div>
        </div>
        <a href="?page=register"
            class="inline-block bg-white text-primary-900 font-semibold px-8 sm:px-10 py-3 sm:py-3.5 rounded-lg hover:bg-primary-50 transition-all shadow-card text-sm sm:text-lg">
            Criar Conta Gratuita →
        </a>
    </div>
</section>

<!-- ── Features Section ────────────────────────────────── -->
<section class="py-12 sm:py-16 md:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-8 sm:mb-12">
            <h2 class="text-h1 text-ink mb-3 sm:mb-4">Tudo que você precisa</h2>
            <p class="text-ink-secondary max-w-lg mx-auto text-sm sm:text-base">Ferramentas completas para gerenciar seu negócio de serviços.</p>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <div class="p-5 sm:p-6 rounded-xl border border-border hover:shadow-card hover:border-primary/20 transition-all duration-200">
                <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center mb-3 sm:mb-4">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-ink mb-1 sm:mb-2 text-sm sm:text-base">Catálogo</h3>
                <p class="text-ink-secondary text-xs sm:text-sm">Organize seus serviços e produtos em categorias.</p>
            </div>
            <div class="p-5 sm:p-6 rounded-xl border border-border hover:shadow-card hover:border-primary/20 transition-all duration-200">
                <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center mb-3 sm:mb-4">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-ink mb-1 sm:mb-2 text-sm sm:text-base">Clientes</h3>
                <p class="text-ink-secondary text-xs sm:text-sm">Gerencie sua base de clientes com histórico completo.</p>
            </div>
            <div class="p-5 sm:p-6 rounded-xl border border-border hover:shadow-card hover:border-primary/20 transition-all duration-200">
                <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center mb-3 sm:mb-4">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-ink mb-1 sm:mb-2 text-sm sm:text-base">Propostas</h3>
                <p class="text-ink-secondary text-xs sm:text-sm">Crie propostas profissionais e envie por WhatsApp.</p>
            </div>
            <div class="p-5 sm:p-6 rounded-xl border border-border hover:shadow-card hover:border-primary/20 transition-all duration-200">
                <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center mb-3 sm:mb-4">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-ink mb-1 sm:mb-2 text-sm sm:text-base">Financeiro</h3>
                <p class="text-ink-secondary text-xs sm:text-sm">Acompanhe seus ganhos com dashboard completo.</p>
            </div>
        </div>
    </div>
</section>

<!-- ── JavaScript ─────────────────────────────────────── -->
<script>
// ── Load Categories ─────────────────────────────────────
async function loadCategories() {
    try {
        const response = await fetch('/api/v1/public/categories');
        if (!response.ok) throw new Error('Erro ao carregar categorias');
        const data = await response.json();

        // Category Tags (Hero)
        const tagsContainer = document.getElementById('category-tags');
        data.categories.forEach(c => {
            const tag = document.createElement('a');
            tag.href = '#categorias';
            tag.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs sm:text-sm font-medium transition-all duration-200 hover:scale-105';
            tag.style.backgroundColor = c.color + '15';
            tag.style.color = c.color;
            tag.innerHTML = `
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${c.iconSvg}"/>
                </svg>
                ${c.name}
            `;
            tag.onclick = (e) => {
                e.preventDefault();
                searchByCategory(c.name);
            };
            tagsContainer.appendChild(tag);
        });

        // Category Grid
        const grid = document.getElementById('categories-grid');
        if (data.categories.length === 0) {
            grid.innerHTML = `
                <div class="col-span-full text-center py-12">
                    <svg class="w-16 h-16 text-ink-muted/30 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <p class="text-ink-muted">Nenhuma categoria disponível no momento.</p>
                </div>
            `;
            return;
        }

        grid.innerHTML = data.categories.map(c => `
            <div class="group cursor-pointer bg-white rounded-xl p-4 sm:p-5 md:p-6 shadow-card border border-border hover:shadow-modal transition-all duration-200"
                 onclick="searchByCategory('${c.name}')" style="--cat-color: ${c.color}">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center mb-3 sm:mb-4 transition-transform group-hover:scale-110 duration-200"
                     style="background-color: ${c.color}15">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: ${c.color}">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="${c.iconSvg}"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-ink text-sm sm:text-base mb-1">${c.name}</h3>
                <p class="text-xs sm:text-sm text-ink-muted">${c.serviceCount > 0 ? c.serviceCount + ' serviço' + (c.serviceCount > 1 ? 's' : '') : 'Ver serviços'}</p>
            </div>
        `).join('');
    } catch (err) {
        console.warn('[Landing] Erro categorias:', err.message);
        document.getElementById('category-tags').innerHTML = '<span class="text-ink-muted text-sm">Categorias indisponíveis</span>';
        document.getElementById('categories-grid').innerHTML = `
            <div class="col-span-full text-center py-12">
                <p class="text-ink-muted">Erro ao carregar categorias. Tente novamente mais tarde.</p>
            </div>
        `;
    }
}

// ── Search Service ─────────────────────────────────────
let searchTimeout;

function setupSearch() {
    const input = document.getElementById('search-input');
    const results = document.getElementById('search-results');
    const container = document.getElementById('search-container');

    input.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();

        if (query.length < 2) {
            results.classList.add('hidden');
            return;
        }

        searchTimeout = setTimeout(() => performSearch(query), 300);
    });

    // Teclas: Enter = buscar, Escape = fechar
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const query = this.value.trim();
            if (query.length >= 2) {
                performSearch(query);
            }
        }
        if (e.key === 'Escape') {
            results.classList.add('hidden');
            this.blur();
        }
    });

    // Fechar resultados ao clicar fora
    document.addEventListener('click', (e) => {
        if (!container.contains(e.target)) {
            results.classList.add('hidden');
        }
    });
}

async function performSearch(query) {
    const results = document.getElementById('search-results');

    try {
        const response = await fetch(`/api/v1/public/services?search=${encodeURIComponent(query)}`);
        if (!response.ok) throw new Error('Erro na busca');
        const data = await response.json();

        if (data.services.length === 0) {
            results.innerHTML = `
                <div class="p-4 sm:p-6 text-center">
                    <svg class="w-10 h-10 text-ink-muted/30 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <p class="text-ink-secondary text-sm font-medium">Nenhum serviço encontrado</p>
                    <p class="text-ink-muted text-xs mt-1">Tente outros termos como "corte", "manicure" ou "maquiagem"</p>
                </div>
            `;
            results.classList.remove('hidden');
            return;
        }

        results.innerHTML = data.services.map(s => `
            <div class="flex items-center gap-3 sm:gap-4 p-3 sm:p-4 hover:bg-surface/50 transition-colors cursor-pointer border-b border-border/50 last:border-b-0"
                 onclick="selectService('${s.tenant}', '${s.name.replace(/'/g, "\\'")}')">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-lg flex items-center justify-center flex-shrink-0"
                     style="background-color: ${s.categoryColor}15">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: ${s.categoryColor}">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm sm:text-base font-medium text-ink truncate">${s.name}</p>
                    <p class="text-xs text-ink-muted truncate">
                        ${s.category} · ${s.tenant}
                        ${s.tenantCity ? ' · ' + s.tenantCity + (s.tenantState ? '/' + s.tenantState : '') : ''}
                        ${s.duration ? ' · ' + s.duration : ''}
                    </p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-sm sm:text-base font-semibold text-primary">${s.price}</p>
                </div>
            </div>
        `).join('');

        results.classList.remove('hidden');
    } catch (err) {
        results.innerHTML = `
            <div class="p-4 text-center">
                <p class="text-danger text-sm">Erro ao buscar serviços. Tente novamente.</p>
            </div>
        `;
        results.classList.remove('hidden');
    }
}

function searchByCategory(categoryName) {
    document.getElementById('search-input').value = categoryName;
    document.getElementById('search-input').focus();
    performSearch(categoryName);
    // Scroll to search
    document.getElementById('search-container').scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function selectService(tenant, serviceName) {
    document.getElementById('search-input').value = `${serviceName} - ${tenant}`;
    document.getElementById('search-results').classList.add('hidden');
    // Story 6.2 — Redirecionar para wizard de solicitação
    window.location.href = `?page=solicitar&servico=${encodeURIComponent(serviceName)}`;
}

// ── Load Worker Categories ──────────────────────────────
async function loadWorkerCategories() {
    try {
        const response = await fetch('/api/v1/public/worker-categories');
        if (!response.ok) throw new Error('Erro ao carregar categorias domésticas');
        const data = await response.json();

        const grid = document.getElementById('worker-categories-grid');
        if (!data.categories || data.categories.length === 0) {
            document.getElementById('categorias-domesticas')?.remove();
            return;
        }

        const regimeBadge = (regime, maxFreq) => {
            if (regime === 'AUTONOMO_DIARISTA') {
                return `<span class="text-xs font-medium px-2 py-0.5 rounded-full bg-warning/10 text-warning">Até ${maxFreq}x/sem</span>`;
            }
            return `<span class="text-xs font-medium px-2 py-0.5 rounded-full bg-primary/10 text-primary">CLT</span>`;
        };

        grid.innerHTML = data.categories.map(c => `
            <div class="group bg-white rounded-xl p-4 sm:p-5 md:p-6 shadow-card border border-border hover:shadow-modal transition-all duration-200">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-primary/10 flex items-center justify-center mb-3 sm:mb-4 transition-transform group-hover:scale-110 duration-200">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="${c.iconSvg}"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-ink text-sm sm:text-base mb-1">${c.name}</h3>
                <p class="text-xs text-ink-muted mb-2 line-clamp-2">${c.description || c.legalRegimeLabel || ''}</p>
                <div>
                    ${regimeBadge(c.legalRegime, c.maxWeeklyFrequency)}
                    <span class="text-xs text-ink-muted ml-1">CBO ${c.cboCode}</span>
                </div>
            </div>
        `).join('');
    } catch (err) {
        console.warn('[Landing] Erro worker categories:', err.message);
        document.getElementById('categorias-domesticas')?.remove();
    }
}

// ── Search Button ───────────────────────────────────────
document.getElementById('search-btn')?.addEventListener('click', () => {
    const query = document.getElementById('search-input').value.trim();
    if (query.length >= 2) performSearch(query);
    else document.getElementById('search-input').focus();
});

// ── Init ────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    loadCategories();
    loadWorkerCategories();
    setupSearch();
});
</script>

<?php else: ?>
    <!-- Logado: redirecionar para dashboard -->
    <script>window.location.href = '?page=dashboard';</script>
<?php endif; ?>
