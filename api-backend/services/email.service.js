// ═══════════════════════════════════════════════════════════════
// services/email.service.js — Email Service (Stub MVP)
// ═══════════════════════════════════════════════════════════════
// MVP: Apenas console.log. Em produção: SendGrid, SES, etc.
// ═══════════════════════════════════════════════════════════════

/**
 * Envia e-mail de recuperação de senha
 * @param {Object} params
 * @param {string} params.to - E-mail do destinatário
 * @param {string} params.name - Nome do usuário
 * @param {string} params.token - Token de reset (UUID)
 */
async function sendResetPasswordEmail({ to, name, token }) {
  const resetLink = `http://localhost:8080/?page=reset-password&token=${token}`;

  // ── MVP: apenas log no console ─────────────────────────
  console.log('');
  console.log('═══════════════════════════════════════════════════════');
  console.log('  📧 EMAIL SERVICE (MVP STUB)');
  console.log('═══════════════════════════════════════════════════════');
  console.log(`  To:      ${to}`);
  console.log(`  Name:    ${name}`);
  console.log(`  Subject: Recuperação de Senha — ServiceSaaS`);
  console.log('─────────────────────────────────────────────────────');
  console.log(`  Olá ${name},`);
  console.log('');
  console.log(`  Recebemos uma solicitação de recuperação de senha`);
  console.log(`  para sua conta no ServiceSaaS.`);
  console.log('');
  console.log(`  👉 ${resetLink}`);
  console.log('');
  console.log(`  Este link expira em 1 hora.`);
  console.log(`  Se você não solicitou esta recuperação, ignore`);
  console.log(`  este e-mail.`);
  console.log('─────────────────────────────────────────────────────');
  console.log('  ServiceSaaS — Sua plataforma de serviços');
  console.log('═══════════════════════════════════════════════════════');
  console.log('');

  return { sent: true, to, resetLink };
}

module.exports = { sendResetPasswordEmail };
