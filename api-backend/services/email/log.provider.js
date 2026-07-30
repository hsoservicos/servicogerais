async function send({ to, subject, html, text }) {
  console.log('\n═══════════════════════════════════════════════════════');
  console.log('  📧 EMAIL (LOG) — To: ' + to);
  console.log('  Subject: ' + subject);
  if (text) { console.log('─────────────────────────────────────────────────────'); console.log(text); }
  console.log('═══════════════════════════════════════════════════════\n');
  return { sent: true, provider: 'log', to };
}

module.exports = { send };
