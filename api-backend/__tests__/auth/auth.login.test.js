const request = require('supertest');
const app = require('../../server');
const { seedTenant, seedUser } = require('../setup/fixtures');
const { generateToken } = require('../helpers/auth.helper');
const { cleanDatabase } = require('../setup/fixtures');

describe('POST /api/v1/auth/login', () => {
  let tenantId;

  beforeEach(async () => {
    await cleanDatabase();
    const tenant = await seedTenant();
    tenantId = tenant.id;
    await seedUser({
      tenant_id: tenantId,
      email: 'login@teste.com',
      password: '12345678',
    });
  });

  it('deve autenticar com credenciais válidas', async () => {
    const res = await request(app)
      .post('/api/v1/auth/login')
      .send({ email: 'login@teste.com', password: '12345678' })
      .expect(200);

    expect(res.body).toHaveProperty('token');
    expect(res.body).toHaveProperty('user');
    expect(res.body.user.email).toBe('login@teste.com');
    expect(res.body.user.role).toBe('admin');
  });

  it('deve rejeitar login sem email ou password', async () => {
    const res = await request(app)
      .post('/api/v1/auth/login')
      .send({})
      .expect(400);

    expect(res.body.error).toBe('ERR_VALIDATION');
  });

  it('deve rejeitar login com senha incorreta', async () => {
    const res = await request(app)
      .post('/api/v1/auth/login')
      .send({ email: 'login@teste.com', password: 'senha_errada' })
      .expect(401);

    expect(res.body.error).toBe('ERR_INVALID_CREDENTIALS');
  });

  it('deve rejeitar login com e-mail inexistente', async () => {
    const res = await request(app)
      .post('/api/v1/auth/login')
      .send({ email: 'naoexiste@teste.com', password: '12345678' })
      .expect(401);

    expect(res.body.error).toBe('ERR_INVALID_CREDENTIALS');
  });

  it('deve rejeitar login de tenant inativo', async () => {
    const inactiveTenant = await seedTenant({ name: 'Inativo', slug: 'inativo', active: 0 });
    await seedUser({ tenant_id: inactiveTenant.id, email: 'inativo@teste.com', password: '12345678' });

    const res = await request(app)
      .post('/api/v1/auth/login')
      .send({ email: 'inativo@teste.com', password: '12345678' })
      .expect(401);

    expect(res.body.error).toBe('ERR_INVALID_CREDENTIALS');
  });

  it('deve rejeitar login de usuário inativo', async () => {
    await seedUser({
      tenant_id: tenantId,
      email: 'inativo-user@teste.com',
      password: '12345678',
      active: 0,
    });

    const res = await request(app)
      .post('/api/v1/auth/login')
      .send({ email: 'inativo-user@teste.com', password: '12345678' })
      .expect(401);

    expect(res.body.error).toBe('ERR_INVALID_CREDENTIALS');
  });

  it('deve gerar token JWT com claims corretas', async () => {
    const res = await request(app)
      .post('/api/v1/auth/login')
      .send({ email: 'login@teste.com', password: '12345678' })
      .expect(200);

    const jwt = require('jsonwebtoken');
    const { jwt: jwtConfig } = require('../../config/auth');
    const decoded = jwt.verify(res.body.token, jwtConfig.secret);

    expect(decoded).toHaveProperty('sub');
    expect(decoded).toHaveProperty('tenant_id');
    expect(decoded).toHaveProperty('role');
    expect(decoded.role).toBe('admin');
  });

  it('deve retornar token JWT expirando em 24h', async () => {
    const res = await request(app)
      .post('/api/v1/auth/login')
      .send({ email: 'login@teste.com', password: '12345678' })
      .expect(200);

    const jwt = require('jsonwebtoken');
    const { jwt: jwtConfig } = require('../../config/auth');
    const decoded = jwt.verify(res.body.token, jwtConfig.secret);

    const expSeconds = decoded.exp - decoded.iat;
    expect(expSeconds).toBe(86400);
  });
});
