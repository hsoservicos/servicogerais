// ═══════════════════════════════════════════════════════════════
// modules/proposals/proposals.routes.js — Proposals CRUD Routes
// ═══════════════════════════════════════════════════════════════
// Todas as rotas são protegidas: authenticate + injectTenant

const { Router } = require('express');
const controller = require('./proposals.controller');
const itemsRouter = require('./items.routes');
const { authenticate } = require('../../middlewares/auth.middleware');
const { injectTenant } = require('../../middlewares/tenant.middleware');

const router = Router();

// ── Todas as rotas exigem autenticação + tenant ──────────
router.use(authenticate);
router.use(injectTenant);

// ── CRUD Propostas ───────────────────────────────────────
router.get('/', controller.list);              // GET  /proposals?search=&status=&client_id=&page=
router.post('/', controller.create);           // POST /proposals
router.get('/:id', controller.read);           // GET  /proposals/:id  (inclui itens)
router.put('/:id', controller.update);         // PUT  /proposals/:id
router.patch('/:id/status', controller.updateStatus); // PATCH /proposals/:id/status
router.delete('/:id', controller.remove);      // DELETE /proposals/:id

// ── Itens da Proposta (sub-rotas) ───────────────────────
// Rotas disponíveis em /proposals/:proposalId/items
router.use('/:proposalId/items', itemsRouter);

module.exports = router;
