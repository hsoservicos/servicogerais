const { Router } = require('express');
const controller = require('./incidents.controller');
const { authenticate } = require('../../middlewares/auth.middleware');
const { injectTenant } = require('../../middlewares/tenant.middleware');

const router = Router();
router.use(authenticate, injectTenant);

router.get('/', controller.list);
router.post('/', controller.create);
router.get('/:id', controller.read);
router.put('/:id', controller.update);
router.patch('/:id/status', controller.updateStatus);

router.post('/:id/sos', controller.triggerSos);
router.post('/:id/cat', controller.emitCat);

module.exports = router;
