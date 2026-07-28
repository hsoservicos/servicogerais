// ═══════════════════════════════════════════════════════════════
// modules/payments/payments.controller.js — Mercado Pago Controller
// ═══════════════════════════════════════════════════════════════
// Story 5.1 — Integração com Mercado Pago via SDK v2.1.
// Handlers completos com validação HMAC e idempotência.
// ═══════════════════════════════════════════════════════════════

const { query } = require('../../config/database');
const mercadopagoService = require('../../services/mercadopagoService');
const { isMercadoPagoAvailable } = require('../../config/mercadopago');

// ═══════════════════════════════════════════════════════════════
// 1. Webhook IPN (Story 5.3)
// ═══════════════════════════════════════════════════════════════
// Recebe notificações do Mercado Pago sobre mudanças de status.
// ✅ Valida assinatura HMAC (x-signature) — NUNCA confiar no body.
// ✅ Idempotência via mp_notification_id UNIQUE.
// ✅ Atualiza status da transação + proposta.
// ═══════════════════════════════════════════════════════════════
async function handleWebhook(req, res, next) {
  try {
    // ── Validar assinatura HMAC (exceto em modo degradado/dev) ──
    const mpAvailable = isMercadoPagoAvailable();
    const webhookSecret = process.env.MP_WEBHOOK_SECRET;

    if (mpAvailable && webhookSecret) {
      // MP configurado — validar HMAC obrigatoriamente
      if (!mercadopagoService.validateWebhookSignature(req)) {
        console.warn('[MP] 🔴 Webhook rejeitado — assinatura HMAC inválida', {
          ip: req.ip,
          timestamp: new Date().toISOString(),
        });
        return res.status(401).json({ error: 'Invalid signature' });
      }
    } else {
      // Modo degradado/dev — aceitar webhook sem validação HMAC
      // A consulta à API MP também falhará, mas o fluxo de teste funciona
      console.info('[MP] ⚠️  Webhook recebido em modo degradado (sem HMAC)');
    }

    const { type, data } = req.body;

    // ── Só processamos notificações de pagamento ────────
    if (type !== 'payment') {
      console.log(`[MP] 📨 Webhook ignorado — tipo não suportado: ${type}`);
      return res.status(200).json({ received: true, ignored: true });
    }

    const paymentId = String(data.id);
    console.log(`[MP] 📨 Webhook recebido — Payment ID: ${paymentId}`);

    // ── Idempotência: verificar se já processamos este pagamento ──
    const existingTx = await query(
      'SELECT id, status FROM transactions WHERE mp_id = ?',
      [paymentId]
    );

    if (existingTx.length > 0) {
      const current = existingTx[0];
      // Se já está completed e recebe approved novamente, ignorar
      if (current.status === 'completed') {
        console.log(`[MP] ⏭️  Webhook ignorado — transação ${paymentId} já processada (${current.status})`);
        return res.status(200).json({ received: true, duplicate: true, status: current.status });
      }
    }

    // ── Buscar dados atualizados do MP (NUNCA confiar no body) ──
    const payment = await mercadopagoService.getPayment(paymentId);

    // ── Mapear status MP → interno ──────────────────────
    // chargeback não está no ENUM atual — tratamos como 'cancelled' localmente
    const STATUS_MAP = {
      approved: 'completed',
      in_process: 'pending',
      pending: 'pending',
      rejected: 'cancelled',
      refunded: 'refunded',
      cancelled: 'cancelled',
      chargeback: 'cancelled',
      // 'chargeback' é tratado como 'cancelled' no nosso ENUM
      // A distinção é feita via mp_status
    };

    const internalStatus = STATUS_MAP[payment.status] || 'pending';

    // ── Extrair dados financeiros ───────────────────────
    const amount = payment.transaction_amount || 0;
    const fee = payment.fee_details
      ? payment.fee_details.reduce((sum, f) => sum + (f.amount || 0), 0)
      : 0;
    const paymentMethod = payment.payment_method_id || null;

    // ── Extrair tenant_id e proposal_id do external_reference ──
    const extRef = payment.external_reference || '';
    const extParts = extRef.split('-');
    const webhookTenantId = extParts[0] ? parseInt(extParts[0], 10) : null;
    const webhookProposalId = extParts[1] ? parseInt(extParts[1], 10) : null;

    if (!webhookTenantId || !webhookProposalId) {
      console.warn(`[MP] ⚠️  Webhook ignorado — external_reference inválido (Payment ID: ${paymentId})`);
      return res.status(200).json({ received: true, skipped: true });
    }

    // ── Persistir transação com idempotência ────────────
    const paidAtStr = internalStatus === 'completed'
      ? new Date().toISOString().slice(0, 19).replace('T', ' ')
      : null;

    await query(
      `INSERT INTO transactions 
        (tenant_id, proposal_id, mp_id, mp_status,
         amount, fee, payment_method, status, paid_at)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
       ON DUPLICATE KEY UPDATE
        mp_status = VALUES(mp_status),
        status = VALUES(status),
        fee = VALUES(fee),
        paid_at = VALUES(paid_at),
        updated_at = CURRENT_TIMESTAMP`,
      [
        webhookTenantId,
        webhookProposalId,
        paymentId,
        payment.status,
        amount,
        fee,
        paymentMethod,
        internalStatus,
        paidAtStr,
      ]
    );

    console.log(`[MP] ✅ Transação ${paymentId} registrada — status MP: ${payment.status} → interno: ${internalStatus}`);

    // ── Atualizar proposta de acordo com o status ──────
    if (internalStatus === 'completed' && payment.status === 'approved') {
      // Pagamento aprovado → proposta paga
      await query(
        "UPDATE proposals SET status = 'paid', accepted_at = COALESCE(accepted_at, NOW()) WHERE id = ? AND tenant_id = ?",
        [webhookProposalId, webhookTenantId]
      );
      console.log(`[MP] ✅ Proposta #${webhookProposalId} → paga`);

    } else if (payment.status === 'chargeback' || payment.status === 'refunded') {
      // Chargeback ou estorno → proposta volta para 'accepted'
      await query(
        "UPDATE proposals SET status = 'accepted' WHERE id = ? AND tenant_id = ? AND status = 'paid'",
        [webhookProposalId, webhookTenantId]
      );
      console.log(`[MP] 🔄 Proposta #${webhookProposalId} → aceita (${payment.status})`);
    }
    // Rejeitado/cancelado: não altera status da proposta (fica 'accepted')

    res.status(200).json({ received: true });
  } catch (err) {
    next(err);
  }
}

