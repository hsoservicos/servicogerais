// ═══════════════════════════════════════════════════════════════
// utils/mysql.js — MySQL Helper Utilities
// ═══════════════════════════════════════════════════════════════
// Funções auxiliares para sanitização de valores em queries SQL.
// ═══════════════════════════════════════════════════════════════

/**
 * Escapa um valor para uso em queries SQL raw.
 * Útil para middlewares que montam filtros dinâmicos (ex: tenantFilter).
 *
 * @param {*} value — Valor a ser escapado
 * @returns {string} — Valor escapado e entre aspas se string
 */
function mysqlEscape(value) {
  if (value === null || value === undefined) {
    return 'NULL';
  }
  if (typeof value === 'number') {
    return String(value);
  }
  if (typeof value === 'boolean') {
    return value ? '1' : '0';
  }

  // Escapar caracteres especiais para MySQL
  const str = String(value);
  const escaped = str
    .replace(/\\/g, '\\\\')
    .replace(/'/g, "\\'")
    .replace(/"/g, '\\"')
    .replace(/\n/g, '\\n')
    .replace(/\r/g, '\\r')
    .replace(/\x00/g, '\\x00')
    .replace(/\x1a/g, '\\x1a');

  return `'${escaped}'`;
}

/**
 * Cria um filtro WHERE a partir de um objeto { coluna: valor }.
 *
 * @param {Object} filters — { field1: value1, field2: value2 }
 * @param {string} prefix — Prefixo opcional (ex: 'p.' para proposals)
 * @returns {string} — Ex: "p.field1 = 'valor1' AND p.field2 = 42"
 */
function buildWhereClause(filters, prefix = '') {
  const conditions = Object.entries(filters)
    .filter(([, value]) => value !== null && value !== undefined && value !== '')
    .map(([field, value]) => {
      const prefixed = prefix ? `${prefix}.${field}` : field;
      return `${prefixed} = ${mysqlEscape(value)}`;
    });

  return conditions.length > 0 ? conditions.join(' AND ') : '1=1';
}

module.exports = {
  mysqlEscape,
  buildWhereClause,
};
