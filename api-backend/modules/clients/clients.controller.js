// ═══════════════════════════════════════════════════════════════
// modules/clients/clients.controller.js — CRUD Clientes
// ═══════════════════════════════════════════════════════════════
// Endpoints: list, create, read, update, delete (lógico)

const { query } = require('../../config/database');
const { validateCPF, validateCNPJ, validateEmail } = require('../../utils/validation');

// ── GET /clients — Listar (paginado + busca) ─────────────
async function list(req, res, next) {
  try {
    const { search, page = 1, perPage = 20 } = req.query;
    const tenantFilter = req.tenantFilter || '1=1';
    const offset = (parseInt(page, 10) - 1) * parseInt(perPage, 10);
    const limit = parseInt(perPage, 10);

    let whereClause = `${tenantFilter} AND active = TRUE`;
    const params = [];

    if (search && search.length >= 2) {
      whereClause += ' AND name LIKE ?';
      params.push(`%${search}%`);
    }

    // Total de registros
    const countSql = `SELECT COUNT(*) as total FROM clients WHERE ${whereClause}`;
    const countRows = params.length > 0
      ? await query(countSql, params)
      : await query(countSql);
    const total = countRows[0]?.total || 0;

    // Listagem paginada
    const listParams = params.length > 0
      ? [...params, limit, offset]
      : [limit, offset];
    const clients = await query(
      `SELECT id, name, email, document_cpf, document_cnpj,
              zipcode, phone, whatsapp, address, city, state, notes, active,
              created_at, updated_at
       FROM clients
       WHERE ${whereClause}
       ORDER BY name ASC
       LIMIT ? OFFSET ?`,
      listParams
    );

    res.json({
      clients,
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

// ── GET /clients/:id — Obter um cliente ──────────────────
async function read(req, res, next) {
  try {
    const { id } = req.params;
    const tenantFilter = req.tenantFilter || '1=1';

    const clients = await query(
      `SELECT id, name, email, document_cpf, document_cnpj,
              zipcode, phone, whatsapp, address, city, state, notes, active,
              created_at, updated_at
       FROM clients
       WHERE id = ? AND ${tenantFilter}`,
      [id]
    );

    if (clients.length === 0) {
      return res.status(404).json({
        error: 'ERR_NOT_FOUND',
        message: 'Cliente não encontrado',
        correlationId: req.correlationId,
      });
    }

    res.json({ client: clients[0], correlationId: req.correlationId });
  } catch (err) {
    next(err);
  }
}

// ── POST /clients — Criar ────────────────────────────────
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
      name, email, documentCpf, documentCnpj,
      zipcode, phone, whatsapp, address, city, state, notes,
    } = req.body;

    // Validações
    if (!name || name.trim().length === 0) {
      return res.status(400).json({
        error: 'ERR_VALIDATION',
        message: 'Nome é obrigatório',
        correlationId: req.correlationId,
      });
    }

    if (email && !validateEmail(email)) {
      return res.status(400).json({
        error: 'ERR_VALIDATION',
        message: 'Formato de e-mail inválido',
        correlationId: req.correlationId,
      });
    }

    if (documentCpf && !validateCPF(documentCpf)) {
      return res.status(400).json({
        error: 'ERR_VALIDATION',
        message: 'CPF inválido',
        correlationId: req.correlationId,
      });
    }

    if (documentCnpj && !validateCNPJ(documentCnpj)) {
      return res.status(400).json({
        error: 'ERR_VALIDATION',
        message: 'CNPJ inválido',
        correlationId: req.correlationId,
      });
    }

    const result = await query(
      `INSERT INTO clients (tenant_id, name, email, document_cpf, document_cnpj,
                            zipcode, phone, whatsapp, address, city, state, notes, active)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, TRUE)`,
      [
        tenantId,
        name.trim(),
        email || null,
        documentCpf || null,
        documentCnpj || null,
        zipcode || null,
        phone || null,
        whatsapp || null,
        address || null,
        city || null,
        state || null,
        notes || null,
      ]
    );

    res.status(201).json({
      message: 'Cliente cadastrado com sucesso!',
      client: {
        id: result.insertId,
        name: name.trim(),
        email: email || null,
        tenantId,
      },
      correlationId: req.correlationId,
    });
  } catch (err) {
    if (err.code === 'ER_DUP_ENTRY') {
      return res.status(409).json({
        error: 'ERR_DUPLICATE_ENTRY',
        message: 'Já existe um cliente com este documento.',
        correlationId: req.correlationId,
      });
    }
    next(err);
  }
}

// ── PUT /clients/:id — Atualizar ─────────────────────────
async function update(req, res, next) {
  try {
    const { id } = req.params;
    const tenantFilter = req.tenantFilter || '1=1';

    const {
      name, email, documentCpf, documentCnpj,
      zipcode, phone, whatsapp, address, city, state, notes,
    } = req.body;

    // Validação de nome obrigatório (antes da checagem de existência)
    if (!name || name.trim().length === 0) {
      return res.status(400).json({
        error: 'ERR_VALIDATION',
        message: 'Nome é obrigatório',
        correlationId: req.correlationId,
      });
    }

    if (email && !validateEmail(email)) {
      return res.status(400).json({
        error: 'ERR_VALIDATION',
        message: 'Formato de e-mail inválido',
        correlationId: req.correlationId,
      });
    }

    if (documentCpf && !validateCPF(documentCpf)) {
      return res.status(400).json({
        error: 'ERR_VALIDATION',
        message: 'CPF inválido',
        correlationId: req.correlationId,
      });
    }

    if (documentCnpj && !validateCNPJ(documentCnpj)) {
      return res.status(400).json({
        error: 'ERR_VALIDATION',
        message: 'CNPJ inválido',
        correlationId: req.correlationId,
      });
    }

    // Verificar se o cliente existe e pertence ao tenant
    const existing = await query(
      `SELECT id FROM clients WHERE id = ? AND ${tenantFilter}`,
      [id]
    );

    if (existing.length === 0) {
      return res.status(404).json({
        error: 'ERR_NOT_FOUND',
        message: 'Cliente não encontrado',
        correlationId: req.correlationId,
      });
    }

    await query(
      `UPDATE clients SET
        name = ?,
        email = ?,
        document_cpf = ?,
        document_cnpj = ?,
        zipcode = ?,
        phone = ?,
        whatsapp = ?,
        address = ?,
        city = ?,
        state = ?,
        notes = ?,
        updated_at = NOW()
       WHERE id = ? AND ${tenantFilter}`,
      [
        name.trim(),
        email || null,
        documentCpf || null,
        documentCnpj || null,
        zipcode || null,
        phone || null,
        whatsapp || null,
        address || null,
        city || null,
        state || null,
        notes || null,
        id,
      ]
    );

    res.json({
      message: 'Cliente atualizado com sucesso!',
      correlationId: req.correlationId,
    });
  } catch (err) {
    next(err);
  }
}

// ── DELETE /clients/:id — Excluir (lógico) ───────────────
async function remove(req, res, next) {
  try {
    const { id } = req.params;
    const tenantFilter = req.tenantFilter || '1=1';

    const existing = await query(
      `SELECT id FROM clients WHERE id = ? AND ${tenantFilter} AND active = TRUE`,
      [id]
    );

    if (existing.length === 0) {
      return res.status(404).json({
        error: 'ERR_NOT_FOUND',
        message: 'Cliente não encontrado',
        correlationId: req.correlationId,
      });
    }

    // Exclusão lógica: marca como inativo
    await query(
      `UPDATE clients SET active = FALSE, updated_at = NOW() WHERE id = ? AND ${tenantFilter}`,
      [id]
    );

    res.json({
      message: 'Cliente excluído com sucesso!',
      correlationId: req.correlationId,
    });
  } catch (err) {
    next(err);
  }
}

module.exports = { list, read, create, update, remove };
