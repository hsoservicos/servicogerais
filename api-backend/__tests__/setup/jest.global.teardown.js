const mysql = require('mysql2');

const TEST_DB = process.env.TEST_DB_NAME || 'servicos_flex_test';

module.exports = async function globalTeardown() {
  const host = process.env.TEST_DB_HOST || process.env.DB_HOST || 'localhost';
  const port = parseInt(process.env.TEST_DB_PORT || process.env.DB_PORT, 10) || 3306;
  const user = process.env.TEST_DB_USER || process.env.DB_USER || 'root';
  const password = process.env.TEST_DB_PASSWORD || process.env.DB_PASSWORD || 'root';

  const conn = mysql.createConnection({ host, port, user, password });

  const connect = () => new Promise((resolve, reject) => {
    conn.connect((err) => {
      if (err) reject(err);
      else resolve();
    });
  });

  const queryPromise = (sql) => new Promise((resolve, reject) => {
    conn.query(sql, (err, result) => {
      if (err) reject(err);
      else resolve(result);
    });
  });

  await connect();
  await queryPromise(`DROP DATABASE IF EXISTS \`${TEST_DB}\``);
  conn.end();
};
