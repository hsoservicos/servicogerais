// ═══════════════════════════════════════════════════════════════
// modules/admin/admin.controller.js — Admin Platform Controller
// ═══════════════════════════════════════════════════════════════
// Epic 7 — Endpoints exclusivos para super_admin
// Stories: 7.1 (Dashboard), 7.2 (Tenants), 7.3 (Financeiro), 7.4 (Auditoria)
// ═══════════════════════════════════════════════════════════════

const { query } = require('../../config/database');

// ── Helpers ──────────────────────────────────────────────
function formatCurrency(value) {
  const num = parseFloat(value) || 0;
  return num.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
}

function formatDate(dt) {
  if (!dt) return '—';
  const d = new Date(dt);
  return d.toLocaleDateString('pt-BR') + ' ' + d.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
}

// ═══════════════════════════════════════════════════════════════
// Story 7.1 — Dashboard Global
// ═══════════════════════════════════════════════════════════════
// FR-ADM-01, FR-ADM-02
// KPIs: tenants ativos/suspensos, receita mês, transações recentes, alertas
// ═══════════════════════════════════════════════════════════════
async function dashboard(req, res, next) {
  try {
    // ── Total de tenants ────────────────────────────────
    const [totalTenants] = await query('SELECT COUNT(*) as total FROM tenants');
    const [activeTenants] = await query("SELECT COUNT(*) as total FROM tenants WHERE active = TRUE");
    const [suspendedTenants] = await query("SELECT COUNT(*) as total FROM tenants WHERE active = FALSE");

    // ── Total de usuários ───────────────────────────────
    const [totalUsers] = await query("SELECT COUNT(*) as total FROM users WHERE active = TRUE");

    // ── Total de clientes ───────────────────────────────
    const [totalClients] = await query("SELECT COUNT(*) as total FROM clients WHERE active = TRUE");

    // ── Receita do mês atual (transações completed) ──────
    const [monthRevenue] = await query(
      `SELECT COALESCE(SUM(amount), 0) as total,
              COALESCE(SUM(fee), 0) as total_fees
       FROM transactions
       WHERE status = 'completed'
         AND MONTH(paid_at) = MONTH(CURRENT_DATE)
         AND YEAR(paid_at) = YEAR(CURRENT_DATE)`
    );

    // ── Receita do mês anterior (comparação) ────────────
    const [prevRevenue] = await query(
      `SELECT COALESCE(SUM(amount), 0) as total
       FROM transactions
       WHERE status = 'completed'
         AND MONTH(paid_at) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)
         AND YEAR(paid_at) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)`
    );

    // ── Propostas pendentes globais ─────────────────────
    const [pendingProposals] = await query(
      "SELECT COUNT(*) as total FROM proposals WHERE status IN ('draft', 'sent', 'viewed')"
    );

    // ── Transações recentes (últimas 10) ────────────────
    const recentTransactions = await query(
      `SELECT t.id, t.amount, t.fee, t.status, t.payment_method, t.paid_at,
              tn.name as tenant_name, p.number as proposal_number
       FROM transactions t
       JOIN tenants tn ON tn.id = t.tenant_id
       LEFT JOIN proposals p ON p.id = t.proposal_id
       ORDER BY t.created_at DESC
       LIMIT 10`
    );

    // ── Distribuição de planos ──────────────────────────
    const planDistribution = await query(
      'SELECT plan, COUNT(*) as total FROM tenants GROUP BY plan ORDER BY total DESC'
    );

    // ── Crescimento mensal (últimos 6 meses) ────────────
    const monthlyGrowth = await query(
      `SELECT DATE_FORMAT(created_at, '%Y-%m') as month,
              COUNT(*) as new_tenants
       FROM tenants
       WHERE created_at >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)
       GROUP BY DATE_FORMAT(created_at, '%Y-%m')
       ORDER BY month ASC`
    );

    // ── Propostas por status ────────────────────────────
    const proposalsByStatus = await query(
      'SELECT status, COUNT(*) as total FROM proposals GROUP BY status'
    );

    // ── Revenue mensal (últimos 6 meses) ───────────────
    const monthlyRevenue = await query(
      `SELECT DATE_FORMAT(paid_at, '%Y-%m') as month,
              COALESCE(SUM(amount), 0) as revenue,
              COALESCE(SUM(fee), 0) as fees
       FROM transactions
       WHERE status = 'completed'
         AND paid_at >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)
       GROUP BY DATE_FORMAT(paid_at, '%Y-%m')
       ORDER BY month ASC`
    );

    const prevMonthRevenue = parseFloat(prevRevenue?.total || 0);
    const currentMonthRevenue = parseFloat(monthRevenue?.total || 0);
    const revenueGrowth = prevMonthRevenue > 0
      ? ((currentMonthRevenue - prevMonthRevenue) / prevMonthRevenue * 100).toFixed(1)
      : currentMonthRevenue > 0 ? 100 : 0;

    res.json({
      kpis: {
        tenants: { total: totalTenants?.total || 0, active: activeTenants?.total || 0, suspended: suspendedTenants?.total || 0 },
        users: totalUsers?.total || 0,
        clients: totalClients?.total || 0,
        revenue: {
          current: formatCurrency(monthRevenue?.total || 0),
          previous: formatCurrency(prevMonthRevenue),
          growth: `${revenueGrowth}%`,
          fees: formatCurrency(monthRevenue?.total_fees || 0),
        },
        pendingProposals: pendingProposals?.total || 0,
      },
      recentTransactions: recentTransactions.map(t => ({
        id: t.id,
        tenant: t.tenant_name,
        proposal: t.proposal_number,
        amount: formatCurrency(t.amount),
        fee: formatCurrency(t.fee),
        status: t.status,
        payment_method: t.payment_method,
        paid_at: formatDate(t.paid_at),
      })),
      charts: {
        planDistribution: planDistribution.map(p => ({ label: p.plan, value: p.total })),
        monthlyGrowth: monthlyGrowth.map(m => ({ month: m.month, newTenants: m.new_tenants })),
        proposalsByStatus: proposalsByStatus.map(p => ({ status: p.status, count: p.total })),
        monthlyRevenue: monthlyRevenue.map(m => ({
          month: m.month,
          revenue: parseFloat(m.revenue),
          fees: parseFloat(m.fees),
        })),
      },
      correlationId: req.correlationId,
    });
  } catch (err) {
    next(err);
  }
}

