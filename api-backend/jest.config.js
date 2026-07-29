module.exports = {
  testEnvironment: 'node',
  setupFiles: ['./__tests__/setup/jest.env.js'],
  globalSetup: './__tests__/setup/jest.global.setup.js',
  globalTeardown: './__tests__/setup/jest.global.teardown.js',
  testMatch: ['**/__tests__/**/*.test.js'],
  testTimeout: 30000,
  forceExit: true,
  detectOpenHandles: true,
  verbose: true,
  maxConcurrency: 1,
  roots: ['<rootDir>/__tests__'],
};
