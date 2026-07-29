const { query } = require('../../config/database');

const SCHEDULE_FIELDS = `
  ss.id, ss.tenant_id, ss.worker_id, ss.client_id,
  ss.service_category, ss.regime, ss.scheduled_date,
  ss.start_time, ss.end_time, ss.status,
  ss.hourly_rate, ss.total_amount, ss.transport_voucher,
  ss.notes, ss.created_at, ss.updated_at
`;

async function list({ tenantFilter, workerId, clientId, dateFrom, dateTo, status, page = 1, perPage = 20 }) {
  const offset = (parseInt(page, 10) - 1) * parseInt(perPage, 10);
  const limit = parseInt(perPage, 10);
  const params = [];

  let whereClause = `${tenantFilter}`;

  if (workerId) { whereClause += ' AND ss.worker_id = ?'; params.push(workerId); }
  if (clientId) { whereClause += ' AND ss.client_id = ?'; params.push(clientId); }
  if (dateFrom) { whereClause += ' AND ss.scheduled_date >= ?'; params.push(dateFrom); }
  if (dateTo) { whereClause += ' AND ss.scheduled_date <= ?'; params.push(dateTo); }
  if (status && ['scheduled','confirmed','in_progress','completed','cancelled'].includes(status)) {
    whereClause += ' AND ss.status = ?';
    params.push(status);
  }

  const countResult = await query(
    `SELECT COUNT(*) as total FROM service_schedules ss WHERE ${whereClause}`, params
  );
  const total = countResult[0]?.total || 0;

  const schedules = await query(
    `SELECT ${SCHEDULE_FIELDS},
            w.name as worker_name, w.cpf as worker_cpf, w.worker_category,
            c.name as client_name, c.phone as client_phone
     FROM service_schedules ss
     LEFT JOIN workers w ON ss.worker_id = w.id
     LEFT JOIN clients c ON ss.client_id = c.id
     WHERE ${whereClause}
     ORDER BY ss.scheduled_date DESC, ss.start_time ASC
     LIMIT ? OFFSET ?`,
    [...params, limit, offset]
  );

  return {
    schedules,
    pagination: { page: parseInt(page, 10), perPage: limit, total, totalPages: Math.ceil(total / limit) },
  };
}

async function findById(id, tenantFilter) {
  const rows = await query(
    `SELECT ${SCHEDULE_FIELDS},
            w.name as worker_name, w.cpf as worker_cpf, w.worker_category,
            c.name as client_name, c.phone as client_phone
     FROM service_schedules ss
     LEFT JOIN workers w ON ss.worker_id = w.id
     LEFT JOIN clients c ON ss.client_id = c.id
     WHERE ss.id = ? AND ${tenantFilter}`,
    [id]
  );
  return rows.length > 0 ? rows[0] : null;
}

async function create({ tenantId, workerId, clientId, serviceCategory, regime, scheduledDate, startTime, endTime, hourlyRate, totalAmount, transportVoucher, notes }) {
  const result = await query(
    `INSERT INTO service_schedules
     (tenant_id, worker_id, client_id, service_category, regime,
      scheduled_date, start_time, end_time, status,
      hourly_rate, total_amount, transport_voucher, notes)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'scheduled', ?, ?, ?, ?)`,
    [tenantId, workerId, clientId, serviceCategory, regime,
     scheduledDate, startTime || null, endTime || null,
     hourlyRate || null, totalAmount || null, transportVoucher || 0, notes || null]
  );
  return { id: result.insertId };
}

async function update(id, tenantFilter, fields) {
  const existing = await query(
    `SELECT id FROM service_schedules WHERE id = ? AND ${tenantFilter}`, [id]
  );
  if (existing.length === 0) {
    throw Object.assign(new Error('Schedule not found'), { statusCode: 404 });
  }

  const sets = [];
  const params = [];

  if (fields.scheduledDate !== undefined) { sets.push('scheduled_date = ?'); params.push(fields.scheduledDate); }
  if (fields.startTime !== undefined) { sets.push('start_time = ?'); params.push(fields.startTime); }
  if (fields.endTime !== undefined) { sets.push('end_time = ?'); params.push(fields.endTime); }
  if (fields.hourlyRate !== undefined) { sets.push('hourly_rate = ?'); params.push(fields.hourlyRate); }
  if (fields.totalAmount !== undefined) { sets.push('total_amount = ?'); params.push(fields.totalAmount); }
  if (fields.transportVoucher !== undefined) { sets.push('transport_voucher = ?'); params.push(fields.transportVoucher); }
  if (fields.notes !== undefined) { sets.push('notes = ?'); params.push(fields.notes); }

  if (sets.length === 0) return;

  sets.push('updated_at = NOW()');
  params.push(id);

  await query(
    `UPDATE service_schedules SET ${sets.join(', ')} WHERE id = ? AND ${tenantFilter}`, params
  );
}

async function updateStatus(id, tenantFilter, status) {
  const existing = await query(
    `SELECT id, status FROM service_schedules WHERE id = ? AND ${tenantFilter}`, [id]
  );
  if (existing.length === 0) {
    throw Object.assign(new Error('Schedule not found'), { statusCode: 404 });
  }

  await query(
    `UPDATE service_schedules SET status = ?, updated_at = NOW() WHERE id = ? AND ${tenantFilter}`,
    [status, id]
  );
}

async function remove(id, tenantFilter) {
  const existing = await query(
    `SELECT id FROM service_schedules WHERE id = ? AND ${tenantFilter}`, [id]
  );
  if (existing.length === 0) return false;

  await query(
    `DELETE FROM service_schedules WHERE id = ? AND ${tenantFilter}`, [id]
  );
  return true;
}

async function checkFrequencyLimit(workerId, clientId, scheduledDate) {
  const worker = await query(
    `SELECT w.worker_category, w.tenant_id
     FROM workers w WHERE w.id = ? AND w.active = TRUE`, [workerId]
  );
  if (worker.length === 0) return { allowed: false, reason: 'WORKER_NOT_FOUND' };

  const category = worker[0].worker_category;

  if (category !== 'DIARISTA') {
    return { allowed: true };
  }

  const scheduleCount = await query(
    `SELECT COUNT(*) as cnt FROM service_schedules
     WHERE worker_id = ? AND client_id = ?
       AND YEARWEEK(scheduled_date, 1) = YEARWEEK(?, 1)
       AND status != 'cancelled'`,
    [workerId, clientId, scheduledDate]
  );

  const count = scheduleCount[0]?.cnt || 0;

  if (count >= 2) {
    return {
      allowed: false,
      reason: 'FREQUENCY_LIMIT',
      currentCount: count,
      maxAllowed: 2,
      message: 'Limite de 2 dias/semana atingido para esta diarista no mesmo tomador.',
      transitionUrl: '/api/v1/domestic/transition-to-clt',
    };
  }

  return { allowed: true, currentCount: count, maxAllowed: 2 };
}

module.exports = { list, findById, create, update, updateStatus, remove, checkFrequencyLimit };
