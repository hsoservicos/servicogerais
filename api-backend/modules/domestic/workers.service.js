// ═══════════════════════════════════════════════════════════════
// modules/domestic/workers.service.js — Workers Data Access
// ═══════════════════════════════════════════════════════════════

const { query } = require('../../config/database');

const WORKER_FIELDS = `
  id, tenant_id, name, email, cpf, rg, cbo_code,
  worker_category, phone, whatsapp, pix_key,
  address, avatar_url, background_check_status,
  background_check_date, background_check_provider,
  active, created_at, updated_at
`;

// ── Listar workers paginado com busca ─────────────────────
async function list({ search, category, page = 1, perPage = 20, tenantFilter }) {
  const offset = (parseInt(page, 10) - 1) * parseInt(perPage, 10);
  const limit = parseInt(perPage, 10);
  const params = [];

  let whereClause = `${tenantFilter}`;

  if (search && search.length >= 2) {
    whereClause += ' AND (name LIKE ? OR cpf LIKE ?)';
    params.push(`%${search}%`, `%${search}%`);
  }

  if (category && category.length > 0) {
    whereClause += ' AND worker_category = ?';
    params.push(category);
  }

  const countSql = `SELECT COUNT(*) as total FROM workers WHERE ${whereClause}`;
  const countRows = params.length > 0
    ? await query(countSql, params)
    : await query(countSql);
  const total = countRows[0]?.total || 0;

  const listParams = [...params, limit, offset];
  const workers = await query(
    `SELECT ${WORKER_FIELDS}
     FROM workers
     WHERE ${whereClause}
     ORDER BY name ASC
     LIMIT ? OFFSET ?`,
    listParams
  );

  return {
    workers,
    pagination: {
      page: parseInt(page, 10),
      perPage: limit,
      total,
      totalPages: Math.ceil(total / limit),
    },
  };
}

// ── Buscar worker por ID ──────────────────────────────────
async function findById(id, tenantFilter) {
  const rows = await query(
    `SELECT ${WORKER_FIELDS}
     FROM workers
     WHERE id = ? AND ${tenantFilter}`,
    [id]
  );
  return rows.length > 0 ? rows[0] : null;
}

// ── Criar worker ──────────────────────────────────────────
async function create({ tenantId, name, email, cpf, rg, cboCode, workerCategory, phone, whatsapp, pixKey, address }) {
  const result = await query(
    `INSERT INTO workers
     (tenant_id, name, email, cpf, rg, cbo_code, worker_category,
      phone, whatsapp, pix_key, address, active)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, TRUE)`,
    [tenantId, name, email, cpf, rg, cboCode, workerCategory, phone, whatsapp, pixKey, address]
  );

  return { id: result.insertId, name, cpf, workerCategory };
}

// ── Atualizar worker ──────────────────────────────────────
async function update(id, tenantFilter, fields) {
  const existing = await query(
    `SELECT id FROM workers WHERE id = ? AND ${tenantFilter}`,
    [id]
  );

  if (existing.length === 0) {
    throw Object.assign(new Error('Worker not found'), { statusCode: 404 });
  }

  const sets = [];
  const params = [];

  if (fields.name !== undefined) { sets.push('name = ?'); params.push(fields.name); }
  if (fields.email !== undefined) { sets.push('email = ?'); params.push(fields.email); }
  if (fields.cpf !== undefined) { sets.push('cpf = ?'); params.push(fields.cpf); }
  if (fields.rg !== undefined) { sets.push('rg = ?'); params.push(fields.rg); }
  if (fields.cboCode !== undefined) { sets.push('cbo_code = ?'); params.push(fields.cboCode); }
  if (fields.workerCategory !== undefined) { sets.push('worker_category = ?'); params.push(fields.workerCategory); }
  if (fields.phone !== undefined) { sets.push('phone = ?'); params.push(fields.phone); }
  if (fields.whatsapp !== undefined) { sets.push('whatsapp = ?'); params.push(fields.whatsapp); }
  if (fields.pixKey !== undefined) { sets.push('pix_key = ?'); params.push(fields.pixKey); }
  if (fields.address !== undefined) { sets.push('address = ?'); params.push(fields.address); }

  if (sets.length === 0) return;

  sets.push('updated_at = NOW()');
  params.push(id);

  await query(
    `UPDATE workers SET ${sets.join(', ')} WHERE id = ? AND ${tenantFilter}`,
    params
  );
}

// ── Excluir worker (lógico) ───────────────────────────────
async function remove(id, tenantFilter) {
  const existing = await query(
    `SELECT id FROM workers WHERE id = ? AND ${tenantFilter} AND active = TRUE`,
    [id]
  );

  if (existing.length === 0) return false;

  await query(
    `UPDATE workers SET active = FALSE, updated_at = NOW() WHERE id = ? AND ${tenantFilter}`,
    [id]
  );

  return true;
}

module.exports = { list, findById, create, update, remove };