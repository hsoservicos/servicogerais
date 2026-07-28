// ═══════════════════════════════════════════════════════════════
// middlewares/requestId.middleware.js — Correlation ID (Rastreio)
// ═══════════════════════════════════════════════════════════════
// Gera ou propaga correlation ID para tracing de requisições

const { v4: uuidv4 } = require('uuid');

function requestId(req, res, next) {
  // Propaga ID do Nginx ou gera novo
  const correlationId = req.headers['x-request-id'] || uuidv4();

  req.correlationId = correlationId;
  res.setHeader('X-Request-ID', correlationId);

  next();
}

module.exports = { requestId };
