require('dotenv').config();

function getEnvOrWarn(key, defaultValue, warning) {
  const val = process.env[key] || defaultValue;
  if (!process.env[key] && process.env.NODE_ENV === 'production') {
    console.warn(`[SEC] ⚠️ ${warning} — defina ${key} no .env`);
  }
  return val;
}

module.exports = {
  jwt: {
    secret: getEnvOrWarn(
      'JWT_SECRET',
      'change-me-in-production-use-64-char-random-string',
      'JWT_SECRET está usando valor padrão'
    ),
    expiresIn: process.env.JWT_EXPIRES_IN || '24h',
    algorithm: 'HS256',
  },
  refreshToken: {
    secret: getEnvOrWarn(
      'JWT_REFRESH_SECRET',
      'change-me-refresh-secret',
      'JWT_REFRESH_SECRET está usando valor padrão'
    ),
    expiresIn: '30d',
  },
  password: {
    saltRounds: 12,
    minLength: 8,
  },
  rateLimit: {
    windowMs: parseInt(process.env.RATE_LIMIT_WINDOW_MS, 10) || 15 * 60 * 1000,
    max: parseInt(process.env.RATE_LIMIT_MAX, 10) || 100,
    authMax: parseInt(process.env.RATE_LIMIT_AUTH_MAX, 10) || 5,
  },
  cors: {
    origin: process.env.CORS_ORIGIN
      ? process.env.CORS_ORIGIN.split(',').map(s => s.trim())
      : (process.env.NODE_ENV === 'production' ? ['https://seu-dominio.com'] : '*'),
    methods: ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'],
    allowedHeaders: ['Content-Type', 'Authorization', 'X-Request-ID'],
    credentials: true,
    maxAge: 86400,
  },
  helmet: {
    contentSecurityPolicy: {
      directives: {
        defaultSrc: ["'self'"],
        styleSrc: ["'self'", "'unsafe-inline'", 'https://fonts.googleapis.com', 'https://cdnjs.cloudflare.com'],
        scriptSrc: ["'self'", "'unsafe-inline'", 'https://cdn.jsdelivr.net', 'https://cdnjs.cloudflare.com'],
        fontSrc: ["'self'", 'https://fonts.gstatic.com'],
        imgSrc: ["'self'", 'data:', 'https:'],
        connectSrc: ["'self'"],
      },
    },
    crossOriginEmbedderPolicy: false,
    crossOriginResourcePolicy: { policy: 'cross-origin' },
  },
};
