const request = require('supertest');
const app = require('../../server');
const { seedTenant, seedUser, seedCategory, seedService } = require('../setup/fixtures');
const { generateToken } = require('../helpers/auth.helper');
const { findServiceById } = require('../helpers/db.helper');
const { cleanDatabase } = require('../setup/fixtures');

describe('Services CRUD', () => {
  let token;
  let tenantId;
  let categoryId;

  beforeEach(async () => {
    await cleanDatabase();
    const tenant = await seedTenant();
    tenantId = tenant.id;
    const cat = await seedCategory({ tenant_id: tenantId });
    categoryId = cat.id;
    const user = await seedUser({ tenant_id: tenantId });
    token = generateToken({ id: user.id, tenantId, email: user.email });
  });

  describe('POST /api/v1/services', () => {
    it('deve criar serviço com dados mínimos', async () => {
      const res = await request(app)
        .post('/api/v1/services')
        .set('Authorization', `Bearer ${token}`)
        .send({ name: 'Novo Serviço' })
        .expect(201);

      expect(res.body.service.name).toBe('Novo Serviço');
    });

    it('deve criar serviço com todos os campos', async () => {
      const res = await request(app)
        .post('/api/v1/services')
        .set('Authorization', `Bearer ${token}`)
        .send({
          name: 'Serviço Premium',
          description: 'Descrição premium',
          price: 250.00,
          duration_minutes: 120,
          category_id: categoryId,
        })
        .expect(201);

      expect(res.body.service.name).toBe('Serviço Premium');
    });

    it('deve rejeitar serviço sem nome', async () => {
      const res = await request(app)
        .post('/api/v1/services')
        .set('Authorization', `Bearer ${token}`)
        .send({ price: 100 })
        .expect(400);

      expect(res.body.error).toBe('ERR_VALIDATION');
    });

    it('deve rejeitar sem autenticação', async () => {
      const res = await request(app)
        .post('/api/v1/services')
        .send({ name: 'Sem Auth' })
        .expect(401);
    });
  });

  describe('GET /api/v1/services', () => {
    it('deve listar serviços vazia', async () => {
      const res = await request(app)
        .get('/api/v1/services')
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      expect(res.body.services.length).toBe(0);
    });

    it('deve listar serviços com paginação', async () => {
      await seedService({ tenant_id: tenantId, category_id: categoryId, name: 'Serv A' });
      await seedService({ tenant_id: tenantId, category_id: categoryId, name: 'Serv B' });

      const res = await request(app)
        .get('/api/v1/services')
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      expect(res.body.services.length).toBe(2);
    });

    it('deve filtrar serviços por categoria', async () => {
      const cat2 = await seedCategory({ tenant_id: tenantId, name: 'Outra Cat' });
      await seedService({ tenant_id: tenantId, category_id: categoryId, name: 'Serv Cat1' });
      await seedService({ tenant_id: tenantId, category_id: cat2.id, name: 'Serv Cat2' });

      const res = await request(app)
        .get(`/api/v1/services?category_id=${categoryId}`)
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      expect(res.body.services.length).toBe(1);
      expect(res.body.services[0].name).toBe('Serv Cat1');
    });

    it('deve filtrar por search', async () => {
      await seedService({ tenant_id: tenantId, category_id: categoryId, name: 'Limpeza Pesada' });
      await seedService({ tenant_id: tenantId, category_id: categoryId, name: 'Jardinagem' });

      const res = await request(app)
        .get('/api/v1/services?search=Limpeza')
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      expect(res.body.services.length).toBe(1);
    });
  });

  describe('GET /api/v1/services/:id', () => {
    it('deve retornar serviço por ID', async () => {
      const svc = await seedService({ tenant_id: tenantId, category_id: categoryId });

      const res = await request(app)
        .get(`/api/v1/services/${svc.id}`)
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      expect(res.body.service.name).toBe(svc.name);
      expect(res.body.service).toHaveProperty('category_name');
    });

    it('deve retornar 404 para serviço inexistente', async () => {
      const res = await request(app)
        .get('/api/v1/services/99999')
        .set('Authorization', `Bearer ${token}`)
        .expect(404);
    });
  });

  describe('PUT /api/v1/services/:id', () => {
    it('deve atualizar serviço', async () => {
      const svc = await seedService({ tenant_id: tenantId, category_id: categoryId });

      await request(app)
        .put(`/api/v1/services/${svc.id}`)
        .set('Authorization', `Bearer ${token}`)
        .send({ name: 'Serviço Atualizado', price: 199.90 })
        .expect(200);

      const updated = await findServiceById(svc.id);
      expect(updated.name).toBe('Serviço Atualizado');
    });

    it('deve retornar 404 para serviço de outro tenant', async () => {
      const tenant2 = await seedTenant({ name: 'Outro', slug: 'outro-svc' });
      const svc = await seedService({ tenant_id: tenant2.id, category_id: null });

      const res = await request(app)
        .put(`/api/v1/services/${svc.id}`)
        .set('Authorization', `Bearer ${token}`)
        .send({ name: 'Hack' })
        .expect(404);
    });
  });

  describe('DELETE /api/v1/services/:id', () => {
    it('deve desativar serviço', async () => {
      const svc = await seedService({ tenant_id: tenantId, category_id: categoryId });

      await request(app)
        .delete(`/api/v1/services/${svc.id}`)
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      const deleted = await findServiceById(svc.id);
      expect(deleted.active).toBe(0);
    });

    it('deve retornar 404 para serviço já inativo', async () => {
      const svc = await seedService({ tenant_id: tenantId, category_id: categoryId, active: false });

      const res = await request(app)
        .delete(`/api/v1/services/${svc.id}`)
        .set('Authorization', `Bearer ${token}`)
        .expect(404);
    });
  });
});
