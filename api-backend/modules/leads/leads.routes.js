// ═══════════════════════════════════════════════════════════════
// modules/leads/leads.routes.js — Leads Routes (Epic 6 — Story 6.4)
// ═══════════════════════════════════════════════════════════════

const { Router } = require('express');
const controller = require('./leads.controller');
const { authenticate } = require('../../middlewares/auth.middleware');

const router = Router();

// Todas as rotas de leads exigem autenticação
router.use(authenticate);

// GET  /api/v1/leads       — Listar leads
router.get('/', controller.list);

// PATCH /api/v1/leads/:id  — Atualizar status do lead
router.patch('/:id', controller.updateStatus);

module.exports = router;
