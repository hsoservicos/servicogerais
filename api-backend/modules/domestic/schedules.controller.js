const scheduleService = require('./schedules.service');
const comm = require('../../services/communication.service');

async function list(req, res, next) {
  try {
    const tenantFilter = req.tenantFilter || '1=1';
    const { workerId, clientId, dateFrom, dateTo, status, page, perPage } = req.query;

    const result = await scheduleService.list({
      tenantFilter, workerId, clientId, dateFrom, dateTo, status, page, perPage,
    });

    res.json({ ...result, correlationId: req.correlationId });
  } catch (err) {
    next(err);
  }
}

async function read(req, res, next) {
  try {
    const { id } = req.params;
    const tenantFilter = req.tenantFilter || '1=1';

    const schedule = await scheduleService.findById(id, tenantFilter);

    if (!schedule) {
      return res.status(404).json({
        error: 'ERR_NOT_FOUND',
        message: 'Agendamento não encontrado',
        correlationId: req.correlationId,
      });
    }

    res.json({ schedule, correlationId: req.correlationId });
  } catch (err) {
    next(err);
  }
}

async function create(req, res, next) {
  try {
    const tenantId = req.tenantId || req.user?.tenantId;

    if (!tenantId) {
      return res.status(403).json({
        error: 'ERR_TENANT_REQUIRED',
        message: 'Tenant não identificado',
        correlationId: req.correlationId,
      });
    }

    const tenantFilter = req.tenantFilter || '1=1';

    const errors = [];
    const {
      workerId, clientId, serviceCategory, regime,
      scheduledDate, startTime, endTime,
      hourlyRate, totalAmount, transportVoucher, notes,
    } = req.body;

    if (!workerId) errors.push('Trabalhador é obrigatório');
    if (!clientId) errors.push('Cliente é obrigatório');
    if (!scheduledDate) errors.push('Data do agendamento é obrigatória');
    if (!serviceCategory) errors.push('Categoria do serviço é obrigatória');
    if (!regime) errors.push('Regime é obrigatório');

    if (errors.length > 0) {
      return res.status(422).json({
        error: 'ERR_VALIDATION',
        message: errors.join('; '),
        correlationId: req.correlationId,
      });
    }

    const frequencyCheck = await scheduleService.checkFrequencyLimit(workerId, clientId, scheduledDate);

    if (!frequencyCheck.allowed) {
      if (frequencyCheck.reason === 'FREQUENCY_LIMIT') {
        return res.status(429).json({
          error: 'ERR_FREQUENCY_LIMIT',
          message: frequencyCheck.message,
          details: {
            currentCount: frequencyCheck.currentCount,
            maxAllowed: frequencyCheck.maxAllowed,
            transitionUrl: frequencyCheck.transitionUrl,
          },
          correlationId: req.correlationId,
        });
      }
      if (frequencyCheck.reason === 'WORKER_NOT_FOUND') {
        return res.status(404).json({
          error: 'ERR_NOT_FOUND',
          message: 'Trabalhador não encontrado ou inativo',
          correlationId: req.correlationId,
        });
      }
    }

    const schedule = await scheduleService.create({
      tenantId, workerId, clientId, serviceCategory, regime,
      scheduledDate, startTime, endTime,
      hourlyRate, totalAmount, transportVoucher, notes,
    });

    const alertMessage = frequencyCheck.currentCount === 1
      ? 'Atenção: Esta é a segunda diária desta diarista na semana. O limite é 2 dias/semana.'
      : null;

    comm.onScheduleCreated({
      schedule: { scheduled_date: scheduledDate },
      tenantId,
      clientId,
    }).catch(() => {});

    res.status(201).json({
      message: 'Agendamento criado com sucesso!',
      schedule,
      alert: alertMessage,
      frequency: { currentCount: frequencyCheck.currentCount, maxAllowed: frequencyCheck.maxAllowed },
      correlationId: req.correlationId,
    });
  } catch (err) {
    next(err);
  }
}

async function update(req, res, next) {
  try {
    const { id } = req.params;
    const tenantFilter = req.tenantFilter || '1=1';

    const {
      scheduledDate, startTime, endTime,
      hourlyRate, totalAmount, transportVoucher, notes,
    } = req.body;

    await scheduleService.update(id, tenantFilter, {
      scheduledDate, startTime, endTime,
      hourlyRate, totalAmount, transportVoucher, notes,
    });

    res.json({
      message: 'Agendamento atualizado com sucesso!',
      correlationId: req.correlationId,
    });
  } catch (err) {
    if (err.statusCode === 404) {
      return res.status(404).json({
        error: 'ERR_NOT_FOUND',
        message: 'Agendamento não encontrado',
        correlationId: req.correlationId,
      });
    }
    next(err);
  }
}

async function updateStatus(req, res, next) {
  try {
    const { id } = req.params;
    const tenantFilter = req.tenantFilter || '1=1';
    const { status } = req.body;

    const validStatuses = ['scheduled', 'confirmed', 'in_progress', 'completed', 'cancelled'];
    if (!status || !validStatuses.includes(status)) {
      return res.status(400).json({
        error: 'ERR_VALIDATION',
        message: `Status inválido. Valores: ${validStatuses.join(', ')}`,
        correlationId: req.correlationId,
      });
    }

    await scheduleService.updateStatus(id, tenantFilter, status);

    res.json({
      message: 'Status do agendamento atualizado!',
      correlationId: req.correlationId,
    });
  } catch (err) {
    if (err.statusCode === 404) {
      return res.status(404).json({
        error: 'ERR_NOT_FOUND',
        message: 'Agendamento não encontrado',
        correlationId: req.correlationId,
      });
    }
    next(err);
  }
}

async function remove(req, res, next) {
  try {
    const { id } = req.params;
    const tenantFilter = req.tenantFilter || '1=1';

    const deleted = await scheduleService.remove(id, tenantFilter);

    if (!deleted) {
      return res.status(404).json({
        error: 'ERR_NOT_FOUND',
        message: 'Agendamento não encontrado',
        correlationId: req.correlationId,
      });
    }

    res.json({
      message: 'Agendamento excluído com sucesso!',
      correlationId: req.correlationId,
    });
  } catch (err) {
    next(err);
  }
}

module.exports = { list, read, create, update, updateStatus, remove };
