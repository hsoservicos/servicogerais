require('dotenv').config();

const EMAIL_PROVIDER = process.env.EMAIL_PROVIDER || 'log';

async function sendEmail({ to, subject, html, text }) {
  if (EMAIL_PROVIDER === 'sendgrid') {
    try {
      const sgMail = require('@sendgrid/mail');
      sgMail.setApiKey(process.env.SENDGRID_API_KEY);
      await sgMail.send({
        to,
        from: process.env.SENDGRID_FROM_EMAIL || 'noreply@servicesaas.com',
        subject,
        html,
        text,
      });
      return { sent: true, provider: 'sendgrid', to };
    } catch (err) {
      console.error('[EMAIL] SendGrid error:', err.message);
      return { sent: false, error: err.message };
    }
  }

  console.log('');
  console.log('═══════════════════════════════════════════════════════');
  console.log('  📧 EMAIL SERVICE (' + EMAIL_PROVIDER + ')');
  console.log('═══════════════════════════════════════════════════════');
  console.log('  To:      ' + to);
  console.log('  Subject: ' + subject);
  console.log('─────────────────────────────────────────────────────');
  if (text) console.log('  ' + text);
  console.log('═══════════════════════════════════════════════════════');
  console.log('');

  return { sent: true, provider: 'log', to };
}

async function sendResetPasswordEmail({ to, name, token }) {
  const resetLink = process.env.APP_URL
    + '/?page=reset-password&token=' + token;
  const subject = 'Recuperação de Senha — ServiceSaaS';
  const text = 'Olá ' + name + ',\n\n'
    + 'Recebemos uma solicitação de recuperação de senha.\n\n'
    + 'Link: ' + resetLink + '\n\n'
    + 'Este link expira em 1 hora.\n'
    + 'Se você não solicitou, ignore este e-mail.';
  const html = '<p>Olá <strong>' + name + '</strong>,</p>'
    + '<p>Recebemos uma solicitação de recuperação de senha.</p>'
    + '<p><a href="' + resetLink + '" style="padding:12px 24px;background:#10B981;color:#fff;text-decoration:none;border-radius:6px;">Redefinir Senha</a></p>'
    + '<p>Este link expira em <strong>1 hora</strong>.</p>';

  return sendEmail({ to, subject, html, text });
}

module.exports = { sendEmail, sendResetPasswordEmail };
