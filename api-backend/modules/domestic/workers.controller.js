// ═══════════════════════════════════════════════════════════════
// modules/domestic/workers.controller.js — CRUD Workers
// ═══════════════════════════════════════════════════════════════

const workerService = require('./workers.service');
const { validateCPF, validateEmail } = require('../../utils/validation');

const VALID_CATEGORIES = [
  'EMPREGADO_DOMESTICO_GERAL', 'DIARISTA', 'BABA',
  'CUIDADOR_IDOSOS', 'COZINHEIRO', 'MOTORISTA',
  'JARDINEIRO', 'CASEIRO', 'GOVERNANTA',
];

// ── GET /workers — Listar (paginado + busca) ─────────────
async function list(req, res, next) {
  try {
    const { search, category, page = 1, perPage = 20 } = req.query;
    const tenantFilter = req.tenantFilter || '1=1';

    const result = await workerService.list({
      search, category, page, perPage, tenantFilter,
    });

    res.json({ ...result, correlationId: req.correlationId });
  } catch (err) {
    next(err);
  }
}

// ── GET /workers/:id — Obter um worker ──────────────────
async function read(req, res, next) {
  try {
    const { id } = req.params;
    const tenantFilter = req.tenantFilter || '1=1';

    const worker = await workerService.findById(id, tenantFilter);

    if (!worker) {
      return res.status(404).json({
        error: 'ERR_NOT_FOUND',
        message: 'Trabalhador não encontrado',
        correlationId: req.correlationId,
      });
    }

    res.json({ worker, correlationId: req.correlationId });
  } catch (err) {
    next(err);
  }
}

// ── POST /workers — Criar ────────────────────────────────
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

    const errors = [];
    const {
      name, email, cpf, rg, cboCode, workerCategory,
      phone, whatsapp, pixKey, address,
    } = req.body;

    if (!name || name.trim().length === 0) {
      errors.push('Nome é obrigatório');
    }

    if (!cpf || !validateCPF(cpf)) {
      errors.push('CPF inválido');
    }

    if (email && !validateEmail(email)) {
      errors.push('E-mail inválido');
    }

    if (!cboCode || cboCode.trim().length === 0) {
      errors.push('Código CBO é obrigatório');
    }

    if (!workerCategory || !VALID_CATEGORIES.includes(workerCategory)) {
      errors.push(`Categoria inválida. Valores: ${VALID_CATEGORIES.join(', ')}`);
    }

    if (errors.length > 0) {
      return res.status(422).json({
        error: 'ERR_VALIDATION',
        message: errors.join('; '),
        correlationId: req.correlationId,
      });
    }

    const worker = await workerService.create({
      tenantId,
      name: name.trim(),
      email: email || null,
      cpf: cpf.replace(/\D/g, ''),
      rg: rg || null,
      cboCode: cboCode.trim(),
      workerCategory,
      phone: phone || null,
      whatsapp: whatsapp || null,
      pixKey: pixKey || null,
      address: address || null,
    });

    res.status(201).json({
      message: 'Trabalhador cadastrado com sucesso!',
      worker,
      correlationId: req.correlationId,
    });
  } catch (err) {
    if (err.code === 'ER_DUP_ENTRY') {
      return res.status(409).json({
        error: 'ERR_DUPLICATE_ENTRY',
        message: 'Já existe um trabalhador com este CPF.',
        correlationId: req.correlationId,
      });
    }
    next(err);
  }
}

// ── PUT /workers/:id — Atualizar ─────────────────────────
async function update(req, res, next) {
  try {
    const { id } = req.params;
    const tenantFilter = req.tenantFilter || '1=1';

    const errors = [];
    const {
      name, email, cpf, rg, cboCode, workerCategory,
      phone, whatsapp, pixKey, address,
    } = req.body;

    if (!name || name.trim().length === 0) {
      errors.push('Nome é obrigatório');
    }

    if (workerCategory && !VALID_CATEGORIES.includes(workerCategory)) {
      errors.push(`Categoria inválida. Valores: ${VALID_CATEGORIES.join(', ')}`);
    }

    if (email && !validateEmail(email)) {
      errors.push('E-mail inválido');
    }

    if (cpf && !validateCPF(cpf)) {
      errors.push('CPF inválido');
    }

    if (errors.length > 0) {
      return res.status(422).json({
        error: 'ERR_VALIDATION',
        message: errors.join('; '),
        correlationId: req.correlationId,
      });
    }

    await workerService.update(id, tenantFilter, {
      name: name?.trim(),
      email: email || null,
      cpf: cpf ? cpf.replace(/\D/g, '') : undefined,
      rg: rg || null,
      cboCode: cboCode?.trim(),
      workerCategory,
      phone: phone || null,
      whatsapp: whatsapp || null,
      pixKey: pixKey || null,
      address: address || null,
    });

    res.json({
      message: 'Trabalhador atualizado com sucesso!',
      correlationId: req.correlationId,
    });
  } catch (err) {
    if (err.code === 'ER_DUP_ENTRY') {
      return res.status(409).json({
        error: 'ERR_DUPLICATE_ENTRY',
        message: 'Já existe um trabalhador com este CPF.',
        correlationId: req.correlationId,
      });
    }
    next(err);
  }
}

