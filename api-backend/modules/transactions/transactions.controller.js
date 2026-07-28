// ═══════════════════════════════════════════════════════════════
// modules/transactions/transactions.controller.js — Transactions
// ═══════════════════════════════════════════════════════════════
// Story 4.4 — Histórico de Transações financeiras do prestador.
// FR-043: visualizar extrato com tenant isolation.
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
// GET /api/v1/transactions — Listar transações do tenant
// ═══════════════════════════════════════════════════════════════
// FR-043: Prestador visualiza extrato com paginação.
// Query params: status, page, perPage
// Respeita tenantFilter do middleware.
async function list(req, res, next) {
  try {
    const tenantFilter = req.tenantFilter || '1=1';
    // O tenantFilter vem como "tenant_id = X" — prefixar com 't.'
    // para evitar ambiguous column em queries com JOIN
    const prefixedFilter = tenantFilter.replace(/\btenant_id\b/g, 't.tenant_id');

    const { status, page = 1, perPage = 20 } = req.query;
    const offset = (parseInt(page, 10) - 1) * parseInt(perPage, 10);
    const limit = parseInt(perPage, 10);

    let whereClause = prefixedFilter;
    const params = [];

    // Filtro por status
    if (status) {
      whereClause += ' AND t.status = ?';
      params.push(status);
    }

    // Total de registros
    const [countRow] = await query(
      `SELECT COUNT(*) as total FROM transactions t WHERE ${whereClause}`,
      params.length > 0 ? params : undefined
    );

    // Lista paginada com JOINs para cliente e proposta
    const transactions = await query(
      `SELECT t.id, t.proposal_id, t.amount, t.fee, t.net_amount,
              t.status, t.payment_method, t.mp_id, t.mp_status,
              t.paid_at, t.created_at,
              p.number as proposal_number, p.client_id,
              c.name as client_name
       FROM transactions t
       LEFT JOIN proposals p ON p.id = t.proposal_id
       LEFT JOIN clients c ON c.id = p.client_id
       WHERE ${whereClause}
       ORDER BY t.created_at DESC
       LIMIT ? OFFSET ?`,
      params.length > 0 ? [...params, limit, offset] : [limit, offset]
    );

    // Resumo por status (para cards) — sem JOIN, usar tenantFilter original
    const summary = await query(
      `SELECT status, COUNT(*) as count, COALESCE(SUM(amount), 0) as total_amount
       FROM transactions
       WHERE ${tenantFilter}
       GROUP BY status`
    );

    // Totais gerais — usar prefixedFilter para consistency
    const [totals] = await query(
      `SELECT COUNT(*) as total_count,
              COALESCE(SUM(amount), 0) as total_amount,
              COALESCE(SUM(fee), 0) as total_fees,
              COALESCE(SUM(net_amount), 0) as total_net
       FROM transactions t
       WHERE ${whereClause}`,
      params.length > 0 ? params : undefined
    );

    const statusBadge = {
      completed: { label: 'Aprovado', class: 'bg-success/10 text-success' },
      pending: { label: 'Pendente', class: 'bg-warning/10 text-warning' },
      processing: { label: 'Processando', class: 'bg-info/10 text-info' },
      refunded: { label: 'Estornado', class: 'bg-danger/10 text-danger' },
      cancelled: { label: 'Cancelado', class: 'bg-ink-muted/10 text-ink-muted' },
    };

    res.json({
      transactions: transactions.map(tx => ({
        id: tx.id,
        proposalId: tx.proposal_id,
        proposalNumber: tx.proposal_number,
        clientName: tx.client_name,
        amount: formatCurrency(tx.amount),
        amountValue: parseFloat(tx.amount),
        fee: formatCurrency(tx.fee),
        netAmount: formatCurrency(tx.net_amount),
        status: tx.status,
        statusInfo: statusBadge[tx.status] || { label: tx.status, class: 'bg-ink-muted/10 text-ink-muted' },
        paymentMethod: tx.payment_method || '—',
        mpId: tx.mp_id,
        paidAt: formatDate(tx.paid_at),
        createdAt: formatDate(tx.created_at),
      })),
      summary: summary.map(s => ({
        status: s.status,
        count: s.count,
        total: formatCurrency(s.total_amount),
        info: statusBadge[s.status] || { label: s.status, class: '' },
      })),
      totals: {
        count: totals?.total_count || 0,
        amount: formatCurrency(totals?.total_amount || 0),
        fees: formatCurrency(totals?.total_fees || 0),
        net: formatCurrency(totals?.total_net || 0),
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

module.exports = { list };
