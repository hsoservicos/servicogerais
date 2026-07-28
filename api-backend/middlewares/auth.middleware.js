// ═══════════════════════════════════════════════════════════════
// middlewares/auth.middleware.js — JWT Authentication
// ═══════════════════════════════════════════════════════════════
// Extrai JWT do header Authorization: Bearer <token>
// Anexa: req.user = { id, tenant_id, role, email }

const jwt = require('jsonwebtoken');
const { jwt: jwtConfig } = require('../config/auth');

function authenticate(req, res, next) {
  try {
    const authHeader = req.headers.authorization;

    if (!authHeader) {
      return res.status(401).json({
        error: 'ERR_UNAUTHORIZED',
        message: 'Token de autenticação não fornecido',
        correlationId: req.correlationId,
      });
    }

    const parts = authHeader.split(' ');
    if (parts.length !== 2 || parts[0] !== 'Bearer') {
      return res.status(401).json({
        error: 'ERR_INVALID_TOKEN_FORMAT',
        message: 'Formato do token inválido. Use: Authorization: Bearer <token>',
        correlationId: req.correlationId,
      });
    }

    const token = parts[1];
    const decoded = jwt.verify(token, jwtConfig.secret, {
      algorithms: [jwtConfig.algorithm],
    });

    req.user = {
      id: decoded.sub || decoded.id,
      tenantId: decoded.tenant_id,
      role: decoded.role || 'admin',
      email: decoded.email,
      isAdmin: decoded.role === 'super_admin',
    };

    next();
  } catch (err) {
    if (err.name === 'TokenExpiredError') {
      return res.status(401).json({
        error: 'ERR_TOKEN_EXPIRED',
        message: 'Token expirado. Faça login novamente.',
        correlationId: req.correlationId,
      });
    }

    if (err.name === 'JsonWebTokenError') {
      return res.status(401).json({
        error: 'ERR_INVALID_TOKEN',
        message: 'Token inválido',
        correlationId: req.correlationId,
      });
    }

    console.error('[AUTH] ❌ Erro inesperado:', err.message);
    return res.status(500).json({
      error: 'ERR_INTERNAL',
      message: 'Erro interno de autenticação',
      correlationId: req.correlationId,
    });
  }
}

// ── Role-based Access Control ─────────────────────────────
function authorize(...allowedRoles) {
  return (req, res, next) => {
    if (!req.user) {
      return res.status(401).json({
        error: 'ERR_UNAUTHORIZED',
        message: 'Autenticação necessária',
        correlationId: req.correlationId,
      });
    }

    if (!allowedRoles.includes(req.user.role)) {
      return res.status(403).json({
        error: 'ERR_FORBIDDEN',
        message: 'Acesso não autorizado para este perfil',
        correlationId: req.correlationId,
      });
    }

    next();
  };
}

module.exports = { authenticate, authorize };