// ── DELETE /workers/:id — Excluir (lógico) ───────────────
async function remove(req, res, next) {
  try {
    const { id } = req.params;
    const tenantFilter = req.tenantFilter || '1=1';

    const deleted = await workerService.remove(id, tenantFilter);

    if (!deleted) {
      return res.status(404).json({
        error: 'ERR_NOT_FOUND',
        message: 'Trabalhador não encontrado',
        correlationId: req.correlationId,
      });
    }

    res.json({
      message: 'Trabalhador excluído com sucesso!',
      correlationId: req.correlationId,
    });
  } catch (err) {
    next(err);
  }
}

const VALID_CERT_TYPES = ['CUIDADOR_IDOSOS', 'APH', 'BABA', 'COZINHA', 'JARDINAGEM', 'PRIMEIROS_SOCORROS', 'OUTRO'];

async function listCertifications(req, res, next) {
  try {
    const { id } = req.params;
    const tenantFilter = req.tenantFilter || '1=1';
    const certs = await workerService.listCertifications(id, tenantFilter);
    if (certs === null) {
      return res.status(404).json({ error: 'ERR_NOT_FOUND', message: 'Trabalhador não encontrado', correlationId: req.correlationId });
    }
    res.json({ certifications: certs, correlationId: req.correlationId });
  } catch (err) { next(err); }
}

async function createCertification(req, res, next) {
  try {
    const { id } = req.params;
    const tenantFilter = req.tenantFilter || '1=1';
    const { certificationType, title, issuer, issueDate, expiryDate, documentUrl } = req.body;

    const errors = [];
    if (!certificationType || !VALID_CERT_TYPES.includes(certificationType)) errors.push(`Tipo inválido. Valores: ${VALID_CERT_TYPES.join(', ')}`);
    if (!title || title.trim().length === 0) errors.push('Título é obrigatório');
    if (errors.length > 0) {
      return res.status(422).json({ error: 'ERR_VALIDATION', message: errors.join('; '), correlationId: req.correlationId });
    }

    const cert = await workerService.createCertification(id, tenantFilter, { certificationType, title, issuer, issueDate, expiryDate, documentUrl });
    res.status(201).json({ message: 'Certificação cadastrada com sucesso!', ...cert, correlationId: req.correlationId });
  } catch (err) {
    if (err.statusCode) return res.status(err.statusCode).json({ error: 'ERR_VALIDATION', message: err.message, correlationId: req.correlationId });
    next(err);
  }
}

async function updateCertification(req, res, next) {
  try {
    const { id, certId } = req.params;
    const tenantFilter = req.tenantFilter || '1=1';
    await workerService.updateCertification(certId, id, tenantFilter, { ...req.body });
    res.json({ message: 'Certificação atualizada com sucesso!', correlationId: req.correlationId });
  } catch (err) {
    if (err.statusCode) return res.status(err.statusCode).json({ error: 'ERR_VALIDATION', message: err.message, correlationId: req.correlationId });
    next(err);
  }
}

async function deleteCertification(req, res, next) {
  try {
    const { id, certId } = req.params;
    const tenantFilter = req.tenantFilter || '1=1';
    const deleted = await workerService.deleteCertification(certId, id, tenantFilter);
    if (!deleted) {
      return res.status(404).json({ error: 'ERR_NOT_FOUND', message: 'Certificação não encontrada', correlationId: req.correlationId });
    }
    res.json({ message: 'Certificação excluída com sucesso!', correlationId: req.correlationId });
  } catch (err) { next(err); }
}

async function backgroundCheck(req, res, next) {
  try {
    const { id } = req.params;
    const tenantFilter = req.tenantFilter || '1=1';
    const result = await workerService.runBackgroundCheck(id, tenantFilter);
    res.json({ ...result, correlationId: req.correlationId });
  } catch (err) {
    if (err.statusCode) return res.status(err.statusCode).json({ error: 'ERR_NOT_FOUND', message: err.message, correlationId: req.correlationId });
    next(err);
  }
}

async function certificationRequiredCheck(req, res, next) {
  try {
    const { id } = req.params;
    const result = await workerService.checkCertificationRequired(id);
    res.json({ ...result, correlationId: req.correlationId });
  } catch (err) { next(err); }
}

module.exports = { list, read, create, update, remove, listCertifications, createCertification, updateCertification, deleteCertification, backgroundCheck, certificationRequiredCheck };