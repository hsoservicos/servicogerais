const { query, transaction } = require('../../config/database');

async function calculateCosts({ salary, transportVoucher, foodAllowance, regime }) {
  if (!salary || salary <= 0) {
    throw Object.assign(new Error('Salário deve ser maior que zero'), { statusCode: 400 });
  }

  const isCLT = regime === 'LC_150_CLT';
  const monthlySalary = parseFloat(salary);

  let inssEmployer = 0;
  let fgts = 0;
  let thirteenth = 0;
  let vacation = 0;
  let fgtsOnThirteenth = 0;
  let fgtsOnVacation = 0;
  let inssOnThirteenth = 0;
  let annualTotal = 0;

  if (isCLT) {
    const inssRate = monthlySalary <= 1412.00 ? 0.075
      : monthlySalary <= 2666.68 ? 0.09
      : monthlySalary <= 4000.03 ? 0.12
      : 0.14;
    inssEmployer = monthlySalary * inssRate;

    fgts = monthlySalary * 0.08;
    thirteenth = monthlySalary / 12;
    vacation = (monthlySalary / 12) + ((monthlySalary / 12) / 3);
    fgtsOnThirteenth = thirteenth * 0.08;
    fgtsOnVacation = vacation * 0.08;
    inssOnThirteenth = thirteenth * inssRate;

    annualTotal = (monthlySalary * 12)
      + (inssEmployer * 12)
      + (fgts * 12)
      + thirteenth
      + vacation
      + fgtsOnThirteenth
      + fgtsOnVacation
      + inssOnThirteenth
      + (parseFloat(transportVoucher || 0) * 12)
      + (parseFloat(foodAllowance || 0) * 12);
  }

  const monthlyTotal = isCLT
    ? monthlySalary + inssEmployer + fgts + (parseFloat(transportVoucher || 0)) + (parseFloat(foodAllowance || 0))
    : monthlySalary;

  return {
    regime,
    monthlySalary,
    inssEmployer: round(inssEmployer),
    fgts: round(fgts),
    thirteenth: round(thirteenth),
    vacation: round(vacation),
    fgtsOnThirteenth: round(fgtsOnThirteenth),
    fgtsOnVacation: round(fgtsOnVacation),
    inssOnThirteenth: round(inssOnThirteenth),
    transportVoucher: parseFloat(transportVoucher || 0),
    foodAllowance: parseFloat(foodAllowance || 0),
    monthlyTotal: round(monthlyTotal),
    annualTotal: round(annualTotal),
    annualSalary: round(monthlySalary * 12),
  };
}

async function transitionToCLT(workerId, tenantFilter, { salary, startDate, transportVoucher, foodAllowance, weeklyFrequencyDays }) {
  const worker = await query(
    'SELECT id, name, cpf, worker_category FROM workers WHERE id = ? AND ?',
    [workerId, tenantFilter.replace('1=1', '1')]
  );
  if (worker.length === 0) throw Object.assign(new Error('Trabalhador não encontrado'), { statusCode: 404 });

  const existing = await query(
    'SELECT id FROM domestic_agreements WHERE worker_id = ? AND status = \'ACTIVE\'',
    [workerId]
  );
  if (existing.length > 0) throw Object.assign(new Error('Trabalhador já possui contrato CLT ativo'), { statusCode: 409 });

  const result = await transaction(async (conn) => {
    const insert = await conn.query(
      `INSERT INTO domestic_agreements
       (worker_id, tenant_id, contract_type, regime, status, salary, transport_voucher, food_allowance, weekly_frequency_days, start_date)
       VALUES (?, ?, 'CLT_DOMESTICO', 'LC_150_CLT', 'ACTIVE', ?, ?, ?, ?, ?)`,
      [workerId, worker[0].tenant_id, salary, transportVoucher || 0, foodAllowance || 0, weeklyFrequencyDays || 5, startDate]
    );

    await conn.query(
      'UPDATE workers SET updated_at = NOW() WHERE id = ?',
      [workerId]
    );

    const costs = await calculateCosts({ salary, transportVoucher, foodAllowance, regime: 'LC_150_CLT' });

    return { agreementId: insert.insertId, costs };
  });

  return result;
}

function round(v) {
  return Math.round(v * 100) / 100;
}

module.exports = { calculateCosts, transitionToCLT };