const { query, transaction } = require('../../config/database');

async function exportUserData(userId, tenantId) {
  const user = await query('SELECT id, name, email, role, active, created_at FROM users WHERE id = ?', [userId]);
  if (user.length === 0) return null;

  const tenant = await query(
    'SELECT id, name, slug, document_cpf, document_cnpj, phone, whatsapp, logo_url, plan, active, created_at FROM tenants WHERE id = ?',
    [tenantId]
  );

  const clients = await query(
    'SELECT id, name, email, document_cpf, document_cnpj, phone, whatsapp, address, city, state, notes, active, created_at FROM clients WHERE tenant_id = ?',
    [tenantId]
  );

  const proposals = await query(
    `SELECT p.id, p.number, p.title, p.description, p.total_amount, p.status, p.created_at,
            c.name as client_name
     FROM proposals p LEFT JOIN clients c ON p.client_id = c.id
     WHERE p.tenant_id = ?`, [tenantId]
  );

  const transactions = await query(
    'SELECT id, mp_id, mp_status, amount, fee, net_amount, payment_method, status, paid_at, created_at FROM transactions WHERE tenant_id = ?',
    [tenantId]
  );

  const consents = await query(
    'SELECT consent_type, granted, granted_at, revoked_at FROM lgpd_consent WHERE user_id = ?',
    [userId]
  );

  return {
    exportedAt: new Date().toISOString(),
    user: user[0],
    tenant: tenant[0] || null,
    clients,
    proposals,
    transactions,
    consents,
  };
}

async function requestDataDeletion(userId, tenantId) {
  const user = await query('SELECT id, name, email FROM users WHERE id = ?', [userId]);
  if (user.length === 0) throw Object.assign(new Error('User not found'), { statusCode: 404 });

  const ANONYMIZED = '[ANONYMIZED]';

  await transaction(async (conn) => {
    await conn.execute(
      `UPDATE users SET
        name = ?, email = CONCAT('deleted-', id, '@anonymized.com'),
        password_hash = '', active = FALSE,
        updated_at = NOW()
       WHERE id = ?`,
      [ANONYMIZED, userId]
    );

    await conn.execute(
      `UPDATE tenants SET
        name = CONCAT(SUBSTRING(name, 1, 3), '...DELETED'),
        document_cpf = NULL, document_cnpj = NULL,
        phone = NULL, whatsapp = NULL,
        active = FALSE
       WHERE id = ?`,
      [tenantId]
    );

    const clientsData = await conn.execute(
      `SELECT id, name, email, document_cpf, document_cnpj, phone, whatsapp,
              address, city, state, notes
       FROM clients WHERE tenant_id = ?`,
      [tenantId]
    );

    if (clientsData[0] && clientsData[0].length > 0) {
      for (const client of clientsData[0]) {
        await conn.execute(
          `UPDATE clients SET
            name = CONCAT(SUBSTRING(name, 1, 2), '...DELETED'),
            email = NULL, document_cpf = NULL, document_cnpj = NULL,
            phone = NULL, whatsapp = NULL, notes = NULL
           WHERE id = ?`,
          [client.id]
        );
      }
    }

    await conn.execute(
      `UPDATE lgpd_consent SET granted = FALSE, revoked_at = NOW()
       WHERE user_id = ? AND granted = TRUE`,
      [userId]
    );
  });

  await query(
    `INSERT INTO audit_log (tenant_id, user_id, action, entity_type, entity_id, metadata)
     VALUES (?, ?, 'data_deletion_executed', 'user', ?, ?)`,
    [tenantId, userId, userId, JSON.stringify({
      executedAt: new Date().toISOString(),
      type: 'full_anonymization',
    })]
  );

  return { message: 'Seus dados foram anonimizados com sucesso. Concluído em 15 dias úteis.' };
}

async function getConsents(userId) {
  const rows = await query(
    'SELECT id, consent_type, granted, ip_address, granted_at, revoked_at FROM lgpd_consent WHERE user_id = ?',
    [userId]
  );
  return rows;
}

async function setConsent(userId, consentType, granted, ipAddress) {
  const existing = await query(
    'SELECT id FROM lgpd_consent WHERE user_id = ? AND consent_type = ?', [userId, consentType]
  );

  if (existing.length > 0) {
    if (granted) {
      await query(
        'UPDATE lgpd_consent SET granted = TRUE, revoked_at = NULL, ip_address = ? WHERE id = ?',
        [ipAddress, existing[0].id]
      );
    } else {
      await query(
        'UPDATE lgpd_consent SET granted = FALSE, revoked_at = NOW(), ip_address = ? WHERE id = ?',
        [ipAddress, existing[0].id]
      );
    }
    return { id: existing[0].id, consentType, granted };
  }

  const result = await query(
    `INSERT INTO lgpd_consent (user_id, consent_type, granted, ip_address, granted_at)
     VALUES (?, ?, ?, ?, NOW())`,
    [userId, consentType, granted, ipAddress]
  );
  return { id: result.insertId, consentType, granted };
}

module.exports = { exportUserData, requestDataDeletion, getConsents, setConsent };