// ═══════════════════════════════════════════════════════════════
// Story 7.2 — Listar Tenants (paginado + busca)
// ═══════════════════════════════════════════════════════════════
async function listTenants(req, res, next) {
  try {
    const { search, status, page = 1, perPage = 20 } = req.query;
    const offset = (parseInt(page, 10) - 1) * parseInt(perPage, 10);
    const limit = parseInt(perPage, 10);

    let whereClause = '1=1';
    const params = [];

    if (search && search.length >= 2) {
      whereClause += ' AND (t.name LIKE ? OR t.slug LIKE ? OR t.document_cpf LIKE ? OR t.document_cnpj LIKE ?)';
      const term = `%${search}%`;
      params.push(term, term, term, term);
    }

    if (status === 'active') {
      whereClause += ' AND t.active = TRUE';
    } else if (status === 'suspended') {
      whereClause += ' AND t.active = FALSE';
    }

    const [countRow] = await query(
      `SELECT COUNT(*) as total FROM tenants t WHERE ${whereClause}`,
      params.length > 0 ? params : undefined
    );

    const tenants = await query(
      `SELECT t.id, t.name, t.slug, t.document_cpf, t.document_cnpj,
              t.phone, t.whatsapp, t.active, t.plan, t.created_at,
              (SELECT COUNT(*) FROM users u WHERE u.tenant_id = t.id AND u.active = TRUE) as user_count,
              (SELECT COUNT(*) FROM clients c WHERE c.tenant_id = t.id AND c.active = TRUE) as client_count,
              (SELECT COUNT(*) FROM proposals p WHERE p.tenant_id = t.id) as proposal_count,
              (SELECT COALESCE(SUM(amount), 0) FROM transactions tx WHERE tx.tenant_id = t.id AND tx.status = 'completed') as total_revenue
       FROM tenants t
       WHERE ${whereClause}
       ORDER BY t.created_at DESC
       LIMIT ? OFFSET ?`,
      params.length > 0 ? [...params, limit, offset] : [limit, offset]
    );

    res.json({
      tenants: tenants.map(t => ({
        ...t,
        total_revenue: formatCurrency(t.total_revenue),
        created_at: formatDate(t.created_at),
      })),
      pagination: {
        page: parseInt(page, 10),
        perPage: limit,
        total: countRow?.total || 0,
        totalPages: Math.ceil((countRow?.total || 0) / limit),
      },
      correlationId: req.correlationId,
    });
  } catch (err) {
    next(err);
  }
}

