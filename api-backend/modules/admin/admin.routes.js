// ═══════════════════════════════════════════════════════════════
// modules/admin/admin.routes.js — Admin Routes (Epic 7)
// ═══════════════════════════════════════════════════════════════
// Todas as rotas: authenticate → adminAuth (só super_admin)
// Stories: 7.1 (Dashboard), 7.2 (Tenants), 7.3 (Financeiro), 7.4 (Auditoria)
// ═══════════════════════════════════════════════════════════════

const { Router } = require('express');
const controller = require('./admin.controller');
const plansController = require('./plans.controller');
const { authenticate } = require('../../middlewares/auth.middleware');
const { adminAuth, auditLog } = require('./admin.middleware');

const router = Router();

// ── Todas as rotas exigem autenticação + role super_admin ─
router.use(authenticate);
router.use(adminAuth);

// ═══════════════════════════════════════════════════════════════
// Story 7.1 — Dashboard Global
// ═══════════════════════════════════════════════════════════════
// FR-ADM-01: Login Admin, FR-ADM-02: Dashboard Global
router.get('/dashboard', controller.dashboard);

// ═══════════════════════════════════════════════════════════════
// Story 7.2 — Gestão de Tenants
// ═══════════════════════════════════════════════════════════════
// FR-ADM-03: Listar Tenants, FR-ADM-04: Editar Tenant, FR-ADM-05: Suspender
router.get('/tenants', controller.listTenants);
router.get('/tenants/:id', controller.getTenant);
router.put('/tenants/:id', auditLog('update_tenant'), controller.updateTenant);
router.delete('/tenants/:id', controller.toggleTenantStatus); // audit interno (ações: activate_tenant/suspend_tenant)

// ═══════════════════════════════════════════════════════════════
// Story 7.3 — Admin Financeiro
// ═══════════════════════════════════════════════════════════════
// FR-ADM-06: Transações, FR-ADM-07: Estorno, FR-ADM-08: Planos, FR-ADM-09: Relatório
router.get('/transactions', controller.listTransactions);
router.post('/transactions/:id/refund', controller.refundTransaction); // audit interno (action: refund_transaction, com detalhes enriquecidos)

// ═══════════════════════════════════════════════════════════════
// Story 7.3 — Planos (FR-ADM-08)
// ═══════════════════════════════════════════════════════════════
router.get('/plans', plansController.list);
router.get('/plans/:id', plansController.read);
router.post('/plans', auditLog('create_plan'), plansController.create);
router.put('/plans/:id', auditLog('update_plan'), plansController.update);
router.delete('/plans/:id', auditLog('delete_plan'), plansController.remove);

// ═══════════════════════════════════════════════════════════════
// Story 7.3 — Relatório Financeiro (FR-ADM-09)
// ═══════════════════════════════════════════════════════════════
router.get('/reports/financial', controller.financialReport);

// ═══════════════════════════════════════════════════════════════
// Story 7.4 — Auditoria
// ═══════════════════════════════════════════════════════════════
// FR-ADM-10: Auditoria de Ações
router.get('/audit', controller.listAudit);

module.exports = router;
