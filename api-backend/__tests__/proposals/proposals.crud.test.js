const request = require('supertest');
const app = require('../../server');
const { seedTenant, seedUser, seedClient, seedProposal, seedProposalItem, cleanDatabase } = require('../setup/fixtures');
const { generateToken } = require('../helpers/auth.helper');
const { query } = require('../../config/database');

describe('Proposals CRUD', () => {
  let token, tenantId, clientId;

  beforeEach(async () => {
    await cleanDatabase();
    const tenant = await seedTenant();
    tenantId = tenant.id;
    const user = await seedUser({ tenant_id: tenantId });
    token = generateToken({ id: user.id, tenantId, email: user.email });
    const client = await seedClient({ tenant_id: tenantId });
    clientId = client.id;
  });

  describe('POST /api/v1/proposals', () => {
    it('deve criar proposta sem itens', async () => {
      const res = await request(app)
        .post('/api/v1/proposals')
        .set('Authorization', `Bearer ${token}`)
        .send({ title: 'Proposta Simples', client_id: clientId })
        .expect(201);

      expect(res.body.proposal.title).toBe('Proposta Simples');
      expect(res.body.proposal.status).toBe('draft');
      expect(res.body.proposal.number).toContain('PROP-');
    });

    it('deve criar proposta com itens', async () => {
      const res = await request(app)
        .post('/api/v1/proposals')
        .set('Authorization', `Bearer ${token}`)
        .send({
          title: 'Proposta com Itens',
          client_id: clientId,
          items: [
            { description: 'Item 1', quantity: 2, unit_price: 50 },
            { description: 'Item 2', quantity: 1, unit_price: 100 },
          ],
        })
        .expect(201);

      expect(res.body.proposal.total_amount).toBe(200);
    });

    it('deve rejeitar proposta sem titulo', async () => {
      const res = await request(app)
        .post('/api/v1/proposals')
        .set('Authorization', `Bearer ${token}`)
        .send({ client_id: clientId })
        .expect(400);

      expect(res.body.error).toBe('ERR_VALIDATION');
    });

    it('deve gerar public_token na criacao', async () => {
      const res = await request(app)
        .post('/api/v1/proposals')
        .set('Authorization', `Bearer ${token}`)
        .send({ title: 'Proposta Token', client_id: clientId })
        .expect(201);

      expect(res.body.proposal.public_token).toBeTruthy();
    });
  });

  describe('GET /api/v1/proposals', () => {
    it('deve listar propostas vazia', async () => {
      const res = await request(app)
        .get('/api/v1/proposals')
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      expect(res.body.proposals).toHaveLength(0);
    });

    it('deve listar propostas do tenant', async () => {
      await seedProposal({ tenant_id: tenantId, title: 'Prop A' });
      await seedProposal({ tenant_id: tenantId, title: 'Prop B' });

      const res = await request(app)
        .get('/api/v1/proposals')
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      expect(res.body.proposals).toHaveLength(2);
    });

    it('deve filtrar por status', async () => {
      await seedProposal({ tenant_id: tenantId, title: 'Draft', status: 'draft' });
      await seedProposal({ tenant_id: tenantId, title: 'Sent', status: 'sent' });

      const res = await request(app)
        .get('/api/v1/proposals?status=sent')
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      expect(res.body.proposals).toHaveLength(1);
      expect(res.body.proposals[0].status).toBe('sent');
    });
  });

  describe('GET /api/v1/proposals/:id', () => {
    it('deve retornar proposta com itens', async () => {
      const prop = await seedProposal({ tenant_id: tenantId, client_id: clientId });
      await seedProposalItem({ proposal_id: prop.id });

      const res = await request(app)
        .get(`/api/v1/proposals/${prop.id}`)
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      expect(res.body.proposal.items).toHaveLength(1);
    });

    it('deve retornar 404 para proposta inexistente', async () => {
      const res = await request(app)
        .get('/api/v1/proposals/99999')
        .set('Authorization', `Bearer ${token}`)
        .expect(404);
    });
  });
});
