// ═══════════════════════════════════════════════════════════════
// modules/auth/auth.routes.js — Auth Routes (ServiceSaaS)
// ═══════════════════════════════════════════════════════════════
// Rotas: register, login, me

const { Router } = require('express');
const controller = require('./auth.controller');
const { authenticate } = require('../../middlewares/auth.middleware');
const rateLimit = require('express-rate-limit');
const { rateLimit: rateConfig } = require('../../config/auth');

const router = Router();

// ── Rate Limiter específico para login ───────────────────
const loginLimiter = rateLimit({
  windowMs: rateConfig.windowMs,
  max: rateConfig.authMax,
  message: {
    error: 'ERR_RATE_LIMIT',
    message: 'Muitas tentativas de login. Tente novamente em 15 minutos.',
  },
  standardHeaders: true,
  legacyHeaders: false,
});

// ── Rotas Públicas ───────────────────────────────────────
router.post('/register', controller.register);

router.post('/login', loginLimiter, controller.login);

router.post('/forgot-password', loginLimiter, controller.forgotPassword);

router.post('/reset-password', loginLimiter, controller.resetPassword);

// ── Rotas Protegidas ─────────────────────────────────────
router.get('/me', authenticate, controller.me);

module.exports = router;
