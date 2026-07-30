const { query } = require('../../config/database');
const { formatCurrency, formatDate } = require('./admin.dashboard.controller');

async function listTransactions(req, res, next) {
  try {
    const { status, tenant_id, page = 1, perPage = 20 } = req.query;
    const offset = (parseInt(page, 10) - 1) * parseInt(perPage, 10);
    const limit = parseInt(perPage, 10);
    let whereClause = '1=1'; const params = [];
    if (status) { whereClause += ' AND t.status = ?'; params.push(status); }
    if (tenant_id) { whereClause += ' AND t.tenant_id = ?'; params.push(parseInt(tenant_id, 10)); }

    const [countRow] = await query(`SELECT COUNT(*) as total FROM transactions t WHERE ${whereClause}`, params.length > 0 ? params : undefined);
    const transactions = await query(
      `SELECT t.*, tn.name as tenant_name, tn.slug as tenant_slug, p.number as proposal_number
       FROM transactions t JOIN tenants tn ON tn.id = t.tenant_id LEFT JOIN proposals p ON p.id = t.proposal_id
       WHERE ${whereClause} ORDER BY t.created_at DESC LIMIT ? OFFSET ?`,
      params.length > 0 ? [...params, limit, offset] : [limit, offset]
    );
    const summary = await query('SELECT status, COUNT(*) as count, COALESCE(SUM(amount), 0) as total_amount FROM transactions GROUP BY status');

    res.json({
      transactions: transactions.map(tx => ({ id: tx.id, tenant_id: tx.tenant_id, tenant_name: tx.tenant_name, proposal_number: tx.proposal_number, amount: formatCurrency(tx.amount), fee: formatCurrency(tx.fee), net_amount: formatCurrency(tx.net_amount), status: tx.status, payment_method: tx.payment_method, mp_id: tx.mp_id, paid_at: formatDate(tx.paid_at), created_at: formatDate(tx.created_at) })),
      summary: summary.map(s => ({ status: s.status, count: s.count, total: formatCurrency(s.total_amount) })),
      pagination: { page: parseInt(page, 10), perPage: limit, total: countRow?.total || 0, totalPages: Math.ceil((countRow?.total || 0) / limit) },
      correlationId: req.correlationId,
    });
  } catch (err) { next(err); }
}

async function refundTransaction(req, res, next) {
  try {
    const { id } = req.params;
    const transactions = await query("SELECT * FROM transactions WHERE id = ? AND status = 'completed'", [id]);
    if (transactions.length === 0) return res.status(404).json({ error: 'ERR_NOT_FOUND', message: 'Transação não encontrada', correlationId: req.correlationId });
    const tx = transactions[0];
    if (tx.mp_id) {
      try { const { refundPayment } = require('../../services/mercadopagoService'); await refundPayment(tx.mp_id); } catch (mpErr) { console.warn(`[ADMIN] MP estorno falhou: ${mpErr.message}`); }
    }
    await query("UPDATE transactions SET status = 'refunded', updated_at = NOW() WHERE id = ?", [id]);
    if (tx.proposal_id) await query("UPDATE proposals SET status = 'accepted' WHERE id = ?", [tx.proposal_id]);
    try { await query(`INSERT INTO admin_audit_log (admin_user_id, action, target_type, target_id, details, ip_address) VALUES (?,?,?,?,?,?)`,
      [req.user.id, 'refund_transaction', 'transaction', id, JSON.stringify({ amount: tx.amount, tenant_id: tx.tenant_id, mp_id: tx.mp_id }), req.ip || null]); } catch (_) {}
    res.json({ message: 'Estorno processado', data: { transaction_id: id, amount: formatCurrency(tx.amount) }, correlationId: req.correlationId });
  } catch (err) { next(err); }
}

async function financialReport(req, res, next) {
  try {
    const { start_date, end_date, format } = req.query;
    let dateFilter = ''; const params = [];
    if (start_date && end_date) { dateFilter = ' AND t.paid_at >= ? AND t.paid_at <= ?'; params.push(start_date, end_date + ' 23:59:59'); }
    else dateFilter = ' AND t.paid_at >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)';

    const revenue = await query(
      `SELECT DATE_FORMAT(t.paid_at, '%Y-%m-%d') as day, COALESCE(SUM(t.amount),0) as revenue, COALESCE(SUM(t.fee),0) as fees, COUNT(*) as transactions
       FROM transactions t WHERE t.status = 'completed'${dateFilter} GROUP BY DATE_FORMAT(t.paid_at, '%Y-%m-%d') ORDER BY day ASC`,
      params.length > 0 ? params : undefined
    );
    const totals = await query(
      `SELECT COUNT(*) as total_transactions, COALESCE(SUM(t.amount),0) as total_revenue, COALESCE(SUM(t.fee),0) as total_fees
       FROM transactions t WHERE t.status = 'completed'${dateFilter}`, params.length > 0 ? params : undefined
    );
    const byPlan = await query(
      `SELECT tn.plan, COALESCE(SUM(t.amount),0) as revenue, COUNT(*) as count
       FROM transactions t JOIN tenants tn ON tn.id = t.tenant_id WHERE t.status = 'completed'${dateFilter}
       GROUP BY tn.plan ORDER BY revenue DESC`, params.length > 0 ? params : undefined
    );

    if (format === 'csv') {
      const csvLines = ['Data,Receita,Taxas,Transacoes'];
      revenue.forEach(r => csvLines.push(`${r.day},${r.revenue},${r.fees},${r.transactions}`));
      res.set({ 'Content-Type': 'text/csv; charset=utf-8', 'Content-Disposition': `attachment; filename="relatorio-financeiro.csv"` });
      return res.send(csvLines.join('\n'));
    }
    res.json({
      period: { start: start_date || '30d', end: end_date || 'today' },
      totals: { transactions: totals[0]?.total_transactions || 0, revenue: totals[0]?.total_revenue || 0, fees: totals[0]?.total_fees || 0, net: (totals[0]?.total_revenue || 0) - (totals[0]?.total_fees || 0), formatted: { revenue: formatCurrency(totals[0]?.total_revenue || 0), fees: formatCurrency(totals[0]?.total_fees || 0), net: formatCurrency((totals[0]?.total_revenue || 0) - (totals[0]?.total_fees || 0)) } },
      byPlan: byPlan.map(p => ({ plan: p.plan, revenue: parseFloat(p.revenue), count: p.count })),
      daily: revenue.map(r => ({ day: r.day, revenue: parseFloat(r.revenue), fees: parseFloat(r.fees), transactions: r.transactions })),
      correlationId: req.correlationId,
    });
  } catch (err) { next(err); }
}

module.exports = { listTransactions, refundTransaction, financialReport };
