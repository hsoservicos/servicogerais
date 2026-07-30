const { Router } = require('express');
const controller = require('./data.controller');
const { authenticate } = require('../../middlewares/auth.middleware');
const { injectTenant } = require('../../middlewares/tenant.middleware');

const router = Router();

router.use(authenticate, injectTenant);

router.get('/export', controller.exportData);
router.post('/delete-request', controller.requestDeletion);
router.post('/process-deletion', controller.processDeletion);
router.get('/consent', controller.listConsents);
router.post('/consent', controller.updateConsent);

module.exports = router;
