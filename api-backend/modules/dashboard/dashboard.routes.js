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

module.exports = router;
