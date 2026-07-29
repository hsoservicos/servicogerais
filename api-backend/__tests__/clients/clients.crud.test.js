const request = require('supertest');
const app = require('../../server');
const { seedTenant, seedUser, seedClient } = require('../setup/fixtures');
const { generateToken } = require('../helpers/auth.helper');
const { findClientById } = require('../helpers/db.helper');
const { cleanDatabase } = require('../setup/fixtures');

describe('Clients CRUD', () => {
  let token;
  let tenantId;

  beforeEach(async () => {
    await cleanDatabase();
    const tenant = await seedTenant();
    tenantId = tenant.id;
    const user = await seedUser({ tenant_id: tenantId });
    token = generateToken({ id: user.id, tenantId, email: user.email });
  });

  describe('POST /api/v1/clients', () => {
    it('deve criar cliente com dados mínimos', async () => {
      const res = await request(app)
        .post('/api/v1/clients')
        .set('Authorization', `Bearer ${token}`)
        .send({ name: 'Novo Cliente' })
        .expect(201);

      expect(res.body).toHaveProperty('client');
      expect(res.body.client.name).toBe('Novo Cliente');
    });

    it('deve criar cliente com dados completos', async () => {
      const res = await request(app)
        .post('/api/v1/clients')
        .set('Authorization', `Bearer ${token}`)
        .send({
          name: 'Cliente Completo',
          email: 'completo@cliente.com',
          phone: '11977777777',
          city: 'São Paulo',
          state: 'SP',
        })
        .expect(201);

      expect(res.body.client.name).toBe('Cliente Completo');
    });

    it('deve rejeitar cliente sem nome', async () => {
      const res = await request(app)
        .post('/api/v1/clients')
        .set('Authorization', `Bearer ${token}`)
        .send({ email: 'sem@nome.com' })
        .expect(400);

      expect(res.body.error).toBe('ERR_VALIDATION');
    });

    it('deve rejeitar cliente com e-mail inválido', async () => {
      const res = await request(app)
        .post('/api/v1/clients')
        .set('Authorization', `Bearer ${token}`)
        .send({ name: 'Cliente', email: 'invalido' })
        .expect(400);

      expect(res.body.error).toBe('ERR_VALIDATION');
    });

    it('deve rejeitar sem autenticação', async () => {
      const res = await request(app)
        .post('/api/v1/clients')
        .send({ name: 'Sem Token' })
        .expect(401);
    });
  });

  describe('GET /api/v1/clients', () => {
    it('deve listar clientes vazia', async () => {
      const res = await request(app)
        .get('/api/v1/clients')
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      expect(res.body).toHaveProperty('clients');
      expect(res.body).toHaveProperty('pagination');
      expect(res.body.clients.length).toBe(0);
    });

    it('deve listar clientes com paginação', async () => {
      await seedClient({ tenant_id: tenantId, name: 'Cliente A' });
      await seedClient({ tenant_id: tenantId, name: 'Cliente B' });

      const res = await request(app)
        .get('/api/v1/clients')
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      expect(res.body.clients.length).toBe(2);
      expect(res.body.pagination.total).toBe(2);
    });

    it('deve filtrar clientes por search', async () => {
      await seedClient({ tenant_id: tenantId, name: 'Joao Silva' });
      await seedClient({ tenant_id: tenantId, name: 'Maria Santos' });

      const res = await request(app)
        .get('/api/v1/clients?search=Joao')
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      expect(res.body.clients.length).toBe(1);
      expect(res.body.clients[0].name).toBe('Joao Silva');
    });

    it('deve respeitar isolamento de tenant', async () => {
      const tenant2 = await seedTenant({ name: 'Outro Tenant', slug: 'outro' });
      await seedClient({ tenant_id: tenantId, name: 'Meu Cliente' });
      await seedClient({ tenant_id: tenant2.id, name: 'Outro Cliente' });

      const res = await request(app)
        .get('/api/v1/clients')
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      expect(res.body.clients.length).toBe(1);
      expect(res.body.clients[0].name).toBe('Meu Cliente');
    });
  });

  describe('GET /api/v1/clients/:id', () => {
    it('deve retornar cliente por ID', async () => {
      const client = await seedClient({ tenant_id: tenantId });

      const res = await request(app)
        .get(`/api/v1/clients/${client.id}`)
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      expect(res.body.client.name).toBe(client.name);
    });

    it('deve retornar 404 para cliente inexistente', async () => {
      const res = await request(app)
        .get('/api/v1/clients/99999')
        .set('Authorization', `Bearer ${token}`)
        .expect(404);

      expect(res.body.error).toBe('ERR_NOT_FOUND');
    });

    it('deve retornar 404 para cliente de outro tenant', async () => {
      const tenant2 = await seedTenant({ name: 'Outro', slug: 'outro2' });
      const client = await seedClient({ tenant_id: tenant2.id });

      const res = await request(app)
        .get(`/api/v1/clients/${client.id}`)
        .set('Authorization', `Bearer ${token}`)
        .expect(404);

      expect(res.body.error).toBe('ERR_NOT_FOUND');
    });
  });

  describe('PUT /api/v1/clients/:id', () => {
    it('deve atualizar cliente', async () => {
      const client = await seedClient({ tenant_id: tenantId });

      const res = await request(app)
        .put(`/api/v1/clients/${client.id}`)
        .set('Authorization', `Bearer ${token}`)
        .send({ name: 'Nome Atualizado' })
        .expect(200);

      const updated = await findClientById(client.id);
      expect(updated.name).toBe('Nome Atualizado');
    });

    it('deve rejeitar atualização sem nome', async () => {
      const client = await seedClient({ tenant_id: tenantId });

      const res = await request(app)
        .put(`/api/v1/clients/${client.id}`)
        .set('Authorization', `Bearer ${token}`)
        .send({ name: '' })
        .expect(400);

      expect(res.body.error).toBe('ERR_VALIDATION');
    });

    it('deve retornar 404 para cliente de outro tenant', async () => {
      const tenant2 = await seedTenant({ name: 'Outro', slug: 'outro3' });
      const client = await seedClient({ tenant_id: tenant2.id });

      const res = await request(app)
        .put(`/api/v1/clients/${client.id}`)
        .set('Authorization', `Bearer ${token}`)
        .send({ name: 'Hack' })
        .expect(404);
    });
  });

  describe('DELETE /api/v1/clients/:id', () => {
    it('deve fazer soft-delete do cliente', async () => {
      const client = await seedClient({ tenant_id: tenantId });

      await request(app)
        .delete(`/api/v1/clients/${client.id}`)
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      const deleted = await findClientById(client.id);
      expect(deleted.active).toBe(0);
    });

    it('deve retornar 404 para cliente já deletado', async () => {
      const client = await seedClient({ tenant_id: tenantId });
      await request(app)
        .delete(`/api/v1/clients/${client.id}`)
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      const res = await request(app)
        .delete(`/api/v1/clients/${client.id}`)
        .set('Authorization', `Bearer ${token}`)
        .expect(404);

      expect(res.body.error).toBe('ERR_NOT_FOUND');
    });
  });
});
