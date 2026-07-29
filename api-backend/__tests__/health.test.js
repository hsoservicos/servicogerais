const request = require('supertest');
const app = require('../server');

describe('Health Endpoints', () => {
  it('GET /health deve retornar status 200', async () => {
    const res = await request(app)
      .get('/health')
      .expect(200);

    expect(res.body).toHaveProperty('status');
    expect(res.body).toHaveProperty('service');
    expect(res.body.service).toBe('servicos-flex-api');
  });

  it('GET /api/v1/health deve retornar status', async () => {
    const res = await request(app)
      .get('/api/v1/health')
      .expect(200);

    expect(res.body).toHaveProperty('status');
    expect(res.body).toHaveProperty('database');
  });

  it('GET /rota-inexistente deve retornar 404', async () => {
    const res = await request(app)
      .get('/api/v1/rota-inexistente')
      .expect(404);

    expect(res.body.error).toBe('ERR_NOT_FOUND');
  });

  it('deve retornar correlationId em respostas', async () => {
    const res = await request(app)
      .get('/api/v1/health');

    expect(res.headers['x-request-id']).toBeDefined();
  });
});
