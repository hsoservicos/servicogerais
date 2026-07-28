// ═══════════════════════════════════════════════════════════════
// modules/admin/admin.middleware.js — Admin Auth & Audit
// ═══════════════════════════════════════════════════════════════

const { query } = require('../../config/database');

/**
 * Middleware de autorização: apenas super_admin pode acessar
 * Deve ser usado APÓS o middleware authenticate
 */
function adminAuth(req, res, next) {
  if (!req.user) {
    return res.status(401).json({
      error: 'ERR_UNAUTHORIZED',
      message: 'Autenticação necessária',
      correlationId: req.correlationId,
    });
  }

  if (req.user.role !== 'super_admin') {
    return res.status(403).json({
      error: 'ERR_FORBIDDEN',
      message: 'Acesso restrito a administradores da plataforma',
      correlationId: req.correlationId,
    });
  }

  // Super admin não tem tenant filter — vê tudo
  req.tenantId = null;
  req.tenantFilter = '1=1';

  next();
}

/**
 * Middleware de auditoria: registra ação do admin no log
 * Uso: router.post('/tenants/:id/suspend', adminAuth, auditLog('suspend_tenant'), controller)
 */
function auditLog(action) {
  return async (req, res, next) => {
    // Salva referência para usar após a resposta
    const originalJson = res.json.bind(res);

    res.json = function (body) {
      // Se a requisição foi bem-sucedida (2xx), registra no audit log
      if (res.statusCode >= 200 && res.statusCode < 300) {
        const targetType = req.params?.id ? 'tenant' : req.baseUrl?.split('/').pop();
        const targetId = req.params?.id || body?.id || null;

        query(
          `INSERT INTO admin_audit_log (admin_user_id, action, target_type, target_id, details, ip_address)
           VALUES (?, ?, ?, ?, ?, ?)`,
          [
            req.user.id,
            action || `${req.method}_${targetType || 'unknown'}`,
            targetType || 'unknown',
            targetId,
            JSON.stringify({
              method: req.method,
              path: req.originalUrl,
              body: req.method !== 'GET' ? req.body : undefined,
              result: body,
            }),
            req.ip || req.headers['x-forwarded-for'] || null,
          ]
        ).catch(err => console.error('[ADMIN AUDIT] ❌ Erro ao registrar:', err.message));
      }

      return originalJson(body);
    };

    next();
  };
}

module.exports = { adminAuth, auditLog };
