const { query } = require('../../config/database');

function formatCurrency(value) {
  const num = parseFloat(value) || 0;
  return num.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
}

async function list(req, res, next) {
  try {
    const { active, page = 1, perPage = 50 } = req.query;
    const offset = (parseInt(page, 10) - 1) * parseInt(perPage, 10);
    const limit = parseInt(perPage, 10);

    let whereClause = '1=1';
    const params = [];
    if (active === 'true') { whereClause += ' AND active = TRUE'; }
    else if (active === 'false') { whereClause += ' AND active = FALSE'; }

    const [countRow] = await query(
      `SELECT COUNT(*) as total FROM plans WHERE ${whereClause}`,
      params.length > 0 ? params : undefined
    );

    const plans = await query(
      `SELECT * FROM plans WHERE ${whereClause}
       ORDER BY sort_order ASC, name ASC
       LIMIT ? OFFSET ?`,
      params.length > 0 ? [...params, limit, offset] : [limit, offset]
    );

    res.json({
      plans: plans.map(p => ({
        id: p.id,
        slug: p.slug,
        name: p.name,
        description: p.description,
        price: formatCurrency(p.price),
        price_raw: parseFloat(p.price),
        limits: typeof p.limits === 'string' ? JSON.parse(p.limits) : p.limits,
        features: typeof p.features === 'string' ? JSON.parse(p.features) : p.features,
        active: p.active,
        sort_order: p.sort_order,
        created_at: p.created_at,
        updated_at: p.updated_at,
      })),
      pagination: {
        page: parseInt(page, 10), perPage: limit,
        total: countRow?.total || 0,
        totalPages: Math.ceil((countRow?.total || 0) / limit),
      },
      correlationId: req.correlationId,
    });
  } catch (err) { next(err); }
}

async function read(req, res, next) {
  try {
    const { id } = req.params;
    const plans = await query('SELECT * FROM plans WHERE id = ?', [id]);
    if (plans.length === 0) {
      return res.status(404).json({ error: 'ERR_NOT_FOUND', message: 'Plano não encontrado' });
    }
    const p = plans[0];
    res.json({
      plan: {
        id: p.id, slug: p.slug, name: p.name, description: p.description,
        price: parseFloat(p.price),
        limits: typeof p.limits === 'string' ? JSON.parse(p.limits) : p.limits,
        features: typeof p.features === 'string' ? JSON.parse(p.features) : p.features,
        active: p.active, sort_order: p.sort_order,
      },
      correlationId: req.correlationId,
    });
  } catch (err) { next(err); }
}

async function create(req, res, next) {
  try {
    const { slug, name, description, price, limits, features, active, sort_order } = req.body;
    if (!slug || !name) {
      return res.status(400).json({ error: 'ERR_VALIDATION', message: 'slug e name são obrigatórios' });
    }
    const result = await query(
      `INSERT INTO plans (slug, name, description, price, limits, features, active, sort_order)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?)`,
      [
        slug.trim().toLowerCase().replace(/\s+/g, '_'),
        name.trim(),
        description || null,
        parseFloat(price) || 0,
        limits ? JSON.stringify(limits) : null,
        features ? JSON.stringify(features) : null,
        active !== undefined ? active : true,
        sort_order || 0,
      ]
    );
    res.status(201).json({ message: 'Plano criado com sucesso!', id: result.insertId });
  } catch (err) { next(err); }
}

async function update(req, res, next) {
  try {
    const { id } = req.params;
    const { name, description, price, limits, features, active, sort_order } = req.body;

    const updates = []; const params = [];
    if (name) { updates.push('name = ?'); params.push(name.trim()); }
    if (description !== undefined) { updates.push('description = ?'); params.push(description); }
    if (price !== undefined) { updates.push('price = ?'); params.push(parseFloat(price)); }
    if (limits) { updates.push('limits = ?'); params.push(JSON.stringify(limits)); }
    if (features) { updates.push('features = ?'); params.push(JSON.stringify(features)); }
    if (active !== undefined) { updates.push('active = ?'); params.push(active); }
    if (sort_order !== undefined) { updates.push('sort_order = ?'); params.push(sort_order); }
    if (updates.length === 0) {
      return res.status(400).json({ error: 'ERR_VALIDATION', message: 'Nenhum campo para atualizar' });
    }
    params.push(id);
    await query(`UPDATE plans SET ${updates.join(', ')} WHERE id = ?`, params);
    res.json({ message: 'Plano atualizado com sucesso!' });
  } catch (err) { next(err); }
}

const VALID_PLANS = ['free', 'basic', 'pro', 'enterprise'];
async function remove(req, res, next) {
  try {
    const { id } = req.params;

    const plans = await query('SELECT slug FROM plans WHERE id = ?', [id]);
    if (plans.length === 0) {
      return res.status(404).json({ error: 'ERR_NOT_FOUND', message: 'Plano não encontrado' });
    }
    if (VALID_PLANS.includes(plans[0].slug)) {
      return res.status(422).json({ error: 'ERR_INVALID_OPERATION', message: 'Planos padrão não podem ser excluídos. Desative-os.' });
    }
    await query('UPDATE plans SET active = FALSE WHERE id = ?', [id]);
    res.json({ message: 'Plano desativado com sucesso' });
  } catch (err) { next(err); }
}

module.exports = { list, read, create, update, remove };
