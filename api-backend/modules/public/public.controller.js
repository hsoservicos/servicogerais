// ═══════════════════════════════════════════════════════════════
// modules/public/public.controller.js — Public Endpoints (Epic 6)
// ═══════════════════════════════════════════════════════════════
// Story 6.1 — Endpoints públicos SEM autenticação para Landing Page.
// Qualquer visitante pode consultar categorias e serviços
// disponíveis na plataforma.
// ═══════════════════════════════════════════════════════════════

const { query } = require('../../config/database');

// ═══════════════════════════════════════════════════════════════
// GET /api/v1/public/categories — Listar Categorias Públicas
// ═══════════════════════════════════════════════════════════════
// Retorna todas as categorias ativas de TODOS os tenants,
// agregadas por nome (deduplicadas).
async function listCategories(req, res, next) {
  try {
    // Categorias públicas — usa ANY_VALUE() para compatibilidade com
    // MySQL 8.0 ONLY_FULL_GROUP_BY (padrão). Agrupa por nome para
    // deduplicar categorias com mesmo nome de diferentes tenants.
    const categories = await query(
      `SELECT ANY_VALUE(c.id) as id, c.name,
              ANY_VALUE(c.description) as description,
              ANY_VALUE(c.icon) as icon,
              ANY_VALUE(c.color) as color,
              COUNT(s.id) as service_count
       FROM categories c
       LEFT JOIN services s ON s.category_id = c.id AND s.active = TRUE
       WHERE c.active = TRUE
       GROUP BY c.name
       ORDER BY ANY_VALUE(c.sort_order) ASC, c.name ASC`
    );

    // Mapear ícones para SVGs inline
    const iconMap = {
      scissors: 'M7.464 1.066a.5.5 0 01.66.193l2.5 4.33a.5.5 0 01-.193.66l-1.5.866a.5.5 0 01-.66-.193L5.31 2.532a.5.5 0 01.193-.66l1.5-.866a.5.5 0 01.46-.04zM3.102 4.268a.5.5 0 01.216.667l-1 1.732a.5.5 0 01-.667.216l-1.5-.866a.5.5 0 01-.216-.667l1-1.732a.5.5 0 01.667-.216l1.5.866zM12.5 7a.5.5 0 01.5.5v1a.5.5 0 01-.5.5h-1a.5.5 0 01-.5-.5v-1a.5.5 0 01.5-.5h1z',
      sparkles: 'M5 3a.5.5 0 01.5.5V5h1.5a.5.5 0 010 1H5.5v1.5a.5.5 0 01-1 0V6H3a.5.5 0 010-1h1.5V3.5A.5.5 0 015 3zM12 8a.5.5 0 01.5.5V10h1.5a.5.5 0 010 1H12.5v1.5a.5.5 0 01-1 0V11H10a.5.5 0 010-1h1.5V8.5A.5.5 0 0112 8z',
      brush: 'M15.502 1.94a.5.5 0 010 .706L14.46 3.69l-2-2L13.05.214a.5.5 0 01.706 0l1.745 1.726zm-1.745 3.12l2 2L8.55 14.44a.5.5 0 01-.26.19l-4 1a.5.5 0 01-.61-.61l1-4a.5.5 0 01.19-.26l6.878-6.88z',
      face: 'M8 15A7 7 0 118 1a7 7 0 010 14zm0 1A8 8 0 108 0a8 8 0 000 16zM4.5 6.5a1 1 0 112 0 1 1 0 01-2 0zm5 0a1 1 0 112 0 1 1 0 01-2 0zM8 10a2 2 0 11.001 3.999A2 2 0 018 10z',
      star: 'M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z',
      heart: 'M8 2.748l-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01L8 2.748zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143c.06.055.119.112.176.171a3.12 3.12 0 01.176-.17C12.72-3.042 23.333 4.867 8 15z',
      briefcase: 'M2 5a3 3 0 013-3h6a3 3 0 013 3v1h1a2 2 0 012 2v7a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h1V5zm3 0a1 1 0 011-1h4a1 1 0 011 1v1H5V5zm-1 4v5h8V9H4zm10 0v5h2V9h-2z',
      tools: 'M7.068.727c.243-.97 1.62-.97 1.864 0l.071.286a.96.96 0 001.622.434l.205-.211c.695-.719 1.888-.03 1.613.931l-.08.284a.96.96 0 001.187 1.187l.283-.081c.96-.275 1.65.918.931 1.613l-.211.205a.96.96 0 00.434 1.622l.286.071c.97.243.97 1.62 0 1.864l-.286.071a.96.96 0 00-.434 1.622l.211.205c.719.695.03 1.888-.931 1.613l-.284-.08a.96.96 0 00-1.187 1.187l.081.283c.275.96-.918 1.65-1.613.931l-.205-.211a.96.96 0 00-1.622.434l-.071.286c-.243.97-1.62.97-1.864 0l-.071-.286a.96.96 0 00-1.622-.434l-.205.211c-.695.719-1.888.03-1.613-.931l.08-.284a.96.96 0 00-1.187-1.187l-.283.081c-.96.275-1.65-.918-.931-1.613l.211-.205a.96.96 0 00-.434-1.622l-.286-.071c-.97-.243-.97-1.62 0-1.864l.286-.071a.96.96 0 00.434-1.622l-.211-.205c-.719-.695-.03-1.888.931-1.613l.284.08a.96.96 0 001.187-1.186l-.081-.284c-.275-.96.918-1.65 1.613-.931l.205.211a.96.96 0 001.622-.434l.071-.286zM8 11a3 3 0 100-6 3 3 0 000 6z',
    };

    res.json({
      categories: categories.map(c => ({
        id: c.id,
        name: c.name,
        description: c.description,
        icon: c.icon,
        iconSvg: iconMap[c.icon] || iconMap.star,
        color: c.color || '#10B981',
        serviceCount: c.service_count || 0,
        tenantName: c.tenant_name,
      })),
      correlationId: req.correlationId,
    });
  } catch (err) {
    next(err);
  }
}