// ═══════════════════════════════════════════════════════════════
// Story 7.2 — Detalhes do Tenant
// ═══════════════════════════════════════════════════════════════
async function getTenant(req, res, next) {
  try {
    const { id } = req.params;

    const tenants = await query(
      `SELECT t.*,
              (SELECT COUNT(*) FROM users u WHERE u.tenant_id = t.id) as user_count,
              (SELECT COUNT(*) FROM clients c WHERE c.tenant_id = t.id AND c.active = TRUE) as client_count,
              (SELECT COUNT(*) FROM proposals p WHERE p.tenant_id = t.id) as proposal_count,
              (SELECT COALESCE(SUM(amount), 0) FROM transactions tx WHERE tx.tenant_id = t.id AND tx.status = 'completed') as total_revenue
       FROM tenants t
       WHERE t.id = ?`,
      [id]
    );

    if (tenants.length === 0) {
      return res.status(404).json({
        error: 'ERR_NOT_FOUND',
        message: 'Tenant não encontrado',
        correlationId: req.correlationId,
      });
    }

    const tenant = tenants[0];

    // Últimas atividades
    const recentActivity = await query(
      `SELECT action, entity_type, entity_id, created_at
       FROM audit_log
       WHERE tenant_id = ?
       ORDER BY created_at DESC
       LIMIT 5`,
      [id]
    );

    // Últimas transações
    const recentTransactions = await query(
      `SELECT id, amount, status, payment_method, paid_at
       FROM transactions
       WHERE tenant_id = ?
       ORDER BY created_at DESC
       LIMIT 5`,
      [id]
    );

    res.json({
      tenant: {
        ...tenant,
        total_revenue: formatCurrency(tenant.total_revenue),
        created_at: formatDate(tenant.created_at),
        updated_at: formatDate(tenant.updated_at),
      },
      recentActivity: recentActivity.map(a => ({
        action: a.action,
        entity: `${a.entity_type}#${a.entity_id}`,
        date: formatDate(a.created_at),
      })),
      recentTransactions: recentTransactions.map(t => ({
        id: t.id,
        amount: formatCurrency(t.amount),
        status: t.status,
        method: t.payment_method,
        date: formatDate(t.paid_at),
      })),
      correlationId: req.correlationId,
    });
  } catch (err) {
    next(err);
  }
}

// ═══════════════════════════════════════════════════════════════
// Story 7.2 — Atualizar Tenant
// ═══════════════════════════════════════════════════════════════
async function updateTenant(req, res, next) {
  try {
    const { id } = req.params;
    const { name, phone, whatsapp, plan, settings } = req.body;

    if (!name && !phone && !whatsapp && !plan && !settings) {
      return res.status(400).json({
        error: 'ERR_VALIDATION',
        message: 'Forneça ao menos um campo para atualizar',
        correlationId: req.correlationId,
      });
    }

    const updates = [];
    const params = [];

    if (name) { updates.push('name = ?'); params.push(name); }
    if (phone) { updates.push('phone = ?'); params.push(phone); }
    if (whatsapp) { updates.push('whatsapp = ?'); params.push(whatsapp); }
    if (plan) {
      const validPlans = ['free', 'basic', 'pro', 'enterprise'];
      if (!validPlans.includes(plan)) {
        return res.status(400).json({
          error: 'ERR_VALIDATION',
          message: `Plano inválido. Valores: ${validPlans.join(', ')}`,
          correlationId: req.correlationId,
        });
      }
      updates.push('plan = ?');
      params.push(plan);
    }
    if (settings) { updates.push('settings = ?'); params.push(JSON.stringify(settings)); }

    params.push(id);
    await query(
      `UPDATE tenants SET ${updates.join(', ')} WHERE id = ?`,
      params
    );

    res.json({
      message: 'Tenant atualizado com sucesso',
      correlationId: req.correlationId,
    });
  } catch (err) {
    next(err);
  }
}

