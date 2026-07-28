// ═══════════════════════════════════════════════════════════════
// modules/catalog/categories.controller.js — CRUD Categorias
// ═══════════════════════════════════════════════════════════════
// Endpoints: list, create, read, update, remove (lógico)

const { query } = require('../../config/database');

// ── GET /categories — Listar (paginado + busca) ─────────
async function list(req, res, next) {
  try {
    const { search, page = 1, perPage = 50, active } = req.query;
    const tenantFilter = req.tenantFilter || '1=1';
    const offset = (parseInt(page, 10) - 1) * parseInt(perPage, 10);
    const limit = parseInt(perPage, 10);

    let whereClause = `${tenantFilter}`;
    const params = [];

    // Filtro por status
    if (active === 'true' || active === '1') {
      whereClause += ' AND active = TRUE';
    } else if (active === 'false' || active === '0') {
      whereClause += ' AND active = FALSE';
    } else {
      whereClause += ' AND active = TRUE'; // default: apenas ativos
    }

    if (search && search.length >= 2) {
      whereClause += ' AND name LIKE ?';
      params.push(`%${search}%`);
    }

    const countSql = `SELECT COUNT(*) as total FROM categories WHERE ${whereClause}`;
    const countRows = params.length > 0
      ? await query(countSql, params)
      : await query(countSql);
    const total = countRows[0]?.total || 0;

    const listParams = params.length > 0
      ? [...params, limit, offset]
      : [limit, offset];
    const categories = await query(
      `SELECT id, name, description, icon, color, active, sort_order,
              created_at, updated_at
       FROM categories
       WHERE ${whereClause}
       ORDER BY sort_order ASC, name ASC
       LIMIT ? OFFSET ?`,
      listParams
    );

    res.json({
      categories,
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

// ── GET /categories/:id — Obter uma categoria ───────────
async function read(req, res, next) {
  try {
    const { id } = req.params;
    const tenantFilter = req.tenantFilter || '1=1';

    const rows = await query(
      `SELECT id, name, description, icon, color, active, sort_order,
              created_at, updated_at
       FROM categories
       WHERE id = ? AND ${tenantFilter}`,
      [id]
    );

    if (rows.length === 0) {
      return res.status(404).json({
        error: 'ERR_NOT_FOUND',
        message: 'Categoria não encontrada',
        correlationId: req.correlationId,
      });
    }

    res.json({ category: rows[0], correlationId: req.correlationId });
  } catch (err) {
    next(err);
  }
}

// ── POST /categories — Criar ────────────────────────────
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

    const { name, description, icon, color } = req.body;

    if (!name || name.trim().length === 0) {
      return res.status(400).json({
        error: 'ERR_VALIDATION',
        message: 'Nome é obrigatório',
        correlationId: req.correlationId,
      });
    }

    const result = await query(
      `INSERT INTO categories (tenant_id, name, description, icon, color, active, sort_order)
       VALUES (?, ?, ?, ?, ?, TRUE, 0)`,
      [
        tenantId,
        name.trim(),
        description || null,
        icon || null,
        color || null,
      ]
    );

    res.status(201).json({
      message: 'Categoria cadastrada com sucesso!',
      category: {
        id: result.insertId,
        name: name.trim(),
      },
      correlationId: req.correlationId,
    });
  } catch (err) {
    next(err);
  }
}

// ── PUT /categories/:id — Atualizar ─────────────────────
async function update(req, res, next) {
  try {
    const { id } = req.params;
    const tenantFilter = req.tenantFilter || '1=1';
    const { name, description, icon, color, sortOrder, active } = req.body;

    // Validação de nome
    if (name !== undefined && name !== null && name.trim().length === 0) {
      return res.status(400).json({
        error: 'ERR_VALIDATION',
        message: 'Nome é obrigatório',
        correlationId: req.correlationId,
      });
    }

    const existing = await query(
      `SELECT id FROM categories WHERE id = ? AND ${tenantFilter}`,
      [id]
    );

    if (existing.length === 0) {
      return res.status(404).json({
        error: 'ERR_NOT_FOUND',
        message: 'Categoria não encontrada',
        correlationId: req.correlationId,
      });
    }

    await query(
      `UPDATE categories SET
        name = COALESCE(?, name),
        description = COALESCE(?, description),
        icon = COALESCE(?, icon),
        color = COALESCE(?, color),
        sort_order = COALESCE(?, sort_order),
        active = COALESCE(?, active),
        updated_at = NOW()
       WHERE id = ? AND ${tenantFilter}`,
      [
        name ? name.trim() : null,
        description ?? null,
        icon ?? null,
        color ?? null,
        sortOrder ?? null,
        active !== undefined ? (active ? 1 : 0) : null,
        id,
      ]
    );

    res.json({
      message: 'Categoria atualizada com sucesso!',
      correlationId: req.correlationId,
    });
  } catch (err) {
    next(err);
  }
}

// ── DELETE /categories/:id — Excluir (lógico) ───────────
async function remove(req, res, next) {
  try {
    const { id } = req.params;
    const tenantFilter = req.tenantFilter || '1=1';

    const existing = await query(
      `SELECT id FROM categories WHERE id = ? AND ${tenantFilter} AND active = TRUE`,
      [id]
    );

    if (existing.length === 0) {
      return res.status(404).json({
        error: 'ERR_NOT_FOUND',
        message: 'Categoria não encontrada',
        correlationId: req.correlationId,
      });
    }

    await query(
      `UPDATE categories SET active = FALSE, updated_at = NOW() WHERE id = ? AND ${tenantFilter}`,
      [id]
    );

    res.json({
      message: 'Categoria desativada com sucesso!',
      correlationId: req.correlationId,
    });
  } catch (err) {
    next(err);
  }
}

module.exports = { list, read, create, update, remove };
