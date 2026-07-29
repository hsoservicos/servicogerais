const request = require('supertest');
const app = require('../../server');
const { seedTenant, seedUser, seedClient, cleanDatabase } = require('../setup/fixtures');
const { generateToken } = require('../helpers/auth.helper');

describe('Schedules', () => {
  let token, tenantId, clientId;
  const createdWorkerIds = [];

  beforeEach(async () => {
    await cleanDatabase();
    createdWorkerIds.length = 0;
    const tenant = await seedTenant();
    tenantId = tenant.id;
    const user = await seedUser({ tenant_id: tenantId });
    token = generateToken({ id: user.id, tenantId, email: user.email });
    const client = await seedClient({ tenant_id: tenantId });
    clientId = client.id;
  });

  let workerSeq = 0;
  const createWorker = async () => {
    workerSeq++;
    const cpf = '52998224725';
    const res = await request(app)
      .post('/api/v1/workers')
      .set('Authorization', `Bearer ${token}`)
      .send({
        name: 'Maria Diarista',
        cpf,
        cboCode: '911105',
        workerCategory: 'DIARISTA',
        phone: '11977777777',
      });
    if (!res.body.worker) return null;
    return res.body.worker.id;
  };

  const tomorrow = () => {
    const d = new Date();
    d.setDate(d.getDate() + 1);
    return d.toISOString().slice(0, 10);
  };

  const schedulePayload = async () => ({
    workerId: await createWorker(),
    clientId,
    serviceCategory: 'DIARISTA',
    regime: 'AUTONOMO_DIARISTA',
    scheduledDate: tomorrow(),
    startTime: '08:00',
    endTime: '17:00',
    hourlyRate: 20,
  });

  describe('POST /api/v1/schedules', () => {
    it('deve criar agendamento', async () => {
      const res = await request(app)
        .post('/api/v1/schedules')
        .set('Authorization', `Bearer ${token}`)
        .send(await schedulePayload());

      expect(res.status).toBe(201);
      expect(res.body.schedule).toBeDefined();
    });

    it('deve rejeitar agendamento sem worker', async () => {
      const res = await request(app)
        .post('/api/v1/schedules')
        .set('Authorization', `Bearer ${token}`)
        .send({ clientId, serviceCategory: 'DIARISTA', regime: 'AUTONOMO_DIARISTA', scheduledDate: tomorrow() })
        .expect(422);

      expect(res.body.error).toBe('ERR_VALIDATION');
    });
  });

  describe('GET /api/v1/schedules/:id', () => {
    it('deve retornar 404 para agendamento inexistente', async () => {
      await request(app)
        .get('/api/v1/schedules/99999')
        .set('Authorization', `Bearer ${token}`)
        .expect(404);
    });
  });

  describe('PATCH /api/v1/schedules/:id/status', () => {
    it('deve atualizar status do agendamento', async () => {
      const createRes = await request(app)
        .post('/api/v1/schedules')
        .set('Authorization', `Bearer ${token}`)
        .send(await schedulePayload());

      expect(createRes.status).toBe(201);
      expect(createRes.body.schedule).toBeDefined();

      await request(app)
        .patch(`/api/v1/schedules/${createRes.body.schedule.id}/status`)
        .set('Authorization', `Bearer ${token}`)
        .send({ status: 'confirmed' })
        .expect(200);
    });
  });
});
