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

module.exports = router;