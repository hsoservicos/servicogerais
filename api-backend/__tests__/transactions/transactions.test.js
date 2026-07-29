const request = require('supertest');
const app = require('../../server');
const { seedTenant, seedUser, seedClient, seedProposal, seedTransaction, cleanDatabase } = require('../setup/fixtures');
const { generateToken } = require('../helpers/auth.helper');

describe('Transactions', () => {
  let token, tenantId;

  beforeEach(async () => {
    await cleanDatabase();
    const tenant = await seedTenant();
    tenantId = tenant.id;
    const user = await seedUser({ tenant_id: tenantId });
    token = generateToken({ id: user.id, tenantId, email: user.email });
  });

  describe('GET /api/v1/transactions', () => {
    it('deve listar transacoes vazia', async () => {
      const res = await request(app)
        .get('/api/v1/transactions')
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      expect(res.body.transactions).toHaveLength(0);
      expect(res.body).toHaveProperty('summary');
      expect(res.body).toHaveProperty('totals');
      expect(res.body).toHaveProperty('pagination');
    });

    it('deve listar transacoes do tenant', async () => {
      const prop = await seedProposal({ tenant_id: tenantId });
      await seedTransaction({ tenant_id: tenantId, proposal_id: prop.id });
      await seedTransaction({ tenant_id: tenantId, proposal_id: prop.id, mp_id: 'MP-TEST-002' });

      const res = await request(app)
        .get('/api/v1/transactions')
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      expect(res.body.transactions).toHaveLength(2);
    });

    it('deve respeitar isolamento de tenant', async () => {
      const tenant2 = await seedTenant({ name: 'Outro', slug: 'outro-tx' });
      const prop2 = await seedProposal({ tenant_id: tenant2.id });
      await seedTransaction({ tenant_id: tenant2.id, proposal_id: prop2.id });

      const res = await request(app)
        .get('/api/v1/transactions')
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      expect(res.body.transactions).toHaveLength(0);
    });

    it('deve retornar resumo por status', async () => {
      const prop = await seedProposal({ tenant_id: tenantId });
      await seedTransaction({ tenant_id: tenantId, proposal_id: prop.id, status: 'completed' });
      await seedTransaction({ tenant_id: tenantId, proposal_id: prop.id, mp_id: 'MP-TEST-002', status: 'pending' });

      const res = await request(app)
        .get('/api/v1/transactions')
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      expect(res.body.summary.length).toBeGreaterThanOrEqual(2);
    });

    it('deve filtrar por status', async () => {
      const prop = await seedProposal({ tenant_id: tenantId });
      await seedTransaction({ tenant_id: tenantId, proposal_id: prop.id, status: 'completed' });
      await seedTransaction({ tenant_id: tenantId, proposal_id: prop.id, mp_id: 'MP-TEST-002', status: 'refunded' });

      const res = await request(app)
        .get('/api/v1/transactions?status=completed')
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      expect(res.body.transactions).toHaveLength(1);
      res.body.transactions.forEach(tx => {
        expect(tx.status).toBe('completed');
      });
    });
  });
});
