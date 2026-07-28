// ═══════════════════════════════════════════════════════════════
// modules/public/public.routes.js — Public Routes (Epic 6)
// ═══════════════════════════════════════════════════════════════
// Story 6.1 — Rotas públicas SEM autenticação.
// Consumidas pela Landing Page e busca pública.
// ═══════════════════════════════════════════════════════════════

const { Router } = require('express');
const path = require('path');
const rateLimit = require('express-rate-limit');
const controller = require('./public.controller');
const uploadController = require('./upload.controller');
const publicProposalsController = require('./publicProposals.controller');

const router = Router();

// ── Rate Limiter específico para criação de leads ───────
// Endpoint público sujeito a spam — limite de 5 req/min por IP
const leadLimiter = rateLimit({
  windowMs: 60 * 1000, // 1 minuto
  max: 5,
  message: {
    error: 'ERR_LEAD_RATE_LIMIT',
    message: 'Muitas solicitações. Tente novamente em 1 minuto.',
  },
  standardHeaders: true,
  legacyHeaders: false,
});

// GET /api/v1/public/categories — Categorias disponíveis
router.get('/categories', controller.listCategories);

// GET /api/v1/public/services — Busca pública de serviços
router.get('/services', controller.listServices);

// POST /api/v1/public/leads — Criar lead (Story 6.2 — Wizard 3 passos)
// Rate limit: 5 req/min/IP para evitar spam
router.post('/leads', leadLimiter, controller.createLead);

// POST /api/v1/public/upload — Upload de fotos (Story 6.2 — Wizard Step 2)
// Rate limit: 10 req/min/IP (upload consome mais recursos)
router.post('/upload', rateLimit({ windowMs: 60 * 1000, max: 10 }), uploadController.uploadHandler);

// ── Propostas Públicas (Story 6.3) ──────────────────────
// Endpoints SEM autenticação para cliente visualizar e responder proposta

// GET /api/v1/public/proposals/:token — Visualizar proposta pública
router.get('/proposals/:token', publicProposalsController.getByToken);

// GET /api/v1/public/proposals/:token/payment — Status do pagamento
router.get('/proposals/:token/payment', publicProposalsController.getPaymentStatus);

// POST /api/v1/public/proposals/:token/pay — Criar pagamento Pix (Story 5.4)
router.post('/proposals/:token/pay', publicProposalsController.createPaymentPreference);

// PATCH /api/v1/public/proposals/:token/status — Aprovar/rejeitar proposta
router.patch('/proposals/:token/status', publicProposalsController.updateStatus);

// GET /api/v1/public/uploads/:filename — Servir arquivos estáticos
router.get('/uploads/:filename', (req, res) => {
  const filePath = path.join(__dirname, '..', '..', 'uploads', req.params.filename);
  
  // Security: prevent directory traversal
  if (req.params.filename.includes('..') || req.params.filename.includes('/')) {
    return res.status(403).json({ error: 'ERR_FORBIDDEN', message: 'Acesso negado.' });
  }

  res.sendFile(filePath, (err) => {
    if (err) {
      res.status(404).json({ error: 'ERR_NOT_FOUND', message: 'Arquivo não encontrado.' });
    }
  });
});

module.exports = router;
