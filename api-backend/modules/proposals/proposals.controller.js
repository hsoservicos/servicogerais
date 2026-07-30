// ═══════════════════════════════════════════════════════════════
// modules/proposals/proposals.controller.js — CRUD Propostas
// ═══════════════════════════════════════════════════════════════
// Endpoints: list, create, read, update, updateStatus, remove

const { v4: uuidv4 } = require('uuid');
const { query } = require('../../config/database');
const comm = require('../../services/communication.service');

// ── Helpers ──────────────────────────────────────────────

/**
 * Gera número sequencial da proposta no formato PROP-{ANO}-{SEQUENCIAL}
 * Ex: PROP-2026-0001
 */
async function generateNumber(tenantId) {
  const year = new Date().getFullYear();
  const rows = await query(
    `SELECT COUNT(*) as total FROM proposals
     WHERE tenant_id = ? AND YEAR(created_at) = ?`,
    [tenantId, year]
  );
  const seq = (rows[0]?.total || 0) + 1;
  return `PROP-${year}-${String(seq).padStart(4, '0')}`;
}

// ── GET /proposals — Listar (paginado + busca + filtros) ─
async function list(req, res, next) {
  try {
    const {
      search, status, client_id,
      date_from, date_to,
      page = 1, perPage = 20,
    } = req.query;
    const tenantFilter = req.tenantFilter || '1=1';
    // Prefixar tenantFilter com 'p.' pois a query faz JOIN (ambas as tabelas têm tenant_id)
    const prefixedFilter = tenantFilter.replace(/\btenant_id\b/g, 'p.tenant_id');
    const offset = (parseInt(page, 10) - 1) * parseInt(perPage, 10);
    const limit = parseInt(perPage, 10);

    let whereClause = `${prefixedFilter}`;
    const params = [];

    // Filtro por status (draft, sent, viewed, accepted, rejected, cancelled)
    if (status) {
      const validStatuses = ['draft', 'sent', 'viewed', 'accepted', 'rejected', 'cancelled'];
      if (validStatuses.includes(status)) {
        whereClause += ' AND p.status = ?';
        params.push(status);
      }
    } else {
      // Default: não mostrar cancelled
      whereClause += ' AND p.status != \'cancelled\'';
    }

    // Filtro por cliente
    if (client_id) {
      whereClause += ' AND p.client_id = ?';
      params.push(parseInt(client_id, 10));
    }

    // Filtro por data
    if (date_from) {
      whereClause += ' AND p.created_at >= ?';
      params.push(date_from);
    }
    if (date_to) {
      whereClause += ' AND p.created_at <= ?';
      params.push(date_to + ' 23:59:59');
    }

    if (search && search.length >= 2) {
      whereClause += ' AND (p.title LIKE ? OR p.number LIKE ? OR c.name LIKE ?)';
      const like = `%${search}%`;
      params.push(like, like, like);
    }

    // Total de registros
    const countSql = `SELECT COUNT(*) as total FROM proposals p
                      LEFT JOIN clients c ON p.client_id = c.id
                      WHERE ${whereClause}`;
    const countRows = params.length > 0
      ? await query(countSql, params)
      : await query(countSql);
    const total = countRows[0]?.total || 0;

    // Listagem paginada
    const listParams = params.length > 0
      ? [...params, limit, offset]
      : [limit, offset];
    const proposals = await query(
      `SELECT p.id, p.number, p.title, p.description, p.total_amount,
              p.status, p.valid_until, p.notes,
              p.client_id, c.name as client_name, c.whatsapp as client_whatsapp,
              p.sent_at, p.accepted_at,
              p.created_at, p.updated_at
       FROM proposals p
       LEFT JOIN clients c ON p.client_id = c.id
       WHERE ${whereClause}
       ORDER BY p.created_at DESC
       LIMIT ? OFFSET ?`,
      listParams
    );

    res.json({
      proposals,
      pagination: {
        page: parseInt(page, 10),
        perPage: limit,
        total,
        totalPages: Math.ceil(total / limit),
      },
      correlationId: req.correlationId,
    });
  } catch (err) {
    next(err);
  }
}

