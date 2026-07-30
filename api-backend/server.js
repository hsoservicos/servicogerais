// ═══════════════════════════════════════════════════════════════
// server.js — Express Server Entry Point (ServiceSaaS)
// ═══════════════════════════════════════════════════════════════

require('dotenv').config();

const express = require('express');
const cors = require('cors');
const helmet = require('helmet');
const rateLimit = require('express-rate-limit');

const { requestId } = require('./middlewares/requestId.middleware');
const { errorHandler } = require('./middlewares/error.middleware');
const { testConnection } = require('./config/database');
const authConfig = require('./config/auth');
const { cors: corsConfig, rateLimit: rateConfig } = authConfig;

const app = express();
const PORT = process.env.PORT || 3000;

// ═══════════════════════════════════════════════════════════════
// 1. Global Middleware Stack
// ═══════════════════════════════════════════════════════════════

app.set('trust proxy', 1); // Trust first proxy (nginx)

app.use(helmet(authConfig.helmet || {}));
app.use(cors(corsConfig));
app.use(express.json({ limit: '10mb' }));
app.use(express.urlencoded({ extended: true }));
app.use(requestId);

// ── Rate Limiter Global ──────────────────────────────────
app.use(rateLimit({
  windowMs: rateConfig.windowMs,
  max: rateConfig.max,
  message: {
    error: 'ERR_RATE_LIMIT',
    message: 'Muitas requisições. Tente novamente mais tarde.',
  },
  standardHeaders: true,
  legacyHeaders: false,
}));

// ── Request Logger Estruturado ───────────────────────────
const LOG_LEVELS = { silent: 0, error: 1, warn: 2, info: 3, debug: 4 };
const currentLevel = LOG_LEVELS[process.env.LOG_LEVEL] || LOG_LEVELS.info;

function shouldLog(level) {
  const levelNum = LOG_LEVELS[level] || LOG_LEVELS.info;
  return levelNum <= currentLevel;
}

app.use((req, res, next) => {
  const start = Date.now();
  res.on('finish', () => {
    if (!shouldLog('info')) return;
    const logData = {
      level: res.statusCode >= 500 ? 'error' : res.statusCode >= 400 ? 'warn' : 'info',
      timestamp: new Date().toISOString(),
      method: req.method,
      path: req.path,
      status: res.statusCode,
      duration_ms: Date.now() - start,
      correlationId: req.correlationId,
    };
    if (logData.level === 'error') {
      console.error(JSON.stringify(logData));
    } else if (logData.level === 'warn') {
      console.warn(JSON.stringify(logData));
    } else {
      console.log(JSON.stringify(logData));
    }
  });
  next();
});

// ═══════════════════════════════════════════════════════════════
// 2. Routes
// ═══════════════════════════════════════════════════════════════

// ── Health Check Handler (reutilizável) ─────────────────
async function healthHandler(req, res) {
  const dbHealth = await testConnection();
  const status = dbHealth.status === 'healthy' ? 'healthy' : 'degraded';
  const statusCode = dbHealth.status === 'healthy' ? 200 : 503;

  res.status(statusCode).json({
    status,
    service: 'servicos-flex-api',
    version: '1.0.0',
    timestamp: new Date().toISOString(),
    database: dbHealth,
    correlationId: req.correlationId,
  });
}

// /health → Docker HEALTHCHECK interno
// /api/v1/health → via nginx proxy (externo)
app.get('/health', healthHandler);
app.get('/api/v1/health', healthHandler);

// ── Auth Routes ──────────────────────────────────────────
app.use('/api/v1/auth', require('./modules/auth/auth.routes'));

// ── Tenants Routes (Profile) ─────────────────────────────
app.use('/api/v1/tenants', require('./modules/tenants/tenants.routes'));

// ── Clients Routes ───────────────────────────────────────
app.use('/api/v1/clients', require('./modules/clients/clients.routes'));

// ── Dashboard Routes ─────────────────────────────────────
app.use('/api/v1/dashboard', require('./modules/dashboard/dashboard.routes'));

