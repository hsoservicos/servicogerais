const service = require('./tenants.service');

async function getProfile(req, res, next) {
  try {
    const tenant = await service.findById(req.tenantId);
    if (!tenant) {
      return res.status(404).json({
        error: 'ERR_NOT_FOUND',
        message: 'Perfil não encontrado',
        correlationId: req.correlationId,
      });
    }
    res.json({ tenant, correlationId: req.correlationId });
  } catch (err) {
    next(err);
  }
}

async function updateProfile(req, res, next) {
  try {
    const tenant = await service.findById(req.tenantId);
    if (!tenant) {
      return res.status(404).json({
        error: 'ERR_NOT_FOUND',
        message: 'Perfil não encontrado',
        correlationId: req.correlationId,
      });
    }

    const updated = await service.update(req.tenantId, req.body);
    if (!updated) {
      return res.status(400).json({
        error: 'ERR_VALIDATION',
        message: 'Nenhum campo válido para atualização',
        correlationId: req.correlationId,
      });
    }

    res.json({
      message: 'Perfil atualizado com sucesso!',
      tenant: updated,
      correlationId: req.correlationId,
    });
  } catch (err) {
    next(err);
  }
}

module.exports = { getProfile, updateProfile };