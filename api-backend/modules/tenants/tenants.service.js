const { query } = require('../../config/database');

async function findById(tenantId) {
  const rows = await query(
    `SELECT id, name, slug, document_cpf, document_cnpj,
            phone, whatsapp,
            zipcode, address, neighborhood, city, state,
            latitude, longitude,
            logo_url, active, plan, settings, created_at, updated_at
     FROM tenants WHERE id = ?`,
    [tenantId]
  );
  return rows.length > 0 ? rows[0] : null;
}

async function update(tenantId, data) {
  const fields = [];
  const params = [];

  const allowedFields = [
    'name', 'phone', 'whatsapp',
    'zipcode', 'address', 'neighborhood', 'city', 'state',
    'latitude', 'longitude',
    'logo_url', 'settings',
  ];

  for (const field of allowedFields) {
    if (data[field] !== undefined) {
      fields.push(`${field} = ?`);
      params.push(data[field] === '' ? null : data[field]);
    }
  }

  if (fields.length === 0) return null;

  params.push(tenantId);
  await query(
    `UPDATE tenants SET ${fields.join(', ')}, updated_at = NOW() WHERE id = ?`,
    params
  );

  return findById(tenantId);
}

module.exports = { findById, update };