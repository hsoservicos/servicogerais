// ═══════════════════════════════════════════════════════════════
// modules/clients/clients.routes.js — Clients CRUD Routes
// ═══════════════════════════════════════════════════════════════
// Todas as rotas são protegidas: authenticate + injectTenant

const { Router } = require('express');
const controller = require('./clients.controller');
const { authenticate } = require('../../middlewares/auth.middleware');
const { injectTenant } = require('../../middlewares/tenant.middleware');

const router = Router();

// ── Todas as rotas exigem autenticação + tenant ──────────
router.use(authenticate);
router.use(injectTenant);

// ── CRUD ─────────────────────────────────────────────────
router.get('/', controller.list);       // GET  /clients?search=&page=&perPage=
router.post('/', controller.create);    // POST /clients
router.get('/:id', controller.read);    // GET  /clients/:id
router.put('/:id', controller.update);  // PUT  /clients/:id
router.delete('/:id', controller.remove); // DELETE /clients/:id

module.exports = router;
