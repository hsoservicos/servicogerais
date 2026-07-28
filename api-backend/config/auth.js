// ═══════════════════════════════════════════════════════════════
// config/auth.js — JWT & Autenticação (ServiceSaaS)
// ═══════════════════════════════════════════════════════════════

require('dotenv').config();

module.exports = {
  jwt: {
    secret: process.env.JWT_SECRET || 'change-me-in-production',
    expiresIn: process.env.JWT_EXPIRES_IN || '24h',
    algorithm: 'HS256',
  },
  refreshToken: {
    secret: process.env.JWT_REFRESH_SECRET || 'change-me-refresh-secret',
    expiresIn: '30d',
  },
  password: {
    saltRounds: 12,
    minLength: 8,
  },
  rateLimit: {
    windowMs: 15 * 60 * 1000, // 15 minutos
    max: 100, // limite por IP
    authMax: 5, // limite de tentativas de login
  },
  cors: {
    origin: process.env.CORS_ORIGIN || '*',
    methods: ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'],
    allowedHeaders: ['Content-Type', 'Authorization', 'X-Request-ID'],
    credentials: true,
  },
};
