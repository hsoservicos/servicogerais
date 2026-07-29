<!-- ═══════════════════════════════════════════════════════════════
     templates/solicitar.php — Wizard de Solicitação (Epic 6 — Story 6.2)
     ═══════════════════════════════════════════════════════════════
     3-Step: ① Selecionar Serviço → ② Detalhes → ③ Dados Pessoais
     ═══════════════════════════════════════════════════════════════ -->

<div class="min-h-screen bg-gradient-to-b from-primary-50/30 via-white to-white">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-8 sm:py-12">

        <!-- ── Back Link ──────────────────────────────────── -->
        <a href="?page=home" class="inline-flex items-center gap-2 text-ink-secondary hover:text-primary transition-colors mb-6 sm:mb-8 group">
            <svg class="w-5 h-5 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span class="text-sm font-medium">Voltar para página inicial</span>
        </a>

        <!-- ── Wizard Container ──────────────────────────── -->
        <div class="bg-white rounded-2xl shadow-modal border border-border overflow-hidden">

            <!-- ── Header ─────────────────────────────────── -->
            <div class="px-6 sm:px-8 pt-6 sm:pt-8 pb-4 sm:pb-6 border-b border-border bg-gradient-to-r from-primary-50/50 to-white">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-h2 text-ink">Solicitar Orçamento</h1>
                        <p class="text-sm text-ink-secondary">Preencha os dados abaixo para receber propostas de profissionais</p>
                    </div>
                </div>

                <!-- ── Progress Bar ────────────────────────── -->
                <div class="flex items-center gap-1 sm:gap-2" id="progress-bar">
                    <div class="flex items-center gap-1 sm:gap-2 flex-1">
                        <div class="step-dot active" data-step="1">
                            <span class="step-number">1</span>
                        </div>
                        <div class="step-line active" data-step="1"></div>
                    </div>
                    <div class="flex items-center gap-1 sm:gap-2 flex-1">
                        <div class="step-dot" data-step="2">
                            <span class="step-number">2</span>
                        </div>
                        <div class="step-line" data-step="2"></div>
                    </div>
                    <div class="flex items-center gap-1 sm:gap-2 flex-1">
                        <div class="step-dot" data-step="3">
                            <span class="step-number">3</span>
                        </div>
                        <div class="step-line" data-step="3"></div>
                    </div>
                    <div class="flex items-center gap-1 sm:gap-2">
                        <div class="step-dot" data-step="4">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <!-- Step Labels -->
                <div class="flex items-center justify-between mt-2 px-1">
                    <span class="step-label active" data-step="1">Serviço</span>
                    <span class="step-label" data-step="2">Detalhes</span>
                    <span class="step-label" data-step="3">Dados</span>
                    <span class="step-label" data-step="4">Confirmação</span>
                </div>
            </div>

            <!-- ── Wizard Body ────────────────────────────── -->
            <div class="px-6 sm:px-8 py-6 sm:py-8">

                <!-- ══════════════════════════════════════════════════════
                     STEP 1 — Selecionar Serviço
                     ══════════════════════════════════════════════════════ -->
                <div class="step-content active" id="step-1">
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-ink mb-2">Buscar serviço</label>
                        <div class="relative" id="wizard-search-container">
                            <div class="flex items-center bg-white border-2 border-border focus-within:border-primary rounded-xl transition-all duration-200 overflow-hidden">
                                <svg class="w-5 h-5 text-ink-muted ml-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input type="text" id="wizard-search-input"
                                    class="w-full px-3 py-3 text-ink placeholder-ink-muted outline-none bg-transparent text-sm"
                                    placeholder="Digite o nome do serviço..."
                                    autocomplete="off" minlength="2">
                            </div>
                            <div id="wizard-search-results" class="hidden absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-modal border border-border overflow-hidden z-50 max-h-64 overflow-y-auto animate-fade-in">
                            </div>
                        </div>
                    </div>

                    <div id="wizard-categories">
                        <label class="block text-sm font-medium text-ink mb-3">Ou escolha por categoria</label>
                        <div id="wizard-categories-grid" class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <!-- Populated by JS -->
                        </div>
                    </div>

                    <div id="wizard-selected-service" class="hidden mt-6 p-4 bg-primary-50/50 rounded-xl border border-primary/20 animate-fade-in">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-ink text-sm" id="selected-service-name">—</p>
                                    <p class="text-xs text-ink-muted" id="selected-service-category">—</p>
                                </div>
                            </div>
                            <button onclick="clearSelectedService()" class="text-ink-muted hover:text-danger transition-colors p-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ══════════════════════════════════════════════════════
                     STEP 2 — Detalhes do Pedido
                     ══════════════════════════════════════════════════════ -->
                <div class="step-content" id="step-2">
                    <div class="space-y-5">
                        <!-- Descrição -->
                        <div>
                            <label for="lead-description" class="block text-sm font-medium text-ink mb-1.5">
                                Descrição do que precisa <span class="text-danger">*</span>
                            </label>
                            <textarea id="lead-description" rows="3"
                                class="input-field w-full px-4 py-3 rounded-xl border-2 border-border focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all duration-200 text-sm resize-none"
                                placeholder="Ex: Preciso cortar o cabelo e fazer manicure..."></textarea>
                        </div>

                        <!-- Data e Horário -->
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label for="lead-date" class="block text-sm font-medium text-ink mb-1.5">Data preferencial</label>
                                <input type="date" id="lead-date"
                                    class="input-field w-full px-4 py-3 rounded-xl border-2 border-border focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all duration-200 text-sm">
                            </div>
                            <div>
                                <label for="lead-time" class="block text-sm font-medium text-ink mb-1.5">Horário preferencial</label>
                                <input type="time" id="lead-time"
                                    class="input-field w-full px-4 py-3 rounded-xl border-2 border-border focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all duration-200 text-sm">
                            </div>
                        </div>

                        <!-- Fotos (Upload) -->
                        <div>
                            <h3 class="text-sm font-medium text-ink mb-3">Fotos <span class="text-ink-muted text-xs">(opcional)</span></h3>
                            <p class="text-xs text-ink-secondary mb-3">Adicione fotos do que precisa — ajuda o profissional a entender melhor o serviço.</p>
                            
                            <!-- Drop Zone -->
                            <div id="photo-dropzone"
                                class="relative border-2 border-dashed border-border rounded-xl p-6 sm:p-8 text-center cursor-pointer hover:border-primary/40 hover:bg-primary-50/20 transition-all duration-200 group">
                                <input type="file" id="photo-input" accept="image/jpeg,image/png,image/webp,image/gif" multiple
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                <div class="flex flex-col items-center gap-2">
                                    <div class="w-12 h-12 bg-primary-50 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-ink-secondary group-hover:text-ink transition-colors">
                                            <span class="text-primary font-medium">Clique para enviar</span> ou arraste as fotos aqui
                                        </p>
                                        <p class="text-xs text-ink-muted mt-1">JPEG, PNG, WebP ou GIF — Máx. 5MB cada (até 5 fotos)</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Preview Grid -->
                            <div id="photo-previews" class="grid grid-cols-3 sm:grid-cols-5 gap-3 mt-3">
                                <!-- Populated by JS -->
                            </div>
                            <div id="photo-upload-progress" class="hidden mt-3">
                                <div class="flex items-center gap-3 text-sm text-ink-muted">
                                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    <span id="photo-upload-status">Enviando fotos...</span>
                                </div>
                            </div>
                        </div>

                        <!-- Endereço -->
                        <div>
                            <h3 class="text-sm font-medium text-ink mb-3">Local de atendimento</h3>
                            <div class="space-y-3">
                                <div class="grid sm:grid-cols-3 gap-3">
                                    <div>
                                        <label for="lead-zipcode" class="block text-xs text-ink-secondary mb-1">CEP</label>
                                        <input type="text" id="lead-zipcode" maxlength="9"
                                            class="input-field w-full px-4 py-2.5 rounded-xl border-2 border-border focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all duration-200 text-sm"
                                            placeholder="00000-000">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label for="lead-address" class="block text-xs text-ink-secondary mb-1">Endereço</label>
                                        <input type="text" id="lead-address"
                                            class="input-field w-full px-4 py-2.5 rounded-xl border-2 border-border focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all duration-200 text-sm"
                                            placeholder="Rua, número, bairro">
                                    </div>
                                </div>
                                <div class="grid sm:grid-cols-3 gap-3">
                                    <div>
                                        <label for="lead-city" class="block text-xs text-ink-secondary mb-1">Cidade</label>
                                        <input type="text" id="lead-city"
                                            class="input-field w-full px-4 py-2.5 rounded-xl border-2 border-border focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all duration-200 text-sm"
                                            placeholder="São Paulo">
                                    </div>
                                    <div>
                                        <label for="lead-state" class="block text-xs text-ink-secondary mb-1">Estado</label>
                                        <select id="lead-state"
                                            class="input-field w-full px-4 py-2.5 rounded-xl border-2 border-border focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all duration-200 text-sm bg-white">
                                            <option value="">Selecione</option>
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
                                    <div>
                                        <label for="lead-reference" class="block text-xs text-ink-secondary mb-1">Referência</label>
                                        <input type="text" id="lead-reference"
                                            class="input-field w-full px-4 py-2.5 rounded-xl border-2 border-border focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all duration-200 text-sm"
                                            placeholder="Próximo ao mercado...">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ══════════════════════════════════════════════════════
                     STEP 3 — Dados Pessoais + LGPD
                     ══════════════════════════════════════════════════════ -->
                <div class="step-content" id="step-3">
                    <div class="space-y-5">
                        <!-- Nome -->
                        <div>
                            <label for="lead-name" class="block text-sm font-medium text-ink mb-1.5">
                                Seu nome <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="lead-name"
                                class="input-field w-full px-4 py-3 rounded-xl border-2 border-border focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all duration-200 text-sm"
                                placeholder="Como prefere ser chamado?">
                        </div>

                        <!-- Telefone e Email -->
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label for="lead-phone" class="block text-sm font-medium text-ink mb-1.5">
                                    Telefone / WhatsApp <span class="text-danger">*</span>
                                </label>
                                <input type="tel" id="lead-phone" maxlength="15"
                                    class="input-field w-full px-4 py-3 rounded-xl border-2 border-border focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all duration-200 text-sm"
                                    placeholder="(11) 99999-0000">
                            </div>
                            <div>
                                <label for="lead-email" class="block text-sm font-medium text-ink mb-1.5">
                                    E-mail <span class="text-ink-muted text-xs">(opcional)</span>
                                </label>
                                <input type="email" id="lead-email"
                                    class="input-field w-full px-4 py-3 rounded-xl border-2 border-border focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all duration-200 text-sm"
                                    placeholder="seu@email.com">
                            </div>
                        </div>

                        <!-- Resumo do Pedido -->
                        <div class="bg-surface rounded-xl p-4 sm:p-5 border border-border">
                            <h3 class="text-sm font-semibold text-ink mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Resumo do Pedido
                            </h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-ink-secondary">Serviço:</span>
                                    <span class="text-ink font-medium" id="summary-service">—</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-ink-secondary">Descrição:</span>
                                    <span class="text-ink text-right max-w-[60%]" id="summary-description">—</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-ink-secondary">Data:</span>
                                    <span class="text-ink" id="summary-date">—</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-ink-secondary">Local:</span>
                                    <span class="text-ink text-right max-w-[60%]" id="summary-address">—</span>
                                </div>
                            </div>
                        </div>

                        <!-- ── LGPD Consent ──────────────────────── -->
                        <div class="bg-info/5 rounded-xl p-4 sm:p-5 border border-info/10">
                            <div class="flex items-start gap-3 mb-3">
                                <svg class="w-5 h-5 text-info mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-ink">Privacidade e Consentimento</p>
                                    <p class="text-xs text-ink-secondary mt-1">Seus dados estão seguros conosco. Utilizamos suas informações apenas para conectar você aos profissionais parceiros.</p>
                                </div>
                            </div>

                            <label class="flex items-start gap-3 p-3 rounded-lg hover:bg-white/50 transition-colors cursor-pointer group">
                                <div class="relative mt-0.5">
                                    <input type="checkbox" id="lgpd-consent-marketing"
                                        class="peer w-5 h-5 rounded-md border-2 border-ink-muted checked:border-primary checked:bg-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all cursor-pointer">
                                </div>
                                <div>
                                    <span class="text-sm text-ink font-medium">Aceito ser contactado</span>
                                    <p class="text-xs text-ink-secondary mt-0.5">Autorizo o profissional a entrar em contato comigo por telefone ou WhatsApp para tratar sobre meu pedido.</p>
                                </div>
                            </label>

                            <label class="flex items-start gap-3 p-3 rounded-lg hover:bg-white/50 transition-colors cursor-pointer group">
                                <div class="relative mt-0.5">
                                    <input type="checkbox" id="lgpd-consent-terms"
                                        class="peer w-5 h-5 rounded-md border-2 border-ink-muted checked:border-primary checked:bg-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all cursor-pointer">
                                </div>
                                <div>
                                    <span class="text-sm text-ink font-medium">Aceito os Termos de Uso</span>
                                    <p class="text-xs text-ink-secondary mt-0.5">Li e concordo com os <a href="?page=termos-de-uso" class="text-primary hover:underline" target="_blank">Termos de Uso</a> e a <a href="?page=termos-de-uso" class="text-primary hover:underline" target="_blank">Política de Privacidade</a> da plataforma.</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- ══════════════════════════════════════════════════════
                     STEP 4 — Confirmação / Sucesso
                     ══════════════════════════════════════════════════════ -->
                <div class="step-content" id="step-4">
                    <div class="text-center py-8 sm:py-12 animate-fade-in">
                        <div class="w-20 h-20 sm:w-24 sm:h-24 bg-success/10 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 sm:w-12 sm:h-12 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h2 class="text-h1 text-ink mb-3">Solicitação Enviada! 🎉</h2>
                        <p class="text-ink-secondary max-w-md mx-auto mb-2">
                            Seu pedido foi registrado com sucesso. Os profissionais da região irão analisar e entrar em contato com você em breve.
                        </p>
                        <div class="bg-surface rounded-xl p-4 border border-border max-w-sm mx-auto mb-6 text-left">
                            <p class="text-xs text-ink-secondary mb-1">Serviço solicitado</p>
                            <p class="font-medium text-ink" id="success-service-name">—</p>
                        </div>
                        <p class="text-sm text-ink-muted mb-8">
                            Protocolo: <span id="lead-protocol" class="font-mono font-medium text-ink">—</span>
                        </p>
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                            <a href="?page=home"
                                class="bg-primary text-white font-medium px-6 py-3 rounded-lg hover:bg-primary-600 transition-all shadow-card">
                                Voltar para página inicial
                            </a>
                            <a href="?page=solicitar"
                                class="bg-surface text-ink font-medium px-6 py-3 rounded-lg border border-border hover:bg-white transition-all">
                                Nova solicitação
                            </a>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ── Footer Actions ─────────────────────────── -->
            <div class="px-6 sm:px-8 py-4 sm:py-5 border-t border-border bg-surface/50 flex items-center justify-between" id="wizard-actions">
                <button id="btn-back"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-ink-secondary hover:text-ink transition-colors rounded-lg hover:bg-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Voltar
                </button>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-ink-muted" id="step-counter">Passo 1 de 3</span>
                    <button id="btn-next"
                        class="bg-primary text-white font-medium px-6 py-2.5 rounded-lg hover:bg-primary-600 active:bg-primary-700 transition-all shadow-card text-sm disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-primary">
                        Continuar →
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- ── Inline Styles ──────────────────────────────────── -->
<style>
    .step-dot {
        @apply w-8 h-8 rounded-full flex items-center justify-center transition-all duration-300;
        background-color: #E2E8F0;
    }
    .step-dot.active {
        background-color: #2563EB;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
    }
    .step-dot.completed {
        background-color: #2563EB;
    }
    .step-number {
        @apply text-xs font-bold;
        color: #94A3B8;
    }
    .step-dot.active .step-number,
    .step-dot.completed .step-number {
        color: white;
    }
    .step-line {
        height: 2px;
        flex: 1;
        background-color: #E2E8F0;
        transition: background-color 0.3s ease;
    }
    .step-line.active {
        background-color: #2563EB;
    }
    .step-label {
        @apply text-xs font-medium transition-colors duration-300;
        color: #94A3B8;
    }
    .step-label.active {
        color: #2563EB;
    }
    .step-label.completed {
        color: #2563EB;
    }
    .step-content {
        display: none;
        animation: fadeIn 0.35s ease-out;
    }
    .step-content.active {
        display: block;
    }
    .input-field.error {
        border-color: #DC2626 !important;
        /* background sutil removido — a borda vermelha + mensagem de erro já dão feedback visual suficiente */
    }
    .error-message {
        color: #DC2626;
        font-size: 0.75rem;
        margin-top: 4px;
        display: none;
    }
    .error-message.visible {
        display: block;
    }
    /* Custom checkbox style */
    input[type="checkbox"]:checked {
        background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3e%3c/svg%3e");
        background-size: 100% 100%;
        background-position: center;
        background-repeat: no-repeat;
    }


