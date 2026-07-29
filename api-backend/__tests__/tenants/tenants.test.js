const request = require('supertest');
const app = require('../../server');
const { seedTenant, seedUser, cleanDatabase } = require('../setup/fixtures');
const { generateToken } = require('../helpers/auth.helper');

describe('Tenants Profile', () => {
  let token;
  let tenantId;

  beforeEach(async () => {
    await cleanDatabase();
    const tenant = await seedTenant();
    tenantId = tenant.id;
    const user = await seedUser({ tenant_id: tenantId });
    token = generateToken({ id: user.id, tenantId, email: user.email });
  });

  describe('GET /api/v1/tenants/me', () => {
    it('deve retornar perfil do tenant autenticado', async () => {
      const res = await request(app)
        .get('/api/v1/tenants/me')
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      expect(res.body).toHaveProperty('tenant');
    });

    it('deve rejeitar sem autenticação', async () => {
      const res = await request(app)
        .get('/api/v1/tenants/me')
        .expect(401);
    });

    it('deve retornar dados básicos do tenant', async () => {
      const res = await request(app)
        .get('/api/v1/tenants/me')
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      expect(res.body.tenant).toHaveProperty('name');
      expect(res.body.tenant).toHaveProperty('plan');
    });
  });

  describe('PUT /api/v1/tenants/me', () => {
    it('deve atualizar perfil do tenant', async () => {
      const res = await request(app)
        .put('/api/v1/tenants/me')
        .set('Authorization', `Bearer ${token}`)
        .send({ name: 'Tenant Atualizado', phone: '11988888888' })
        .expect(200);

      expect(res.body.tenant.name).toBe('Tenant Atualizado');
    });

    it('deve atualizar apenas campos permitidos', async () => {
      const res = await request(app)
        .put('/api/v1/tenants/me')
        .set('Authorization', `Bearer ${token}`)
        .send({ name: 'Novo Nome' })
        .expect(200);

      expect(res.body.tenant.name).toBe('Novo Nome');
    });

    it('deve retornar erro para tenant inexistente', async () => {
      const fakeToken = generateToken({ id: 1, tenantId: 99999, email: 'fake@teste.com' });

      const res = await request(app)
        .get('/api/v1/tenants/me')
        .set('Authorization', `Bearer ${fakeToken}`)
        .expect(404);

      expect(res.body.error).toBe('ERR_NOT_FOUND');
    });
  });
});
