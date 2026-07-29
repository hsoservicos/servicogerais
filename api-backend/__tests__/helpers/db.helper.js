const { query } = require('../../config/database');

async function findTenantById(id) {
  const rows = await query('SELECT * FROM tenants WHERE id = ?', [id]);
  return rows[0] || null;
}

async function findUserByEmail(email) {
  const rows = await query('SELECT * FROM users WHERE email = ?', [email]);
  return rows[0] || null;
}

async function findCategoryById(id) {
  const rows = await query('SELECT * FROM categories WHERE id = ?', [id]);
  return rows[0] || null;
}

async function findServiceById(id) {
  const rows = await query('SELECT * FROM services WHERE id = ?', [id]);
  return rows[0] || null;
}

async function findClientById(id) {
  const rows = await query('SELECT * FROM clients WHERE id = ?', [id]);
  return rows[0] || null;
}

module.exports = { findTenantById, findUserByEmail, findCategoryById, findServiceById, findClientById };
