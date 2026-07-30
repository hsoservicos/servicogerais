async function send({ to, subject, html, text }) {
  const apiKey = process.env.BREVO_API_KEY;
  if (!apiKey) throw new Error('BREVO_API_KEY não configurada');

  const fetch = require('node-fetch');
  const response = await fetch('https://api.brevo.com/v3/smtp/email', {
    method: 'POST',
    headers: {
      'api-key': apiKey,
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
    body: JSON.stringify({
      sender: {
        name: process.env.BREVO_FROM_NAME || 'ServiceSaaS',
        email: process.env.BREVO_FROM_EMAIL || 'noreply@servicesaas.com.br',
      },
      to: [{ email: to }],
      subject,
      htmlContent: html,
      textContent: text,
    }),
  });

  if (!response.ok) {
    const err = await response.text();
    throw new Error(`Brevo API error ${response.status}: ${err}`);
  }

  return { sent: true, provider: 'brevo', to };
}

module.exports = { send };