// ═══════════════════════════════════════════════════════════════
// GET /api/v1/public/services — Buscar Serviços Públicos
// ═══════════════════════════════════════════════════════════════
// Busca por nome com autocomplete. Retorna serviços ativos
// de TODOS os tenants com nome da categoria e tenant.
// Query params: ?search= (mínimo 2 caracteres), ?category_id=
async function listServices(req, res, next) {
  try {
    const { search, category_id, city } = req.query;

    let whereClause = 's.active = TRUE';
    const params = [];

    if (search && search.length >= 2) {
      whereClause += ' AND s.name LIKE ?';
      params.push(`%${search}%`);
    }

    if (category_id) {
      whereClause += ' AND s.category_id = ?';
      params.push(parseInt(category_id, 10));
    }

    if (city && city.trim().length >= 2) {
      whereClause += ' AND t.city LIKE ?';
      params.push(`%${city.trim()}%`);
    }

    // Se não tiver search nem category_id nem city, limitar a 50 resultados
    let limitClause = '';
    if (!search && !category_id && !city) {
      limitClause = 'LIMIT 50';
    }

    const services = await query(
      `SELECT s.id, s.name, s.description, s.price, s.duration_minutes,
              c.name as category_name, c.color as category_color,
              t.name as tenant_name, t.slug as tenant_slug,
              t.city as tenant_city, t.state as tenant_state
       FROM services s
       LEFT JOIN categories c ON s.category_id = c.id
       LEFT JOIN tenants t ON t.id = s.tenant_id
       WHERE ${whereClause}
       ORDER BY c.sort_order ASC, s.name ASC
       ${limitClause}`,
      params.length > 0 ? params : undefined
    );

    res.json({
      services: services.map(s => ({
        id: s.id,
        name: s.name,
        description: s.description,
        price: `R$ ${parseFloat(s.price).toFixed(2).replace('.', ',')}`,
        priceValue: parseFloat(s.price),
        duration: s.duration_minutes ? `${s.duration_minutes} min` : null,
        category: s.category_name,
        categoryColor: s.category_color || '#10B981',
        tenant: s.tenant_name,
        tenantSlug: s.tenant_slug,
        tenantCity: s.tenant_city,
        tenantState: s.tenant_state,
      })),
      correlationId: req.correlationId,
    });
  } catch (err) {
    next(err);
  }
}

// ═══════════════════════════════════════════════════════════════
// POST /api/v1/public/leads — Criar Lead (Story 6.2)
// ═══════════════════════════════════════════════════════════════
// Recebe dados do wizard de solicitação de orçamento (3 passos).
// Cria lead na tabela public_leads sem autenticação.
async function createLead(req, res, next) {
  try {
    const {
      service_name,
      description,
      desired_date,
      desired_time,
      zipcode,
      address,
      city,
      state,
      reference,
      customer_name,
      customer_phone,
      customer_email,
      photo_urls,
      lgpd_consent_marketing,
      lgpd_consent_terms,
    } = req.body;

    // ── Validações obrigatórias ──
    const errors = [];

    if (!service_name || service_name.trim().length < 2) {
      errors.push({ field: 'service_name', message: 'Selecione um serviço' });
    }
    if (!customer_name || customer_name.trim().length < 2) {
      errors.push({ field: 'customer_name', message: 'Nome é obrigatório' });
    }
    if (!customer_phone || customer_phone.replace(/\D/g, '').length < 10) {
      errors.push({ field: 'customer_phone', message: 'Telefone inválido' });
    }
    if (!lgpd_consent_marketing) {
      errors.push({ field: 'lgpd_consent_marketing', message: 'É necessário aceitar ser contactado' });
    }
    if (!lgpd_consent_terms) {
      errors.push({ field: 'lgpd_consent_terms', message: 'É necessário aceitar os termos de uso' });
    }

    if (errors.length > 0) {
      return res.status(422).json({
        error: 'ERR_VALIDATION',
        message: 'Campos obrigatórios não preenchidos',
        details: errors,
      });
    }

    // ── Sanitizar telefone (remove tudo que não é dígito) ──
    const cleanPhone = customer_phone.replace(/\D/g, '');

    // ── Inserir lead ──
    const result = await query(
      `INSERT INTO public_leads 
       (service_name, description, desired_date, desired_time, 
        zipcode, address, city, state, reference, photo_urls,
        customer_name, customer_phone, customer_email,
        lgpd_consent_marketing, lgpd_consent_terms, status)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'new')`,
      [
        service_name.trim(),
        description?.trim() || null,
        desired_date || null,
        desired_time || null,
        zipcode?.replace(/\D/g, '') || null,
        address?.trim() || null,
        city?.trim() || null,
        state?.toUpperCase().slice(0, 2) || null,
        reference?.trim() || null,
        photo_urls || null,
        customer_name.trim(),
        cleanPhone,
        customer_email?.trim()?.toLowerCase() || null,
        lgpd_consent_marketing ? 1 : 0,
        lgpd_consent_terms ? 1 : 0,
      ]
    );

    res.status(201).json({
      message: 'Solicitação enviada com sucesso!',
      data: {
        id: result.insertId,
        status: 'new',
      },
      correlationId: req.correlationId,
    });
  } catch (err) {
    next(err);
  }
}

module.exports = { listCategories, listServices, createLead };
