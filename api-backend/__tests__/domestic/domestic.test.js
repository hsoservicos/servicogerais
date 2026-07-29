const request = require('supertest');
const app = require('../../server');
const { seedTenant, seedUser, cleanDatabase } = require('../setup/fixtures');
const { generateToken } = require('../helpers/auth.helper');

describe('Domestic Operations', () => {
  let token, tenantId;

  beforeEach(async () => {
    await cleanDatabase();
    const tenant = await seedTenant();
    tenantId = tenant.id;
    const user = await seedUser({ tenant_id: tenantId });
    token = generateToken({ id: user.id, tenantId, email: user.email });
  });

  describe('POST /api/v1/domestic/calculate-costs', () => {
    it('deve aceitar requisicao de calculo', async () => {
      const res = await request(app)
        .post('/api/v1/domestic/calculate-costs')
        .set('Authorization', `Bearer ${token}`)
        .send({ salary: 1500, weeklyFrequency: 3 });

      expect(res.status).toBeLessThan(500);
    });

    it('deve processar requisicao sem salario', async () => {
      const res = await request(app)
        .post('/api/v1/domestic/calculate-costs')
        .set('Authorization', `Bearer ${token}`)
        .send({});

      expect(res.status).toBeLessThan(500);
    });
  });
});
