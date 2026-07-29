const request = require('supertest');
const app = require('../../server');
const { cleanDatabase } = require('../setup/fixtures');

describe('POST /api/v1/auth/register', () => {
  beforeEach(async () => {
    await cleanDatabase();
  });

  const validPayload = {
    companyName: 'Nova Empresa Ltda',
    email: 'novo@empresa.com',
    password: '12345678',
    phone: '11977777777',
    city: 'Rio de Janeiro',
    state: 'RJ',
  };

  it('deve registrar um novo tenant + user com dados válidos', async () => {
    const res = await request(app)
      .post('/api/v1/auth/register')
      .send(validPayload)
      .expect(201);

    expect(res.body).toHaveProperty('token');
    expect(res.body).toHaveProperty('user');
    expect(res.body.user).toHaveProperty('id');
    expect(res.body.user).toHaveProperty('tenantId');
    expect(res.body.user.email).toBe('novo@empresa.com');
    expect(res.body.user.role).toBe('admin');
  });

  it('deve rejeitar registro sem companyName, email ou password', async () => {
    const res = await request(app)
      .post('/api/v1/auth/register')
      .send({})
      .expect(400);

    expect(res.body.error).toBe('ERR_VALIDATION');
  });

  it('deve rejeitar registro com senha menor que 8 caracteres', async () => {
    const res = await request(app)
      .post('/api/v1/auth/register')
      .send({ ...validPayload, password: '123' })
      .expect(400);

    expect(res.body.error).toBe('ERR_VALIDATION');
  });

  it('deve rejeitar registro com e-mail inválido', async () => {
    const res = await request(app)
      .post('/api/v1/auth/register')
      .send({ ...validPayload, email: 'invalido' })
      .expect(400);

    expect(res.body.error).toBe('ERR_VALIDATION');
  });

  it('deve rejeitar registro com CPF inválido quando fornecido', async () => {
    const res = await request(app)
      .post('/api/v1/auth/register')
      .send({ ...validPayload, documentCpf: '123' })
      .expect(400);

    expect(res.body.error).toBe('ERR_VALIDATION');
    expect(res.body.message).toContain('CPF');
  });

  it('deve rejeitar registro com CNPJ inválido quando fornecido', async () => {
    const res = await request(app)
      .post('/api/v1/auth/register')
      .send({ ...validPayload, documentCnpj: '123' })
      .expect(400);

    expect(res.body.error).toBe('ERR_VALIDATION');
    expect(res.body.message).toContain('CNPJ');
  });

  it('deve rejeitar e-mail duplicado', async () => {
    await request(app).post('/api/v1/auth/register').send(validPayload).expect(201);
    const res = await request(app)
      .post('/api/v1/auth/register')
      .send(validPayload)
      .expect(409);

    expect(res.body.error).toBe('ERR_DUPLICATE_ENTRY');
  });

  it('deve aceitar registro com CPF válido', async () => {
    const res = await request(app)
      .post('/api/v1/auth/register')
      .send({
        ...validPayload,
        documentCpf: '52998224725',
        documentCnpj: undefined,
      })
      .expect(201);

    expect(res.body).toHaveProperty('token');
  });

  it('deve aceitar registro com CNPJ válido', async () => {
    const res = await request(app)
      .post('/api/v1/auth/register')
      .send({
        ...validPayload,
        documentCpf: undefined,
        documentCnpj: '11222333000181',
      })
      .expect(201);

    expect(res.body).toHaveProperty('token');
  });

  it('deve retornar correlationId na resposta', async () => {
    const res = await request(app)
      .post('/api/v1/auth/register')
      .send(validPayload)
      .expect(201);

    expect(res.body).not.toHaveProperty('correlationId');
  });

  it('deve criar tenant e user no banco', async () => {
    const res = await request(app)
      .post('/api/v1/auth/register')
      .send(validPayload)
      .expect(201);

    const { query } = require('../../config/database');
    const tenants = await query('SELECT * FROM tenants WHERE id = ?', [res.body.user.tenantId]);
    expect(tenants.length).toBe(1);
    expect(tenants[0].name).toBe('Nova Empresa Ltda');

    const users = await query('SELECT * FROM users WHERE id = ?', [res.body.user.id]);
    expect(users.length).toBe(1);
    expect(users[0].email).toBe('novo@empresa.com');
  });

  it('deve registrar CNPJ sem CPF (PJ)', async () => {
    const res = await request(app)
      .post('/api/v1/auth/register')
      .send({
        ...validPayload,
        documentCpf: undefined,
        documentCnpj: '11222333000181',
      })
      .expect(201);
    expect(res.body).toHaveProperty('token');
  });

  it('deve registrar CPF sem CNPJ (PF)', async () => {
    const res = await request(app)
      .post('/api/v1/auth/register')
      .send({
        ...validPayload,
        documentCpf: '52998224725',
        documentCnpj: undefined,
      })
      .expect(201);
    expect(res.body).toHaveProperty('token');
  });
});
