const { Router } = require('express');
const controller = require('./domestic.controller');
const { authenticate } = require('../../middlewares/auth.middleware');
const { injectTenant } = require('../../middlewares/tenant.middleware');

const router = Router();

router.use(authenticate, injectTenant);

router.post('/calculate-costs', controller.calculateCosts);
router.post('/transition-to-clt/:id', controller.transitionToCLT);

module.exports = router;