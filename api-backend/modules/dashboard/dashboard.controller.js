// ═══════════════════════════════════════════════════════════════
// modules/dashboard/dashboard.controller.js — Dashboard KPIs
// ═══════════════════════════════════════════════════════════════
// Agrega dados de clients, proposals, transactions e audit_log

const { query } = require('../../config/database');

// ── GET /dashboard — KPIs e Atividades Recentes ──────────
async function dashboard(req, res, next) {
  try {
    const tenantFilter = req.tenantFilter || '1=1';
    const tenantId = req.tenantId;

    // ── 1. Total de Clientes Ativos ──────────────────────
    const [clientRow] = await query(
      `SELECT COUNT(*) as total FROM clients WHERE ${tenantFilter} AND active = TRUE`
    );

    // ── 2. Propostas este mês ────────────────────────────
    const [proposalRow] = await query(
      `SELECT COUNT(*) as total FROM proposals
       WHERE ${tenantFilter}
         AND MONTH(created_at) = MONTH(CURRENT_DATE)
         AND YEAR(created_at) = YEAR(CURRENT_DATE)`
    );

    // ── 3. Faturamento este mês (transações concluídas) ──
    const [revenueRow] = await query(
      `SELECT COALESCE(SUM(amount), 0) as total FROM transactions
       WHERE ${tenantFilter}
         AND status = 'completed'
         AND MONTH(paid_at) = MONTH(CURRENT_DATE)
         AND YEAR(paid_at) = YEAR(CURRENT_DATE)`
    );

    // ── 4. Propostas Pendentes ───────────────────────────
    const [pendingRow] = await query(
      `SELECT COUNT(*) as total FROM proposals
       WHERE ${tenantFilter}
         AND status IN ('draft', 'sent', 'viewed')`
    );

    // ── 5. Atividades Recentes (audit_log) ───────────────
    const activities = await query(
      `SELECT action, entity_type, entity_id, metadata, created_at
       FROM audit_log
       WHERE ${tenantFilter}
       ORDER BY created_at DESC
       LIMIT 5`
    );

    // ── Montar Resposta ──────────────────────────────────
    const formatTimeAgo = (dateStr) => {
      if (!dateStr) return '';
      const now = new Date();
      const date = new Date(dateStr);
      const diffMs = now - date;
      const diffMin = Math.floor(diffMs / 60000);
      const diffHours = Math.floor(diffMs / 3600000);
      const diffDays = Math.floor(diffMs / 86400000);

      if (diffMin < 1) return 'agora mesmo';
      if (diffMin < 60) return `há ${diffMin} min`;
      if (diffHours < 24) return `há ${diffHours} hora${diffHours > 1 ? 's' : ''}`;
      if (diffDays < 7) return `há ${diffDays} dia${diffDays > 1 ? 's' : ''}`;
      return date.toLocaleDateString('pt-BR');
    };

    const formatCurrency = (value) => {
      const num = parseFloat(value) || 0;
      return num.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    };

    // Se não houver atividades no audit_log, gerar baseadas nos dados existentes
    let activitiesList = activities.map(a => {
      let type = 'info';
      if (a.action === 'create' && a.entity_type === 'client') type = 'client';
      else if (a.action === 'create' && a.entity_type === 'proposal') type = 'proposal';
      else if (a.action === 'update') type = 'info';
      else if (a.action === 'delete') type = 'warning';

      return {
        type,
        description: `${a.action === 'create' ? 'Novo' : a.action === 'update' ? 'Atualizado' : a.action} ${a.entity_type} #${a.entity_id || ''}`,
        time: formatTimeAgo(a.created_at),
      };
    });

    // Se não houver atividades, buscar dos registros existentes
    if (activitiesList.length === 0) {
      // Últimos clientes criados
      const recentClients = await query(
        `SELECT name, created_at FROM clients
         WHERE ${tenantFilter} AND active = TRUE
         ORDER BY created_at DESC LIMIT 3`
      );

      activitiesList = recentClients.map(c => ({
        type: 'client',
        description: `Cliente cadastrado: ${c.name}`,
        time: formatTimeAgo(c.created_at),
      }));

      // Últimas propostas
      const recentProposals = await query(
        `SELECT title, created_at FROM proposals
         WHERE ${tenantFilter}
         ORDER BY created_at DESC LIMIT 2`
      );

      recentProposals.forEach(p => {
        activitiesList.push({
          type: 'proposal',
          description: `Proposta criada: ${p.title}`,
          time: formatTimeAgo(p.created_at),
        });
      });
    }

    res.json({
      clients: clientRow?.total || 0,
      proposals: proposalRow?.total || 0,
      revenue: formatCurrency(revenueRow?.total || 0),
      pending: pendingRow?.total || 0,
      activities: activitiesList.slice(0, 5),
      correlationId: req.correlationId,
    });
  } catch (err) {
    next(err);
  }
}

