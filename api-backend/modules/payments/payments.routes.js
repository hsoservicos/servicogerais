// ═══════════════════════════════════════════════════════════════
// modules/payments/payments.routes.js — Mercado Pago Routes
// ═══════════════════════════════════════════════════════════════
// Story 5.1 — Rotas de pagamento com separação de autenticação.
//
// 🔓 Webhook IPN → SEM auth middleware (validação via HMAC)
// 🔐 Demais rotas → auth JWT + tenant obrigatório
// ═══════════════════════════════════════════════════════════════

const { Router } = require('express');
const controller = require('./payments.controller');
const { authenticate } = require('../../middlewares/auth.middleware');
const { injectTenant } = require('../../middlewares/tenant.middleware');

// ── Router Público (webhook) ─────────────────────────────
// O webhook do Mercado Pago NÃO envia JWT — a segurança é
// feita via validação HMAC da assinatura x-signature.
const publicRouter = Router();

// POST /api/v1/payments/webhook — Notificação IPN do MP
publicRouter.post('/webhook', controller.handleWebhook);

// ── Router Protegido (auth JWT + tenant) ─────────────────
const protectedRouter = Router();
protectedRouter.use(authenticate);
protectedRouter.use(injectTenant);

// POST /api/v1/payments/preference — Criar preferência de pagamento
protectedRouter.post('/preference', controller.createPreference);

// GET /api/v1/payments/:id — Consultar status do pagamento
protectedRouter.get('/:id', controller.getPayment);

// POST /api/v1/payments/:id/refund — Estornar pagamento
protectedRouter.post('/:id/refund', controller.refundPayment);

// ── Merge: rotas públicas + protegidas no mesmo path ────
const router = Router();
router.use(publicRouter);
router.use(protectedRouter);

module.exports = router;