// ═══════════════════════════════════════════════════════════════
// 2. Criar Preferência de Pagamento (Story 5.2)
// ═══════════════════════════════════════════════════════════════
async function createPreference(req, res, next) {
  try {
    const tenantId = req.tenantId;
    const { proposalId, items, payer, notificationUrl, metadata } = req.body;

    if (!proposalId || !items || !items.length || !payer?.email) {
      return res.status(400).json({
        error: 'ERR_VALIDATION',
        message: 'proposalId, items (array) e payer.email são obrigatórios',
      });
    }

    // External reference = "tenant_id-proposal_id" para rastreamento no webhook
    const externalReference = `${tenantId}-${proposalId}`;

    const preference = await mercadopagoService.createPreference({
      items,
      payer,
      externalReference,
      notificationUrl: notificationUrl || `${req.protocol}://${req.hostname}/api/v1/payments/webhook`,
      metadata: metadata || { tenantId, proposalId },
    });

    res.status(201).json({
      message: 'Preferência criada com sucesso',
      data: {
        id: preference.id,
        init_point: preference.init_point,
        sandbox_init_point: preference.sandbox_init_point,
        external_reference: externalReference,
      },
    });
  } catch (err) {
    next(err);
  }
}

// ═══════════════════════════════════════════════════════════════
// 3. Consultar Pagamento (Story 5.3)
// ═══════════════════════════════════════════════════════════════
async function getPayment(req, res, next) {
  try {
    const tenantId = req.tenantId;
    const { id } = req.params;

    if (!id) {
      return res.status(400).json({
        error: 'ERR_VALIDATION',
        message: 'ID do pagamento é obrigatório',
      });
    }

    // Buscar no banco primeiro (cache local)
    const rows = await query(
      'SELECT * FROM transactions WHERE mp_id = ? AND tenant_id = ?',
      [id, tenantId]
    );

    // Se encontrou localmente, retornar dados locais + consultar MP em background
    if (rows.length > 0) {
      const local = rows[0];

      // Tentar atualizar status via MP (assíncrono, não bloquear resposta)
      mercadopagoService.getPayment(id)
        .then(mpPayment => {
          if (mpPayment.status !== local.mp_status) {
            query(
              "UPDATE transactions SET mp_status = ?, status = ?, updated_at = NOW() WHERE mp_id = ?",
              [mpPayment.status, mpPayment.status === 'approved' ? 'completed' : 'pending', id]
            );
          }
        })
        .catch(err => {
          if (process.env.NODE_ENV !== 'production') {
            console.warn('[MP] ⚠️  Background refresh falhou (não crítico):', err.message);
          }
        });

      return res.json({
        message: 'Pagamento encontrado',
        data: {
          id: local.mp_id,
          status: local.status,
          mp_status: local.mp_status,
          amount: local.amount,
          fee: local.fee,
          net_amount: local.net_amount,
          payment_method: local.payment_method,
          paid_at: local.paid_at,
        },
      });
    }

    // Não encontrou localmente — consultar MP diretamente
    // (usado durante o fluxo de pagamento antes do webhook chegar)
    const payment = await mercadopagoService.getPayment(id);

    res.json({
      message: 'Pagamento consultado via Mercado Pago',
      data: {
        id: payment.id,
        status: payment.status,
        amount: payment.transaction_amount,
        payment_method: payment.payment_method_id,
        pix_qr_code: payment.point_of_interaction?.transaction_data?.qr_code || null,
      },
    });
  } catch (err) {
    next(err);
  }
}

