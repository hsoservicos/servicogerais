const mysql = require('mysql2');
const path = require('path');
const fs = require('fs');

const TEST_DB = process.env.TEST_DB_NAME || 'servicos_flex_test';

module.exports = async function globalSetup() {
  const host = process.env.TEST_DB_HOST || process.env.DB_HOST || 'localhost';
  const port = parseInt(process.env.TEST_DB_PORT || process.env.DB_PORT, 10) || 3306;
  const user = process.env.TEST_DB_USER || process.env.DB_USER || 'root';
  const password = process.env.TEST_DB_PASSWORD || process.env.DB_PASSWORD || 'root';

  const conn = mysql.createConnection({ host, port, user, password, multipleStatements: true });

  const connect = () => new Promise((resolve, reject) => {
    conn.connect((err) => { if (err) reject(err); else resolve(); });
  });

  const queryPromise = (sql) => new Promise((resolve, reject) => {
    conn.query(sql, (err, result) => { if (err) reject(err); else resolve(result); });
  });

  await connect();
  await queryPromise(`DROP DATABASE IF EXISTS \`${TEST_DB}\``);
  await queryPromise(`CREATE DATABASE \`${TEST_DB}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci`);

  const initSqlPath = path.resolve(__dirname, '../../../scripts/init.sql');
  let initSql = fs.readFileSync(initSqlPath, 'utf8');
  initSql = initSql
    .replace(/CREATE DATABASE IF NOT EXISTS servicos_flex/g, 'CREATE DATABASE IF NOT EXISTS `' + TEST_DB + '`')
    .replace(/USE servicos_flex;/g, 'USE `' + TEST_DB + '`;');
  await queryPromise(initSql);

  const migrationExtra = [
    'ALTER TABLE users ADD COLUMN reset_token VARCHAR(255) NULL',
    'ALTER TABLE users ADD COLUMN reset_token_expires DATETIME NULL',
    'ALTER TABLE tenants ADD COLUMN zipcode VARCHAR(9) NULL',
    'ALTER TABLE tenants ADD COLUMN address VARCHAR(500) NULL',
    'ALTER TABLE tenants ADD COLUMN neighborhood VARCHAR(100) NULL',
    'ALTER TABLE tenants ADD COLUMN city VARCHAR(100) NULL',
    'ALTER TABLE tenants ADD COLUMN state CHAR(2) NULL',
    'ALTER TABLE tenants ADD COLUMN latitude DECIMAL(10,8) NULL',
    'ALTER TABLE tenants ADD COLUMN longitude DECIMAL(11,8) NULL',
    'ALTER TABLE clients ADD COLUMN zipcode VARCHAR(9) NULL',
  ];
  for (const stmt of migrationExtra) {
    try { await queryPromise(stmt); } catch (_) { }
  }

  conn.end();
  console.log(`[TEST] Banco de testes '${TEST_DB}' criado.`);
};
