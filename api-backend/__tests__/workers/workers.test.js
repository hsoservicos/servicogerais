const request = require('supertest');
const app = require('../../server');
const { seedTenant, seedUser, seedClient, cleanDatabase } = require('../setup/fixtures');
const { generateToken } = require('../helpers/auth.helper');

describe('Workers CRUD', () => {
  let token, tenantId;

  beforeEach(async () => {
    await cleanDatabase();
    const tenant = await seedTenant();
    tenantId = tenant.id;
    const user = await seedUser({ tenant_id: tenantId });
    token = generateToken({ id: user.id, tenantId, email: user.email });
  });

  const validWorker = {
    name: 'Maria Trabalhadora',
    cpf: '52998224725',
    cboCode: '911105',
    workerCategory: 'EMPREGADO_DOMESTICO_GERAL',
    phone: '11977777777',
  };

  describe('POST /api/v1/workers', () => {
    it('deve criar trabalhador', async () => {
      const res = await request(app)
        .post('/api/v1/workers')
        .set('Authorization', `Bearer ${token}`)
        .send(validWorker)
        .expect(201);

      expect(res.body.worker.name).toBe('Maria Trabalhadora');
    });

    it('deve rejeitar trabalhador sem CPF', async () => {
      const res = await request(app)
        .post('/api/v1/workers')
        .set('Authorization', `Bearer ${token}`)
        .send({ ...validWorker, cpf: undefined })
        .expect(422);

      expect(res.body.error).toBe('ERR_VALIDATION');
    });

    it('deve rejeitar CPF duplicado', async () => {
      await request(app)
        .post('/api/v1/workers')
        .set('Authorization', `Bearer ${token}`)
        .send(validWorker)
        .expect(201);

      const res = await request(app)
        .post('/api/v1/workers')
        .set('Authorization', `Bearer ${token}`)
        .send(validWorker)
        .expect(409);

      expect(res.body.error).toBe('ERR_DUPLICATE_ENTRY');
    });
  });

  describe('GET /api/v1/workers', () => {
    it('deve listar trabalhadores vazio', async () => {
      const res = await request(app)
        .get('/api/v1/workers')
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      expect(res.body.workers).toHaveLength(0);
    });
  });

  describe('POST /api/v1/workers/:id/certifications', () => {
    it('deve criar worker antes de certificar', async () => {
      const res = await request(app)
        .post('/api/v1/workers')
        .set('Authorization', `Bearer ${token}`)
        .send(validWorker)
        .expect(201);

      expect(res.body.worker.id).toBeGreaterThan(0);
    });
  });
});
