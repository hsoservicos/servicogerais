const providers = {
  brevo: () => { try { return require('./brevo.provider'); } catch (_) { return null; } },
  sendgrid: () => { try { return require('./sendgrid.provider'); } catch (_) { return null; } },
  log: () => require('./log.provider'),
};

function getProvider(name) {
  const providerName = name || process.env.EMAIL_PROVIDER || 'log';
  const factory = providers[providerName];
  if (!factory) {
    console.warn(`[EMAIL] Provider "${providerName}" não encontrado. Usando "log".`);
    return providers.log();
  }
  const provider = factory();
  if (!provider) {
    console.warn(`[EMAIL] Provider "${providerName}" não disponível (SDK ausente?). Usando "log".`);
    return providers.log();
  }
  return provider;
}

module.exports = { getProvider };
