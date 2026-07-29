const request = require('supertest');
const app = require('../../server');
const { seedTenant, seedUser, seedClient, seedProposal, seedTransaction, cleanDatabase } = require('../setup/fixtures');
const { generateToken } = require('../helpers/auth.helper');

describe('Dashboard', () => {
  let token, tenantId;

  beforeEach(async () => {
    await cleanDatabase();
    const tenant = await seedTenant();
    tenantId = tenant.id;
    const user = await seedUser({ tenant_id: tenantId });
    token = generateToken({ id: user.id, tenantId, email: user.email });
  });

  describe('GET /api/v1/dashboard', () => {
    it('deve retornar KPIs com dados zerados', async () => {
      const res = await request(app)
        .get('/api/v1/dashboard')
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      expect(res.body).toHaveProperty('clients');
      expect(res.body).toHaveProperty('proposals');
      expect(res.body).toHaveProperty('revenue');
      expect(res.body).toHaveProperty('pending');
      expect(res.body).toHaveProperty('activities');
      expect(res.body.clients).toBe(0);
      expect(res.body.proposals).toBe(0);
    });

    it('deve refletir dados cadastrados', async () => {
      await seedClient({ tenant_id: tenantId, name: 'Cliente A' });
      await seedClient({ tenant_id: tenantId, name: 'Cliente B' });
      await seedProposal({ tenant_id: tenantId, status: 'draft' });

      const res = await request(app)
        .get('/api/v1/dashboard')
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      expect(res.body.clients).toBe(2);
      expect(res.body.pending).toBe(1);
    });

    it('deve respeitar isolamento de tenant', async () => {
      const tenant2 = await seedTenant({ name: 'Outro', slug: 'outro-dash' });
      await seedClient({ tenant_id: tenant2.id });

      const res = await request(app)
        .get('/api/v1/dashboard')
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      expect(res.body.clients).toBe(0);
    });
  });

  describe('GET /api/v1/dashboard/chart', () => {
    it('deve retornar 6 meses de dados', async () => {
      const res = await request(app)
        .get('/api/v1/dashboard/chart')
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      expect(res.body.months).toHaveLength(6);
    });

    it('deve retornar meses com receita zero', async () => {
      const res = await request(app)
        .get('/api/v1/dashboard/chart')
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      res.body.months.forEach(m => {
        expect(m.revenue).toBe(0);
      });
    });
  });

  describe('GET /api/v1/dashboard/followup', () => {
    it('deve retornar lista vazia sem propostas pendentes', async () => {
      const res = await request(app)
        .get('/api/v1/dashboard/followup')
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      expect(res.body.proposals).toHaveLength(0);
    });
  });
});
