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

module.exports = { dashboard };
