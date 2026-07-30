const { query } = require('../../config/database');
const { formatDate } = require('./admin.dashboard.controller');

async function listAudit(req, res, next) {
  try {
    const { action, target_type, admin_id, start_date, end_date, page = 1, perPage = 50 } = req.query;
    const offset = (parseInt(page, 10) - 1) * parseInt(perPage, 10);
    const limit = parseInt(perPage, 10);
    let whereClause = '1=1'; const params = [];
    if (action) { whereClause += ' AND a.action = ?'; params.push(action); }
    if (target_type) { whereClause += ' AND a.target_type = ?'; params.push(target_type); }
    if (admin_id) { whereClause += ' AND a.admin_user_id = ?'; params.push(parseInt(admin_id, 10)); }
    if (start_date) { whereClause += ' AND a.created_at >= ?'; params.push(start_date); }
    if (end_date) { whereClause += ' AND a.created_at <= ?'; params.push(end_date + ' 23:59:59'); }

    const [countRow] = await query(`SELECT COUNT(*) as total FROM admin_audit_log a WHERE ${whereClause}`, params.length > 0 ? params : undefined);
    const logs = await query(
      `SELECT a.*, u.name as admin_name, u.email as admin_email FROM admin_audit_log a
       LEFT JOIN users u ON u.id = a.admin_user_id WHERE ${whereClause} ORDER BY a.created_at DESC LIMIT ? OFFSET ?`,
      params.length > 0 ? [...params, limit, offset] : [limit, offset]
    );
    const uniqueActions = await query('SELECT DISTINCT action FROM admin_audit_log ORDER BY action');

    res.json({
      logs: logs.map(log => ({ id: log.id, admin: log.admin_name || `ID ${log.admin_user_id}`, admin_email: log.admin_email, action: log.action, target: { type: log.target_type, id: log.target_id }, details: log.details, ip: log.ip_address, created_at: formatDate(log.created_at) })),
      filters: { actions: uniqueActions.map(a => a.action) },
      pagination: { page: parseInt(page, 10), perPage: limit, total: countRow?.total || 0, totalPages: Math.ceil((countRow?.total || 0) / limit) },
      correlationId: req.correlationId,
    });
  } catch (err) { next(err); }
}

module.exports = { listAudit };
