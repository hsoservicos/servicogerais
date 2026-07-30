require('dotenv').config();
const { getProvider } = require('./email/provider');

function buildTemplate(title, bodyHtml) {
  return '<div style="max-width:600px;margin:0 auto;font-family:Arial,sans-serif;">'
    + '<div style="background:#10B981;padding:24px;text-align:center;border-radius:8px 8px 0 0;">'
    + '<h1 style="color:#fff;margin:0;font-size:20px;">ServiceSaaS</h1></div>'
    + '<div style="background:#fff;padding:32px;border:1px solid #e5e7eb;border-top:0;">'
    + '<h2 style="margin:0 0 16px;font-size:18px;color:#1f2937;">' + title + '</h2>'
    + bodyHtml
    + '<hr style="border:none;border-top:1px solid #e5e7eb;margin:24px 0;">'
    + '<p style="color:#9ca3af;font-size:12px;">ServiceSaaS — Sua plataforma de serviços</p></div></div>';
}

async function sendEmail({ to, subject, html, text }) {
  const provider = getProvider();
  try {
    return await provider.send({ to, subject, html, text });
  } catch (err) {
    console.error('[EMAIL] Provider error:', err.message);
    const logProvider = getProvider('log');
    return logProvider.send({ to, subject, html, text });
  }
}

async function sendResetPasswordEmail({ to, name, token }) {
  const resetLink = (process.env.APP_URL || 'http://localhost:8080') + '/?page=reset-password&token=' + token;
  const html = buildTemplate('Recuperação de Senha',
    '<p>Olá <strong>' + name + '</strong>,</p><p>Recebemos uma solicitação de recuperação de senha.</p>'
    + '<div style="text-align:center;margin:24px 0;"><a href="' + resetLink + '" style="display:inline-block;padding:12px 32px;background:#10B981;color:#fff;text-decoration:none;border-radius:6px;font-weight:bold;">Redefinir Senha</a></div>'
    + '<p style="color:#6b7280;font-size:13px;">Este link expira em <strong>1 hora</strong>.<br>Se você não solicitou esta recuperação, ignore este e-mail.</p>');
  const text = 'Olá ' + name + ',\n\nRecebemos uma solicitação de recuperação de senha.\n\nLink: ' + resetLink + '\n\nEste link expira em 1 hora.';
  return sendEmail({ to, subject: 'Recuperação de Senha — ServiceSaaS', html, text });
}

async function sendWelcomeEmail({ to, name }) {
  const html = buildTemplate('Bem-vindo ao ServiceSaaS!',
    '<p>Olá <strong>' + name + '</strong>,</p><p>Sua conta foi criada com sucesso!</p>'
    + '<p>Agora você pode gerenciar clientes, criar propostas profissionais e muito mais.</p>'
    + '<div style="text-align:center;margin:24px 0;"><a href="' + (process.env.APP_URL || 'http://localhost:8080') + '/?page=dashboard" style="display:inline-block;padding:12px 32px;background:#10B981;color:#fff;text-decoration:none;border-radius:6px;font-weight:bold;">Acessar Dashboard</a></div>');
  return sendEmail({ to, subject: 'Bem-vindo ao ServiceSaaS!', html });
}

async function sendLeadNotificationEmail({ to, tenantName, leadName, serviceName }) {
  const html = buildTemplate('Novo Lead Recebido!',
    '<p>Olá <strong>' + tenantName + '</strong>,</p><p>Você recebeu um novo lead!</p>'
    + '<div style="background:#f9fafb;padding:16px;border-radius:6px;margin:16px 0;">'
    + '<p><strong>Cliente:</strong> ' + leadName + '</p>'
    + '<p><strong>Serviço:</strong> ' + serviceName + '</p></div>'
    + '<div style="text-align:center;"><a href="' + (process.env.APP_URL || 'http://localhost:8080') + '/?page=leads" style="display:inline-block;padding:12px 32px;background:#10B981;color:#fff;text-decoration:none;border-radius:6px;font-weight:bold;">Ver Lead</a></div>');
  return sendEmail({ to, subject: 'Novo Lead — ServiceSaaS', html });
}

async function sendPaymentConfirmationEmail({ to, name, proposalNumber, amount }) {
  const html = buildTemplate('Pagamento Confirmado!',
    '<p>Olá <strong>' + name + '</strong>,</p><p>O pagamento da proposta <strong>' + proposalNumber + '</strong> foi confirmado!</p>'
    + '<div style="background:#f0fdf4;padding:16px;border-radius:6px;margin:16px 0;text-align:center;">'
    + '<p style="font-size:24px;font-weight:bold;color:#10B981;margin:0;">' + amount + '</p></div>');
  return sendEmail({ to, subject: 'Pagamento Confirmado — ServiceSaaS', html });
}

module.exports = { sendEmail, sendResetPasswordEmail, sendWelcomeEmail, sendLeadNotificationEmail, sendPaymentConfirmationEmail };
