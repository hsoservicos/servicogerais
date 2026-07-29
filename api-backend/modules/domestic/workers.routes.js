// ═══════════════════════════════════════════════════════════════
// modules/domestic/workers.routes.js — Workers Routes
// ═══════════════════════════════════════════════════════════════

const { Router } = require('express');
const controller = require('./workers.controller');
const { authenticate } = require('../../middlewares/auth.middleware');
const { injectTenant } = require('../../middlewares/tenant.middleware');

const router = Router();

router.use(authenticate, injectTenant);

router.get('/', controller.list);
router.post('/', controller.create);
router.get('/:id', controller.read);
router.put('/:id', controller.update);
router.delete('/:id', controller.remove);

router.get('/:id/certifications', controller.listCertifications);
router.post('/:id/certifications', controller.createCertification);
router.put('/:id/certifications/:certId', controller.updateCertification);
router.delete('/:id/certifications/:certId', controller.deleteCertification);

router.post('/:id/background-check', controller.backgroundCheck);
router.get('/:id/certification-required', controller.certificationRequiredCheck);

module.exports = router;