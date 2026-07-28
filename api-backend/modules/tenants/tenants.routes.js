const { Router } = require('express');
const controller = require('./tenants.controller');
const { authenticate } = require('../../middlewares/auth.middleware');
const { injectTenant } = require('../../middlewares/tenant.middleware');

const router = Router();

router.use(authenticate, injectTenant);

router.get('/me', controller.getProfile);
router.put('/me', controller.updateProfile);

module.exports = router;