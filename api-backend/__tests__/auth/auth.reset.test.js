const request = require('supertest');
const app = require('../../server');
const { seedTenant, seedUser } = require('../setup/fixtures');
const { query } = require('../../config/database');
const { v4: uuidv4 } = require('uuid');
const { cleanDatabase } = require('../setup/fixtures');

describe('POST /api/v1/auth/reset-password', () => {
  let resetToken;

  beforeEach(async () => {
    await cleanDatabase();
    const tenant = await seedTenant();
    await seedUser({
      tenant_id: tenant.id,
      email: 'reset@teste.com',
      password: '12345678',
    });

    resetToken = uuidv4();
    const expiresAt = new Date(Date.now() + 60 * 60 * 1000);
    const expiresAtStr = expiresAt.toISOString().slice(0, 19).replace('T', ' ');
    await query(
      `UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE email = ?`,
      [resetToken, expiresAtStr, 'reset@teste.com']
    );
  });

  it('deve redefinir senha com token válido', async () => {
    const res = await request(app)
      .post('/api/v1/auth/reset-password')
      .send({ token: resetToken, password: 'novaSenha123' });

    expect(res.status).toBe(200);
    expect(res.body).toHaveProperty('message');
    expect(res.body.message).toContain('Senha');
  });

  it('deve rejeitar token inválido', async () => {
    const res = await request(app)
      .post('/api/v1/auth/reset-password')
      .send({ token: 'token_invalido', password: 'novaSenha123' })
      .expect(400);

    expect(res.body.error).toBe('ERR_INVALID_TOKEN');
  });

  it('deve rejeitar requisição sem token ou password', async () => {
    const res = await request(app)
      .post('/api/v1/auth/reset-password')
      .send({})
      .expect(400);

    expect(res.body.error).toBe('ERR_VALIDATION');
  });

  it('deve rejeitar senha menor que 8 caracteres', async () => {
    const res = await request(app)
      .post('/api/v1/auth/reset-password')
      .send({ token: resetToken, password: '123' })
      .expect(400);

    expect(res.body.error).toBe('ERR_VALIDATION');
  });

  it('deve rejeitar token expirado', async () => {
    const expiredToken = uuidv4();
    const pastDate = new Date(Date.now() - 60 * 60 * 1000);
    const pastDateStr = pastDate.toISOString().slice(0, 19).replace('T', ' ');
    await query(
      `UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE email = ?`,
      [expiredToken, pastDateStr, 'reset@teste.com']
    );

    const res = await request(app)
      .post('/api/v1/auth/reset-password')
      .send({ token: expiredToken, password: 'novaSenha123' })
      .expect(400);

    expect(res.body.error).toBe('ERR_INVALID_TOKEN');
  });

  it('deve limpar reset_token após redefinição', async () => {
    await request(app)
      .post('/api/v1/auth/reset-password')
      .send({ token: resetToken, password: 'novaSenha123' })
      .expect(200);

    const { findUserByEmail } = require('../helpers/db.helper');
    const user = await findUserByEmail('reset@teste.com');
    expect(user.reset_token).toBeNull();
    expect(user.reset_token_expires).toBeNull();
  });

  it('deve permitir login com nova senha após reset', async () => {
    await request(app)
      .post('/api/v1/auth/reset-password')
      .send({ token: resetToken, password: 'novaSenha123' })
      .expect(200);

    const res = await request(app)
      .post('/api/v1/auth/login')
      .send({ email: 'reset@teste.com', password: 'novaSenha123' })
      .expect(200);

    expect(res.body).toHaveProperty('token');
  });

  it('deve negar login com senha antiga após reset', async () => {
    await request(app)
      .post('/api/v1/auth/reset-password')
      .send({ token: resetToken, password: 'novaSenha123' })
      .expect(200);

    const res = await request(app)
      .post('/api/v1/auth/login')
      .send({ email: 'reset@teste.com', password: '12345678' })
      .expect(401);

    expect(res.body.error).toBe('ERR_INVALID_CREDENTIALS');
  });
});
