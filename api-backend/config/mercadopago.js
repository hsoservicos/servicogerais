// ═══════════════════════════════════════════════════════════════
// config/mercadopago.js — Mercado Pago Client Configuration
// ═══════════════════════════════════════════════════════════════
// Story 5.1 — Cria e exporta uma instância compartilhada do
// cliente Mercado Pago (NUNCA criar nova instância por request)
//
// SDK: mercadopago v2.x (client-based architecture)
// Docs: https://github.com/mercadopago/sdk-nodejs
// ═══════════════════════════════════════════════════════════════

const { MercadoPagoConfig } = require('mercadopago');

// ── Configuração do Cliente MP ───────────────────────────
function createMercadoPagoClient() {
  const accessToken = process.env.MP_ACCESS_TOKEN;

  if (!accessToken) {
    console.warn('[MP] ⚠️  MP_ACCESS_TOKEN não configurado. Mercado Pago em modo degradado.');
    return null;
  }

  const client = new MercadoPagoConfig({
    accessToken,
    options: {
      timeout: Number(process.env.MP_TIMEOUT) || 5000,
      // Idempotency key é gerada por request (não global)
    },
  });

  console.log('[MP] ✅ Cliente Mercado Pago inicializado.');
  return client;
}

// Instância compartilhada (singleton) — criada na inicialização
const mercadopagoClient = createMercadoPagoClient();

// ── Validação de Disponibilidade ─────────────────────────
function isMercadoPagoAvailable() {
  return mercadopagoClient !== null;
}

// ── Status do MP ─────────────────────────────────────────
function getMercadoPagoStatus() {
  if (!isMercadoPagoAvailable()) {
    return { configured: false, status: 'degraded' };
  }
  return {
    configured: true,
    status: 'ready',
    timeout: Number(process.env.MP_TIMEOUT) || 5000,
  };
}

module.exports = {
  mercadopagoClient,
  isMercadoPagoAvailable,
  getMercadoPagoStatus,
};
