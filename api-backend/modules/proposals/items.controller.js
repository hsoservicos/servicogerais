// ═══════════════════════════════════════════════════════════════
// modules/proposals/items.controller.js — CRUD Itens da Proposta
// ═══════════════════════════════════════════════════════════════
// Endpoints: list, create, update, remove (todos recalculam total)

const { query } = require('../../config/database');

/**
 * Recalcula o total_amount da proposta somando (quantity * unit_price)
 * de todos os itens ativos.
 */
async function recalculateTotal(proposalId, tenantFilter) {
  const rows = await query(
    `SELECT COALESCE(SUM(quantity * unit_price), 0) as total
     FROM proposal_items
     WHERE proposal_id = ?`,
    [proposalId]
  );
  const total = parseFloat(rows[0]?.total || 0);

  await query(
    `UPDATE proposals SET total_amount = ?, updated_at = NOW()
     WHERE id = ?${tenantFilter ? ' AND ' + tenantFilter : ''}`,
    [total, proposalId]
  );

  return total;
}

/**
 * Verifica se a proposta existe, pertence ao tenant e está em estado editável
 * (draft ou rejected).
 */
async function validateProposalEditable(proposalId, tenantFilter) {
  const rows = await query(
    `SELECT id, status FROM proposals WHERE id = ? AND ${tenantFilter}`,
    [proposalId]
  );

  if (rows.length === 0) {
    return { valid: false, status: 404, error: 'ERR_NOT_FOUND', message: 'Proposta não encontrada' };
  }

  if (!['draft', 'rejected'].includes(rows[0].status)) {
    return {
      valid: false, status: 422, error: 'ERR_INVALID_STATUS',
      message: `Não é possível editar itens de uma proposta com status "${rows[0].status}"`,
    };
  }

  return { valid: true, proposal: rows[0] };
}

// ── GET /proposals/:proposalId/items — Listar itens ─────
async function list(req, res, next) {
  try {
    const { proposalId } = req.params;
    const tenantFilter = req.tenantFilter || '1=1';

    // Verificar se a proposta existe e pertence ao tenant
    const proposal = await query(
      `SELECT id FROM proposals WHERE id = ? AND ${tenantFilter}`,
      [proposalId]
    );

    if (proposal.length === 0) {
      return res.status(404).json({
        error: 'ERR_NOT_FOUND',
        message: 'Proposta não encontrada',
        correlationId: req.correlationId,
      });
    }

    const items = await query(
      `SELECT id, proposal_id, description, quantity, unit_price,
              total_price, sort_order, created_at
       FROM proposal_items
       WHERE proposal_id = ?
       ORDER BY sort_order ASC, id ASC`,
      [proposalId]
    );

    res.json({
      items,
      correlationId: req.correlationId,
    });
  } catch (err) {
    next(err);
  }
}

// ── POST /proposals/:proposalId/items — Adicionar item ──
async function create(req, res, next) {
  try {
    const { proposalId } = req.params;
    const tenantFilter = req.tenantFilter || '1=1';

    const validation = await validateProposalEditable(proposalId, tenantFilter);
    if (!validation.valid) {
      return res.status(validation.status).json({
        error: validation.error,
        message: validation.message,
        correlationId: req.correlationId,
      });
    }

    const { description, quantity, unit_price, sort_order } = req.body;

    if (!description || description.trim().length === 0) {
      return res.status(400).json({
        error: 'ERR_VALIDATION',
        message: 'Descrição do item é obrigatória',
        correlationId: req.correlationId,
      });
    }

    const qty = parseFloat(quantity) || 1;
    const price = parseFloat(unit_price) || 0;

    const result = await query(
      `INSERT INTO proposal_items (proposal_id, description, quantity, unit_price, sort_order)
       VALUES (?, ?, ?, ?, ?)`,
      [
        parseInt(proposalId, 10),
        description.trim(),
        qty,
        price,
        sort_order || 0,
      ]
    );

    // Recalcular total da proposta
    const totalAmount = await recalculateTotal(proposalId, req.tenantFilter);

    res.status(201).json({
      message: 'Item adicionado com sucesso!',
      item: {
        id: result.insertId,
        proposal_id: parseInt(proposalId, 10),
        description: description.trim(),
        quantity: qty,
        unit_price: price,
        total_price: qty * price,
      },
      total_amount: totalAmount,
      correlationId: req.correlationId,
    });
  } catch (err) {
    next(err);
  }
}

