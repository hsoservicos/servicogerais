const request = require('supertest');
const app = require('../../server');
const { seedTenant, seedUser, cleanDatabase } = require('../setup/fixtures');
const { generateToken } = require('../helpers/auth.helper');
const { query } = require('../../config/database');

describe('LGPD Data Privacy', () => {
  let token, tenantId, userId;

  beforeEach(async () => {
    await cleanDatabase();
    const tenant = await seedTenant();
    tenantId = tenant.id;
    const user = await seedUser({ tenant_id: tenantId, email: 'lgpd@teste.com' });
    userId = user.id;
    token = generateToken({ id: userId, tenantId, email: 'lgpd@teste.com' });
  });

  describe('GET /api/v1/data/export', () => {
    it('deve exportar dados do usuario', async () => {
      const res = await request(app)
        .get('/api/v1/data/export')
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      expect(res.body).toHaveProperty('data');
      expect(res.body.data).toHaveProperty('user');
      expect(res.body.data).toHaveProperty('tenant');
    });
  });

  describe('GET /api/v1/data/consent', () => {
    it('deve listar consentimentos', async () => {
      const res = await request(app)
        .get('/api/v1/data/consent')
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      expect(res.body).toHaveProperty('consents');
    });
  });

  describe('POST /api/v1/data/consent', () => {
    it('deve registrar consentimento', async () => {
      const res = await request(app)
        .post('/api/v1/data/consent')
        .set('Authorization', `Bearer ${token}`)
        .send({ consentType: 'communications', granted: true })
        .expect(200);

      expect(res.body).toHaveProperty('consent');
      expect(res.body.consent.granted).toBe(true);
    });

    it('deve rejeitar tipo de consentimento invalido', async () => {
      const res = await request(app)
        .post('/api/v1/data/consent')
        .set('Authorization', `Bearer ${token}`)
        .send({ consentType: 'invalid', granted: true })
        .expect(400);

      expect(res.body.error).toBe('ERR_VALIDATION');
    });
  });

  describe('POST /api/v1/data/delete-request', () => {
    it('deve registrar e executar delecao de dados', async () => {
      const res = await request(app)
        .post('/api/v1/data/delete-request')
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      expect(res.body.message).toContain('anonimizados');

      const users = await query('SELECT name, email, active FROM users WHERE id = ?', [userId]);
      expect(users[0].name).toBe('[ANONYMIZED]');
      expect(users[0].active).toBe(0);

      const tenants = await query('SELECT active FROM tenants WHERE id = ?', [tenantId]);
      expect(tenants[0].active).toBe(0);
    });

    it('deve rejeitar sem autenticacao', async () => {
      const res = await request(app)
        .post('/api/v1/data/delete-request')
        .expect(401);
    });
  });
});
