const request = require('supertest');
const app = require('../../server');
const { seedTenant, seedUser } = require('../setup/fixtures');
const { findUserByEmail } = require('../helpers/db.helper');
const { cleanDatabase } = require('../setup/fixtures');

describe('POST /api/v1/auth/forgot-password', () => {
  beforeEach(async () => {
    await cleanDatabase();
    const tenant = await seedTenant();
    await seedUser({
      tenant_id: tenant.id,
      email: 'forgot@teste.com',
      password: '12345678',
    });
  });

  it('deve retornar mensagem genérica para e-mail existente', async () => {
    const res = await request(app)
      .post('/api/v1/auth/forgot-password')
      .send({ email: 'forgot@teste.com' })
      .expect(200);

    expect(res.body.message).toContain('Se o e-mail existir');
  });

  it('deve retornar mensagem genérica para e-mail inexistente', async () => {
    const res = await request(app)
      .post('/api/v1/auth/forgot-password')
      .send({ email: 'naoexiste@teste.com' })
      .expect(200);

    expect(res.body.message).toContain('Se o e-mail existir');
  });

  it('deve criar reset_token no banco para e-mail existente', async () => {
    await request(app)
      .post('/api/v1/auth/forgot-password')
      .send({ email: 'forgot@teste.com' })
      .expect(200);

    const user = await findUserByEmail('forgot@teste.com');
    expect(user.reset_token).toBeTruthy();
    expect(user.reset_token_expires).toBeTruthy();
  });

  it('deve rejeitar requisição sem email', async () => {
    const res = await request(app)
      .post('/api/v1/auth/forgot-password')
      .send({})
      .expect(400);

    expect(res.body.error).toBe('ERR_VALIDATION');
  });

  it('não deve criar reset_token para e-mail inexistente', async () => {
    await request(app)
      .post('/api/v1/auth/forgot-password')
      .send({ email: 'naoexiste@teste.com' })
      .expect(200);

    const { query } = require('../../config/database');
    const users = await query('SELECT * FROM users WHERE reset_token IS NOT NULL');
    expect(users.length).toBe(0);
  });
});
