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

async function seedProposal(overrides = {}) {
  const { v4: uuidv4 } = require('uuid');
  const suffix = String(Date.now()).slice(-4);
  const fields = ['tenant_id', 'client_id', 'number', 'title', 'description', 'total_amount', 'status', 'public_token'];
  const values = [
    overrides.tenant_id,
    overrides.client_id || null,
    overrides.number || ('PROP-TEST-' + suffix),
    overrides.title || 'Proposta Teste',
    overrides.description || 'Descrição da proposta',
    overrides.total_amount || 0,
    overrides.status || 'draft',
    overrides.public_token || uuidv4(),
  ];
  const placeholders = fields.map(() => '?');
  const result = await query(
    `INSERT INTO proposals (${fields.join(', ')}) VALUES (${placeholders.join(', ')})`,
    values
  );
  return { id: result.insertId, number: values[2], status: values[5], public_token: values[7] };
}

async function seedProposalItem(overrides = {}) {
  const fields = ['proposal_id', 'description', 'quantity', 'unit_price', 'sort_order'];
  const values = [
    overrides.proposal_id,
    overrides.description || 'Item Padrão',
    overrides.quantity || 1,
    overrides.unit_price || 100,
    overrides.sort_order || 1,
  ];
  const placeholders = fields.map(() => '?');
  const result = await query(
    `INSERT INTO proposal_items (${fields.join(', ')}) VALUES (${placeholders.join(', ')})`,
    values
  );
  return { id: result.insertId, proposal_id: values[0] };
}

async function seedTransaction(overrides = {}) {
  const fields = ['tenant_id', 'proposal_id', 'mp_id', 'mp_status', 'amount', 'fee', 'payment_method', 'status'];
  const values = [
    overrides.tenant_id,
    overrides.proposal_id || null,
    overrides.mp_id || 'MP-TEST-001',
    overrides.mp_status || 'approved',
    overrides.amount || 100.00,
    overrides.fee || 3.99,
    overrides.payment_method || 'pix',
    overrides.status || 'completed',
  ];
  const placeholders = fields.map(() => '?');
  const result = await query(
    `INSERT INTO transactions (${fields.join(', ')}) VALUES (${placeholders.join(', ')})`,
    values
  );
  return { id: result.insertId, mp_id: values[2], status: values[7] };
}

async function cleanDatabase() {
  const tables = [
    'domestic_agreements', 'service_schedules', 'worker_certifications', 'workers',
    'lgpd_consent', 'admin_audit_log', 'audit_log',
    'proposal_items', 'transactions', 'proposals',
    'public_leads', 'services', 'categories',
    'clients', 'users', 'tenants',
  ];
  for (const table of tables) {
    try { await query(`DELETE FROM \`${table}\``); } catch (_) { }
  }
}

module.exports = { seedTenant, seedUser, seedCategory, seedService, seedClient, seedProposal, seedProposalItem, seedTransaction, cleanDatabase };