</style>

<!-- ── JavaScript ────────────────────────────────────── -->
<script>
    // ═══════════════════════════════════════════════════════════════
    // State
    // ═══════════════════════════════════════════════════════════════
    let currentStep = 1;
    let selectedService = null;
    let categoriesData = [];
    let servicesData = [];

    // ═══════════════════════════════════════════════════════════════
    // Step Navigation
    // ═══════════════════════════════════════════════════════════════
    function goToStep(step) {
        // Hide all steps
        document.querySelectorAll('.step-content').forEach(el => el.classList.remove('active'));
        document.getElementById(`step-${step}`).classList.add('active');

        // Update dots
        document.querySelectorAll('.step-dot').forEach((dot, i) => {
            const stepNum = i + 1;
            dot.classList.remove('active', 'completed');
            if (stepNum === step) dot.classList.add('active');
            else if (stepNum < step) dot.classList.add('completed');
        });

        // Update lines
        document.querySelectorAll('.step-line').forEach((line, i) => {
            const stepNum = i + 1;
            line.classList.toggle('active', stepNum < step);
        });

        // Update labels
        document.querySelectorAll('.step-label').forEach((label, i) => {
            const stepNum = i + 1;
            label.classList.remove('active', 'completed');
            if (stepNum === step) label.classList.add('active');
            else if (stepNum < step) label.classList.add('completed');
        });

        // Update counter
        if (step <= 3) {
            document.getElementById('step-counter').textContent = `Passo ${step} de 3`;
        } else {
            document.getElementById('step-counter').textContent = 'Concluído';
        }

        // Show/hide buttons
        const backBtn = document.getElementById('btn-back');
        const nextBtn = document.getElementById('btn-next');

        backBtn.style.display = (step <= 1 || step === 4) ? 'none' : 'inline-flex';

        if (step === 3) {
            nextBtn.innerHTML = `
                <svg class="w-4 h-4 animate-spin hidden" id="btn-spinner" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span id="btn-text">Solicitar Orçamento</span>
            `;
        } else if (step === 4) {
            nextBtn.style.display = 'none';
        } else {
            nextBtn.innerHTML = 'Continuar →';
            nextBtn.style.display = 'inline-flex';
        }

        currentStep = step;
        updateNextButton();
    }

    function updateNextButton() {
        const btn = document.getElementById('btn-next');
        btn.disabled = false;

        if (currentStep === 1 && !selectedService) {
            btn.disabled = true;
        }
    }

    // ── Navigation Handlers ──────────────────────────────
    document.getElementById('btn-next').addEventListener('click', async () => {
        if (currentStep === 1) {
            if (!selectedService) return;
            goToStep(2);
        } else if (currentStep === 2) {
            if (validateStep2()) {
                // Update summary
                updateSummary();
                goToStep(3);
            }
        } else if (currentStep === 3) {
            if (validateStep3()) {
                await submitLead();
            }
        }
    });

    document.getElementById('btn-back').addEventListener('click', () => {
        if (currentStep > 1 && currentStep <= 3) {
            goToStep(currentStep - 1);
        }
    });

    // ═══════════════════════════════════════════════════════════════
    // Step 1 — Service Search & Selection
    // ═══════════════════════════════════════════════════════════════

    // Load categories
    async function loadWizardCategories() {
        const grid = document.getElementById('wizard-categories-grid');

        // Show skeleton loading cards (covers network latency)
        grid.innerHTML = Array.from({ length: 4 }, () => `
            <div class="bg-white rounded-xl p-3 sm:p-4 border-2 border-border">
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg bg-surface animate-pulse mx-auto mb-2"></div>
                <div class="h-3 w-16 bg-surface animate-pulse rounded mx-auto mb-1"></div>
                <div class="h-3 w-10 bg-surface animate-pulse rounded mx-auto"></div>
            </div>
        `).join('');

        try {
            const resp = await fetch('/api/v1/public/categories');
            const data = await resp.json();
            categoriesData = data.categories;

            if (categoriesData.length === 0) {
                grid.innerHTML = `<div class="col-span-full text-center py-6 text-ink-muted text-sm">Nenhuma categoria disponível</div>`;
                return;
            }

            grid.innerHTML = categoriesData.map(c => `
                <div class="category-card cursor-pointer bg-white rounded-xl p-3 sm:p-4 border-2 border-border hover:border-primary/40 hover:shadow-card transition-all duration-200 text-center"
                     onclick="selectServiceFromCategory('${c.name.replace(/'/g, "\\'")}')"
                     style="border-color: ${c.color}20; hover:border-color: ${c.color}40">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg flex items-center justify-center mx-auto mb-2"
                         style="background-color: ${c.color}15">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: ${c.color}">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="${c.iconSvg}"/>
                        </svg>
                    </div>
                    <p class="text-xs sm:text-sm font-medium text-ink truncate">${c.name}</p>
                    <p class="text-xs text-ink-muted mt-0.5">${c.serviceCount > 0 ? c.serviceCount + ' serv' + (c.serviceCount > 1 ? 'os' : 'o') : 'Ver'}</p>
                </div>
            `).join('');
        } catch (err) {
            document.getElementById('wizard-categories-grid').innerHTML =
                '<div class="col-span-full text-center py-6 text-ink-muted text-sm">Erro ao carregar categorias</div>';
        }
    }

    // Search service
    let wizardSearchTimeout;

    function setupWizardSearch() {
        const input = document.getElementById('wizard-search-input');
        const results = document.getElementById('wizard-search-results');

        input.addEventListener('input', function() {
            clearTimeout(wizardSearchTimeout);
            const query = this.value.trim();
            if (query.length < 2) {
                results.classList.add('hidden');
                return;
            }
            wizardSearchTimeout = setTimeout(() => wizardSearch(query), 300);
        });

        input.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                results.classList.add('hidden');
                this.blur();
            }
        });

        document.addEventListener('click', (e) => {
            if (!document.getElementById('wizard-search-container').contains(e.target)) {
                results.classList.add('hidden');
            }
        });
    }

    async function wizardSearch(query) {
        const results = document.getElementById('wizard-search-results');
        try {
            const resp = await fetch(`/api/v1/public/services?search=${encodeURIComponent(query)}`);
            const data = await resp.json();
            servicesData = data.services;

            if (data.services.length === 0) {
                results.innerHTML = `
                    <div class="p-4 text-center">
                        <p class="text-ink-secondary text-sm">Nenhum serviço encontrado</p>
                        <p class="text-ink-muted text-xs mt-1">Tente outros termos</p>
                    </div>
                `;
                results.classList.remove('hidden');
                return;
            }

            results.innerHTML = data.services.map(s => `
                <div class="flex items-center gap-3 p-3 hover:bg-primary-50/30 transition-colors cursor-pointer border-b border-border/50 last:border-b-0"
                     onclick="selectService('${s.name.replace(/'/g, "\\'")}', '${(s.category || '').replace(/'/g, "\\'")}')">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                         style="background-color: ${s.categoryColor}15">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: ${s.categoryColor}">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-ink truncate">${s.name}</p>
                        <p class="text-xs text-ink-muted">${s.category || ''}${s.tenantCity ? ' · ' + s.tenantCity + (s.tenantState ? '/' + s.tenantState : '') : ''}${s.duration ? ' · ' + s.duration : ''}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-primary">${s.price}</p>
                    </div>
                </div>
            `).join('');
            results.classList.remove('hidden');
        } catch (err) {
            results.innerHTML = '<div class="p-4 text-center text-danger text-sm">Erro ao buscar serviços</div>';
            results.classList.remove('hidden');
        }
    }

    function selectService(name, category) {
        selectedService = { name, category };
        document.getElementById('selected-service-name').textContent = name;
        document.getElementById('selected-service-category').textContent = category || '—';
        document.getElementById('wizard-selected-service').classList.remove('hidden');
        document.getElementById('wizard-search-results').classList.add('hidden');
        document.getElementById('wizard-search-input').value = name;
        updateNextButton();
    }

    function selectServiceFromCategory(categoryName) {
        // Seleciona a categoria — sem subcategoria para evitar redundância no resumo
        selectService(categoryName, '');
        // Mostra serviços disponíveis na categoria
        document.getElementById('wizard-search-input').value = categoryName;
        wizardSearch(categoryName);
    }

    function clearSelectedService() {
        selectedService = null;
        document.getElementById('wizard-selected-service').classList.add('hidden');
        document.getElementById('wizard-search-input').value = '';
        document.getElementById('wizard-search-results').classList.add('hidden');
        updateNextButton();
    }

    // ═══════════════════════════════════════════════════════════════
    // Photo Upload — Step 2
    // ═══════════════════════════════════════════════════════════════
    let uploadedPhotos = []; // { file, preview, url }
    let isUploading = false;

    const dropzone = document.getElementById('photo-dropzone');
    const photoInput = document.getElementById('photo-input');
    const previewsContainer = document.getElementById('photo-previews');
    const uploadProgress = document.getElementById('photo-upload-progress');
    const uploadStatus = document.getElementById('photo-upload-status');

    // Drag-and-drop highlight
    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.classList.add('border-primary', 'bg-primary-50/30');
    });
    dropzone.addEventListener('dragleave', () => {
        dropzone.classList.remove('border-primary', 'bg-primary-50/30');
    });
    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.classList.remove('border-primary', 'bg-primary-50/30');
        if (e.dataTransfer.files.length > 0) {
            handleFiles(e.dataTransfer.files);
        }
    });

    // Click to select
    photoInput.addEventListener('change', () => {
        if (photoInput.files.length > 0) {
            handleFiles(photoInput.files);
            photoInput.value = ''; // Reset para permitir re-seleção
        }
    });

    function handleFiles(files) {
        const remaining = 5 - uploadedPhotos.length;
        const toAdd = Math.min(files.length, remaining);

        if (toAdd === 0) {
            showToast('Máximo de 5 fotos atingido', 'warning');
            return;
        }

        for (let i = 0; i < toAdd; i++) {
            const file = files[i];

            // Validate size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                showToast(`"${file.name}" excede 5MB`, 'warning');
                continue;
            }

            // Validate type
            if (!['image/jpeg', 'image/png', 'image/webp', 'image/gif'].includes(file.type)) {
                showToast(`"${file.name}" não é um formato suportado`, 'warning');
                continue;
            }

            const preview = URL.createObjectURL(file);
            const photoEntry = { file, preview, url: null, uploading: false };
            uploadedPhotos.push(photoEntry);
            renderPhotoPreviews();
        }
    }

    function renderPhotoPreviews() {
        if (uploadedPhotos.length === 0) {
            previewsContainer.innerHTML = '';
            return;
        }

        previewsContainer.innerHTML = uploadedPhotos.map((p, i) => `
            <div class="relative group aspect-square rounded-lg overflow-hidden border border-border bg-surface">
                <img src="${p.preview}" alt="Foto ${i + 1}" class="w-full h-full object-cover">
                ${p.uploading ? `
                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                        <svg class="w-6 h-6 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </div>
                ` : ''}
                <button onclick="removePhoto(${i})" type="button"
                    class="absolute top-1 right-1 w-6 h-6 bg-black/60 hover:bg-black/80 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                ${p.url ? '<div class="absolute bottom-1 right-1 w-5 h-5 bg-success rounded-full flex items-center justify-center"><svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg></div>' : ''}
            </div>
        `).join('');

        // Add button if has space
        if (uploadedPhotos.length < 5) {
            previewsContainer.insertAdjacentHTML('beforeend', `
                <div onclick="document.getElementById('photo-input').click()"
                     class="aspect-square rounded-lg border-2 border-dashed border-border hover:border-primary/40 hover:bg-primary-50/20 flex flex-col items-center justify-center gap-1 cursor-pointer transition-all duration-200 group">
                    <svg class="w-6 h-6 text-ink-muted group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span class="text-xs text-ink-muted">Adicionar</span>
                </div>
            `);
        }
    }

    function removePhoto(index) {
        const photo = uploadedPhotos[index];
        if (photo.preview) URL.revokeObjectURL(photo.preview);
        uploadedPhotos.splice(index, 1);
        renderPhotoPreviews();
    }

    async function uploadPhotos() {
        const pending = uploadedPhotos.filter(p => !p.url && !p.uploading);
        if (pending.length === 0) return [];

        isUploading = true;
        uploadProgress.classList.remove('hidden');

        const formData = new FormData();
        pending.forEach(p => {
            p.uploading = true;
            formData.append('photos', p.file);
        });
        renderPhotoPreviews();

        try {
            uploadStatus.textContent = `Enviando ${pending.length} foto(s)...`;
            const resp = await fetch('/api/v1/public/upload', { method: 'POST', body: formData });
            const data = await resp.json();

            if (!resp.ok) throw new Error(data.message || 'Erro no upload');

            // Assign URLs to pending photos
            data.data.files.forEach((f, i) => {
                if (pending[i]) {
                    pending[i].url = f.url;
                    pending[i].uploading = false;
                }
            });

            renderPhotoPreviews();
            return data.data.files.map(f => f.url);

        } catch (err) {
            // Mark as failed
            pending.forEach(p => { p.uploading = false; });
            renderPhotoPreviews();
            showToast('Erro ao enviar fotos: ' + err.message, 'error');
            return [];
        } finally {
            isUploading = false;
            uploadProgress.classList.add('hidden');
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // Step 2 — Validation
    // ═══════════════════════════════════════════════════════════════
    function validateStep2() {
        let valid = true;
        const description = document.getElementById('lead-description').value.trim();

        clearErrors();

        if (!description || description.length < 5) {
            showError('lead-description', 'Descreva brevemente o que precisa (mín. 5 caracteres)');
            valid = false;
        }

        return valid;
    }

    // ═══════════════════════════════════════════════════════════════
    // Step 3 — Validation
    // ═══════════════════════════════════════════════════════════════
    function validateStep3() {
        let valid = true;
        clearErrors();

        const name = document.getElementById('lead-name').value.trim();
        const phone = document.getElementById('lead-phone').value.trim();
        const consentMarketing = document.getElementById('lgpd-consent-marketing').checked;
        const consentTerms = document.getElementById('lgpd-consent-terms').checked;

        if (!name || name.length < 2) {
            showError('lead-name', 'Informe seu nome');
            valid = false;
        }

        if (!phone || phone.replace(/\D/g, '').length < 10) {
            showError('lead-phone', 'Informe um telefone válido com DDD');
            valid = false;
        }

        if (!consentMarketing) {
            showError('lgpd-consent-marketing', 'É necessário autorizar o contato');
            valid = false;
        }

        if (!consentTerms) {
            showError('lgpd-consent-terms', 'É necessário aceitar os termos');
            valid = false;
        }

        return valid;
    }

    // ═══════════════════════════════════════════════════════════════
    // Summary
    // ═══════════════════════════════════════════════════════════════
    function updateSummary() {
        const svc = selectedService;
        document.getElementById('summary-service').textContent = svc ? (svc.name + (svc.category ? ` (${svc.category})` : '')) : '—';
        const desc = document.getElementById('lead-description').value.trim();
        document.getElementById('summary-description').textContent = desc || '—';

        const dateVal = document.getElementById('lead-date').value;
        const timeVal = document.getElementById('lead-time').value;
        if (dateVal) {
            const d = new Date(dateVal + 'T12:00:00');
            document.getElementById('summary-date').textContent =
                d.toLocaleDateString('pt-BR') + (timeVal ? ` às ${timeVal.slice(0, 5)}h` : '');
        } else {
            document.getElementById('summary-date').textContent = timeVal ? `${timeVal.slice(0, 5)}h` : '—';
        }

        const addressParts = [
            document.getElementById('lead-address').value.trim(),
            document.getElementById('lead-city').value.trim(),
            document.getElementById('lead-state').value,
        ].filter(Boolean);
        document.getElementById('summary-address').textContent = addressParts.length > 0 ? addressParts.join(', ') : '—';
    }

    // ═══════════════════════════════════════════════════════════════
    // Submit
    // ═══════════════════════════════════════════════════════════════
    async function submitLead() {
        const btn = document.getElementById('btn-next');
        const spinner = document.getElementById('btn-spinner');
        const btnText = document.getElementById('btn-text');

        btn.disabled = true;
        spinner.classList.remove('hidden');
        btnText.textContent = 'Enviando...';

        try {
            // ── Step 1: Upload photos (if any) ────────────────
            let photoUrls = [];
            if (uploadedPhotos.length > 0) {
                btnText.textContent = 'Enviando fotos...';
                photoUrls = await uploadPhotos();
            }

            // ── Step 2: Submit lead ───────────────────────────
            btnText.textContent = 'Finalizando...';
            const payload = {
                service_name: selectedService.name,
                description: document.getElementById('lead-description').value.trim(),
                desired_date: document.getElementById('lead-date').value || null,
                desired_time: document.getElementById('lead-time').value || null,
                zipcode: document.getElementById('lead-zipcode').value.trim() || null,
                address: document.getElementById('lead-address').value.trim() || null,
                city: document.getElementById('lead-city').value.trim() || null,
                state: document.getElementById('lead-state').value || null,
                reference: document.getElementById('lead-reference').value.trim() || null,
                photo_urls: photoUrls.length > 0 ? JSON.stringify(photoUrls) : null,
                customer_name: document.getElementById('lead-name').value.trim(),
                customer_phone: document.getElementById('lead-phone').value.trim(),
                customer_email: document.getElementById('lead-email').value.trim() || null,
                lgpd_consent_marketing: document.getElementById('lgpd-consent-marketing').checked,
                lgpd_consent_terms: document.getElementById('lgpd-consent-terms').checked,
            };

            const resp = await fetch('/api/v1/public/leads', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });

            const data = await resp.json();

            if (!resp.ok) {
                throw new Error(data.message || 'Erro ao enviar solicitação');
            }

            // Success!
            document.getElementById('lead-protocol').textContent = `#${data.data.id}`;
            document.getElementById('success-service-name').textContent = selectedService?.name || '—';
            goToStep(4);

        } catch (err) {
            showToast(err.message, 'error');
            btn.disabled = false;
            spinner.classList.add('hidden');
            btnText.textContent = 'Solicitar Orçamento';
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // Helpers
    // ═══════════════════════════════════════════════════════════════
    function showError(fieldId, message) {
        const field = document.getElementById(fieldId);
        if (!field) return;
        field.classList.add('error');
        const errorEl = document.getElementById(`error-${fieldId}`) || (() => {
            const el = document.createElement('div');
            el.id = `error-${fieldId}`;
            el.className = 'error-message';
            field.parentNode.appendChild(el);
            return el;
        })();
        errorEl.textContent = message;
        errorEl.classList.add('visible');
    }

    function clearErrors() {
        document.querySelectorAll('.input-field.error').forEach(el => el.classList.remove('error'));
        document.querySelectorAll('.error-message.visible').forEach(el => el.classList.remove('visible'));
    }

    // ── Phone Mask ────────────────────────────────────────
    document.getElementById('lead-phone').addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        if (value.length > 11) value = value.slice(0, 11);
        if (value.length > 2) {
            value = `(${value.slice(0, 2)}) ${value.slice(2)}`;
        }
        if (value.length > 10) {
            value = `${value.slice(0, 10)}-${value.slice(10)}`;
        }
        this.value = value;
    });

    // ── CEP Mask ──────────────────────────────────────────
    document.getElementById('lead-zipcode').addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        if (value.length > 8) value = value.slice(0, 8);
        if (value.length > 5) {
            value = `${value.slice(0, 5)}-${value.slice(5)}`;
        }
        this.value = value;
    });

    // ── Use passed params ─────────────────────────────────
    const urlParams = new URLSearchParams(window.location.search);
    const preselectedService = urlParams.get('servico');
    if (preselectedService) {
        selectService(preselectedService, '');
        document.getElementById('wizard-search-input').value = preselectedService;
        wizardSearch(preselectedService);
    }

    // ── Init ──────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', () => {
        loadWizardCategories();
        setupWizardSearch();
        updateNextButton();
    });
</script>
