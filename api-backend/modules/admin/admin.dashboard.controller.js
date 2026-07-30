const { query } = require('../../config/database');

function formatCurrency(value) {
  const num = parseFloat(value) || 0;
  return num.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
}

function formatDate(dt) {
  if (!dt) return '—';
  const d = new Date(dt);
  return d.toLocaleDateString('pt-BR') + ' ' + d.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
}

async function dashboard(req, res, next) {
  try {
    const [totalTenants] = await query('SELECT COUNT(*) as total FROM tenants');
    const [activeTenants] = await query("SELECT COUNT(*) as total FROM tenants WHERE active = TRUE");
    const [suspendedTenants] = await query("SELECT COUNT(*) as total FROM tenants WHERE active = FALSE");
    const [totalUsers] = await query("SELECT COUNT(*) as total FROM users WHERE active = TRUE");
    const [totalClients] = await query("SELECT COUNT(*) as total FROM clients WHERE active = TRUE");
    const [monthRevenue] = await query(
      `SELECT COALESCE(SUM(amount), 0) as total, COALESCE(SUM(fee), 0) as total_fees
       FROM transactions WHERE status = 'completed'
       AND MONTH(paid_at) = MONTH(CURRENT_DATE) AND YEAR(paid_at) = YEAR(CURRENT_DATE)`
    );
    const [prevRevenue] = await query(
      `SELECT COALESCE(SUM(amount), 0) as total FROM transactions
       WHERE status = 'completed' AND MONTH(paid_at) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)
       AND YEAR(paid_at) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)`
    );
    const [pendingProposals] = await query("SELECT COUNT(*) as total FROM proposals WHERE status IN ('draft','sent','viewed')");

    const recentTransactions = await query(
      `SELECT t.id, t.amount, t.fee, t.status, t.payment_method, t.paid_at,
              tn.name as tenant_name, p.number as proposal_number
       FROM transactions t JOIN tenants tn ON tn.id = t.tenant_id
       LEFT JOIN proposals p ON p.id = t.proposal_id
       ORDER BY t.created_at DESC LIMIT 10`
    );
    const planDistribution = await query('SELECT plan, COUNT(*) as total FROM tenants GROUP BY plan ORDER BY total DESC');
    const monthlyGrowth = await query(
      `SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as new_tenants
       FROM tenants WHERE created_at >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)
       GROUP BY DATE_FORMAT(created_at, '%Y-%m') ORDER BY month ASC`
    );
    const proposalsByStatus = await query('SELECT status, COUNT(*) as total FROM proposals GROUP BY status');
    const monthlyRevenue = await query(
      `SELECT DATE_FORMAT(paid_at, '%Y-%m') as month, COALESCE(SUM(amount), 0) as revenue, COALESCE(SUM(fee), 0) as fees
       FROM transactions WHERE status = 'completed' AND paid_at >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)
       GROUP BY DATE_FORMAT(paid_at, '%Y-%m') ORDER BY month ASC`
    );

    const prevMonthRevenue = parseFloat(prevRevenue?.total || 0);
    const currentMonthRevenue = parseFloat(monthRevenue?.total || 0);
    const revenueGrowth = prevMonthRevenue > 0
      ? ((currentMonthRevenue - prevMonthRevenue) / prevMonthRevenue * 100).toFixed(1)
      : currentMonthRevenue > 0 ? 100 : 0;

    res.json({
      kpis: {
        tenants: { total: totalTenants?.total || 0, active: activeTenants?.total || 0, suspended: suspendedTenants?.total || 0 },
        users: totalUsers?.total || 0, clients: totalClients?.total || 0,
        revenue: { current: formatCurrency(monthRevenue?.total || 0), previous: formatCurrency(prevMonthRevenue), growth: `${revenueGrowth}%`, fees: formatCurrency(monthRevenue?.total_fees || 0) },
        pendingProposals: pendingProposals?.total || 0,
      },
      recentTransactions: recentTransactions.map(t => ({ ...t, amount: formatCurrency(t.amount), fee: formatCurrency(t.fee), paid_at: formatDate(t.paid_at) })),
      charts: {
        planDistribution: planDistribution.map(p => ({ label: p.plan, value: p.total })),
        monthlyGrowth: monthlyGrowth.map(m => ({ month: m.month, newTenants: m.new_tenants })),
        proposalsByStatus: proposalsByStatus.map(p => ({ status: p.status, count: p.total })),
        monthlyRevenue: monthlyRevenue.map(m => ({ month: m.month, revenue: parseFloat(m.revenue), fees: parseFloat(m.fees) })),
      },
      correlationId: req.correlationId,
    });
  } catch (err) { next(err); }
}

module.exports = { dashboard, formatCurrency, formatDate };