// ═══════════════════════════════════════════════════════════════
// 4. Estornar Pagamento (Story 5.5)
// ═══════════════════════════════════════════════════════════════
async function refundPayment(req, res, next) {
  try {
    const tenantId = req.tenantId;
    const { id } = req.params;
    const { amount } = req.body; // null = estorno total

    // Verificar se a transação existe e pertence ao tenant
    const rows = await query(
      'SELECT * FROM transactions WHERE mp_id = ? AND tenant_id = ? AND status = ?',
      [id, tenantId, 'completed']
    );

    if (rows.length === 0) {
      return res.status(404).json({
        error: 'ERR_NOT_FOUND',
        message: 'Transação não encontrada ou não está concluída',
      });
    }

    const transaction = rows[0];

    // Processar estorno via MP
    const refund = await mercadopagoService.refundPayment(id, amount || null);

    // Atualizar transação local
    await query(
      "UPDATE transactions SET status = 'refunded', updated_at = NOW() WHERE mp_id = ?",
      [id]
    );

    // Atualizar proposta de volta para 'accepted'
    await query(
      "UPDATE proposals SET status = 'accepted' WHERE id = ?",
      [transaction.proposal_id]
    );

    console.log(`[MP] ✅ Estorno processado — Payment ID: ${id}, Valor: ${amount || 'total'}`);

    res.json({
      message: amount ? 'Estorno parcial processado' : 'Estorno total processado',
      data: {
        id: refund.id,
        amount: refund.amount,
        date: refund.date_created,
      },
    });
  } catch (err) {
    next(err);
  }
}

module.exports = {
  handleWebhook,
  createPreference,
  getPayment,
  refundPayment,
};
