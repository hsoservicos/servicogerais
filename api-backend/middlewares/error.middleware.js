// ═══════════════════════════════════════════════════════════════
// middlewares/error.middleware.js — Error Handler Centralizado
// ═══════════════════════════════════════════════════════════════

const ERROR_CODES = {
  VALIDATION: 'ERR_VALIDATION',
  NOT_FOUND: 'ERR_NOT_FOUND',
  DUPLICATE: 'ERR_DUPLICATE_ENTRY',
  FORBIDDEN: 'ERR_FORBIDDEN',
  UNAUTHORIZED: 'ERR_UNAUTHORIZED',
  INTERNAL: 'ERR_INTERNAL',
  RATE_LIMIT: 'ERR_RATE_LIMIT',
  DEPENDENCY: 'ERR_DEPENDENCY',
};

class AppError extends Error {
  constructor(message, statusCode = 400, code = ERROR_CODES.VALIDATION) {
    super(message);
    this.statusCode = statusCode;
    this.code = code;
    this.isOperational = true;
    Error.captureStackTrace(this, this.constructor);
  }
}

function errorHandler(err, req, res, _next) {
  // Erro operacional conhecido
  if (err.isOperational) {
    return res.status(err.statusCode).json({
      error: err.code,
      message: err.message,
      correlationId: req.correlationId,
      ...(process.env.NODE_ENV === 'development' && { stack: err.stack }),
    });
  }

  // Erro MySQL
  if (err.code === 'ER_DUP_ENTRY') {
    return res.status(409).json({
      error: ERROR_CODES.DUPLICATE,
      message: 'Registro duplicado. Este recurso já existe.',
      correlationId: req.correlationId,
    });
  }

  if (err.code === 'ER_NO_REFERENCED_ROW_2') {
    return res.status(400).json({
      error: ERROR_CODES.DEPENDENCY,
      message: 'Registro referenciado não encontrado.',
      correlationId: req.correlationId,
    });
  }

  // Erro desconhecido — log estruturado
  console.error(JSON.stringify({
    level: 'error',
    timestamp: new Date().toISOString(),
    error: err.message,
    stack: err.stack,
    method: req.method,
    path: req.path,
    correlationId: req.correlationId,
    userId: req.user?.id,
    tenantId: req.user?.tenantId,
  }));

  // Não expor detalhes do erro em produção
  return res.status(500).json({
    error: ERROR_CODES.INTERNAL,
    message: process.env.NODE_ENV === 'development'
      ? err.message
      : 'Erro interno do servidor',
    correlationId: req.correlationId,
  });
}

module.exports = { errorHandler, AppError, ERROR_CODES };
