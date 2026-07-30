const request = require('supertest');
const app = require('../../server');
const { seedTenant, seedUser, cleanDatabase } = require('../setup/fixtures');
const { generateToken } = require('../helpers/auth.helper');
const { query } = require('../../config/database');

describe('Incidents Module (E12)', () => {
  let token, tenantId;

  beforeEach(async () => {
    await cleanDatabase();
    const tenant = await seedTenant();
    tenantId = tenant.id;
    const user = await seedUser({ tenant_id: tenantId });
    token = generateToken({ id: user.id, tenantId, email: user.email });
  });

  describe('POST /api/v1/incidents', () => {
    it('deve criar incidente', async () => {
      const res = await request(app)
        .post('/api/v1/incidents')
        .set('Authorization', `Bearer ${token}`)
        .send({ type: 'ACCIDENT', description: 'Queda durante limpeza da escada com ferimento leve' })
        .expect(201);

      expect(res.body.incident.protocol).toContain('INC-');
    });

    it('deve rejeitar sem descricao', async () => {
      const res = await request(app)
        .post('/api/v1/incidents')
        .set('Authorization', `Bearer ${token}`)
        .send({ type: 'ACCIDENT', description: 'Curta' })
        .expect(422);
    });

    it('deve rejeitar tipo invalido', async () => {
      const res = await request(app)
        .post('/api/v1/incidents')
        .set('Authorization', `Bearer ${token}`)
        .send({ type: 'INVALID', description: 'Descricao com mais de 10 caracteres' })
        .expect(422);
    });
  });

  describe('GET /api/v1/incidents', () => {
    it('deve listar vazio', async () => {
      const res = await request(app)
        .get('/api/v1/incidents')
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      expect(res.body.incidents).toHaveLength(0);
    });

    it('deve listar incidentes do tenant', async () => {
      await query(`INSERT INTO incidents (tenant_id, type, severity, description, protocol, status) VALUES (?, 'ACCIDENT', 'HIGH', 'Teste de incidente com descricao longa', 'INC-001', 'OPEN')`, [tenantId]);

      const res = await request(app)
        .get('/api/v1/incidents')
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      expect(res.body.incidents).toHaveLength(1);
    });
  });

  describe('POST /api/v1/incidents/:id/sos', () => {
    it('deve acionar SOS para incidente existente', async () => {
      const ins = await query(`INSERT INTO incidents (tenant_id, type, severity, description, protocol, status) VALUES (?, 'EMERGENCY', 'CRITICAL', 'Emergencia medica durante o expediente', 'INC-SOS-001', 'OPEN')`, [tenantId]);

      const res = await request(app)
        .post(`/api/v1/incidents/${ins.insertId}/sos`)
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      expect(res.body.message).toContain('SOS');
    });
  });

  describe('POST /api/v1/incidents/:id/cat', () => {
    it('deve emitir CAT para incidente', async () => {
      const ins = await query(`INSERT INTO incidents (tenant_id, type, severity, description, protocol, status) VALUES (?, 'ACCIDENT', 'HIGH', 'Acidente de trabalho com ferimento', 'INC-CAT-001', 'OPEN')`, [tenantId]);

      const res = await request(app)
        .post(`/api/v1/incidents/${ins.insertId}/cat`)
        .set('Authorization', `Bearer ${token}`)
        .send({ catType: 'TYPICAL' })
        .expect(200);

      expect(res.body.data.catNumber).toContain('CAT-');
    });
  });
});
