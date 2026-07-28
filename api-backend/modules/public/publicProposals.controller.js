// ═══════════════════════════════════════════════════════════════
// modules/public/publicProposals.controller.js — Proposta Pública
// ═══════════════════════════════════════════════════════════════
// Story 6.3 — Endpoints públicos SEM autenticação.
// Cliente visualiza proposta e aprova/rejeita via link compartilhável.
// ═══════════════════════════════════════════════════════════════

const { query } = require('../../config/database');

// ═══════════════════════════════════════════════════════════════
// GET /api/v1/public/proposals/:token — Visualizar Proposta
// ═══════════════════════════════════════════════════════════════
async function getByToken(req, res, next) {
  try {
    const { token } = req.params;

    if (!token || token.length < 10) {
      return res.status(404).json({
        error: 'ERR_NOT_FOUND',
        message: 'Proposta não encontrada',
        correlationId: req.correlationId,
      });
    }

    // Buscar proposta pelo token público
    const rows = await query(
      `SELECT p.id, p.number, p.title, p.description, p.total_amount,
              p.status, p.valid_until, p.payment_terms, p.notes,
              p.created_at, p.updated_at,
              t.name as tenant_name, t.whatsapp as tenant_whatsapp,
              t.logo_url as tenant_logo,
              c.name as client_name, c.email as client_email
       FROM proposals p
       LEFT JOIN tenants t ON p.tenant_id = t.id
       LEFT JOIN clients c ON p.client_id = c.id
       WHERE p.public_token = ?`,
      [token]
    );

    if (rows.length === 0) {
      return res.status(404).json({
        error: 'ERR_NOT_FOUND',
        message: 'Proposta não encontrada',
        correlationId: req.correlationId,
      });
    }

    const proposal = rows[0];

    // Buscar itens da proposta
    const items = await query(
      `SELECT id, description, quantity, unit_price, total_price, sort_order
       FROM proposal_items
       WHERE proposal_id = ?
       ORDER BY sort_order ASC, id ASC`,
      [proposal.id]
    );

    // Extrair informações do tenant para exibição
    const tenantInfo = {
      name: proposal.tenant_name,
      whatsapp: proposal.tenant_whatsapp,
      logo: proposal.tenant_logo,
    };

    // Status permitidos para ações
    const canAct = ['sent', 'viewed'].includes(proposal.status);

    res.json({
      proposal: {
        id: proposal.id,
        number: proposal.number,
        title: proposal.title,
        description: proposal.description,
        totalAmount: proposal.total_amount,
        status: proposal.status,
        validUntil: proposal.valid_until,
        paymentTerms: proposal.payment_terms,
        notes: proposal.notes,
        items,
        tenant: tenantInfo,
        clientName: proposal.client_name,
        clientEmail: proposal.client_email,
        createdAt: proposal.created_at,
        canAct,
      },
      correlationId: req.correlationId,
    });
  } catch (err) {
    next(err);
  }
}

