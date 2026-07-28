// ═══════════════════════════════════════════════════════════════
// modules/transactions/transactions.routes.js — Transactions Routes
// ═══════════════════════════════════════════════════════════════
// Story 4.4 — FR-043: Histórico de Transações do prestador.
// Rotas protegidas com tenant isolation.
// ═══════════════════════════════════════════════════════════════

const { Router } = require('express');
const controller = require('./transactions.controller');
const { authenticate } = require('../../middlewares/auth.middleware');
const { injectTenant } = require('../../middlewares/tenant.middleware');

const router = Router();

router.use(authenticate);
router.use(injectTenant);

// GET /api/v1/transactions — Listar transações do tenant
router.get('/', controller.list);

module.exports = router;
