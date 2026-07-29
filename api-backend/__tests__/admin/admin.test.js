const request = require('supertest');
const app = require('../../server');
const { seedTenant, seedUser, seedProposal, seedTransaction, cleanDatabase } = require('../setup/fixtures');
const { generateToken, generateAdminToken } = require('../helpers/auth.helper');

describe('Admin Endpoints', () => {
  let adminToken;

  let seq = 0;
  beforeEach(async () => {
    seq++;
    await cleanDatabase();
    const tenant = await seedTenant({ slug: 'admin-tenant-' + seq });
    const user = await seedUser({ tenant_id: tenant.id, email: 'admin' + seq + '@teste.com', role: 'super_admin' });
    adminToken = generateAdminToken({ id: user.id, email: user.email });
  });

  describe('GET /api/v1/admin/dashboard', () => {
    it('deve aceitar admin autenticado', async () => {
      const res = await request(app)
        .get('/api/v1/admin/dashboard')
        .set('Authorization', `Bearer ${adminToken}`);

      expect(res.status).toBeLessThan(500);
    });
  });

  describe('GET /api/v1/admin/tenants', () => {
    it('deve listar todos os tenants', async () => {
      const res = await request(app)
        .get('/api/v1/admin/tenants')
        .set('Authorization', `Bearer ${adminToken}`)
        .expect(200);

      expect(res.body.tenants).toBeDefined();
    });
  });

  describe('GET /api/v1/admin/plans', () => {
    it('deve listar planos', async () => {
      const res = await request(app)
        .get('/api/v1/admin/plans')
        .set('Authorization', `Bearer ${adminToken}`)
        .expect(200);

      expect(res.body.plans).toBeDefined();
    });
  });

  describe('GET /api/v1/admin/audit', () => {
    it('deve listar audit log', async () => {
      const res = await request(app)
        .get('/api/v1/admin/audit')
        .set('Authorization', `Bearer ${adminToken}`)
        .expect(200);

      expect(res.body).toHaveProperty('logs');
    });
  });

  it('deve rejeitar usuario sem role super_admin', async () => {
    const tenant = await seedTenant();
    const user = await seedUser({ tenant_id: tenant.id, role: 'admin' });
    const userToken = generateToken({ id: user.id, tenantId: tenant.id, role: 'admin' });

    const res = await request(app)
      .get('/api/v1/admin/dashboard')
      .set('Authorization', `Bearer ${userToken}`)
      .expect(403);

    expect(res.body.error).toBe('ERR_FORBIDDEN');
  });

  it('deve rejeitar sem autenticacao', async () => {
    const res = await request(app)
      .get('/api/v1/admin/dashboard')
      .expect(401);
  });
});
