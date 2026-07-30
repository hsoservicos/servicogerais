const { Router } = require('express');
const { authenticate } = require('../../middlewares/auth.middleware');
const { adminAuth, auditLog } = require('./admin.middleware');
const dashboardCtrl = require('./admin.dashboard.controller');
const tenantsCtrl = require('./admin.tenants.controller');
const financeiroCtrl = require('./admin.financeiro.controller');
const auditCtrl = require('./admin.audit.controller');
const plansController = require('./plans.controller');

const router = Router();
router.use(authenticate);
router.use(adminAuth);

router.get('/dashboard', dashboardCtrl.dashboard);
router.get('/tenants', tenantsCtrl.listTenants);
router.get('/tenants/:id', tenantsCtrl.getTenant);
router.put('/tenants/:id', auditLog('update_tenant'), tenantsCtrl.updateTenant);
router.delete('/tenants/:id', tenantsCtrl.toggleTenantStatus);
router.get('/transactions', financeiroCtrl.listTransactions);
router.post('/transactions/:id/refund', financeiroCtrl.refundTransaction);
router.get('/plans', plansController.list);
router.get('/plans/:id', plansController.read);
router.post('/plans', auditLog('create_plan'), plansController.create);
router.put('/plans/:id', auditLog('update_plan'), plansController.update);
router.delete('/plans/:id', auditLog('delete_plan'), plansController.remove);
router.get('/reports/financial', financeiroCtrl.financialReport);
router.get('/audit', auditCtrl.listAudit);

module.exports = router;
