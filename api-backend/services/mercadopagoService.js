// ═══════════════════════════════════════════════════════════════
// services/mercadopagoService.js — Mercado Pago Service Wrapper
// ═══════════════════════════════════════════════════════════════
// Story 5.1 — Wrapper que expõe funções seguras de negócio
// para criar preferências, consultar pagamentos e estornar.
//
// 🔒 Todas as funções validam disponibilidade do MP antes de
//    chamar a API externa.
// 🔄 Retry com exponential backoff para erros 429 e 500.
// ═══════════════════════════════════════════════════════════════

const crypto = require('crypto');
const {
  Preference,
  Payment,
  PaymentRefund,
} = require('mercadopago');

const { mercadopagoClient, isMercadoPagoAvailable } = require('../config/mercadopago');

// ── Helper: Exponential Backoff ───────────────────────────
async function withRetry(fn, maxRetries = 3) {
  let lastError;
  for (let attempt = 0; attempt < maxRetries; attempt++) {
    try {
      return await fn();
    } catch (err) {
      lastError = err;
      // Só retry para 429 (rate limit) e 500+ (servidor)
      if (err.status !== 429 && err.status < 500) {
        throw err;
      }
      if (attempt < maxRetries - 1) {
        const delay = Math.pow(2, attempt) * 200 + Math.random() * 200;
        console.warn(`[MP] ⚠️  Retry ${attempt + 1}/${maxRetries} após ${Math.round(delay)}ms (status ${err.status})`);
        await new Promise(resolve => setTimeout(resolve, delay));
      }
    }
  }
  throw lastError;
}

// ── Helper: Gerar Idempotency Key ─────────────────────────
function generateIdempotencyKey(prefix) {
  const unique = `${prefix}-${Date.now()}-${crypto.randomBytes(4).toString('hex')}`;
  return crypto.createHash('sha256').update(unique).digest('hex');
}

// ── Validação de Disponibilidade ─────────────────────────
function checkAvailable() {
  if (!isMercadoPagoAvailable()) {
    const error = new Error('Mercado Pago não configurado');
    error.code = 'ERR_MP_NOT_CONFIGURED';
    error.status = 503;
    throw error;
  }
}

// ═══════════════════════════════════════════════════════════════
// 1. Criar Preferência de Pagamento (Story 5.2)
// ═══════════════════════════════════════════════════════════════
async function createPreference({ items, payer, externalReference, notificationUrl, metadata, paymentMethods }) {
  checkAvailable();
  const preference = new Preference(mercadopagoClient);

  const body = {
    items: items.map(item => ({
      id: item.id || null,
      title: item.title,
      description: item.description || null,
      quantity: item.quantity,
      unit_price: item.unit_price,
      currency_id: 'BRL',
    })),
    payer: {
      name: payer.name,
      email: payer.email,
    },
    external_reference: externalReference,
    notification_url: notificationUrl,
    expires: true,
    expiration_date_from: new Date().toISOString(),
    expiration_date_to: new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString(),
    metadata: metadata || {},
  };

  // ── Adicionar restrições de método de pagamento ────────
  // Ex: Pix-only (Story 5.4), credit_card-only, etc.
  if (paymentMethods) {
    body.payment_methods = paymentMethods;
  }

  const idempotencyKey = generateIdempotencyKey(`pref-${externalReference}`);

  return withRetry(() =>
    preference.create({
      body,
      requestOptions: { idempotencyKey },
    })
  );
}

// ═══════════════════════════════════════════════════════════════
// 2. Consultar Pagamento (Story 5.3)
// ═══════════════════════════════════════════════════════════════
async function getPayment(paymentId) {
  checkAvailable();
  const payment = new Payment(mercadopagoClient);

  return withRetry(() => payment.get({ id: paymentId }));
}

// ═══════════════════════════════════════════════════════════════
// 3. Estornar Pagamento (Story 5.5)
// ═══════════════════════════════════════════════════════════════
async function refundPayment(paymentId, amount = null) {
  checkAvailable();
  const refund = new PaymentRefund(mercadopagoClient);

  const body = {};
  if (amount !== null) {
    body.amount = amount; // Estorno parcial
  }

  const idempotencyKey = generateIdempotencyKey(`refund-${paymentId}`);

  return withRetry(() =>
    refund.create({
      payment_id: paymentId,
      body: Object.keys(body).length > 0 ? body : undefined,
      requestOptions: { idempotencyKey },
    })
  );
}

// ═══════════════════════════════════════════════════════════════
// 4. Validação de Assinatura de Webhook (Story 5.3)
// ═══════════════════════════════════════════════════════════════
function validateWebhookSignature(req) {
  const signature = req.headers['x-signature'];
  const dataId = req.query['data.id'];

  if (!signature || !dataId) {
    return false;
  }

  // Parse: "ts=1234567890,v1=abcdef123456..."
  const parts = {};
  signature.split(',').forEach(pair => {
    const [key, value] = pair.split('=');
    parts[key.trim()] = value.trim();
  });

  if (!parts.ts || !parts.v1) return false;

  const secret = process.env.MP_WEBHOOK_SECRET;
  if (!secret) {
    console.warn('[MP] ⚠️  MP_WEBHOOK_SECRET não configurado. Webhooks não podem ser validados.');
    return false;
  }

  // ── Montar manifesto para HMAC ────────────────────────
  // Formato moderno (v2): "id:{dataId};request-id:{x-request-id};ts:{ts};"
  // O header x-request-id é preferencial mas nem sempre presente.
  // Fallback para "id:{dataId};ts:{ts};" (formato IPN legado).
  const requestId = req.headers['x-request-id'];
  let manifest;

  if (requestId) {
    manifest = `id:${dataId};request-id:${requestId};ts:${parts.ts};`;
  } else {
    // Fallback para webhooks sem x-request-id (ex: IPN legado)
    const topic = req.query['topic'] || req.body?.topic || 'payment';
    const state = req.query['state'] || req.body?.action || 'unknown';
    manifest = `id:${dataId};topic:${topic};state:${state};`;
  }

  const hash = crypto
    .createHmac('sha256', secret)
    .update(manifest)
    .digest('hex');

  // Comparação em tempo constante (previne timing attack)
  if (hash.length !== parts.v1.length) return false;
  return crypto.timingSafeEqual(Buffer.from(hash), Buffer.from(parts.v1));
}

module.exports = {
  createPreference,
  getPayment,
  refundPayment,
  validateWebhookSignature,
};
