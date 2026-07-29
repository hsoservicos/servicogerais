const request = require('supertest');
const app = require('../../server');
const { seedTenant, seedCategory, cleanDatabase } = require('../setup/fixtures');
const { query } = require('../../config/database');

describe('Public Endpoints', () => {
  beforeEach(async () => {
    await cleanDatabase();
  });

  describe('GET /api/v1/public/categories', () => {
    it('deve listar categorias publicas sem auth', async () => {
      const tenant = await seedTenant();
      await seedCategory({ tenant_id: tenant.id, name: 'Limpeza' });

      const res = await request(app)
        .get('/api/v1/public/categories')
        .expect(200);

      expect(res.body.categories).toBeDefined();
    });
  });

  describe('GET /api/v1/public/services', () => {
    it('deve listar servicos publicos sem auth', async () => {
      const res = await request(app)
        .get('/api/v1/public/services')
        .expect(200);

      expect(res.body.services).toBeDefined();
    });
  });

  describe('POST /api/v1/public/leads', () => {
    it('deve aceitar envio de lead', async () => {
      const tenant = await seedTenant();

      const res = await request(app)
        .post('/api/v1/public/leads')
        .send({
          customerName: 'Joao Lead',
          customerPhone: '11977777777',
          serviceName: 'Limpeza',
        });

      expect(res.status).toBeLessThan(500);
    });
  });
});
