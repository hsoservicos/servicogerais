const request = require('supertest');
const app = require('../../server');
const { seedTenant, seedUser, seedProposal, seedProposalItem, cleanDatabase } = require('../setup/fixtures');
const { generateToken } = require('../helpers/auth.helper');
const { query } = require('../../config/database');

describe('Proposal Items', () => {
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

  describe('POST /api/v1/proposals/:proposalId/items', () => {
    it('deve adicionar item a proposta', async () => {
      const res = await request(app)
        .post(`/api/v1/proposals/${proposalId}/items`)
        .set('Authorization', `Bearer ${token}`)
        .send({ description: 'Serviço de Limpeza', quantity: 2, unit_price: 150 })
        .expect(201);

      expect(res.body.item.description).toBe('Serviço de Limpeza');
      expect(res.body.total_amount).toBe(300);
    });

    it('deve rejeitar item sem descricao', async () => {
      const res = await request(app)
        .post(`/api/v1/proposals/${proposalId}/items`)
        .set('Authorization', `Bearer ${token}`)
        .send({ quantity: 1, unit_price: 100 })
        .expect(400);

      expect(res.body.error).toBe('ERR_VALIDATION');
    });
  });

  describe('GET /api/v1/proposals/:proposalId/items', () => {
    it('deve listar itens da proposta', async () => {
      await seedProposalItem({ proposal_id: proposalId });
      await seedProposalItem({ proposal_id: proposalId });

      const res = await request(app)
        .get(`/api/v1/proposals/${proposalId}/items`)
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      expect(res.body.items).toHaveLength(2);
    });
  });

  describe('PUT /api/v1/proposals/:proposalId/items/:id', () => {
    it('deve atualizar item e recalcular total', async () => {
      const item = await seedProposalItem({ proposal_id: proposalId, unit_price: 100 });

      const res = await request(app)
        .put(`/api/v1/proposals/${proposalId}/items/${item.id}`)
        .set('Authorization', `Bearer ${token}`)
        .send({ unit_price: 250 })
        .expect(200);

      expect(res.body.total_amount).toBe(250);
    });
  });

  describe('DELETE /api/v1/proposals/:proposalId/items/:id', () => {
    it('deve remover item e recalcular total', async () => {
      const item = await seedProposalItem({ proposal_id: proposalId, unit_price: 100 });

      await request(app)
        .delete(`/api/v1/proposals/${proposalId}/items/${item.id}`)
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      const rows = await query('SELECT id FROM proposal_items WHERE id = ?', [item.id]);
      expect(rows).toHaveLength(0);
    });
  });

  it('deve rejeitar alteracao em proposta nao editavel', async () => {
    const sentProp = await seedProposal({ tenant_id: tenantId, status: 'sent' });

    const res = await request(app)
      .post(`/api/v1/proposals/${sentProp.id}/items`)
      .set('Authorization', `Bearer ${token}`)
      .send({ description: 'Item', quantity: 1, unit_price: 50 })
      .expect(422);

    expect(res.body.error).toBe('ERR_INVALID_STATUS');
  });
});
