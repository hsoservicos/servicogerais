const request = require('supertest');
const app = require('../../server');
const { seedTenant, seedUser } = require('../setup/fixtures');
const { generateToken } = require('../helpers/auth.helper');
const { cleanDatabase } = require('../setup/fixtures');

describe('GET /api/v1/auth/me', () => {
  let token;
  let tenantId;
  let userId;

  beforeEach(async () => {
    await cleanDatabase();
    const tenant = await seedTenant();
    tenantId = tenant.id;
    const user = await seedUser({
      tenant_id: tenantId,
      email: 'me@teste.com',
      password: '12345678',
    });
    userId = user.id;
    token = generateToken({ id: userId, tenantId, email: 'me@teste.com' });
  });

  it('deve retornar dados do usuário autenticado', async () => {
    const res = await request(app)
      .get('/api/v1/auth/me')
      .set('Authorization', `Bearer ${token}`)
      .expect(200);

    expect(res.body).toHaveProperty('user');
    expect(res.body.user.email).toBe('me@teste.com');
    expect(res.body.user.tenantId).toBe(tenantId);
  });

  it('deve rejeitar requisição sem token', async () => {
    const res = await request(app)
      .get('/api/v1/auth/me')
      .expect(401);

    expect(res.body.error).toBe('ERR_UNAUTHORIZED');
  });

  it('deve rejeitar token inválido', async () => {
    const res = await request(app)
      .get('/api/v1/auth/me')
      .set('Authorization', 'Bearer token_invalido')
      .expect(401);

    expect(res.body.error).toBe('ERR_INVALID_TOKEN');
  });

  it('deve rejeitar token com formato inválido', async () => {
    const res = await request(app)
      .get('/api/v1/auth/me')
      .set('Authorization', 'InvalidFormat token123')
      .expect(401);

    expect(res.body.error).toBe('ERR_INVALID_TOKEN_FORMAT');
  });

  it('deve rejeitar requisição sem header Authorization', async () => {
    const res = await request(app)
      .get('/api/v1/auth/me')
      .expect(401);

    expect(res.body.error).toBe('ERR_UNAUTHORIZED');
  });

  it('deve retornar dados corretos do tenant', async () => {
    const res = await request(app)
      .get('/api/v1/auth/me')
      .set('Authorization', `Bearer ${token}`)
      .expect(200);

    expect(res.body.user.tenantName).toBe('Tenant Teste Ltda');
  });

  it('deve retornar 404 para usuário inexistente', async () => {
    const fakeToken = generateToken({ id: 99999, tenantId: 1, email: 'fake@teste.com' });
    const res = await request(app)
      .get('/api/v1/auth/me')
      .set('Authorization', `Bearer ${fakeToken}`)
      .expect(404);

    expect(res.body.error).toBe('ERR_NOT_FOUND');
  });
});