// ═══════════════════════════════════════════════════════════════
// PATCH /api/v1/public/proposals/:token/status — Aprovar/Rejeitar
// ═══════════════════════════════════════════════════════════════
async function updateStatus(req, res, next) {
  try {
    const { token } = req.params;
    const { action } = req.body; // 'approve' ou 'reject'

    if (!token || token.length < 10) {
      return res.status(404).json({
        error: 'ERR_NOT_FOUND',
        message: 'Proposta não encontrada',
        correlationId: req.correlationId,
      });
    }

    if (!action || !['approve', 'reject'].includes(action)) {
      return res.status(400).json({
        error: 'ERR_VALIDATION',
        message: 'Ação inválida. Use "approve" ou "reject".',
        correlationId: req.correlationId,
      });
    }

    // Buscar proposta pelo token
    const rows = await query(
      `SELECT id, status FROM proposals WHERE public_token = ?`,
      [token]
    );

    if (rows.length === 0) {
      return res.status(404).json({
        error: 'ERR_NOT_FOUND',
        message: 'Proposta não encontrada',
        correlationId: req.correlationId,
      });
    }

    const proposal = rows[0];
    const currentStatus = proposal.status;

    // Validar: só pode agir se estiver 'sent' ou 'viewed'
    if (!['sent', 'viewed'].includes(currentStatus)) {
      return res.status(422).json({
        error: 'ERR_INVALID_STATUS',
        message: `Esta proposta já foi respondida. Status atual: "${currentStatus}".`,
        correlationId: req.correlationId,
      });
    }

    // Definir novo status baseado na ação
    let newStatus;
    if (action === 'approve') {
      // sent → viewed → accepted (se já viewed, vai direto para accepted)
      newStatus = currentStatus === 'viewed' ? 'accepted' : 'viewed';
    } else {
      newStatus = 'rejected';
    }

    // Campos extras
    let extraSql = '';
    if (newStatus === 'accepted') {
      extraSql = ', accepted_at = NOW()';
    }

    await query(
      `UPDATE proposals SET status = ?, updated_at = NOW()${extraSql}
       WHERE id = ? AND public_token = ?`,
      [newStatus, proposal.id, token]
    );

    console.log(`[PublicProposal] ✅ Proposta #${proposal.id} (${token.slice(0, 8)}...) ${action} → ${newStatus}`);

    res.json({
      message: action === 'approve'
        ? 'Proposta aprovada com sucesso!'
        : 'Proposta rejeitada.',
      data: {
        id: proposal.id,
        status: newStatus,
        action,
      },
      correlationId: req.correlationId,
    });
  } catch (err) {
    next(err);
  }
}

// ═══════════════════════════════════════════════════════════════
// GET /api/v1/public/proposals/:token/payment — Status do Pagamento
// ═══════════════════════════════════════════════════════════════
// Retorna informações de pagamento para a proposta.
// Usado pela página pública para exibir status e opções de pagamento.
async function getPaymentStatus(req, res, next) {
  try {
    const { token } = req.params;

    // Buscar proposta pelo token
    const proposals = await query(
      `SELECT id, status, total_amount FROM proposals WHERE public_token = ?`,
      [token]
    );

    if (proposals.length === 0) {
      return res.status(404).json({ error: 'ERR_NOT_FOUND', message: 'Proposta não encontrada' });
    }

    const proposal = proposals[0];

    // Buscar transações desta proposta
    const transactions = await query(
      `SELECT id, mp_id, mp_status, amount, fee, payment_method, status as local_status,
              paid_at, created_at
       FROM transactions
       WHERE proposal_id = ?
       ORDER BY created_at DESC
       LIMIT 1`,
      [proposal.id]
    );

    const payment = transactions.length > 0 ? transactions[0] : null;

    res.json({
      proposalStatus: proposal.status,
      totalAmount: proposal.total_amount,
      payment: payment ? {
        id: payment.mp_id,
        status: payment.local_status,
        mpStatus: payment.mp_status,
        amount: payment.amount,
        fee: payment.fee,
        method: payment.payment_method,
        paidAt: payment.paid_at,
      } : null,
      isPaid: proposal.status === 'paid',
      canPay: proposal.status === 'accepted',
    });
  } catch (err) {
    next(err);
  }
}