// ═══════════════════════════════════════════════════════════════
// Story 7.2 — Suspender/Reativar Tenant
// ═══════════════════════════════════════════════════════════════
async function toggleTenantStatus(req, res, next) {
  try {
    const { id } = req.params;

    const tenants = await query('SELECT id, name, active FROM tenants WHERE id = ?', [id]);
    if (tenants.length === 0) {
      return res.status(404).json({
        error: 'ERR_NOT_FOUND',
        message: 'Tenant não encontrado',
        correlationId: req.correlationId,
      });
    }

    const tenant = tenants[0];
    const newStatus = !tenant.active;

    await query('UPDATE tenants SET active = ? WHERE id = ?', [newStatus, id]);

    // Registrar no admin_audit_log (não crítico — não quebrar a requisição)
    try {
      await query(
        `INSERT INTO admin_audit_log (admin_user_id, action, target_type, target_id, details, ip_address)
         VALUES (?, ?, ?, ?, ?, ?)`,
        [
          req.user.id,
          newStatus ? 'activate_tenant' : 'suspend_tenant',
          'tenant',
          id,
          JSON.stringify({ tenant_name: tenant.name, previous_status: tenant.active, new_status: newStatus }),
          req.ip || req.headers['x-forwarded-for'] || null,
        ]
      );
    } catch (auditErr) {
      console.warn('[ADMIN] ⚠️  Erro ao registrar auditoria (não crítico):', auditErr.message);
    }

    res.json({
      message: newStatus ? 'Tenant reativado com sucesso' : 'Tenant suspenso com sucesso',
      tenant: { id, active: newStatus },
      correlationId: req.correlationId,
    });
  } catch (err) {
    next(err);
  }
}

// ═══════════════════════════════════════════════════════════════
// Story 7.3 — Listar Transações (todos tenants)
// ═══════════════════════════════════════════════════════════════
async function listTransactions(req, res, next) {
  try {
    const { status, tenant_id, page = 1, perPage = 20 } = req.query;
    const offset = (parseInt(page, 10) - 1) * parseInt(perPage, 10);
    const limit = parseInt(perPage, 10);

    let whereClause = '1=1';
    const params = [];

    if (status) {
      whereClause += ' AND t.status = ?';
      params.push(status);
    }
    if (tenant_id) {
      whereClause += ' AND t.tenant_id = ?';
      params.push(parseInt(tenant_id, 10));
    }

    const [countRow] = await query(
      `SELECT COUNT(*) as total FROM transactions t WHERE ${whereClause}`,
      params.length > 0 ? params : undefined
    );

    const transactions = await query(
      `SELECT t.id, t.tenant_id, t.proposal_id, t.amount, t.fee, t.net_amount,
              t.status, t.payment_method, t.mp_id, t.paid_at, t.created_at,
              tn.name as tenant_name, tn.slug as tenant_slug,
              p.number as proposal_number
       FROM transactions t
       JOIN tenants tn ON tn.id = t.tenant_id
       LEFT JOIN proposals p ON p.id = t.proposal_id
       WHERE ${whereClause}
       ORDER BY t.created_at DESC
       LIMIT ? OFFSET ?`,
      params.length > 0 ? [...params, limit, offset] : [limit, offset]
    );

    const summary = await query(
      `SELECT status, COUNT(*) as count, COALESCE(SUM(amount), 0) as total_amount
       FROM transactions
       GROUP BY status`
    );

    res.json({
      transactions: transactions.map(tx => ({
        id: tx.id,
        tenant_id: tx.tenant_id,
        tenant_name: tx.tenant_name,
        proposal_number: tx.proposal_number,
        amount: formatCurrency(tx.amount),
        fee: formatCurrency(tx.fee),
        net_amount: formatCurrency(tx.net_amount),
        status: tx.status,
        payment_method: tx.payment_method,
        mp_id: tx.mp_id,
        paid_at: formatDate(tx.paid_at),
        created_at: formatDate(tx.created_at),
      })),
      summary: summary.map(s => ({
        status: s.status,
        count: s.count,
        total: formatCurrency(s.total_amount),
      })),
      pagination: {
        page: parseInt(page, 10),
        perPage: limit,
        total: countRow?.total || 0,
        totalPages: Math.ceil((countRow?.total || 0) / limit),
      },
      correlationId: req.correlationId,
    });
  } catch (err) {
    next(err);
  }
}

