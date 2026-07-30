// ═══════════════════════════════════════════════════════════════
// modules/auth/auth.controller.js — Auth Controller
// ═══════════════════════════════════════════════════════════════

const bcrypt = require('bcrypt');
const jwt = require('jsonwebtoken');
const { v4: uuidv4 } = require('uuid');
const { query, transaction } = require('../../config/database');
const { jwt: jwtConfig } = require('../../config/auth');
const { AppError, ERROR_CODES } = require('../../middlewares/error.middleware');
const { sendResetPasswordEmail, sendWelcomeEmail } = require('../../services/email.service');
const { validateCPF, validateCNPJ, validateEmail } = require('../../utils/validation');

// ── POST /auth/register ──────────────────────────────────
async function register(req, res, next) {
  try {
    const {
      companyName,
      email,
      password,
      documentCpf,
      documentCnpj,
      phone,
      whatsapp,
      zipcode,
      address,
      neighborhood,
      city,
      state,
    } = req.body;

    // Validações básicas
    if (!companyName || !email || !password) {
      return res.status(400).json({
        error: 'ERR_VALIDATION',
        message: 'Campos obrigatórios: companyName, email, password',
        correlationId: req.correlationId,
      });
    }

    if (password.length < 8) {
      return res.status(400).json({
        error: 'ERR_VALIDATION',
        message: 'Senha deve ter no mínimo 8 caracteres',
        correlationId: req.correlationId,
      });
    }

    if (!validateEmail(email)) {
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

    const result = await transaction(async (conn) => {
      // 1. Criar Tenant
      const [tenantInsert] = await conn.execute(
        `INSERT INTO tenants (name, document_cpf, document_cnpj, phone, whatsapp,
                              zipcode, address, neighborhood, city, state, active)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, TRUE)`,
        [companyName, documentCpf || null, documentCnpj || null, phone || null, whatsapp || null,
         zipcode || null, address || null, neighborhood || null, city || null, state || null]
      );
      const tenantId = tenantInsert.insertId;

      // 2. Criar Admin do Tenant
      const hashedPassword = await bcrypt.hash(password, 12);
      const [userInsert] = await conn.execute(
        `INSERT INTO users (tenant_id, name, email, password_hash, role, active)
         VALUES (?, ?, ?, ?, 'admin', TRUE)`,
        [tenantId, companyName, email, hashedPassword]
      );

      return { tenantId, userId: userInsert.insertId };
    });

    // 3. Gerar JWT
    const token = jwt.sign(
      {
        sub: result.userId,
        tenant_id: result.tenantId,
        role: 'admin',
        email,
      },
      jwtConfig.secret,
      { expiresIn: jwtConfig.expiresIn, algorithm: jwtConfig.algorithm }
    );

    sendWelcomeEmail({ to: email, name: companyName }).catch(() => {});

    res.status(201).json({
      message: 'Cadastro realizado com sucesso!',
      token,
      user: {
        id: result.userId,
        tenantId: result.tenantId,
        email,
        role: 'admin',
      },
    });
  } catch (err) {
    if (err.code === 'ER_DUP_ENTRY') {
      return res.status(409).json({
        error: 'ERR_DUPLICATE_ENTRY',
        message: 'Este e-mail já está cadastrado.',
        correlationId: req.correlationId,
      });
    }
    next(err);
  }
}

// ── POST /auth/login ─────────────────────────────────────
async function login(req, res, next) {
  try {
    const { email, password } = req.body;

    if (!email || !password) {
      return res.status(400).json({
        error: 'ERR_VALIDATION',
        message: 'Campos obrigatórios: email, password',
        correlationId: req.correlationId,
      });
    }

    const users = await query(
      `SELECT u.id, u.name, u.email, u.password_hash, u.role, u.active, u.tenant_id,
              t.active as tenant_active
       FROM users u
       JOIN tenants t ON u.tenant_id = t.id
       WHERE u.email = ? AND u.active = TRUE AND t.active = TRUE`,
      [email]
    );

    if (users.length === 0) {
      return res.status(401).json({
        error: 'ERR_INVALID_CREDENTIALS',
        message: 'E-mail ou senha inválidos',
        correlationId: req.correlationId,
      });
    }

    const user = users[0];

    if (!user.tenant_active) {
      return res.status(403).json({
        error: 'ERR_TENANT_INACTIVE',
        message: 'Sua conta está desativada. Entre em contato com o suporte.',
        correlationId: req.correlationId,
      });
    }

    const passwordMatch = await bcrypt.compare(password, user.password_hash);
    if (!passwordMatch) {
      return res.status(401).json({
        error: 'ERR_INVALID_CREDENTIALS',
        message: 'E-mail ou senha inválidos',
        correlationId: req.correlationId,
      });
    }

    const token = jwt.sign(
      {
        sub: user.id,
        tenant_id: user.tenant_id,
        role: user.role,
        email: user.email,
      },
      jwtConfig.secret,
      { expiresIn: jwtConfig.expiresIn, algorithm: jwtConfig.algorithm }
    );

    res.json({
      message: 'Login realizado com sucesso!',
      token,
      user: {
        id: user.id,
        name: user.name,
        email: user.email,
        role: user.role,
        tenantId: user.tenant_id,
      },
    });
  } catch (err) {
    next(err);
  }
}

// ── GET /auth/me ─────────────────────────────────────────
async function me(req, res, next) {
  try {
    const users = await query(
      `SELECT u.id, u.name, u.email, u.role, u.tenant_id, u.created_at,
              t.name as tenant_name, t.active as tenant_active,
              t.city, t.state, t.phone as tenant_phone
       FROM users u
       JOIN tenants t ON u.tenant_id = t.id
       WHERE u.id = ?`,
      [req.user.id]
    );

    if (users.length === 0) {
      return res.status(404).json({
        error: 'ERR_NOT_FOUND',
        message: 'Usuário não encontrado',
        correlationId: req.correlationId,
      });
    }

    const user = users[0];
    res.json({
      user: {
        id: user.id,
        name: user.name,
        email: user.email,
        role: user.role,
        tenantId: user.tenant_id,
        tenantName: user.tenant_name,
        tenantActive: user.tenant_active,
        tenantCity: user.city,
        tenantState: user.state,
        tenantPhone: user.tenant_phone,
        createdAt: user.created_at,
      },
    });
  } catch (err) {
    next(err);
  }
}

// ── POST /auth/forgot-password ──────────────────────────
async function forgotPassword(req, res, next) {
  try {
    const { email } = req.body;

    if (!email) {
      return res.status(400).json({
        error: 'ERR_VALIDATION',
        message: 'E-mail é obrigatório',
        correlationId: req.correlationId,
      });
    }

    // Buscar usuário ativo pelo e-mail
    const users = await query(
      `SELECT id, name, email FROM users WHERE email = ? AND active = TRUE`,
      [email]
    );

    // Resposta genérica (segurança — não revela se e-mail existe)
    if (users.length === 0) {
      return res.json({
        message: 'Se o e-mail existir, você receberá um link de recuperação.',
        correlationId: req.correlationId,
      });
    }

    const user = users[0];

    // Gerar token UUID único
    const resetToken = uuidv4();
    const expiresAt = new Date(Date.now() + 60 * 60 * 1000); // 1 hora

    // Salvar token no banco
    await query(
      `UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE id = ?`,
      [resetToken, expiresAt, user.id]
    );

    // Enviar e-mail (stub MVP — apenas console.log)
    await sendResetPasswordEmail({ to: user.email, name: user.name, token: resetToken });

    res.json({
      message: 'Se o e-mail existir, você receberá um link de recuperação.',
      correlationId: req.correlationId,
    });
  } catch (err) {
    next(err);
  }
}

// ── POST /auth/reset-password ───────────────────────────
async function resetPassword(req, res, next) {
  try {
    const { token, password } = req.body;

    if (!token || !password) {
      return res.status(400).json({
        error: 'ERR_VALIDATION',
        message: 'Token e nova senha são obrigatórios',
        correlationId: req.correlationId,
      });
    }

    if (password.length < 8) {
      return res.status(400).json({
        error: 'ERR_VALIDATION',
        message: 'Senha deve ter no mínimo 8 caracteres',
        correlationId: req.correlationId,
      });
    }

    // Buscar usuário com token válido
    const users = await query(
      `SELECT id, name, email FROM users
       WHERE reset_token = ? AND reset_token_expires > NOW() AND active = TRUE`,
      [token]
    );

    if (users.length === 0) {
      return res.status(400).json({
        error: 'ERR_INVALID_TOKEN',
        message: 'Link inválido ou expirado. Solicite um novo.',
        correlationId: req.correlationId,
      });
    }

    const user = users[0];

    // Gerar hash da nova senha (bcrypt já importado no topo)
    const hashedPassword = await bcrypt.hash(password, 12);

    // Atualizar senha e limpar token
    await query(
      `UPDATE users SET
        password_hash = ?,
        reset_token = NULL,
        reset_token_expires = NULL,
        updated_at = NOW()
       WHERE id = ?`,
      [hashedPassword, user.id]
    );

    console.log(`[AUTH] ✅ Senha redefinida para ${user.email}`);

    res.json({
      message: 'Senha redefinida com sucesso!',
      correlationId: req.correlationId,
    });
  } catch (err) {
    next(err);
  }
}

module.exports = { register, login, me, forgotPassword, resetPassword };
