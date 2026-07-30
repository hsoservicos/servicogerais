const { query } = require('../../config/database');
const comm = require('../../services/communication.service');

async function getPreferences(req, res, next) {
  try {
    const tenantId = req.tenantId;
    const prefs = await comm.getTenantPrefs(tenantId);
    if (!prefs) return res.status(404).json({ error: 'ERR_NOT_FOUND', message: 'Tenant não encontrado', correlationId: req.correlationId });
    res.json({ preferences: {
      email: prefs.email,
      whatsapp: prefs.whatsapp,
      notifyEmail: !!prefs.notify_email,
      notifyWhatsapp: !!prefs.notify_whatsapp,
      notifyTelegram: !!prefs.notify_telegram,
      telegramChatId: prefs.telegram_chat_id,
    }, correlationId: req.correlationId });
  } catch (err) { next(err); }
}

async function updatePreferences(req, res, next) {
  try {
    const tenantId = req.tenantId;
    const { notifyEmail, notifyWhatsapp, notifyTelegram, telegramChatId } = req.body;
    const sets = []; const params = [];
    if (notifyEmail !== undefined) { sets.push('notify_email = ?'); params.push(notifyEmail ? 1 : 0); }
    if (notifyWhatsapp !== undefined) { sets.push('notify_whatsapp = ?'); params.push(notifyWhatsapp ? 1 : 0); }
    if (notifyTelegram !== undefined) { sets.push('notify_telegram = ?'); params.push(notifyTelegram ? 1 : 0); }
    if (telegramChatId !== undefined) { sets.push('telegram_chat_id = ?'); params.push(telegramChatId || null); }
    if (sets.length === 0) return res.status(400).json({ error: 'ERR_VALIDATION', message: 'Nenhum campo para atualizar', correlationId: req.correlationId });
    params.push(tenantId);
    await query(`UPDATE tenants SET ${sets.join(', ')} WHERE id = ?`, params);
    res.json({ message: 'Preferências atualizadas!', correlationId: req.correlationId });
  } catch (err) { next(err); }
}

async function getClientPrefs(req, res, next) {
  try {
    const { clientId } = req.params;
    const tenantFilter = req.tenantFilter || '1=1';
    const rows = await query(`SELECT id, name, email, whatsapp, notify_email, notify_whatsapp, notify_telegram, telegram_chat_id FROM clients WHERE id = ? AND ${tenantFilter}`, [clientId]);
    if (rows.length === 0) return res.status(404).json({ error: 'ERR_NOT_FOUND', message: 'Cliente não encontrado', correlationId: req.correlationId });
    const c = rows[0];
    res.json({ preferences: { clientId: c.id, name: c.name, email: c.email, whatsapp: c.whatsapp, notifyEmail: !!c.notify_email, notifyWhatsapp: !!c.notify_whatsapp, notifyTelegram: !!c.notify_telegram, telegramChatId: c.telegram_chat_id }, correlationId: req.correlationId });
  } catch (err) { next(err); }
}

async function updateClientPrefs(req, res, next) {
  try {
    const { clientId } = req.params;
    const tenantFilter = req.tenantFilter || '1=1';
    const { notifyEmail, notifyWhatsapp, notifyTelegram, telegramChatId } = req.body;
    const existing = await query(`SELECT id FROM clients WHERE id = ? AND ${tenantFilter}`, [clientId]);
    if (existing.length === 0) return res.status(404).json({ error: 'ERR_NOT_FOUND', message: 'Cliente não encontrado', correlationId: req.correlationId });
    const sets = []; const params = [];
    if (notifyEmail !== undefined) { sets.push('notify_email = ?'); params.push(notifyEmail ? 1 : 0); }
    if (notifyWhatsapp !== undefined) { sets.push('notify_whatsapp = ?'); params.push(notifyWhatsapp ? 1 : 0); }
    if (notifyTelegram !== undefined) { sets.push('notify_telegram = ?'); params.push(notifyTelegram ? 1 : 0); }
    if (telegramChatId !== undefined) { sets.push('telegram_chat_id = ?'); params.push(telegramChatId || null); }
    if (sets.length === 0) return res.status(400).json({ error: 'ERR_VALIDATION', message: 'Nenhum campo', correlationId: req.correlationId });
    params.push(clientId);
    await query(`UPDATE clients SET ${sets.join(', ')} WHERE id = ? AND ${tenantFilter}`, params);
    res.json({ message: 'Preferências do cliente atualizadas!', correlationId: req.correlationId });
  } catch (err) { next(err); }
}

async function adminSendNotification(req, res, next) {
  try {
    const { tenantId, subject, message } = req.body;
    if (!tenantId || !subject || !message) return res.status(400).json({ error: 'ERR_VALIDATION', message: 'tenantId, subject e message são obrigatórios', correlationId: req.correlationId });
    const result = await comm.notifyAll({ tenantId, subject, message, emailHtml: `<p>${message}</p>`, emailText: message });
    res.json({ message: 'Notificação enviada!', result, correlationId: req.correlationId });
  } catch (err) { next(err); }
}

module.exports = { getPreferences, updatePreferences, getClientPrefs, updateClientPrefs, adminSendNotification };
