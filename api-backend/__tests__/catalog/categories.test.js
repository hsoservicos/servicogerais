const request = require('supertest');
const app = require('../../server');
const { seedTenant, seedUser, seedCategory } = require('../setup/fixtures');
const { generateToken } = require('../helpers/auth.helper');
const { findCategoryById } = require('../helpers/db.helper');
const { cleanDatabase } = require('../setup/fixtures');

describe('Categories CRUD', () => {
  let token;
  let tenantId;

  beforeEach(async () => {
    await cleanDatabase();
    const tenant = await seedTenant();
    tenantId = tenant.id;
    const user = await seedUser({ tenant_id: tenantId });
    token = generateToken({ id: user.id, tenantId, email: user.email });
  });

  describe('POST /api/v1/categories', () => {
    it('deve criar categoria com dados mínimos', async () => {
      const res = await request(app)
        .post('/api/v1/categories')
        .set('Authorization', `Bearer ${token}`)
        .send({ name: 'Nova Categoria' })
        .expect(201);

      expect(res.body.category.name).toBe('Nova Categoria');
    });

    it('deve criar categoria com todos os campos', async () => {
      const res = await request(app)
        .post('/api/v1/categories')
        .set('Authorization', `Bearer ${token}`)
        .send({
          name: 'Categoria Completa',
          description: 'Descrição',
          icon: 'star',
          color: '#FF0000',
        })
        .expect(201);

      expect(res.body.category.name).toBe('Categoria Completa');
    });

    it('deve rejeitar categoria sem nome', async () => {
      const res = await request(app)
        .post('/api/v1/categories')
        .set('Authorization', `Bearer ${token}`)
        .send({})
        .expect(400);

      expect(res.body.error).toBe('ERR_VALIDATION');
    });

    it('deve rejeitar sem autenticação', async () => {
      const res = await request(app)
        .post('/api/v1/categories')
        .send({ name: 'Sem Auth' })
        .expect(401);
    });
  });

  describe('GET /api/v1/categories', () => {
    it('deve listar categorias vazia', async () => {
      const res = await request(app)
        .get('/api/v1/categories')
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      expect(res.body.categories.length).toBe(0);
    });

    it('deve listar categorias com paginação', async () => {
      await seedCategory({ tenant_id: tenantId, name: 'Cat A' });
      await seedCategory({ tenant_id: tenantId, name: 'Cat B' });

      const res = await request(app)
        .get('/api/v1/categories')
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      expect(res.body.categories.length).toBe(2);
    });

    it('deve filtrar por search', async () => {
      await seedCategory({ tenant_id: tenantId, name: 'Limpeza' });
      await seedCategory({ tenant_id: tenantId, name: 'Jardinagem' });

      const res = await request(app)
        .get('/api/v1/categories?search=Limpeza')
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      expect(res.body.categories.length).toBe(1);
    });
  });

  describe('GET /api/v1/categories/:id', () => {
    it('deve retornar categoria por ID', async () => {
      const cat = await seedCategory({ tenant_id: tenantId });

      const res = await request(app)
        .get(`/api/v1/categories/${cat.id}`)
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      expect(res.body.category.name).toBe(cat.name);
    });

    it('deve retornar 404 para categoria inexistente', async () => {
      const res = await request(app)
        .get('/api/v1/categories/99999')
        .set('Authorization', `Bearer ${token}`)
        .expect(404);
    });
  });

  describe('PUT /api/v1/categories/:id', () => {
    it('deve atualizar categoria', async () => {
      const cat = await seedCategory({ tenant_id: tenantId });

      await request(app)
        .put(`/api/v1/categories/${cat.id}`)
        .set('Authorization', `Bearer ${token}`)
        .send({ name: 'Categoria Atualizada' })
        .expect(200);

      const updated = await findCategoryById(cat.id);
      expect(updated.name).toBe('Categoria Atualizada');
    });

    it('deve retornar 404 para categoria de outro tenant', async () => {
      const tenant2 = await seedTenant({ name: 'Outro', slug: 'outro-cat' });
      const cat = await seedCategory({ tenant_id: tenant2.id });

      const res = await request(app)
        .put(`/api/v1/categories/${cat.id}`)
        .set('Authorization', `Bearer ${token}`)
        .send({ name: 'Hack' })
        .expect(404);
    });
  });

  describe('DELETE /api/v1/categories/:id', () => {
    it('deve desativar categoria', async () => {
      const cat = await seedCategory({ tenant_id: tenantId });

      await request(app)
        .delete(`/api/v1/categories/${cat.id}`)
        .set('Authorization', `Bearer ${token}`)
        .expect(200);

      const deleted = await findCategoryById(cat.id);
      expect(deleted.active).toBe(0);
    });

    it('deve retornar 404 para categoria já inativa', async () => {
      const cat = await seedCategory({ tenant_id: tenantId, active: false });

      const res = await request(app)
        .delete(`/api/v1/categories/${cat.id}`)
        .set('Authorization', `Bearer ${token}`)
        .expect(404);
    });
  });
});
