const request = require('supertest');
const app = require('../../server');
const { seedTenant, seedUser, seedProposal, seedClient, cleanDatabase } = require('../setup/fixtures');
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
    it('deve registrar na fila de delecao (15 dias)', async () => {
      const res = await request(app)
        .post('/api/v1/data/delete-request')
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      expect(res.body.message).toContain('15 dias');
      expect(res.body).toHaveProperty('scheduledFor');

      const users = await query('SELECT name, active FROM users WHERE id = ?', [userId]);
      expect(users[0].active).toBe(1);

      const queueItem = await query(
        'SELECT id, status FROM deletion_queue WHERE user_id = ?', [userId]
      );
      expect(queueItem.length).toBe(1);
      expect(queueItem[0].status).toBe('pending');
    });

    it('deve rejeitar sem autenticacao', async () => {
      const res = await request(app)
        .post('/api/v1/data/delete-request')
        .expect(401);
    });
  });

  describe('POST /api/v1/data/process-deletion', () => {
    it('deve processar fila de delecao pendente', async () => {
      const { query: dbQuery } = require('../../config/database');
      const pastDate = new Date(Date.now() - 24 * 60 * 60 * 1000);
      await dbQuery(
        `INSERT INTO deletion_queue (user_id, tenant_id, status, scheduled_for)
         VALUES (?, ?, 'pending', ?)`,
        [userId, tenantId, pastDate]
      );

      const res = await request(app)
        .post('/api/v1/data/process-deletion')
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      expect(res.body.processed).toBeGreaterThanOrEqual(1);

      const users = await query('SELECT name, active FROM users WHERE id = ?', [userId]);
      expect(users[0].name).toBe('[ANONYMIZED]');
      expect(users[0].active).toBe(0);
    });
  });
});
