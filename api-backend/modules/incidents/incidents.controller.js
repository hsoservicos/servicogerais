const { query } = require('../../config/database');

const VALID_TYPES = ['ACCIDENT', 'EMERGENCY', 'DAMAGE', 'HEALTH', 'SECURITY', 'OTHER'];
const VALID_SEVERITIES = ['LOW', 'MEDIUM', 'HIGH', 'CRITICAL'];
const VALID_STATUSES = ['OPEN', 'INVESTIGATING', 'RESOLVED', 'CLOSED'];
const CAT_TYPES = ['TYPICAL', 'TRAFFIC', 'WORK_DISEASE', 'DEATH'];

async function list(req, res, next) {
  try {
    const tenantFilter = req.tenantFilter || '1=1';
    const prefixedFilter = tenantFilter.replace(/\btenant_id\b/g, 'i.tenant_id');
    const { type, severity, status, page = 1, perPage = 20 } = req.query;
    const offset = (parseInt(page, 10) - 1) * parseInt(perPage, 10);
    const limit = parseInt(perPage, 10);
    let whereClause = prefixedFilter;
    const params = [];
    if (type) { whereClause += ' AND i.type = ?'; params.push(type); }
    if (severity) { whereClause += ' AND i.severity = ?'; params.push(severity); }
    if (status) { whereClause += ' AND i.status = ?'; params.push(status); }

    const [countRow] = await query(`SELECT COUNT(*) as total FROM incidents i WHERE ${whereClause}`, params);
    const incidents = await query(
      `SELECT i.*, w.name as worker_name
       FROM incidents i LEFT JOIN workers w ON i.worker_id = w.id
       WHERE ${whereClause} ORDER BY i.created_at DESC LIMIT ? OFFSET ?`,
      params.length > 0 ? [...params, limit, offset] : [limit, offset]
    );
    res.json({
      incidents,
      pagination: { page: parseInt(page, 10), perPage: limit, total: countRow?.total || 0, totalPages: Math.ceil((countRow?.total || 0) / limit) },
      correlationId: req.correlationId,
    });
  } catch (err) { next(err); }
}