// ── GET /proposals/:id — Obter proposta + itens ─────────
async function read(req, res, next) {
  try {
    const { id } = req.params;
    const tenantFilter = req.tenantFilter || '1=1';
    // Prefixar tenantFilter com 'p.' pois a query faz JOIN (ambas as tabelas têm tenant_id)
    const prefixedFilter = tenantFilter.replace(/\btenant_id\b/g, 'p.tenant_id');

    const rows = await query(
      `SELECT p.id, p.number, p.title, p.description, p.total_amount,
              p.status, p.valid_until, p.payment_terms, p.notes, p.public_token,
              p.client_id, c.name as client_name, c.email as client_email,
              c.whatsapp as client_whatsapp, c.phone as client_phone,
              p.sent_at, p.accepted_at,
              p.created_at, p.updated_at
       FROM proposals p
       LEFT JOIN clients c ON p.client_id = c.id
       WHERE p.id = ? AND ${prefixedFilter}`,
      [id]
    );

    if (rows.length === 0) {
      return res.status(404).json({
        error: 'ERR_NOT_FOUND',
        message: 'Proposta não encontrada',
        correlationId: req.correlationId,
      });
    }

    // Buscar itens da proposta
    const items = await query(
      `SELECT id, proposal_id, description, quantity, unit_price, total_price, sort_order
       FROM proposal_items
       WHERE proposal_id = ?
       ORDER BY sort_order ASC, id ASC`,
      [id]
    );

    res.json({
      proposal: {
        ...rows[0],
        items,
      },
      correlationId: req.correlationId,
    });
  } catch (err) {
    next(err);
  }
}

// ── POST /proposals — Criar proposta + itens ────────────
async function create(req, res, next) {
  try {
    const tenantId = req.tenantId || req.user?.tenantId;

    if (!tenantId) {
      return res.status(403).json({
        error: 'ERR_TENANT_REQUIRED',
        message: 'Tenant não identificado',
        correlationId: req.correlationId,
      });
    }

    const {
      client_id, title, description, valid_until,
      payment_terms, notes, items,
    } = req.body;

    // Validações
    if (!title || title.trim().length === 0) {
      return res.status(400).json({
        error: 'ERR_VALIDATION',
        message: 'Título é obrigatório',
        correlationId: req.correlationId,
      });
    }

    // Gerar número sequencial
    const number = await generateNumber(tenantId);

    // Calcular total dos itens (se fornecidos)
    let totalAmount = 0;
    if (items && Array.isArray(items) && items.length > 0) {
      totalAmount = items.reduce((sum, item) => {
        const qty = parseFloat(item.quantity) || 1;
        const price = parseFloat(item.unit_price) || 0;
        return sum + (qty * price);
      }, 0);
    }

    // Gerar token público para compartilhamento (Story 6.3)
    const publicToken = uuidv4();

    const result = await query(
      `INSERT INTO proposals
        (tenant_id, client_id, number, title, description, total_amount,
         status, valid_until, payment_terms, notes, public_token)
       VALUES (?, ?, ?, ?, ?, ?, 'draft', ?, ?, ?, ?)`,
      [
        tenantId,
        client_id || null,
        number,
        title.trim(),
        description || null,
        totalAmount,
        valid_until || null,
        payment_terms || null,
        notes || null,
        publicToken,
      ]
    );

    const proposalId = result.insertId;

    // Inserir itens (se fornecidos)
    if (items && Array.isArray(items) && items.length > 0) {
      for (let i = 0; i < items.length; i++) {
        const item = items[i];
        const qty = parseFloat(item.quantity) || 1;
        const price = parseFloat(item.unit_price) || 0;
        await query(
          `INSERT INTO proposal_items (proposal_id, description, quantity, unit_price, sort_order)
           VALUES (?, ?, ?, ?, ?)`,
          [
            proposalId,
            item.description || 'Item sem descrição',
            qty,
            price,
            item.sort_order || i + 1,
          ]
        );
      }
    }

    res.status(201).json({
      message: 'Proposta criada com sucesso!',
      proposal: {
        id: proposalId,
        number,
        title: title.trim(),
        total_amount: totalAmount,
        status: 'draft',
        public_token: publicToken,
      },
      correlationId: req.correlationId,
    });
  } catch (err) {
    next(err);
  }
}

