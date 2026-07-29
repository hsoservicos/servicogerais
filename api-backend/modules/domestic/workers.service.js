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

const CERT_FIELDS = 'id, worker_id, certification_type, title, issuer, issue_date, expiry_date, document_url, verified, created_at';

async function listCertifications(workerId, tenantFilter) {
  const worker = await query('SELECT id FROM workers WHERE id = ? AND ?', [workerId, tenantFilter.replace('1=1', '1')]);
  if (worker.length === 0) return null;

  const rows = await query(
    `SELECT ${CERT_FIELDS} FROM worker_certifications WHERE worker_id = ? ORDER BY created_at DESC`,
    [workerId]
  );
  return rows;
}

async function createCertification(workerId, tenantFilter, fields) {
  const worker = await query('SELECT id FROM workers WHERE id = ? AND ?', [workerId, tenantFilter.replace('1=1', '1')]);
  if (worker.length === 0) throw Object.assign(new Error('Worker not found'), { statusCode: 404 });

  const { certificationType, title, issuer, issueDate, expiryDate, documentUrl } = fields;
  const validTypes = ['CUIDADOR_IDOSOS', 'APH', 'BABA', 'COZINHA', 'JARDINAGEM', 'PRIMEIROS_SOCORROS', 'OUTRO'];

  if (!certificationType || !validTypes.includes(certificationType)) {
    throw Object.assign(new Error(`Tipo inválido. Valores: ${validTypes.join(', ')}`), { statusCode: 400 });
  }
  if (!title || title.trim().length === 0) {
    throw Object.assign(new Error('Título é obrigatório'), { statusCode: 400 });
  }

  const result = await query(
    `INSERT INTO worker_certifications
     (worker_id, certification_type, title, issuer, issue_date, expiry_date, document_url, verified)
     VALUES (?, ?, ?, ?, ?, ?, ?, FALSE)`,
    [workerId, certificationType, title.trim(), issuer || null, issueDate || null, expiryDate || null, documentUrl || null]
  );
  return { id: result.insertId };
}

async function updateCertification(certId, workerId, tenantFilter, fields) {
  const worker = await query('SELECT id FROM workers WHERE id = ? AND ?', [workerId, tenantFilter.replace('1=1', '1')]);
  if (worker.length === 0) throw Object.assign(new Error('Worker not found'), { statusCode: 404 });

  const existing = await query('SELECT id FROM worker_certifications WHERE id = ? AND worker_id = ?', [certId, workerId]);
  if (existing.length === 0) throw Object.assign(new Error('Certification not found'), { statusCode: 404 });

  const sets = [];
  const params = [];
  if (fields.certificationType !== undefined) {
    const validTypes = ['CUIDADOR_IDOSOS', 'APH', 'BABA', 'COZINHA', 'JARDINAGEM', 'PRIMEIROS_SOCORROS', 'OUTRO'];
    if (!validTypes.includes(fields.certificationType)) throw Object.assign(new Error('Tipo inválido'), { statusCode: 400 });
    sets.push('certification_type = ?'); params.push(fields.certificationType);
  }
  if (fields.title !== undefined) { sets.push('title = ?'); params.push(fields.title); }
  if (fields.issuer !== undefined) { sets.push('issuer = ?'); params.push(fields.issuer); }
  if (fields.issueDate !== undefined) { sets.push('issue_date = ?'); params.push(fields.issueDate); }
  if (fields.expiryDate !== undefined) { sets.push('expiry_date = ?'); params.push(fields.expiryDate); }
  if (fields.documentUrl !== undefined) { sets.push('document_url = ?'); params.push(fields.documentUrl); }
  if (fields.verified !== undefined) { sets.push('verified = ?'); params.push(fields.verified ? 1 : 0); }

  if (sets.length === 0) return;
  params.push(certId, workerId);
  await query(
    `UPDATE worker_certifications SET ${sets.join(', ')} WHERE id = ? AND worker_id = ?`,
    params
  );
}

async function deleteCertification(certId, workerId, tenantFilter) {
  const worker = await query('SELECT id FROM workers WHERE id = ? AND ?', [workerId, tenantFilter.replace('1=1', '1')]);
  if (worker.length === 0) return false;

  const result = await query(
    'DELETE FROM worker_certifications WHERE id = ? AND worker_id = ?', [certId, workerId]
  );
  return result.affectedRows > 0;
}

async function runBackgroundCheck(workerId, tenantFilter) {
  const worker = await query(
    `SELECT id, name, cpf FROM workers WHERE id = ? AND ?`, [workerId, tenantFilter.replace('1=1', '1')]
  );
  if (worker.length === 0) throw Object.assign(new Error('Worker not found'), { statusCode: 404 });

  await query(
    `UPDATE workers SET background_check_status = 'APPROVED', background_check_date = NOW(), background_check_provider = 'system', updated_at = NOW() WHERE id = ?`,
    [workerId]
  );

  return {
    status: 'APPROVED',
    checkedAt: new Date().toISOString(),
    provider: 'system',
    message: 'Background check aprovado automaticamente (modo simulado)',
  };
}

async function checkCertificationRequired(workerId) {
  const worker = await query('SELECT id, worker_category FROM workers WHERE id = ? AND active = TRUE', [workerId]);
  if (worker.length === 0) return { allowed: false, reason: 'WORKER_NOT_FOUND' };

  const category = worker[0].worker_category;
  if (category !== 'BABA' && category !== 'CUIDADOR_IDOSOS') {
    return { allowed: true };
  }

  const certs = await query(
    `SELECT id FROM worker_certifications
     WHERE worker_id = ? AND certification_type = ? AND verified = TRUE`,
    [workerId, category === 'BABA' ? 'BABA' : 'CUIDADOR_IDOSOS']
  );

  if (certs.length === 0) {
    return {
      allowed: false,
      reason: 'CERTIFICATION_REQUIRED',
      message: `${category === 'BABA' ? 'Babás' : 'Cuidadores de idosos'} necessitam de certificação verificada para serem agendados.`,
    };
  }

  return { allowed: true };
}

module.exports = { list, findById, create, update, remove, listCertifications, createCertification, updateCertification, deleteCertification, runBackgroundCheck, checkCertificationRequired };