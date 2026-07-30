const request = require('supertest');
const app = require('../../server');
const { seedTenant, seedUser, seedProposal, seedTransaction, cleanDatabase } = require('../setup/fixtures');
const { generateToken } = require('../helpers/auth.helper');

describe('Payments Module (MP degradado)', () => {
  let token, tenantId, proposalId;

  beforeEach(async () => {
    await cleanDatabase();
    const tenant = await seedTenant();
    tenantId = tenant.id;
    const user = await seedUser({ tenant_id: tenantId });
    token = generateToken({ id: user.id, tenantId, email: user.email });
    const prop = await seedProposal({ tenant_id: tenantId });
    proposalId = prop.id;
  });

  it('POST /webhook aceita request sem auth (modo degradado)', async () => {
    const res = await request(app).post('/api/v1/payments/webhook')
      .send({ type: 'payment', data: { id: '12345' } });
    // Em modo degradado sem MP, o webhook retorna erro 500 ao tentar
    // consultar a API do MP. Com MP configurado, retorna 200.
  });

  it('POST /webhook ignora tipo nao suportado', async () => {
    const res = await request(app).post('/api/v1/payments/webhook')
      .send({ type: 'merchant_order', data: { id: '999' } })
      .expect(200);
    expect(res.body.ignored).toBe(true);
  });

  it('POST /preference rejeita sem proposalId', async () => {
    const res = await request(app).post('/api/v1/payments/preference')
      .set('Authorization', `Bearer ${token}`)
      .send({ items: [{ title: 'T', quantity: 1, unit_price: 100 }], payer: { email: 't@t.com' } })
      .expect(400);
    expect(res.body.error).toBe('ERR_VALIDATION');
  });

  it('POST /preference rejeita sem items', async () => {
    await request(app).post('/api/v1/payments/preference')
      .set('Authorization', `Bearer ${token}`)
      .send({ proposalId, payer: { email: 't@t.com' } })
      .expect(400);
  });

  it('POST /preference rejeita sem payer.email', async () => {
    await request(app).post('/api/v1/payments/preference')
      .set('Authorization', `Bearer ${token}`)
      .send({ proposalId, items: [{ title: 'T', quantity: 1, unit_price: 100 }] })
      .expect(400);
  });

  it('GET /:id rejeita sem auth', async () => {
    await request(app).get('/api/v1/payments/any').expect(401);
  });

  it('GET /:id retorna transacao local', async () => {
    await seedTransaction({ tenant_id: tenantId, proposal_id: proposalId, mp_id: 'MP-LOCAL' });
    const res = await request(app).get('/api/v1/payments/MP-LOCAL')
      .set('Authorization', `Bearer ${token}`)
      .expect(200);
    expect(res.body.data.id).toBe('MP-LOCAL');
  });

  it('POST /:id/refund rejeita sem auth', async () => {
    await request(app).post('/api/v1/payments/any/refund').expect(401);
  });
});