// ── PUT /proposals/:id — Atualizar proposta ─────────────
async function update(req, res, next) {
  try {
    const { id } = req.params;
    const tenantFilter = req.tenantFilter || '1=1';
    const {
      client_id, title, description, total_amount,
      valid_until, payment_terms, notes,
    } = req.body;

    // Validação de título
    if (title !== undefined && title !== null && title.trim().length === 0) {
      return res.status(400).json({
        error: 'ERR_VALIDATION',
        message: 'Título é obrigatório',
        correlationId: req.correlationId,
      });
    }

    const existing = await query(
      `SELECT id, status FROM proposals WHERE id = ? AND ${tenantFilter}`,
      [id]
    );

    if (existing.length === 0) {
      return res.status(404).json({
        error: 'ERR_NOT_FOUND',
        message: 'Proposta não encontrada',
        correlationId: req.correlationId,
      });
    }

    // Só permite editar se estiver em draft ou rejected
    const currentStatus = existing[0].status;
    if (!['draft', 'rejected'].includes(currentStatus)) {
      return res.status(422).json({
        error: 'ERR_INVALID_STATUS',
        message: `Não é possível editar uma proposta com status "${currentStatus}". Crie uma nova versão.`,
        correlationId: req.correlationId,
      });
    }

    await query(
      `UPDATE proposals SET
        client_id = COALESCE(?, client_id),
        title = COALESCE(?, title),
        description = COALESCE(?, description),
        total_amount = COALESCE(?, total_amount),
        valid_until = COALESCE(?, valid_until),
        payment_terms = COALESCE(?, payment_terms),
        notes = COALESCE(?, notes),
        public_token = COALESCE(public_token, UUID()),
        updated_at = NOW()
       WHERE id = ? AND ${tenantFilter}`,
      [
        client_id ?? null,
        title ? title.trim() : null,
        description ?? null,
        total_amount !== undefined ? total_amount : null,
        valid_until ?? null,
        payment_terms ?? null,
        notes ?? null,
        id,
      ]
    );

    // Buscar novamente para retornar dados atualizados (incluindo public_token)
    const updated = await query(
      `SELECT id, public_token, number, status FROM proposals WHERE id = ? AND ${tenantFilter}`,
      [id]
    );

    res.json({
      message: 'Proposta atualizada com sucesso!',
      proposal: updated[0] || { id },
      correlationId: req.correlationId,
    });
  } catch (err) {
    next(err);
  }
}

// ── Status Lifecycle ─────────────────────────────────────
// Fluxo válido:
//   draft ──> sent ──> viewed ──> accepted
//                    ──> rejected
//   (qualquer status pode ir para 'cancelled')

const VALID_TRANSITIONS = {
  draft:     ['sent', 'cancelled'],
  sent:      ['viewed', 'accepted', 'rejected', 'cancelled'],
  viewed:    ['accepted', 'rejected', 'cancelled'],
  accepted:  ['cancelled'],
  rejected:  ['cancelled', 'draft'],  // rejected pode ir pra draft (revisão)
  cancelled: ['draft'],                // cancelled pode ser reaberto como draft
};

