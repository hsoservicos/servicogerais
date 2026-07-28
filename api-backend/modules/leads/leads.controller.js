// ═══════════════════════════════════════════════════════════════
// modules/leads/leads.controller.js — Leads (Epic 6 — Story 6.4)
// ═══════════════════════════════════════════════════════════════
// GET  /api/v1/leads       — Listar leads (paginado)
// PATCH /api/v1/leads/:id  — Atualizar status do lead
// ═══════════════════════════════════════════════════════════════

const { query } = require('../../config/database');

// ═══════════════════════════════════════════════════════════════
// GET /api/v1/leads — Listar Leads
// ═══════════════════════════════════════════════════════════════
// Retorna leads paginados com filtros opcionais por status e busca.
// Sem tenant filter — leads são públicos até serem atribuídos.
async function list(req, res, next) {
  try {
    const page = Math.max(1, parseInt(req.query.page, 10) || 1);
    const limit = Math.min(50, Math.max(5, parseInt(req.query.limit, 10) || 20));
    const offset = (page - 1) * limit;
    const status = req.query.status || '';
    const search = req.query.search || '';

    let whereClause = '1=1';
    const params = [];

    if (status && ['new', 'contacted', 'converted', 'archived'].includes(status)) {
      whereClause += ' AND l.status = ?';
      params.push(status);
    }

    if (search && search.length >= 2) {
      whereClause += ' AND (l.customer_name LIKE ? OR l.customer_phone LIKE ? OR l.service_name LIKE ?)';
      const searchTerm = `%${search}%`;
      params.push(searchTerm, searchTerm, searchTerm);
    }

    // Total de registros (para paginação)
    const countResult = await query(
      `SELECT COUNT(*) as total FROM public_leads l WHERE ${whereClause}`,
      params
    );
    const total = countResult[0]?.total || 0;

    // Listagem paginada
    const leads = await query(
      `SELECT l.id, l.service_name, l.description,
              l.desired_date, l.desired_time,
              l.zipcode, l.address, l.city, l.state,
              l.photo_urls,
              l.customer_name, l.customer_phone, l.customer_email,
              l.lgpd_consent_marketing,
              l.status, l.notes,
              l.created_at, l.updated_at
       FROM public_leads l
       WHERE ${whereClause}
       ORDER BY 
         FIELD(l.status, 'new', 'contacted', 'converted', 'archived'),
         l.created_at DESC
       LIMIT ? OFFSET ?`,
      [...params, limit, offset]
    );

    res.json({
      leads: leads.map(l => ({
        id: l.id,
        service: l.service_name,
        description: l.description,
        desiredDate: l.desired_date,
        desiredTime: l.desired_time ? l.desired_time.slice(0, 5) : null,
        address: [l.address, l.city, l.state].filter(Boolean).join(', ') || null,
        customerName: l.customer_name,
        customerPhone: l.customer_phone,
        customerEmail: l.customer_email,
        hasPhotos: l.photo_urls ? (typeof l.photo_urls === 'string' ? JSON.parse(l.photo_urls).length : l.photo_urls.length) : 0,
        photoUrls: l.photo_urls,
        status: l.status,
        notes: l.notes,
        createdAt: l.created_at,
        updatedAt: l.updated_at,
      })),
      pagination: {
        page,
        limit,
        total,
        totalPages: Math.ceil(total / limit),
      },
      correlationId: req.correlationId,
    });
  } catch (err) {
    next(err);
  }
}

// ═══════════════════════════════════════════════════════════════
// PATCH /api/v1/leads/:id — Atualizar Status do Lead
// ═══════════════════════════════════════════════════════════════
async function updateStatus(req, res, next) {
  try {
    const { id } = req.params;
    const { status, notes } = req.body;

    const validStatuses = ['new', 'contacted', 'converted', 'archived'];
    if (!status || !validStatuses.includes(status)) {
      return res.status(400).json({
        error: 'ERR_VALIDATION',
        message: `Status inválido. Valores válidos: ${validStatuses.join(', ')}`,
      });
    }

    // Verificar se o lead existe
    const existing = await query(
      'SELECT id, status FROM public_leads WHERE id = ?',
      [id]
    );

    if (existing.length === 0) {
      return res.status(404).json({
        error: 'ERR_NOT_FOUND',
        message: 'Lead não encontrado',
      });
    }

    // Atualizar status
    const updateFields = ['status = ?'];
    const updateParams = [status];

    if (notes !== undefined) {
      updateFields.push('notes = ?');
      updateParams.push(notes);
    }

    updateParams.push(id);

    await query(
      `UPDATE public_leads SET ${updateFields.join(', ')}, updated_at = NOW() WHERE id = ?`,
      updateParams
    );

    console.log(`[Leads] ✅ Lead #${id} atualizado: ${existing[0].status} → ${status}`);

    res.json({
      message: 'Lead atualizado com sucesso',
      data: { id: parseInt(id, 10), status },
      correlationId: req.correlationId,
    });
  } catch (err) {
    next(err);
  }
}

module.exports = { list, updateStatus };