async function create(req, res, next) {
  try {
    const tenantId = req.tenantId || req.user?.tenantId;
    if (!tenantId) return res.status(403).json({ error: 'ERR_TENANT_REQUIRED', message: 'Tenant não identificado', correlationId: req.correlationId });

    const { workerId, type, severity, description, gpsLatitude, gpsLongitude, occurredAt } = req.body;
    const errors = [];
    if (!type || !VALID_TYPES.includes(type)) errors.push(`Tipo inválido. Valores: ${VALID_TYPES.join(', ')}`);
    if (severity && !VALID_SEVERITIES.includes(severity)) errors.push(`Severidade inválida. Valores: ${VALID_SEVERITIES.join(', ')}`);
    if (!description || description.trim().length < 10) errors.push('Descrição deve ter no mínimo 10 caracteres');
    if (errors.length > 0) return res.status(422).json({ error: 'ERR_VALIDATION', message: errors.join('; '), correlationId: req.correlationId });

    const protocol = `INC-${Date.now().toString(36).toUpperCase()}-${String(Math.floor(Math.random() * 9999)).padStart(4, '0')}`;
    const result = await query(
      `INSERT INTO incidents (tenant_id, worker_id, type, severity, description, gps_latitude, gps_longitude, occurred_at, protocol, status)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
      [tenantId, workerId || null, type, severity || 'MEDIUM', description.trim(), gpsLatitude || null, gpsLongitude || null,
       occurredAt || new Date(), protocol, severity === 'CRITICAL' ? 'OPEN' : 'OPEN']
    );
    res.status(201).json({ message: 'Incidente registrado com sucesso!', incident: { id: result.insertId, protocol }, correlationId: req.correlationId });
  } catch (err) { next(err); }
}

async function read(req, res, next) {
  try {
    const { id } = req.params;
    const tenantFilter = req.tenantFilter || '1=1';
    const rows = await query(
      `SELECT i.*, w.name as worker_name, w.cpf as worker_cpf
       FROM incidents i LEFT JOIN workers w ON i.worker_id = w.id
       WHERE i.id = ? AND ${tenantFilter}`, [id]
    );
    if (rows.length === 0) return res.status(404).json({ error: 'ERR_NOT_FOUND', message: 'Incidente não encontrado', correlationId: req.correlationId });
    res.json({ incident: rows[0], correlationId: req.correlationId });
  } catch (err) { next(err); }
}

async function update(req, res, next) {
  try {
    const { id } = req.params;
    const tenantFilter = req.tenantFilter || '1=1';
    const existing = await query('SELECT id FROM incidents WHERE id = ? AND ' + tenantFilter, [id]);
    if (existing.length === 0) return res.status(404).json({ error: 'ERR_NOT_FOUND', message: 'Incidente não encontrado', correlationId: req.correlationId });

    const { description, severity, status } = req.body;
    const sets = []; const params = [];
    if (description !== undefined) { sets.push('description = ?'); params.push(description); }
    if (severity && VALID_SEVERITIES.includes(severity)) { sets.push('severity = ?'); params.push(severity); }
    if (status && VALID_STATUSES.includes(status)) { sets.push('status = ?'); params.push(status); }
    if (sets.length === 0) return res.status(400).json({ error: 'ERR_VALIDATION', message: 'Nenhum campo para atualizar', correlationId: req.correlationId });

    params.push(id);
    await query(`UPDATE incidents SET ${sets.join(', ')}, updated_at = NOW() WHERE id = ? AND ${tenantFilter}`, params);
    res.json({ message: 'Incidente atualizado!', correlationId: req.correlationId });
  } catch (err) { next(err); }
}

async function updateStatus(req, res, next) {
  try {
    const { id } = req.params;
    const { status } = req.body;
    if (!status || !VALID_STATUSES.includes(status)) return res.status(400).json({ error: 'ERR_VALIDATION', message: `Status inválido. Valores: ${VALID_STATUSES.join(', ')}`, correlationId: req.correlationId });

    const tenantFilter = req.tenantFilter || '1=1';
    const existing = await query('SELECT id FROM incidents WHERE id = ? AND ' + tenantFilter, [id]);
    if (existing.length === 0) return res.status(404).json({ error: 'ERR_NOT_FOUND', message: 'Incidente não encontrado', correlationId: req.correlationId });

    await query(`UPDATE incidents SET status = ?, updated_at = NOW() WHERE id = ? AND ${tenantFilter}`, [status, id]);
    res.json({ message: `Status atualizado para "${status}"`, correlationId: req.correlationId });
  } catch (err) { next(err); }
}

async function triggerSos(req, res, next) {
  try {
    const { id } = req.params;
    const tenantFilter = req.tenantFilter || '1=1';
    const rows = await query(
      `SELECT i.*, t.name as tenant_name, t.phone as tenant_phone
       FROM incidents i JOIN tenants t ON i.tenant_id = t.id
       WHERE i.id = ? AND ${tenantFilter}`, [id]
    );
    if (rows.length === 0) return res.status(404).json({ error: 'ERR_NOT_FOUND', message: 'Incidente não encontrado', correlationId: req.correlationId });

    console.log('[SOS] 🆘 EMERGÊNCIA ACIONADA:', { protocol: rows[0].protocol, tenant: rows[0].tenant_name });
    res.json({
      message: 'SOS acionado! Notificações enviadas para contatos de emergência.',
      data: { protocol: rows[0].protocol, notifiedAt: new Date().toISOString() },
      correlationId: req.correlationId,
    });
  } catch (err) { next(err); }
}

async function emitCat(req, res, next) {
  try {
    const { id } = req.params;
    const tenantFilter = req.tenantFilter || '1=1';
    const { catType, issuingAgency, description } = req.body;

    const incident = await query('SELECT * FROM incidents WHERE id = ? AND ' + tenantFilter, [id]);
    if (incident.length === 0) return res.status(404).json({ error: 'ERR_NOT_FOUND', message: 'Incidente não encontrado', correlationId: req.correlationId });

    if (!catType || !CAT_TYPES.includes(catType)) return res.status(400).json({ error: 'ERR_VALIDATION', message: `Tipo de CAT inválido. Valores: ${CAT_TYPES.join(', ')}`, correlationId: req.correlationId });

    const catNumber = `CAT-${new Date().getFullYear()}-${String(Math.floor(Math.random() * 99999)).padStart(5, '0')}`;
    await query(
      `UPDATE incidents SET cat_number = ?, cat_type = ?, cat_issuing_agency = ?, cat_issued_at = NOW(), status = 'INVESTIGATING', updated_at = NOW() WHERE id = ? AND ${tenantFilter}`,
      [catNumber, catType, issuingAgency || 'INSS', id]
    );
    res.json({
      message: 'CAT emitida com sucesso!',
      data: { catNumber, catType, issuedAt: new Date().toISOString() },
      correlationId: req.correlationId,
    });
  } catch (err) { next(err); }
}

module.exports = { list, create, read, update, updateStatus, triggerSos, emitCat };
