const { query } = require('../../config/database');
const { formatCurrency, formatDate } = require('./admin.dashboard.controller');

async function listTenants(req, res, next) {
  try {
    const { search, status, page = 1, perPage = 20 } = req.query;
    const offset = (parseInt(page, 10) - 1) * parseInt(perPage, 10);
    const limit = parseInt(perPage, 10);
    let whereClause = '1=1'; const params = [];
    if (search && search.length >= 2) {
      whereClause += ' AND (t.name LIKE ? OR t.slug LIKE ? OR t.document_cpf LIKE ? OR t.document_cnpj LIKE ?)';
      const term = `%${search}%`; params.push(term, term, term, term);
    }
    if (status === 'active') whereClause += ' AND t.active = TRUE';
    else if (status === 'suspended') whereClause += ' AND t.active = FALSE';

    const [countRow] = await query(`SELECT COUNT(*) as total FROM tenants t WHERE ${whereClause}`, params.length > 0 ? params : undefined);
    const tenants = await query(
      `SELECT t.*, (SELECT COUNT(*) FROM users u WHERE u.tenant_id = t.id AND u.active = TRUE) as user_count,
       (SELECT COUNT(*) FROM clients c WHERE c.tenant_id = t.id AND c.active = TRUE) as client_count,
       (SELECT COUNT(*) FROM proposals p WHERE p.tenant_id = t.id) as proposal_count,
       (SELECT COALESCE(SUM(amount), 0) FROM transactions tx WHERE tx.tenant_id = t.id AND tx.status = 'completed') as total_revenue
       FROM tenants t WHERE ${whereClause} ORDER BY t.created_at DESC LIMIT ? OFFSET ?`,
      params.length > 0 ? [...params, limit, offset] : [limit, offset]
    );
    res.json({
      tenants: tenants.map(t => ({ ...t, total_revenue: formatCurrency(t.total_revenue), created_at: formatDate(t.created_at) })),
      pagination: { page: parseInt(page, 10), perPage: limit, total: countRow?.total || 0, totalPages: Math.ceil((countRow?.total || 0) / limit) },
      correlationId: req.correlationId,
    });
  } catch (err) { next(err); }
}

async function getTenant(req, res, next) { /* kept in original for brevity — same as before */ return require('./admin.controller').getTenant(req, res, next); }

async function updateTenant(req, res, next) {
  try {
    const { id } = req.params;
    const { name, phone, whatsapp, plan, settings } = req.body;
    if (!name && !phone && !whatsapp && !plan && !settings)
      return res.status(400).json({ error: 'ERR_VALIDATION', message: 'Forneça ao menos um campo para atualizar', correlationId: req.correlationId });
    const updates = []; const params = [];
    if (name) { updates.push('name = ?'); params.push(name); }
    if (phone) { updates.push('phone = ?'); params.push(phone); }
    if (whatsapp) { updates.push('whatsapp = ?'); params.push(whatsapp); }
    if (plan) {
      if (!['free', 'basic', 'pro', 'enterprise'].includes(plan))
        return res.status(400).json({ error: 'ERR_VALIDATION', message: 'Plano inválido', correlationId: req.correlationId });
      updates.push('plan = ?'); params.push(plan);
    }
    if (settings) { updates.push('settings = ?'); params.push(JSON.stringify(settings)); }
    params.push(id);
    await query(`UPDATE tenants SET ${updates.join(', ')} WHERE id = ?`, params);
    res.json({ message: 'Tenant atualizado com sucesso', correlationId: req.correlationId });
  } catch (err) { next(err); }
}

async function toggleTenantStatus(req, res, next) {
  try {
    const { id } = req.params;
    const tenants = await query('SELECT id, name, active FROM tenants WHERE id = ?', [id]);
    if (tenants.length === 0) return res.status(404).json({ error: 'ERR_NOT_FOUND', message: 'Tenant não encontrado', correlationId: req.correlationId });
    const tenant = tenants[0];
    const newStatus = !tenant.active;
    await query('UPDATE tenants SET active = ? WHERE id = ?', [newStatus, id]);
    try { await query(`INSERT INTO admin_audit_log (admin_user_id, action, target_type, target_id, details, ip_address) VALUES (?,?,?,?,?,?)`,
      [req.user.id, newStatus ? 'activate_tenant' : 'suspend_tenant', 'tenant', id, JSON.stringify({ tenant_name: tenant.name }), req.ip || null]); } catch (_) { }
    res.json({ message: newStatus ? 'Tenant reativado' : 'Tenant suspenso', tenant: { id, active: newStatus }, correlationId: req.correlationId });
  } catch (err) { next(err); }
}

module.exports = { listTenants, getTenant, updateTenant, toggleTenantStatus };
