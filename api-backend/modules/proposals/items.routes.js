// ═══════════════════════════════════════════════════════════════
// modules/proposals/items.routes.js — Proposal Items CRUD Routes
// ═══════════════════════════════════════════════════════════════
// Rotas aninhadas em /proposals/:proposalId/items
// Herdam authenticate + injectTenant do router pai (proposals.routes.js)

const { Router } = require('express');
const controller = require('./items.controller');

const router = Router({ mergeParams: true });

// ── Items CRUD ───────────────────────────────────────────
router.get('/', controller.list);              // GET  /proposals/:proposalId/items
router.post('/', controller.create);           // POST /proposals/:proposalId/items
router.put('/:id', controller.update);         // PUT  /proposals/:proposalId/items/:id
router.delete('/:id', controller.remove);      // DELETE /proposals/:proposalId/items/:id

module.exports = router;
