const { cleanDatabase } = require('./fixtures');

async function setupTestDatabase() {
  await cleanDatabase();
}

module.exports = { setupTestDatabase };
