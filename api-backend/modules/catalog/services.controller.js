// ═══════════════════════════════════════════════════════════════
// modules/catalog/services.controller.js — CRUD Serviços
// ═══════════════════════════════════════════════════════════════
// Endpoints: list, create, read, update, remove (lógico)

const { query } = require('../../config/database');

// ── GET /services — Listar (paginado + busca + filtro) ──
async function list(req, res, next) {
  try {
    const { search, category_id, page = 1, perPage = 50, active } = req.query;
    const tenantFilter = req.tenantFilter || '1=1';
    // Prefixar tenantFilter com 's.' pois a query faz JOIN (ambas as tabelas têm tenant_id)
    const prefixedFilter = tenantFilter.replace(/\btenant_id\b/g, 's.tenant_id');
    const offset = (parseInt(page, 10) - 1) * parseInt(perPage, 10);
    const limit = parseInt(perPage, 10);

    let whereClause = `${prefixedFilter}`;
    const params = [];

    // Filtro por status
    if (active === 'true' || active === '1') {
      whereClause += ' AND s.active = TRUE';
    } else if (active === 'false' || active === '0') {
      whereClause += ' AND s.active = FALSE';
    } else {
      whereClause += ' AND s.active = TRUE';
    }

    // Filtro por categoria
    if (category_id) {
      whereClause += ' AND s.category_id = ?';
      params.push(parseInt(category_id, 10));
    }

    if (search && search.length >= 2) {
      whereClause += ' AND s.name LIKE ?';
      params.push(`%${search}%`);
    }

    const countSql = `SELECT COUNT(*) as total FROM services s WHERE ${whereClause}`;
    const countRows = params.length > 0
      ? await query(countSql, params)
      : await query(countSql);
    const total = countRows[0]?.total || 0;

    const listParams = params.length > 0
      ? [...params, limit, offset]
      : [limit, offset];
    const services = await query(
      `SELECT s.id, s.name, s.description, s.price, s.duration_minutes,
              s.category_id, s.active, s.created_at, s.updated_at,
              c.name as category_name, c.color as category_color
       FROM services s
       LEFT JOIN categories c ON s.category_id = c.id
       WHERE ${whereClause}
       ORDER BY c.sort_order ASC, s.name ASC
       LIMIT ? OFFSET ?`,
      listParams
    );

    res.json({
      services,
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

// ── GET /services/:id — Obter um serviço ────────────────
async function read(req, res, next) {
  try {
    const { id } = req.params;
    const tenantFilter = (req.tenantFilter || '1=1').replace(/\btenant_id\b/g, 's.tenant_id');

    const rows = await query(
      `SELECT s.id, s.name, s.description, s.price, s.duration_minutes,
              s.category_id, s.active, s.created_at, s.updated_at,
              c.name as category_name, c.color as category_color
       FROM services s
       LEFT JOIN categories c ON s.category_id = c.id
       WHERE s.id = ? AND ${tenantFilter}`,
      [id]
    );

    if (rows.length === 0) {
      return res.status(404).json({
        error: 'ERR_NOT_FOUND',
        message: 'Serviço não encontrado',
        correlationId: req.correlationId,
      });
    }

    res.json({ service: rows[0], correlationId: req.correlationId });
  } catch (err) {
    next(err);
  }
}

// ── POST /services — Criar ─────────────────────────────
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

    const { name, description, price, duration_minutes, category_id } = req.body;

    if (!name || name.trim().length === 0) {
      return res.status(400).json({
        error: 'ERR_VALIDATION',
        message: 'Nome é obrigatório',
        correlationId: req.correlationId,
      });
    }

    const result = await query(
      `INSERT INTO services (tenant_id, name, description, price, duration_minutes, category_id, active)
       VALUES (?, ?, ?, ?, ?, ?, TRUE)`,
      [
        tenantId,
        name.trim(),
        description || null,
        price || 0,
        duration_minutes || null,
        category_id || null,
      ]
    );

    res.status(201).json({
      message: 'Serviço cadastrado com sucesso!',
      service: {
        id: result.insertId,
        name: name.trim(),
      },
      correlationId: req.correlationId,
    });
  } catch (err) {
    next(err);
  }
}

// ── PUT /services/:id — Atualizar ──────────────────────
async function update(req, res, next) {
  try {
    const { id } = req.params;
    const tenantFilter = req.tenantFilter || '1=1';
    const { name, description, price, duration_minutes, category_id, active } = req.body;

    // Validação de nome
    if (name !== undefined && name !== null && name.trim().length === 0) {
      return res.status(400).json({
        error: 'ERR_VALIDATION',
        message: 'Nome é obrigatório',
        correlationId: req.correlationId,
      });
    }

    const existing = await query(
      `SELECT id FROM services WHERE id = ? AND ${tenantFilter}`,
      [id]
    );

    if (existing.length === 0) {
      return res.status(404).json({
        error: 'ERR_NOT_FOUND',
        message: 'Serviço não encontrado',
        correlationId: req.correlationId,
      });
    }

await query(
      `UPDATE services SET
        name = COALESCE(?, name),
        description = COALESCE(?, description),
        price = COALESCE(?, price),
        duration_minutes = COALESCE(?, duration_minutes),
        category_id = COALESCE(?, category_id),
        active = COALESCE(?, active),
        updated_at = NOW()
       WHERE id = ? AND ${tenantFilter}`,
      [
        name ? name.trim() : null,
        description ?? null,
        price !== undefined ? price : null,
        duration_minutes ?? null,
        category_id !== undefined ? (category_id || null) : null,
        active !== undefined ? (active ? 1 : 0) : null,
        id,
      ]
    );

    res.json({
      message: 'Serviço atualizado com sucesso!',
      correlationId: req.correlationId,
    });
  } catch (err) {
    next(err);
  }
}

// ── DELETE /services/:id — Excluir (lógico) ────────────
async function remove(req, res, next) {
  try {
    const { id } = req.params;
    const tenantFilter = req.tenantFilter || '1=1';

    const existing = await query(
      `SELECT id FROM services WHERE id = ? AND ${tenantFilter} AND active = TRUE`,
      [id]
    );

    if (existing.length === 0) {
      return res.status(404).json({
        error: 'ERR_NOT_FOUND',
        message: 'Serviço não encontrado',
        correlationId: req.correlationId,
      });
    }

    await query(
      `UPDATE services SET active = FALSE, updated_at = NOW() WHERE id = ? AND ${tenantFilter}`,
      [id]
    );

    res.json({
      message: 'Serviço desativado com sucesso!',
      correlationId: req.correlationId,
    });
  } catch (err) {
    next(err);
  }
}

module.exports = { list, read, create, update, remove };
