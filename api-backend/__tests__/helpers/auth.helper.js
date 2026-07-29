const jwt = require('jsonwebtoken');
const { jwt: jwtConfig } = require('../../config/auth');

function generateToken(userOverrides = {}) {
  const payload = {
    sub: userOverrides.id || 1,
    tenant_id: userOverrides.tenantId || 1,
    role: userOverrides.role || 'admin',
    email: userOverrides.email || 'admin@teste.com',
  };

  return jwt.sign(payload, jwtConfig.secret, {
    expiresIn: jwtConfig.expiresIn,
    algorithm: jwtConfig.algorithm,
  });
}

function generateAdminToken(overrides = {}) {
  return generateToken({
    id: overrides.id || 999,
    tenantId: null,
    role: 'super_admin',
    email: overrides.email || 'admin@servicesaas.com',
  });
}

module.exports = { generateToken, generateAdminToken };
