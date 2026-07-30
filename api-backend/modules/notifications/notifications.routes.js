const { Router } = require('express');
const { authenticate } = require('../../middlewares/auth.middleware');
const { injectTenant } = require('../../middlewares/tenant.middleware');
const ctrl = require('./notifications.controller');
const adminAuth = require('../admin/admin.middleware').adminAuth;

const router = Router();

// ── Tenant preferences (authenticated user) ───────────────
router.get('/preferences', authenticate, injectTenant, ctrl.getPreferences);
router.put('/preferences', authenticate, injectTenant, ctrl.updatePreferences);

// ── Client preferences (authenticated user, manages clients) ──
router.get('/clients/:clientId/preferences', authenticate, injectTenant, ctrl.getClientPrefs);
router.put('/clients/:clientId/preferences', authenticate, injectTenant, ctrl.updateClientPrefs);

// ── Admin: send notification to tenant ────────────────────
router.post('/admin/send', authenticate, adminAuth, ctrl.adminSendNotification);

module.exports = router;