// ── Catalog Routes (Epic 2 - Story 2.2) ─────────────────
app.use('/api/v1/categories', require('./modules/catalog/categories.routes'));
app.use('/api/v1/services', require('./modules/catalog/services.routes'));

// ── Proposals Routes (Epic 3) ───────────────────────────
app.use('/api/v1/proposals', require('./modules/proposals/proposals.routes'));

// ── Admin Routes (Epic 7) ────────────────────────────────
app.use('/api/v1/admin', require('./modules/admin/admin.routes'));

// ── Mercado Pago / Payments Routes (Epic 5) ──────────────
app.use('/api/v1/payments', require('./modules/payments/payments.routes'));

// ── Transactions Routes (Epic 4 — Story 4.4) ───────────
app.use('/api/v1/transactions', require('./modules/transactions/transactions.routes'));

// ── Leads Routes (Epic 6 — Story 6.4) ─────────────────
app.use('/api/v1/leads', require('./modules/leads/leads.routes'));

// ── Public Routes (Epic 6 — Story 6.1) ──────────────────
app.use('/api/v1/public', require('./modules/public/public.routes'));

// ── Domestic Workers Routes (Epic 8 — Story 8.1) ────────
app.use('/api/v1/workers', require('./modules/domestic/workers.routes'));

// ── Schedules Routes (Epic 9 — Story 9.1) ────────────────
app.use('/api/v1/schedules', require('./modules/domestic/schedules.routes'));

// ── Data Privacy Routes (Epic 13 — LGPD) ─────────────────
app.use('/api/v1/data', require('./modules/data/data.routes'));

// ── Domestic Operations Routes (Epic 9 — Story 9.2/9.3) ─
app.use('/api/v1/domestic', require('./modules/domestic/domestic.routes'));

// ── Incidents Routes (Epic 12) ───────────────────────────
app.use('/api/v1/incidents', require('./modules/incidents/incidents.routes'));

// ── 404 Handler ──────────────────────────────────────────
app.use((req, res) => {
  res.status(404).json({
    error: 'ERR_NOT_FOUND',
    message: `Rota não encontrada: ${req.method} ${req.path}`,
    correlationId: req.correlationId,
  });
});

// ═══════════════════════════════════════════════════════════════
// 3. Error Handler
// ═══════════════════════════════════════════════════════════════

app.use(errorHandler);

// ═══════════════════════════════════════════════════════════════
// 4. Server Startup
// ═══════════════════════════════════════════════════════════════

async function start() {
  // Verificar conexão com BD antes de iniciar
  console.log('[API] 🔌 Verificando conexão com o banco de dados...');
  try {
    const dbHealth = await testConnection();
    if (dbHealth.status !== 'healthy') {
      console.warn('[API] ⚠️  Banco de dados não disponível. Iniciando mesmo assim...');
    } else {
      console.log('[API] ✅ Conexão com MySQL estabelecida');
    }
  } catch (err) {
    console.warn('[API] ⚠️  Erro ao conectar ao BD. Servidor iniciará sem BD.');
  }

  // Verificar configuração do Mercado Pago
  const { isMercadoPagoAvailable } = require('./config/mercadopago');
  if (!isMercadoPagoAvailable()) {
    console.warn('[API] ⚠️  Mercado Pago não configurado — modo degradado (defina MP_ACCESS_TOKEN no .env)');
  } else {
    console.log('[API] ✅ Mercado Pago configurado e pronto');
  }

  app.listen(PORT, '0.0.0.0', () => {
    console.log(`[API] 🚀 ServiceSaaS API rodando em http://0.0.0.0:${PORT}`);
    console.log(`[API] 📋 Health: http://localhost:${PORT}/health`);
    console.log(`[API] 🔐 Auth:   http://localhost:${PORT}/api/v1/auth`);
    console.log(`[API] 💳 Payments: http://localhost:${PORT}/api/v1/payments`);
  });
}

if (process.env.NODE_ENV !== 'test') {
  start();
}

module.exports = app;
