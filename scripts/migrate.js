#!/usr/bin/env node
// ═══════════════════════════════════════════════════════════════
// scripts/migrate.js — Migration Framework (ServiceSaaS)
// ═══════════════════════════════════════════════════════════════
// CLIs: node scripts/migrate.js [up|down|status|create <name>]
// ═══════════════════════════════════════════════════════════════

require('dotenv').config();
const mysql = require('mysql2/promise');
const fs = require('fs');
const path = require('path');
const crypto = require('crypto');

const DB = {
  host: process.env.DB_HOST || 'localhost',
  port: parseInt(process.env.DB_PORT, 10) || 3306,
  user: process.env.DB_USER || 'root',
  password: process.env.DB_PASSWORD || 'root',
  database: process.env.DB_NAME || 'servicos_flex',
  multipleStatements: true,
};

const MIGRATIONS_DIR = path.resolve(__dirname, 'migrations');

async function getPool() {
  return mysql.createPool(DB);
}

async function ensureMetaTable(pool) {
  await pool.execute(
    `CREATE TABLE IF NOT EXISTS schema_migrations (
      version VARCHAR(14) NOT NULL PRIMARY KEY,
      name VARCHAR(255) NOT NULL,
      checksum VARCHAR(64) NOT NULL,
      applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB`
  );
}

async function getApplied(pool) {
  const [rows] = await pool.execute('SELECT version, name, checksum FROM schema_migrations ORDER BY version ASC');
  return rows;
}

function computeChecksum(content) {
  return crypto.createHash('sha256').update(content).digest('hex');
}

function getAvailable() {
  const files = fs.readdirSync(MIGRATIONS_DIR).filter(f => f.endsWith('.sql')).sort();
  return files.map(f => {
    const match = f.match(/^(\d{3,})_(.+)\.sql$/);
    return { file: f, version: match ? match[1] : f, name: match ? match[2] : f.replace('.sql', ''), path: path.join(MIGRATIONS_DIR, f) };
  });
}

async function cmdStatus() {
  const pool = await getPool();
  await ensureMetaTable(pool);
  const applied = await getApplied(pool);
  const available = getAvailable();
  const appliedMap = new Map(applied.map(m => [m.version, m]));

  console.log('\n📋 Migration Status:\n');
  for (const m of available) {
    const a = appliedMap.get(m.version);
    const status = a ? '✅ ' + a.applied_at?.toISOString().slice(0, 19).replace('T', ' ') : '⬜ PENDING';
    console.log(`  [${m.version}] ${m.name}  ${status}`);
  }
  console.log(`\n  ${applied.length}/${available.length} migrations applied\n`);
  await pool.end();
}

async function cmdUp() {
  const pool = await getPool();
  await ensureMetaTable(pool);
  const applied = await getApplied(pool);
  const appliedVersions = new Set(applied.map(m => m.version));
  const available = getAvailable();

  for (const m of available) {
    if (appliedVersions.has(m.version)) continue;
    const sql = fs.readFileSync(m.path, 'utf8');
    const checksum = computeChecksum(sql);
    console.log(`⬆️  Applying [${m.version}] ${m.name}...`);
    try {
      await pool.query(sql);
      await pool.execute('INSERT INTO schema_migrations (version, name, checksum) VALUES (?, ?, ?)', [m.version, m.name, checksum]);
      console.log(`  ✅ ${m.name} applied`);
    } catch (err) {
      console.error(`  ❌ ${m.name} failed: ${err.message}`);
      console.log('  ⚠️  Aborting — no migrations were applied after this point.');
      await pool.end();
      process.exit(1);
    }
  }
  await pool.end();
  console.log('\n✅ All pending migrations applied.\n');
}

async function cmdDown() {
  const pool = await getPool();
  await ensureMetaTable(pool);
  const applied = await getApplied(pool);
  const last = applied.pop();
  if (!last) { console.log('No migrations to roll back.'); await pool.end(); return; }
  console.log(`⬇️  Rolling back [${last.version}] ${last.name}...`);
  await pool.execute('DELETE FROM schema_migrations WHERE version = ?', [last.version]);
  console.log(`  ✅ ${last.name} rolled back`);
  await pool.end();
}

async function cmdCreate(name) {
  if (!name) { console.error('Usage: node scripts/migrate.js create <name>'); process.exit(1); }
  const version = new Date().toISOString().slice(0, 10).replace(/-/g, '');
  const seq = String(Date.now()).slice(-4);
  const filename = `${version}${seq}_${name.replace(/\s+/g, '_').toLowerCase()}.sql`;
  const filepath = path.join(MIGRATIONS_DIR, filename);
  fs.writeFileSync(filepath, `-- Migration: ${name}\n-- Date: ${new Date().toISOString()}\n\n`);
  console.log(`✅ Created: ${filepath}`);
}

const cmd = process.argv[2] || 'status';
const arg = process.argv[3];

switch (cmd) {
  case 'up': cmdUp().catch(console.error); break;
  case 'down': cmdDown().catch(console.error); break;
  case 'status': cmdStatus().catch(console.error); break;
  case 'create': cmdCreate(arg); break;
  default: console.log('Usage: node scripts/migrate.js [up|down|status|create <name>]'); break;
}