// ── PUT /proposals/:proposalId/items/:id — Atualizar item
async function update(req, res, next) {
  try {
    const { proposalId, id } = req.params;
    const tenantFilter = req.tenantFilter || '1=1';

    const validation = await validateProposalEditable(proposalId, tenantFilter);
    if (!validation.valid) {
      return res.status(validation.status).json({
        error: validation.error,
        message: validation.message,
        correlationId: req.correlationId,
      });
    }

    const { description, quantity, unit_price, sort_order } = req.body;

    // Validar descrição se fornecida
    if (description !== undefined && description !== null && description.trim().length === 0) {
      return res.status(400).json({
        error: 'ERR_VALIDATION',
        message: 'Descrição do item não pode ficar vazia',
        correlationId: req.correlationId,
      });
    }

    // Verificar se o item existe e pertence à proposta
    const existing = await query(
      `SELECT id FROM proposal_items WHERE id = ? AND proposal_id = ?`,
      [id, proposalId]
    );

    if (existing.length === 0) {
      return res.status(404).json({
        error: 'ERR_NOT_FOUND',
        message: 'Item não encontrado',
        correlationId: req.correlationId,
      });
    }

    await query(
      `UPDATE proposal_items SET
        description = COALESCE(?, description),
        quantity = COALESCE(?, quantity),
        unit_price = COALESCE(?, unit_price),
        sort_order = COALESCE(?, sort_order)
       WHERE id = ? AND proposal_id = ?`,
      [
        description ? description.trim() : null,
        quantity !== undefined ? (parseFloat(quantity) || 1) : null,
        unit_price !== undefined ? (parseFloat(unit_price) || 0) : null,
        sort_order ?? null,
        id,
        proposalId,
      ]
    );

    // Recalcular total da proposta
    const totalAmount = await recalculateTotal(proposalId, req.tenantFilter);

    res.json({
      message: 'Item atualizado com sucesso!',
      total_amount: totalAmount,
      correlationId: req.correlationId,
    });
  } catch (err) {
    next(err);
  }
}

// ── DELETE /proposals/:proposalId/items/:id — Remover item
async function remove(req, res, next) {
  try {
    const { proposalId, id } = req.params;
    const tenantFilter = req.tenantFilter || '1=1';

    const validation = await validateProposalEditable(proposalId, tenantFilter);
    if (!validation.valid) {
      return res.status(validation.status).json({
        error: validation.error,
        message: validation.message,
        correlationId: req.correlationId,
      });
    }

    // Verificar se o item existe
    const existing = await query(
      `SELECT id FROM proposal_items WHERE id = ? AND proposal_id = ?`,
      [id, proposalId]
    );

    if (existing.length === 0) {
      return res.status(404).json({
        error: 'ERR_NOT_FOUND',
        message: 'Item não encontrado',
        correlationId: req.correlationId,
      });
    }

    await query(
      `DELETE FROM proposal_items WHERE id = ? AND proposal_id = ?`,
      [id, proposalId]
    );

    // Recalcular total da proposta
    const totalAmount = await recalculateTotal(proposalId, req.tenantFilter);

    res.json({
      message: 'Item removido com sucesso!',
      total_amount: totalAmount,
      correlationId: req.correlationId,
    });
  } catch (err) {
    next(err);
  }
}

module.exports = { list, create, update, remove };
