require('dotenv').config();

const { query } = require('../config/database');
const emailService = require('./email.service');

const APP_URL = process.env.APP_URL || 'http://localhost:8080';

// ── Notification Queue (evita bloquear responses) ─────────
const queue = [];
let processing = false;

async function processQueue() {
  if (processing) return;
  processing = true;
  while (queue.length > 0) {
    const job = queue.shift();
    try {
      await job.fn();
      console.log(`[COMM] ✅ ${job.type} enviado para ${job.target}`);
    } catch (err) {
      console.error(`[COMM] ❌ ${job.type} falhou para ${job.target}: ${err.message}`);
    }
  }
  processing = false;
}

function enqueue(type, target, fn) {
  queue.push({ type, target, fn });
  if (!processing) processQueue();
}

// ── Preference Checks ─────────────────────────────────────
async function getClientPrefs(clientId) {
  const rows = await query(
    'SELECT email, whatsapp, notify_email, notify_whatsapp, notify_telegram, telegram_chat_id FROM clients WHERE id = ?',
    [clientId]
  );
  return rows[0] || null;
}

async function getTenantPrefs(tenantId) {
  const rows = await query(
    'SELECT email, whatsapp, notify_email, notify_whatsapp, notify_telegram, telegram_chat_id FROM tenants WHERE id = ?',
    [tenantId]
  );
  return rows[0] || null;
}

// ── Email Notifications ───────────────────────────────────
async function notifyEmail({ to, subject, html, text, prefs }) {
  if (prefs && prefs.notify_email === 0) return { skipped: true, reason: 'email_disabled' };
  if (!to) return { skipped: true, reason: 'no_email' };
  return emailService.sendEmail({ to, subject, html, text });
}

// ── WhatsApp Notifications (via wa.me link) ───────────────
function getWhatsAppLink(phone, message) {
  if (!phone) return null;
  const digits = phone.replace(/\D/g, '');
  if (digits.length < 10) return null;
  const text = encodeURIComponent(message || '');
  return `https://wa.me/55${digits}?text=${text}`;
}

async function notifyWhatsApp({ phone, message, prefs }) {
  if (prefs && prefs.notify_whatsapp === 0) return { skipped: true, reason: 'whatsapp_disabled' };
  const link = getWhatsAppLink(phone, message);
  if (!link) return { skipped: true, reason: 'invalid_phone' };
  console.log(`[COMM] 📱 WhatsApp simulada para ${phone}: ${message}`);
  console.log(`[COMM]    Link: ${link}`);
  return { sent: true, channel: 'whatsapp', phone, link };
}

// ── Telegram Notifications ────────────────────────────────
async function notifyTelegram({ chatId, message, prefs }) {
  if (prefs && prefs.notify_telegram === 0) return { skipped: true, reason: 'telegram_disabled' };
  if (!chatId) return { skipped: true, reason: 'no_chat_id' };
  const token = process.env.TELEGRAM_BOT_TOKEN;
  if (!token) {
    console.log(`[COMM] 📱 Telegram simulada para chat ${chatId}: ${message}`);
    return { sent: true, channel: 'telegram_simulated', chatId };
  }
  try {
    const fetch = require('node-fetch');
    const res = await fetch(`https://api.telegram.org/bot${token}/sendMessage`, {
      method: 'POST', headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ chat_id: chatId, text: message, parse_mode: 'HTML' }),
    });
    return { sent: res.ok, channel: 'telegram', chatId };
  } catch (err) {
    console.error(`[COMM] Telegram error: ${err.message}`);
    return { sent: false, error: err.message };
  }
}

// ── Unified Notify — tenta todos os canais habilitados ────
async function notifyAll({ tenantId, clientId, subject, message, emailHtml, emailText }) {
  const results = { email: null, whatsapp: null, telegram: null };

  if (clientId) {
    const prefs = await getClientPrefs(clientId);
    if (prefs) {
      enqueue('email', prefs.email, () => notifyEmail({ to: prefs.email, subject, html: emailHtml, text: emailText, prefs }));
      enqueue('whatsapp', prefs.whatsapp, () => notifyWhatsApp({ phone: prefs.whatsapp, message, prefs }));
      enqueue('telegram', prefs.telegram_chat_id, () => notifyTelegram({ chatId: prefs.telegram_chat_id, message, prefs }));
    }
  }

  if (tenantId) {
    const prefs = await getTenantPrefs(tenantId);
    if (prefs) {
      enqueue('email', prefs.email, () => notifyEmail({ to: prefs.email, subject, html: emailHtml, text: emailText, prefs }));
      enqueue('whatsapp', prefs.whatsapp, () => notifyWhatsApp({ phone: prefs.whatsapp, message, prefs }));
    }
  }

  return results;
}

// ── Event-specific Notifications ──────────────────────────

async function onProposalStatusChange({ proposal, newStatus, tenantId, clientId }) {
  const statusMessages = {
    sent: { subject: 'Proposta Enviada — ServiceSaaS', msg: `Sua proposta #${proposal.number} foi enviada!` },
    accepted: { subject: 'Proposta Aprovada! 🎉', msg: `A proposta #${proposal.number} foi aprovada!` },
    rejected: { subject: 'Proposta Rejeitada', msg: `A proposta #${proposal.number} foi rejeitada.` },
    paid: { subject: 'Pagamento Confirmado! ✅', msg: `O pagamento da proposta #${proposal.number} foi confirmado!` },
  };
  const info = statusMessages[newStatus];
  if (!info) return;
  await notifyAll({
    clientId, tenantId, subject: info.subject, message: info.msg,
    emailHtml: `<p>${info.msg}</p><p><a href="${APP_URL}/?page=proposals">Ver proposta</a></p>`,
    emailText: info.msg,
  });
}

async function onLeadCreated({ lead, tenantId }) {
  const msg = `Novo lead: ${lead.customer_name} — ${lead.service_name}`;
  await notifyAll({
    tenantId, subject: 'Novo Lead — ServiceSaaS', message: msg,
    emailHtml: `<p>${msg}</p><p><a href="${APP_URL}/?page=leads">Ver lead</a></p>`,
    emailText: msg,
  });
}

async function onScheduleCreated({ schedule, tenantId, clientId }) {
  const msg = `Agendamento criado para ${schedule.scheduled_date}`;
  await notifyAll({
    clientId, tenantId, subject: 'Agendamento Confirmado', message: msg,
    emailHtml: `<p>${msg}</p>`,
    emailText: msg,
  });
}

module.exports = {
  notifyAll, notifyEmail, notifyWhatsApp, notifyTelegram, getWhatsAppLink,
  onProposalStatusChange, onLeadCreated, onScheduleCreated,
  getClientPrefs, getTenantPrefs,
};