// ═══════════════════════════════════════════════════════════════
// Story 7.3 — Estornar Transação (admin override)
// ═══════════════════════════════════════════════════════════════
async function refundTransaction(req, res, next) {
  try {
    const { id } = req.params;

    const transactions = await query(
      "SELECT * FROM transactions WHERE id = ? AND status = 'completed'",
      [id]
    );

    if (transactions.length === 0) {
      return res.status(404).json({
        error: 'ERR_NOT_FOUND',
        message: 'Transação não encontrada ou não está concluída',
        correlationId: req.correlationId,
      });
    }

    const tx = transactions[0];

    // Se tiver mp_id, tentar estornar via Mercado Pago
    if (tx.mp_id) {
      try {
        const { refundPayment } = require('../../services/mercadopagoService');
        await refundPayment(tx.mp_id);
      } catch (mpErr) {
        console.warn(`[ADMIN] ⚠️  Estorno MP falhou (ID: ${tx.mp_id}): ${mpErr.message}. Prosseguindo com estorno local.`);
      }
    }

    // Atualizar transação local
    await query(
      "UPDATE transactions SET status = 'refunded', updated_at = NOW() WHERE id = ?",
      [id]
    );

    // Se tiver proposal_id, atualizar proposta
    if (tx.proposal_id) {
      await query(
        "UPDATE proposals SET status = 'accepted' WHERE id = ?",
        [tx.proposal_id]
      );
    }

    // Registrar auditoria (não crítico — não quebrar a requisição)
    try {
      await query(
        `INSERT INTO admin_audit_log (admin_user_id, action, target_type, target_id, details, ip_address)
         VALUES (?, ?, ?, ?, ?, ?)`,
        [
          req.user.id,
          'refund_transaction',
          'transaction',
          id,
          JSON.stringify({
            amount: tx.amount,
            tenant_id: tx.tenant_id,
            proposal_id: tx.proposal_id,
            mp_id: tx.mp_id,
          }),
          req.ip || req.headers['x-forwarded-for'] || null,
        ]
      );
    } catch (auditErr) {
      console.warn('[ADMIN REFUND] ⚠️  Erro ao registrar auditoria (não crítico):', auditErr.message);
    }

    res.json({
      message: 'Estorno processado com sucesso',
      data: { transaction_id: id, amount: formatCurrency(tx.amount) },
      correlationId: req.correlationId,
    });
  } catch (err) {
    next(err);
  }
}

// ═══════════════════════════════════════════════════════════════
// Story 7.4 — Listar Log de Auditoria
// ═══════════════════════════════════════════════════════════════
async function listAudit(req, res, next) {
  try {
    const { action, target_type, admin_id, start_date, end_date, page = 1, perPage = 50 } = req.query;
    const offset = (parseInt(page, 10) - 1) * parseInt(perPage, 10);
    const limit = parseInt(perPage, 10);

    let whereClause = '1=1';
    const params = [];

    if (action) { whereClause += ' AND a.action = ?'; params.push(action); }
    if (target_type) { whereClause += ' AND a.target_type = ?'; params.push(target_type); }
    if (admin_id) { whereClause += ' AND a.admin_user_id = ?'; params.push(parseInt(admin_id, 10)); }
    if (start_date) { whereClause += ' AND a.created_at >= ?'; params.push(start_date); }
    if (end_date) { whereClause += ' AND a.created_at <= ?'; params.push(end_date + ' 23:59:59'); }

    const [countRow] = await query(
      `SELECT COUNT(*) as total FROM admin_audit_log a WHERE ${whereClause}`,
      params.length > 0 ? params : undefined
    );

    const logs = await query(
      `SELECT a.*, u.name as admin_name, u.email as admin_email
       FROM admin_audit_log a
       LEFT JOIN users u ON u.id = a.admin_user_id
       WHERE ${whereClause}
       ORDER BY a.created_at DESC
       LIMIT ? OFFSET ?`,
      params.length > 0 ? [...params, limit, offset] : [limit, offset]
    );

    // Ações únicas para filtros
    const uniqueActions = await query(
      'SELECT DISTINCT action FROM admin_audit_log ORDER BY action'
    );

    res.json({
      logs: logs.map(log => ({
        id: log.id,
        admin: log.admin_name || `ID ${log.admin_user_id}`,
        admin_email: log.admin_email,
        action: log.action,
        target: { type: log.target_type, id: log.target_id },
        details: log.details,
        ip: log.ip_address,
        created_at: formatDate(log.created_at),
      })),
      filters: {
        actions: uniqueActions.map(a => a.action),
      },
      pagination: {
        page: parseInt(page, 10),
        perPage: limit,
        total: countRow?.total || 0,
        totalPages: Math.ceil((countRow?.total || 0) / limit),
      },
      correlationId: req.correlationId,
    });
  } catch (err) {
    next(err);
  }
}

module.exports = {
  dashboard,
  listTenants,
  getTenant,
  updateTenant,
  toggleTenantStatus,
  listTransactions,
  refundTransaction,
  listAudit,
};