// ── PATCH /proposals/:id/status — Atualizar status ──────
async function updateStatus(req, res, next) {
  try {
    const { id } = req.params;
    const tenantFilter = req.tenantFilter || '1=1';
    const { status } = req.body;

    if (!status) {
      return res.status(400).json({
        error: 'ERR_VALIDATION',
        message: 'Status é obrigatório',
        correlationId: req.correlationId,
      });
    }

    const validStatuses = Object.keys(VALID_TRANSITIONS);
    if (!validStatuses.includes(status)) {
      return res.status(400).json({
        error: 'ERR_VALIDATION',
        message: `Status inválido. Valores aceitos: ${validStatuses.join(', ')}`,
        correlationId: req.correlationId,
      });
    }

    // Buscar proposta atual
    const rows = await query(
      `SELECT id, status, number, client_id FROM proposals
       WHERE id = ? AND ${tenantFilter}`,
      [id]
    );

    if (rows.length === 0) {
      return res.status(404).json({
        error: 'ERR_NOT_FOUND',
        message: 'Proposta não encontrada',
        correlationId: req.correlationId,
      });
    }

    const currentStatus = rows[0].status;

    // Validar transição
    const allowed = VALID_TRANSITIONS[currentStatus];
    if (!allowed || !allowed.includes(status)) {
      return res.status(422).json({
        error: 'ERR_INVALID_TRANSITION',
        message: `Transição inválida: "${currentStatus}" → "${status}". Transições permitidas: ${(allowed || []).join(', ') || 'nenhuma'}.`,
        correlationId: req.correlationId,
      });
    }

    // Preparar campos extras conforme o status
    const extraFields = [];
    const extraParams = [];

    if (status === 'sent') {
      extraFields.push('sent_at = NOW()');
    }
    if (status === 'accepted') {
      extraFields.push('accepted_at = NOW()');
    }
    if (status === 'draft' && currentStatus === 'rejected') {
      // Ao reabrir, limpar sent_at
      extraFields.push('sent_at = NULL, accepted_at = NULL');
    }

    const extraSQL = extraFields.length > 0 ? ', ' + extraFields.join(', ') : '';

    await query(
      `UPDATE proposals SET
        status = ?,
        updated_at = NOW()
        ${extraSQL}
       WHERE id = ? AND ${tenantFilter}`,
      [status, ...extraParams, id]
    );

    comm.onProposalStatusChange({
      proposal: { number: rows[0].number },
      newStatus: status,
      tenantId: req.tenantId,
      clientId: rows[0].client_id,
    }).catch(() => {});

    res.json({
      message: `Status atualizado para "${status}" com sucesso!`,
      proposal: {
        id: rows[0].id,
        number: rows[0].number,
        status,
      },
      correlationId: req.correlationId,
    });
  } catch (err) {
    next(err);
  }
}

// ── DELETE /proposals/:id — Excluir (lógico) ────────────
async function remove(req, res, next) {
  try {
    const { id } = req.params;
    const tenantFilter = req.tenantFilter || '1=1';

    const existing = await query(
      `SELECT id, status FROM proposals WHERE id = ? AND ${tenantFilter} AND status != 'cancelled'`,
      [id]
    );

    if (existing.length === 0) {
      return res.status(404).json({
        error: 'ERR_NOT_FOUND',
        message: 'Proposta não encontrada ou já cancelada',
        correlationId: req.correlationId,
      });
    }

    await query(
      `UPDATE proposals SET status = 'cancelled', updated_at = NOW()
       WHERE id = ? AND ${tenantFilter}`,
      [id]
    );

    res.json({
      message: 'Proposta cancelada com sucesso!',
      correlationId: req.correlationId,
    });
  } catch (err) {
    next(err);
  }
}

// ── GET /proposals/:id/pdf — Download PDF da proposta ──
async function downloadPdf(req, res, next) {
  try {
    const { id } = req.params;
    const tenantFilter = req.tenantFilter || '1=1';
    const prefixedFilter = tenantFilter.replace(/\btenant_id\b/g, 'p.tenant_id');

    const rows = await query(
      `SELECT p.id, p.number, p.title, p.description, p.total_amount,
              p.status, p.valid_until, p.payment_terms, p.notes,
              p.client_id, c.name as client_name, c.whatsapp as client_whatsapp,
              t.name as tenant_name, t.whatsapp as tenant_whatsapp
       FROM proposals p
       LEFT JOIN clients c ON p.client_id = c.id
       LEFT JOIN tenants t ON p.tenant_id = t.id
       WHERE p.id = ? AND ${prefixedFilter}`,
      [id]
    );

    if (rows.length === 0) {
      return res.status(404).json({
        error: 'ERR_NOT_FOUND',
        message: 'Proposta não encontrada',
        correlationId: req.correlationId,
      });
    }

    const items = await query(
      `SELECT description, quantity, unit_price, total_price, sort_order
       FROM proposal_items
       WHERE proposal_id = ?
       ORDER BY sort_order ASC, id ASC`,
      [id]
    );

    const proposal = {
      ...rows[0],
      items,
      tenant: { name: rows[0].tenant_name, whatsapp: rows[0].tenant_whatsapp },
    };

    const { generateProposalPDF } = require('../../services/pdfService');
    const pdfBuffer = await generateProposalPDF(proposal);

    res.set({
      'Content-Type': 'application/pdf',
      'Content-Disposition': `attachment; filename="proposta-${rows[0].number}.pdf"`,
      'Content-Length': pdfBuffer.length,
    });
    res.send(pdfBuffer);
  } catch (err) {
    next(err);
  }
}

module.exports = { list, read, create, update, updateStatus, remove, downloadPdf };
