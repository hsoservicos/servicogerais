const domesticService = require('./domestic.service');

async function calculateCosts(req, res, next) {
  try {
    const { salary, transportVoucher, foodAllowance, regime = 'LC_150_CLT' } = req.body;
    if (!salary || salary <= 0) {
      return res.status(422).json({
        error: 'ERR_VALIDATION',
        message: 'Salário deve ser maior que zero',
        correlationId: req.correlationId,
      });
    }
    const result = await domesticService.calculateCosts({ salary, transportVoucher, foodAllowance, regime });
    res.json({ ...result, correlationId: req.correlationId });
  } catch (err) {
    if (err.statusCode) return res.status(err.statusCode).json({ error: 'ERR_VALIDATION', message: err.message, correlationId: req.correlationId });
    next(err);
  }
}

async function transitionToCLT(req, res, next) {
  try {
    const { id } = req.params;
    const tenantFilter = req.tenantFilter || '1=1';
    const { salary, startDate, transportVoucher, foodAllowance, weeklyFrequencyDays } = req.body;

    const errors = [];
    if (!salary || salary <= 0) errors.push('Salário deve ser maior que zero');
    if (!startDate) errors.push('Data de início é obrigatória');
    if (errors.length > 0) {
      return res.status(422).json({
        error: 'ERR_VALIDATION',
        message: errors.join('; '),
        correlationId: req.correlationId,
      });
    }

    const result = await domesticService.transitionToCLT(id, tenantFilter, { salary, startDate, transportVoucher, foodAllowance, weeklyFrequencyDays });
    res.status(201).json({
      message: 'Transição para CLT realizada com sucesso!',
      ...result,
      correlationId: req.correlationId,
    });
  } catch (err) {
    if (err.statusCode) return res.status(err.statusCode).json({ error: err.statusCode === 409 ? 'ERR_CONFLICT' : 'ERR_VALIDATION', message: err.message, correlationId: req.correlationId });
    next(err);
  }
}

module.exports = { calculateCosts, transitionToCLT };