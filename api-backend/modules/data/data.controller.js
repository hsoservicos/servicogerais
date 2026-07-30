const dataService = require('./data.service');

async function exportData(req, res, next) {
  try {
    const userId = req.user?.id || req.user?.sub;
    const tenantId = req.tenantId || req.user?.tenantId;

    if (!userId) {
      return res.status(401).json({ error: 'ERR_UNAUTHORIZED', message: 'Usuário não autenticado', correlationId: req.correlationId });
    }

    const data = await dataService.exportUserData(userId, tenantId);
    if (!data) {
      return res.status(404).json({ error: 'ERR_NOT_FOUND', message: 'Usuário não encontrado', correlationId: req.correlationId });
    }

    res.json({ data, correlationId: req.correlationId });
  } catch (err) {
    next(err);
  }
}

async function requestDeletion(req, res, next) {
  try {
    const userId = req.user?.id || req.user?.sub;
    const tenantId = req.tenantId || req.user?.tenantId;

    if (!userId) {
      return res.status(401).json({ error: 'ERR_UNAUTHORIZED', message: 'Usuário não autenticado', correlationId: req.correlationId });
    }

    const result = await dataService.requestDataDeletion(userId, tenantId);
    res.json({ ...result, correlationId: req.correlationId });
  } catch (err) {
    next(err);
  }
}

async function listConsents(req, res, next) {
  try {
    const userId = req.user?.id || req.user?.sub;
    if (!userId) {
      return res.status(401).json({ error: 'ERR_UNAUTHORIZED', message: 'Usuário não autenticado', correlationId: req.correlationId });
    }

    const consents = await dataService.getConsents(userId);
    res.json({ consents, correlationId: req.correlationId });
  } catch (err) {
    next(err);
  }
}

async function updateConsent(req, res, next) {
  try {
    const userId = req.user?.id || req.user?.sub;
    if (!userId) {
      return res.status(401).json({ error: 'ERR_UNAUTHORIZED', message: 'Usuário não autenticado', correlationId: req.correlationId });
    }

    const { consentType, granted } = req.body;
    const validTypes = ['opt-in', 'communications', 'terms'];

    if (!consentType || !validTypes.includes(consentType)) {
      return res.status(400).json({
        error: 'ERR_VALIDATION',
        message: `Tipo de consentimento inválido. Valores: ${validTypes.join(', ')}`,
        correlationId: req.correlationId,
      });
    }

    if (granted === undefined || granted === null) {
      return res.status(400).json({
        error: 'ERR_VALIDATION',
        message: 'Campo granted (boolean) é obrigatório',
        correlationId: req.correlationId,
      });
    }

    const ipAddress = req.ip || req.connection?.remoteAddress || 'unknown';
    const result = await dataService.setConsent(userId, consentType, granted, ipAddress);

    res.json({ message: granted ? 'Consentimento registrado!' : 'Consentimento revogado!', consent: result, correlationId: req.correlationId });
  } catch (err) {
    next(err);
  }
}

async function processDeletion(req, res, next) {
  try {
    const userId = req.user?.id || req.user?.sub;
    if (!userId) {
      return res.status(401).json({ error: 'ERR_UNAUTHORIZED', message: 'Usuário não autenticado', correlationId: req.correlationId });
    }
    const count = await dataService.processDeletionQueue();
    res.json({ message: `${count} deleções processadas.`, processed: count, correlationId: req.correlationId });
  } catch (err) {
    next(err);
  }
}

module.exports = { exportData, requestDeletion, processDeletion, listConsents, updateConsent };
