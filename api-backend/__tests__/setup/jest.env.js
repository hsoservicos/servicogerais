const path = require('path');
require('dotenv').config({ path: path.resolve(__dirname, '../../.env.test') });

process.env.NODE_ENV = 'test';
process.env.LOG_LEVEL = 'silent';
process.env.DB_NAME = process.env.TEST_DB_NAME || 'servicos_flex_test';
process.env.DB_HOST = process.env.DB_HOST || 'localhost';
process.env.DB_USER = process.env.DB_USER || 'root';
process.env.DB_PASSWORD = process.env.DB_PASSWORD || 'root';
process.env.DB_PORT = process.env.DB_PORT || '3306';
