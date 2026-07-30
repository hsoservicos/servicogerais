async function send({ to, subject, html, text }) {
  const sgMail = require('@sendgrid/mail');
  sgMail.setApiKey(process.env.SENDGRID_API_KEY);
  await sgMail.send({
    to, from: process.env.SENDGRID_FROM_EMAIL || 'noreply@servicesaas.com',
    subject, html, text,
  });
  return { sent: true, provider: 'sendgrid', to };
}

module.exports = { send };
