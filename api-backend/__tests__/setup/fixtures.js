const bcrypt = require('bcrypt');
const { query } = require('../../config/database');

async function seedTenant(overrides = {}) {
  const fields = ['name', 'slug', 'document_cpf', 'document_cnpj', 'phone', 'whatsapp', 'city', 'state', 'active'];
  const values = [
    overrides.name || 'Tenant Teste Ltda',
    overrides.slug || 'tenant-teste',
    overrides.document_cpf || null,
    overrides.document_cnpj || '11222333000181',
    overrides.phone || '11999999999',
    overrides.whatsapp || '11999999999',
    overrides.city || 'São Paulo',
    overrides.state || 'SP',
    overrides.active !== undefined ? overrides.active : 1,
  ];
  const placeholders = fields.map(() => '?');
  const result = await query(
    `INSERT INTO tenants (${fields.join(', ')}) VALUES (${placeholders.join(', ')})`,
    values
  );
  return { id: result.insertId, name: values[0], city: values[6], state: values[7] };
}

async function seedUser(overrides = {}) {
  const passwordHash = await bcrypt.hash(overrides.password || '12345678', 4);
  const fields = ['tenant_id', 'name', 'email', 'password_hash', 'role', 'active'];
  const values = [
    overrides.tenant_id,
    overrides.name || 'Usuário Teste',
    overrides.email || 'teste@teste.com',
    passwordHash,
    overrides.role || 'admin',
    overrides.active !== undefined ? overrides.active : 1,
  ];
  const placeholders = fields.map(() => '?');
  const result = await query(
    `INSERT INTO users (${fields.join(', ')}) VALUES (${placeholders.join(', ')})`,
    values
  );
  return { id: result.insertId, email: values[2], role: values[4] };
}

async function seedCategory(overrides = {}) {
  const fields = ['tenant_id', 'name', 'description', 'icon', 'color', 'active', 'sort_order'];
  const values = [
    overrides.tenant_id,
    overrides.name || 'Categoria Teste',
    overrides.description || 'Descrição da categoria',
    overrides.icon || 'clipboard-list',
    overrides.color || '#10B981',
    overrides.active !== undefined ? overrides.active : 1,
    overrides.sort_order || 0,
  ];
  const placeholders = fields.map(() => '?');
  const result = await query(
    `INSERT INTO categories (${fields.join(', ')}) VALUES (${placeholders.join(', ')})`,
    values
  );
  return { id: result.insertId, name: values[1] };
}

async function seedService(overrides = {}) {
  const fields = ['tenant_id', 'category_id', 'name', 'description', 'price', 'duration_minutes', 'active'];
  const values = [
    overrides.tenant_id,
    overrides.category_id || null,
    overrides.name || 'Serviço Teste',
    overrides.description || 'Descrição do serviço',
    overrides.price || 100.00,
    overrides.duration_minutes || 60,
    overrides.active !== undefined ? overrides.active : 1,
  ];
  const placeholders = fields.map(() => '?');
  const result = await query(
    `INSERT INTO services (${fields.join(', ')}) VALUES (${placeholders.join(', ')})`,
    values
  );
  return { id: result.insertId, name: values[2] };
}

async function seedClient(overrides = {}) {
  const fields = ['tenant_id', 'name', 'email', 'phone', 'active'];
  const values = [
    overrides.tenant_id,
    overrides.name || 'Cliente Teste',
    overrides.email || 'cliente@teste.com',
    overrides.phone || '11988888888',
    overrides.active !== undefined ? overrides.active : 1,
  ];
  const placeholders = fields.map(() => '?');
  const result = await query(
    `INSERT INTO clients (${fields.join(', ')}) VALUES (${placeholders.join(', ')})`,
    values
  );
  return { id: result.insertId, name: values[1] };
}

async function cleanDatabase() {
  const tables = [
    'lgpd_consent', 'admin_audit_log', 'audit_log',
    'proposal_items', 'transactions', 'proposals',
    'public_leads', 'services', 'categories',
    'clients', 'users', 'tenants',
  ];
  for (const table of tables) {
    try { await query(`DELETE FROM \`${table}\``); } catch (_) { }
  }
}

module.exports = { seedTenant, seedUser, seedCategory, seedService, seedClient, cleanDatabase };
