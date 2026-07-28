// ═══════════════════════════════════════════════════════════════
// modules/catalog/services.routes.js — Services CRUD Routes
// ═══════════════════════════════════════════════════════════════
// Todas as rotas são protegidas: authenticate + injectTenant

const { Router } = require('express');
const controller = require('./services.controller');
const { authenticate } = require('../../middlewares/auth.middleware');
const { injectTenant } = require('../../middlewares/tenant.middleware');

const router = Router();

router.use(authenticate);
router.use(injectTenant);

router.get('/', controller.list);       // GET  /services?search=&category_id=&page=
router.post('/', controller.create);    // POST /services
router.get('/:id', controller.read);    // GET  /services/:id
router.put('/:id', controller.update);  // PUT  /services/:id
router.delete('/:id', controller.remove); // DELETE /services/:id

module.exports = router;