// ── GET /dashboard/chart — Receita mensal (6 meses) ────
async function chart(req, res, next) {
  try {
    const tenantFilter = req.tenantFilter || '1=1';

    const monthlyRevenue = await query(
      `SELECT DATE_FORMAT(paid_at, '%Y-%m') as month,
              COALESCE(SUM(amount), 0) as revenue,
              COUNT(*) as transactions
       FROM transactions
       WHERE ${tenantFilter}
         AND status = 'completed'
         AND paid_at >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)
       GROUP BY DATE_FORMAT(paid_at, '%Y-%m')
       ORDER BY month ASC`
    );

    // Preencher meses sem transações com 0
    const months = [];
    for (let i = 5; i >= 0; i--) {
      const d = new Date();
      d.setMonth(d.getMonth() - i);
      const key = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`;
      const found = monthlyRevenue.find(m => m.month === key);
      months.push({
        month: key,
        revenue: found ? parseFloat(found.revenue) : 0,
        transactions: found ? found.transactions : 0,
      });
    }

    res.json({ months, correlationId: req.correlationId });
  } catch (err) {
    next(err);
  }
}

// ── GET /dashboard/followup — Propostas pendentes > 48h ─
async function followup(req, res, next) {
  try {
    const tenantFilter = req.tenantFilter || '1=1';
    const prefixedFilter = tenantFilter.replace(/\btenant_id\b/g, 'p.tenant_id');

    const proposals = await query(
      `SELECT p.id, p.number, p.title, p.status, p.total_amount,
              p.created_at, p.sent_at,
              c.name as client_name, c.whatsapp as client_whatsapp
       FROM proposals p
       LEFT JOIN clients c ON p.client_id = c.id
       WHERE ${prefixedFilter}
         AND p.status IN ('sent', 'viewed')
         AND (p.sent_at IS NOT NULL AND p.sent_at < DATE_SUB(NOW(), INTERVAL 48 HOUR))
       ORDER BY p.sent_at ASC
       LIMIT 20`
    );

    const formatCurrency = (value) => {
      const num = parseFloat(value) || 0;
      return num.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    };

    const formatTimeAgo = (dateStr) => {
      if (!dateStr) return '';
      const now = new Date();
      const date = new Date(dateStr);
      const diffMs = now - date;
      const diffHours = Math.floor(diffMs / 3600000);
      const diffDays = Math.floor(diffMs / 86400000);
      if (diffDays > 0) return `${diffDays}d ${diffHours % 24}h`;
      return `${diffHours}h`;
    };

    res.json({
      proposals: proposals.map(p => ({
        id: p.id,
        number: p.number,
        title: p.title,
        status: p.status,
        total_amount: formatCurrency(p.total_amount),
        client_name: p.client_name || '—',
        client_whatsapp: p.client_whatsapp,
        sent_at: p.sent_at,
        hours_ago: formatTimeAgo(p.sent_at || p.created_at),
      })),
      correlationId: req.correlationId,
    });
  } catch (err) {
    next(err);
  }
}

module.exports = { dashboard, chart, followup };
