// ═══════════════════════════════════════════════════════════════
// modules/dashboard/dashboard.routes.js — Dashboard Routes
// ═══════════════════════════════════════════════════════════════

const { Router } = require('express');
const controller = require('./dashboard.controller');
const { authenticate } = require('../../middlewares/auth.middleware');
const { injectTenant } = require('../../middlewares/tenant.middleware');

const router = Router();

// ── Rota protegida: authenticate + injectTenant ──────────
router.use(authenticate);
router.use(injectTenant);

// GET /api/v1/dashboard
router.get('/', controller.dashboard);

// GET /api/v1/dashboard/chart — Receita mensal (6 meses)
router.get('/chart', controller.chart);

// GET /api/v1/dashboard/followup — Propostas pendentes > 48h
router.get('/followup', controller.followup);

module.exports = router;
