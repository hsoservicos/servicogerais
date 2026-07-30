const request = require('supertest');
const app = require('../../server');
const { cleanDatabase } = require('../setup/fixtures');
const { query } = require('../../config/database');
const { v4: uuidv4 } = require('uuid');

describe('E2E: Fluxo Completo de Proposta', () => {
  let tenantId, userId, clientId, proposalId, publicToken;

  beforeAll(async () => {
    await cleanDatabase();
  });

  afterAll(async () => {
    await cleanDatabase();
  });

  it('1. Registrar tenant', async () => {
    const res = await request(app)
      .post('/api/v1/auth/register')
      .send({
        companyName: 'Empresa E2E',
        email: 'e2e@teste.com',
        password: '12345678',
        phone: '11999999999',
      })
      .expect(201);

    tenantId = res.body.user.tenantId;
    userId = res.body.user.id;
    expect(res.body.token).toBeTruthy();
  });

  it('2. Login', async () => {
    const res = await request(app)
      .post('/api/v1/auth/login')
      .send({ email: 'e2e@teste.com', password: '12345678' })
      .expect(200);

    expect(res.body.token).toBeTruthy();
  });

  it('3. Criar cliente', async () => {
    const login = await request(app)
      .post('/api/v1/auth/login')
      .send({ email: 'e2e@teste.com', password: '12345678' });
    const token = login.body.token;

    const res = await request(app)
      .post('/api/v1/clients')
      .set('Authorization', `Bearer ${token}`)
      .send({ name: 'Cliente E2E', phone: '11988888888' })
      .expect(201);

    clientId = res.body.client.id;
  });

  it('4. Criar categoria', async () => {
    const login = await request(app)
      .post('/api/v1/auth/login')
      .send({ email: 'e2e@teste.com', password: '12345678' });
    const token = login.body.token;

    await request(app)
      .post('/api/v1/categories')
      .set('Authorization', `Bearer ${token}`)
      .send({ name: 'Limpeza' })
      .expect(201);
  });

  it('5. Criar proposta com itens', async () => {
    const login = await request(app)
      .post('/api/v1/auth/login')
      .send({ email: 'e2e@teste.com', password: '12345678' });
    const token = login.body.token;

    const res = await request(app)
      .post('/api/v1/proposals')
      .set('Authorization', `Bearer ${token}`)
      .send({
        title: 'Proposta E2E',
        client_id: clientId,
        items: [
          { description: 'Limpeza simples', quantity: 2, unit_price: 80 },
          { description: 'Limpeza pesada', quantity: 1, unit_price: 150 },
        ],
      })
      .expect(201);

    proposalId = res.body.proposal.id;
    publicToken = res.body.proposal.public_token;
    expect(res.body.proposal.total_amount).toBe(310);
  });

  it('6. Enviar proposta (draft -> sent)', async () => {
    const login = await request(app)
      .post('/api/v1/auth/login')
      .send({ email: 'e2e@teste.com', password: '12345678' });
    const token = login.body.token;

    await request(app)
      .patch(`/api/v1/proposals/${proposalId}/status`)
      .set('Authorization', `Bearer ${token}`)
      .send({ status: 'sent' })
      .expect(200);

    const rows = await query('SELECT status FROM proposals WHERE id = ?', [proposalId]);
    expect(rows[0].status).toBe('sent');
  });

  it('7. Aprovar proposta via link publico (sent -> viewed -> accepted)', async () => {
    await request(app)
      .patch(`/api/v1/public/proposals/${publicToken}/status`)
      .send({ action: 'approve' })
      .expect(200);

    let rows = await query('SELECT status FROM proposals WHERE id = ?', [proposalId]);
    expect(rows[0].status).toBe('viewed');

    await request(app)
      .patch(`/api/v1/public/proposals/${publicToken}/status`)
      .send({ action: 'approve' })
      .expect(200);

    rows = await query('SELECT status FROM proposals WHERE id = ?', [proposalId]);
    expect(rows[0].status).toBe('accepted');
  });

  it('8. Ver dashboard com dados', async () => {
    const login = await request(app)
      .post('/api/v1/auth/login')
      .send({ email: 'e2e@teste.com', password: '12345678' });
    const token = login.body.token;

    const res = await request(app)
      .get('/api/v1/dashboard')
      .set('Authorization', `Bearer ${token}`)
      .expect(200);

    expect(res.body.clients).toBeGreaterThanOrEqual(1);
    expect(res.body.proposals).toBeGreaterThanOrEqual(1);
  });
});
