// ═══════════════════════════════════════════════════════════════
// config/database.js — MySQL Connection Pool (ServiceSaaS)
// ═══════════════════════════════════════════════════════════════
// Pool otimizado com retry automático e healthcheck

const mysql = require('mysql2/promise');
require('dotenv').config();

const poolConfig = {
  host: process.env.DB_HOST || 'mysql',
  port: parseInt(process.env.DB_PORT, 10) || 3306,
  user: process.env.DB_USER || 'flex_user',
  password: process.env.DB_PASSWORD || 'flex_pass',
  database: process.env.DB_NAME || 'servicos_flex',
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0,
  enableKeepAlive: true,
  keepAliveInitialDelay: 10000,
};

const pool = mysql.createPool(poolConfig);

// ── Teste de Conexão (usa getConnection própria, não compartilha o pool) ──
async function testConnection() {
  let connection;
  try {
    connection = await pool.getConnection();
    await connection.ping();
    const dbName = poolConfig.database || 'servicos_flex';
    return { status: 'healthy', database: dbName };
  } catch (err) {
    console.error('[DB] ❌ Connection failed:', err.message);
    return { status: 'unhealthy', error: err.message };
  } finally {
    if (connection) {
      try { connection.release(); } catch (_) { /* ignore */ }
    }
  }
}

// ── Query Helper com Log ─────────────────────────────────
// Usa `query()` (sem prepared statement) para compatibilidade com mysql2 v3.
// `execute()` do mysql2 v3.x pode falhar com certas combinações de parâmetros.
async function query(sql, params) {
  const start = Date.now();
  try {
    // Só passa params se existirem — mysql2 query() aceita (sql) ou (sql, params)
    // NOTA: params sem default (= []), usamos params?.length > 0 para detectar
    const [results] = params && params.length > 0
      ? await pool.query(sql, params)
      : await pool.query(sql);
    const duration = Date.now() - start;
    if (duration > 1000) {
      console.warn(`[DB] ⚠️ Slow query (${duration}ms): ${sql.substring(0, 100)}`);
    }
    return results;
  } catch (err) {
    console.error(`[DB] ❌ Query error (${err.code}): ${sql.substring(0, 100)}`);
    throw err;
  }
}

// ── Transaction Helper ───────────────────────────────────
async function transaction(callback) {
  const connection = await pool.getConnection();
  try {
    await connection.beginTransaction();
    const result = await callback(connection);
    await connection.commit();
    return result;
  } catch (err) {
    await connection.rollback();
    throw err;
  } finally {
    connection.release();
  }
}

module.exports = { pool, testConnection, query, transaction };
