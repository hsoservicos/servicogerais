// ═══════════════════════════════════════════════════════════════
// modules/catalog/categories.routes.js — Categories CRUD Routes
// ═══════════════════════════════════════════════════════════════
// Todas as rotas são protegidas: authenticate + injectTenant

const { Router } = require('express');
const controller = require('./categories.controller');
const { authenticate } = require('../../middlewares/auth.middleware');
const { injectTenant } = require('../../middlewares/tenant.middleware');

const router = Router();

router.use(authenticate);
router.use(injectTenant);

router.get('/', controller.list);       // GET  /categories?search=&page=&perPage=
router.post('/', controller.create);    // POST /categories
router.get('/:id', controller.read);    // GET  /categories/:id
router.put('/:id', controller.update);  // PUT  /categories/:id
router.delete('/:id', controller.remove); // DELETE /categories/:id

module.exports = router;
