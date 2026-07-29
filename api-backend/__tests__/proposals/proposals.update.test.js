const request = require('supertest');
const app = require('../../server');
const { seedTenant, seedUser, seedProposal, cleanDatabase } = require('../setup/fixtures');
const { generateToken } = require('../helpers/auth.helper');
const { query } = require('../../config/database');

describe('Proposals Update & Delete', () => {
  let token, tenantId;

  beforeEach(async () => {
    await cleanDatabase();
    const tenant = await seedTenant();
    tenantId = tenant.id;
    const user = await seedUser({ tenant_id: tenantId });
    token = generateToken({ id: user.id, tenantId, email: user.email });
  });

  describe('PUT /api/v1/proposals/:id', () => {
    it('deve atualizar proposta em draft', async () => {
      const prop = await seedProposal({ tenant_id: tenantId });

      await request(app)
        .put(`/api/v1/proposals/${prop.id}`)
        .set('Authorization', `Bearer ${token}`)
        .send({ title: 'Titulo Atualizado' })
        .expect(200);
    });

    it('deve rejeitar edicao de proposta sent', async () => {
      const prop = await seedProposal({ tenant_id: tenantId, status: 'sent' });

      const res = await request(app)
        .put(`/api/v1/proposals/${prop.id}`)
        .set('Authorization', `Bearer ${token}`)
        .send({ title: 'Tentativa' })
        .expect(422);

      expect(res.body.error).toBe('ERR_INVALID_STATUS');
    });

    it('deve permitir edicao de proposta rejected', async () => {
      const prop = await seedProposal({ tenant_id: tenantId, status: 'rejected' });

      await request(app)
        .put(`/api/v1/proposals/${prop.id}`)
        .set('Authorization', `Bearer ${token}`)
        .send({ title: 'Revisao' })
        .expect(200);
    });
  });

  describe('DELETE /api/v1/proposals/:id', () => {
    it('deve cancelar proposta', async () => {
      const prop = await seedProposal({ tenant_id: tenantId });

      await request(app)
        .delete(`/api/v1/proposals/${prop.id}`)
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      const rows = await query('SELECT status FROM proposals WHERE id = ?', [prop.id]);
      expect(rows[0].status).toBe('cancelled');
    });

    it('deve retornar 404 para proposta ja cancelada', async () => {
      const prop = await seedProposal({ tenant_id: tenantId, status: 'cancelled' });

      const res = await request(app)
        .delete(`/api/v1/proposals/${prop.id}`)
        .set('Authorization', `Bearer ${token}`)
        .expect(404);
    });
  });
});