// ═══════════════════════════════════════════════════════════════
// POST /api/v1/public/proposals/:token/pay — Criar Pagamento Pix
// ═══════════════════════════════════════════════════════════════
// Cria uma preferência de pagamento no Mercado Pago com Pix.
// Retorna QR Code (imagem) + código copia-e-cola.
// ═══════════════════════════════════════════════════════════════
async function createPaymentPreference(req, res, next) {
  try {
    const { token } = req.params;
    const { name, email } = req.body;

    // ── Buscar proposta pelo token ────────────────────────
    const proposals = await query(
      `SELECT id, tenant_id, number, title, total_amount, status
       FROM proposals WHERE public_token = ?`,
      [token]
    );

    if (proposals.length === 0) {
      return res.status(404).json({
        error: 'ERR_NOT_FOUND',
        message: 'Proposta não encontrada',
        correlationId: req.correlationId,
      });
    }

    const proposal = proposals[0];

    // ── Validar status ─────────────────────────────────────
    if (proposal.status !== 'accepted') {
      return res.status(422).json({
        error: 'ERR_INVALID_STATUS',
        message: 'Esta proposta precisa ser aprovada antes do pagamento. Status atual: "' + proposal.status + '".',
        correlationId: req.correlationId,
      });
    }

    // ── Verificar se já existe transação paga ──────────────
    const existingTx = await query(
      `SELECT id FROM transactions WHERE proposal_id = ? AND status = 'completed' LIMIT 1`,
      [proposal.id]
    );

    if (existingTx.length > 0) {
      return res.status(422).json({
        error: 'ERR_ALREADY_PAID',
        message: 'Esta proposta já foi paga.',
        correlationId: req.correlationId,
      });
    }

    // ── Verificar disponibilidade do Mercado Pago ─────────
    const mercadopagoService = require('../../services/mercadopagoService');
    const { isMercadoPagoAvailable } = require('../../config/mercadopago');

    if (!isMercadoPagoAvailable()) {
      return res.status(503).json({
        error: 'ERR_MP_NOT_CONFIGURED',
        message: 'Pagamento via Pix indisponível no momento. Entre em contato com o profissional.',
        correlationId: req.correlationId,
      });
    }

    // ── Dados do pagador ───────────────────────────────────
    // Se não veio name/email no body, buscar do cliente da proposta
    let payerName = name;
    let payerEmail = email;

    if (!payerName || !payerEmail) {
      const clientInfo = await query(
        `SELECT c.name, c.email FROM clients c
         INNER JOIN proposals p ON p.client_id = c.id
         WHERE p.id = ?`,
        [proposal.id]
      );
      if (clientInfo.length > 0) {
        payerName = payerName || clientInfo[0].name;
        payerEmail = payerEmail || clientInfo[0].email;
      }
    }

    if (!payerName) payerName = 'Cliente';
    if (!payerEmail) {
      return res.status(400).json({
        error: 'ERR_VALIDATION',
        message: 'E-mail do pagador é obrigatório para gerar o pagamento Pix.',
        correlationId: req.correlationId,
      });
    }

    // ── External Reference para rastrear no webhook ───────
    const externalReference = `${proposal.tenant_id}-${proposal.id}`;

    // ── Criar preferência com Pix como único método ───────
    const notificationUrl = process.env.MP_NOTIFICATION_URL ||
      `${req.protocol}://${req.hostname}/api/v1/payments/webhook`;

    const preference = await mercadopagoService.createPreference({
      items: [{
        id: `PROP-${proposal.id}`,
        title: `${proposal.number} — ${proposal.title}`,
        description: `Pagamento da proposta ${proposal.number}`,
        quantity: 1,
        unit_price: parseFloat(proposal.total_amount),
      }],
      payer: {
        name: payerName,
        email: payerEmail,
      },
      externalReference,
      notificationUrl,
      metadata: {
        tenantId: proposal.tenant_id,
        proposalId: proposal.id,
        source: 'public_checkout',
      },
      // Forçar Pix como único método de pagamento
      paymentMethods: {
        excluded_payment_types: [
          { id: 'credit_card' },
          { id: 'debit_card' },
          { id: 'ticket' },
          { id: 'bank_transfer' },
        ],
        installments: 1,
      },
    });

    console.log(`[PublicProposal] 💳 Preferência MP criada: ${preference.id} (Proposta #${proposal.id})`);

    // ── Extrair dados Pix da preferência ───────────────────
    const pixData = preference.point_of_interaction?.transaction_data || {};

    res.status(201).json({
      message: 'Pagamento Pix iniciado com sucesso',
      data: {
        preferenceId: preference.id,
        proposalId: proposal.id,
        amount: parseFloat(proposal.total_amount),
        pix: {
          qrCodeBase64: pixData.qr_code_base64 || null,
          copyPaste: pixData.qr_code || null,
          transactionId: pixData.transaction_id || null,
        },
        expiresAt: null, // MP define expiração
      },
      correlationId: req.correlationId,
    });
  } catch (err) {
    next(err);
  }
}

module.exports = { getByToken, updateStatus, getPaymentStatus, createPaymentPreference };
