// ═══════════════════════════════════════════════════════════════
// public/js/validation.js — Validação + Máscaras + Busca CEP
// ═══════════════════════════════════════════════════════════════
// Uso:  <script src="/js/validation.js" defer></script>
// Init: AppValidation.init() chamado automaticamente no DOMContentLoaded
//
// data-mask="cpf|cnpj|cep|phone" → aplica máscara ao digitar
// data-cep-target="prefixo-"     → busca CEP via ViaCEP ao sair do campo,
//                                  preenche {prefix}address, {prefix}neighborhood,
//                                  {prefix}city, {prefix}state
// ═══════════════════════════════════════════════════════════════

window.AppValidation = (() => {
  'use strict';

  // ── Máscaras ─────────────────────────────────────────────
  function applyMask(input, maskType) {
    let value = input.value.replace(/\D/g, '');
    const limits = { cpf: 11, cnpj: 14, cep: 8, phone: 11 };
    const max = limits[maskType] || 11;
    if (value.length > max) value = value.slice(0, max);

    let formatted = '';
    switch (maskType) {
      case 'cpf':
        formatted = value
          .replace(/^(\d{3})(\d)/, '$1.$2')
          .replace(/^(\d{3})\.(\d{3})(\d)/, '$1.$2.$3')
          .replace(/\.(\d{3})(\d)/, '.$1-$2')
          .replace(/-(\d{2})\d+?$/, '-$1');
        break;
      case 'cnpj':
        formatted = value
          .replace(/^(\d{2})(\d)/, '$1.$2')
          .replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3')
          .replace(/\.(\d{3})(\d)/, '.$1/$2')
          .replace(/(\d{4})(\d)/, '$1-$2')
          .replace(/-\d{2}\d+?$/, '-$1');
        break;
      case 'cep':
        formatted = value.replace(/^(\d{5})(\d)/, '$1-$2');
        break;
      case 'phone':
        formatted = value
          .replace(/^(\d{2})(\d)/, '($1) $2')
          .replace(/(\d{5})(\d)/, '$1-$2');
        break;
      default:
        formatted = value;
    }
    input.value = formatted;
  }

  // ── Validação CPF (algoritmo dígitos verificadores) ──────
  function validateCPF(cpf) {
    const digits = (cpf || '').replace(/\D/g, '');
    if (digits.length !== 11) return false;
    if (/^(\d)\1{10}$/.test(digits)) return false;

    let sum = 0;
    for (let i = 0; i < 9; i++) sum += parseInt(digits[i]) * (10 - i);
    let rem = (sum * 10) % 11;
    if (rem === 10) rem = 0;
    if (rem !== parseInt(digits[9])) return false;

    sum = 0;
    for (let i = 0; i < 10; i++) sum += parseInt(digits[i]) * (11 - i);
    rem = (sum * 10) % 11;
    if (rem === 10) rem = 0;
    if (rem !== parseInt(digits[10])) return false;

    return true;
  }

  // ── Validação CNPJ (algoritmo dígitos verificadores) ─────
  function validateCNPJ(cnpj) {
    const digits = (cnpj || '').replace(/\D/g, '');
    if (digits.length !== 14) return false;
    if (/^(\d)\1{13}$/.test(digits)) return false;

    const w1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
    let sum = 0;
    for (let i = 0; i < 12; i++) sum += parseInt(digits[i]) * w1[i];
    let rem = sum % 11;
    if (rem < 2) rem = 0; else rem = 11 - rem;
    if (rem !== parseInt(digits[12])) return false;

    const w2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
    sum = 0;
    for (let i = 0; i < 13; i++) sum += parseInt(digits[i]) * w2[i];
    rem = sum % 11;
    if (rem < 2) rem = 0; else rem = 11 - rem;
    if (rem !== parseInt(digits[13])) return false;

    return true;
  }

  // ── Validação E-mail ─────────────────────────────────────
  function validateEmail(email) {
    if (!email) return false;
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  }

  // ── Busca CEP (ViaCEP) ───────────────────────────────────
  async function buscarCEP(cepValue, fields) {
    const digits = (cepValue || '').replace(/\D/g, '');
    if (digits.length !== 8) return;

    try {
      const resp = await fetch(`https://viacep.com.br/ws/${digits}/json/`);
      const data = await resp.json();

      if (data.erro) {
        showToast?.('CEP não encontrado', 'warning');
        return;
      }

      if (fields.logradouro) fields.logradouro.value = data.logradouro || '';
      if (fields.bairro) fields.bairro.value = data.bairro || '';
      if (fields.localidade) fields.localidade.value = data.localidade || '';
      if (fields.uf) {
        if (fields.uf.tagName === 'SELECT') {
          fields.uf.value = data.uf || '';
        } else {
          fields.uf.value = data.uf || '';
        }
      }

      showToast?.('CEP encontrado! Endereço preenchido automaticamente.', 'success');
    } catch (err) {
      showToast?.('Erro ao buscar CEP. Tente novamente.', 'error');
    }
  }

  // ── Inicialização automática ─────────────────────────────
  function init() {
    // Aplicar máscaras via data-mask
    document.querySelectorAll('[data-mask]').forEach(input => {
      input.addEventListener('input', function () {
        applyMask(this, this.dataset.mask);
      });
    });

    // Busca CEP via data-cep-target
    document.querySelectorAll('[data-cep-target]').forEach(input => {
      input.addEventListener('blur', function () {
        const prefix = this.dataset.cepTarget || '';
        const fields = {
          logradouro:
            document.getElementById(prefix + 'address') ||
            document.getElementById(prefix + 'endereco'),
          bairro:
            document.getElementById(prefix + 'neighborhood') ||
            document.getElementById(prefix + 'bairro'),
          localidade:
            document.getElementById(prefix + 'city') ||
            document.getElementById(prefix + 'cidade'),
          uf:
            document.getElementById(prefix + 'state') ||
            document.getElementById(prefix + 'estado') ||
            document.getElementById(prefix + 'uf'),
        };
        buscarCEP(this.value, fields);
      });
    });

    // Validação inline: data-validate="cpf cnpj email"
    document.querySelectorAll('[data-validate]').forEach(input => {
      input.addEventListener('blur', function () {
        const types = this.dataset.validate.split(/\s+/);
        if (!this.value) return;

        let valid = true;
        for (const type of types) {
          if (type === 'cpf' && !validateCPF(this.value)) { valid = false; break; }
          if (type === 'cnpj' && !validateCNPJ(this.value)) { valid = false; break; }
          if (type === 'email' && !validateEmail(this.value)) { valid = false; break; }
        }

        const errId = 'error-' + this.id;
        const validId = 'valid-' + this.id;
        const errEl = document.getElementById(errId);
        const validEl = document.getElementById(validId);

        if (!valid) {
          this.classList.add('border-danger');
          this.classList.remove('border-success');
          if (errEl) errEl.classList.remove('hidden');
          if (validEl) validEl.classList.add('hidden');
        } else {
          this.classList.remove('border-danger');
          this.classList.add('border-success');
          if (errEl) errEl.classList.add('hidden');
          if (validEl) validEl.classList.remove('hidden');
        }
      });

      input.addEventListener('input', function () {
        this.classList.remove('border-danger');
        this.classList.remove('border-success');
        const errId = 'error-' + this.id;
        const validId = 'valid-' + this.id;
        const errEl = document.getElementById(errId);
        const validEl = document.getElementById(validId);
        if (errEl) errEl.classList.add('hidden');
        if (validEl) validEl.classList.add('hidden');
      });
    });
  }

  // Auto-init after DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  return {
    init,
    validateCPF,
    validateCNPJ,
    validateEmail,
    buscarCEP,
    mask: applyMask,
  };
})();
