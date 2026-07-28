// ═══════════════════════════════════════════════════════════════
// middlewares/tenant.middleware.js — Multi-Tenant Isolation
// ═══════════════════════════════════════════════════════════════
// Anexa tenant_id à requisição e adiciona ao escopo de queries
// Bypass automático para super_admin

const TENANT_TABLES = [
  'clients',
  'proposals',
  'services',
  'categories',
  'transactions',
];

function injectTenant(req, res, next) {
  // Super admin bypass — não filtra por tenant
  if (req.user?.isAdmin) {
    req.tenantId = null;
    req.tenantFilter = '1=1';
    return next();
  }

  const tenantId = req.user?.tenantId;

  if (!tenantId) {
    return res.status(403).json({
      error: 'ERR_TENANT_REQUIRED',
      message: 'Tenant não identificado',
      correlationId: req.correlationId,
    });
  }

  req.tenantId = tenantId;
  req.tenantFilter = `tenant_id = ${mysqlEscape(tenantId)}`;

  next();
}

function mysqlEscape(value) {
  // Sanitização básica para evitar SQL injection em filters
  if (typeof value === 'number') return value;
  return `'${String(value).replace(/['\\]/g, '\\$&')}'`;
}

// ── Middleware para rotas de admin (sem tenant filter) ────
function adminBypass(req, res, next) {
  if (req.user?.isAdmin) {
    req.tenantId = null;
    req.tenantFilter = '1=1';
  }
  next();
}

module.exports = { injectTenant, adminBypass };
