const request = require('supertest');
const app = require('../../server');
const { seedTenant, seedUser, seedProposal, cleanDatabase } = require('../setup/fixtures');
const { generateToken } = require('../helpers/auth.helper');

describe('Proposals Status Lifecycle', () => {
  let token, tenantId;

  beforeEach(async () => {
    await cleanDatabase();
    const tenant = await seedTenant();
    tenantId = tenant.id;
    const user = await seedUser({ tenant_id: tenantId });
    token = generateToken({ id: user.id, tenantId, email: user.email });
  });

  const validTransitions = [
    ['draft', 'sent'],
    ['sent', 'viewed'],
    ['viewed', 'accepted'],
    ['accepted', 'cancelled'],
    ['draft', 'cancelled'],
    ['rejected', 'draft'],
    ['cancelled', 'draft'],
  ];

  const invalidTransitions = [
    ['draft', 'accepted'],
    ['draft', 'rejected'],
    ['sent', 'draft'],
    ['accepted', 'sent'],
    ['accepted', 'rejected'],
  ];

  validTransitions.forEach(([from, to]) => {
    it(`deve permitir transicao: ${from} -> ${to}`, async () => {
      const prop = await seedProposal({ tenant_id: tenantId, status: from });

      const res = await request(app)
        .patch(`/api/v1/proposals/${prop.id}/status`)
        .set('Authorization', `Bearer ${token}`)
        .send({ status: to })
        .expect(200);

      expect(res.body.proposal.status).toBe(to);
    });
  });

  invalidTransitions.forEach(([from, to]) => {
    it(`deve rejeitar transicao invalida: ${from} -> ${to}`, async () => {
      const prop = await seedProposal({ tenant_id: tenantId, status: from });

      const res = await request(app)
        .patch(`/api/v1/proposals/${prop.id}/status`)
        .set('Authorization', `Bearer ${token}`)
        .send({ status: to })
        .expect(422);

      expect(res.body.error).toBe('ERR_INVALID_TRANSITION');
    });
  });

  it('deve rejeitar status invalido', async () => {
    const prop = await seedProposal({ tenant_id: tenantId, status: 'draft' });

    const res = await request(app)
      .patch(`/api/v1/proposals/${prop.id}/status`)
      .set('Authorization', `Bearer ${token}`)
      .send({ status: 'invalid_status' })
      .expect(400);

    expect(res.body.error).toBe('ERR_VALIDATION');
  });

  it('deve rejeitar patch sem status', async () => {
    const prop = await seedProposal({ tenant_id: tenantId, status: 'draft' });

    const res = await request(app)
      .patch(`/api/v1/proposals/${prop.id}/status`)
      .set('Authorization', `Bearer ${token}`)
      .send({})
      .expect(400);

    expect(res.body.error).toBe('ERR_VALIDATION');
  });

  it('deve marcar sent_at ao enviar proposta', async () => {
    const prop = await seedProposal({ tenant_id: tenantId, status: 'draft' });

    await request(app)
      .patch(`/api/v1/proposals/${prop.id}/status`)
      .set('Authorization', `Bearer ${token}`)
      .send({ status: 'sent' })
      .expect(200);

    const { query } = require('../../config/database');
    const rows = await query('SELECT sent_at FROM proposals WHERE id = ?', [prop.id]);
    expect(rows[0].sent_at).toBeTruthy();
  });

  it('deve marcar accepted_at ao aceitar proposta', async () => {
    const prop = await seedProposal({ tenant_id: tenantId, status: 'sent' });

    await request(app)
      .patch(`/api/v1/proposals/${prop.id}/status`)
      .set('Authorization', `Bearer ${token}`)
      .send({ status: 'accepted' })
      .expect(200);

    const { query } = require('../../config/database');
    const rows = await query('SELECT accepted_at FROM proposals WHERE id = ?', [prop.id]);
    expect(rows[0].accepted_at).toBeTruthy();
  });
});
