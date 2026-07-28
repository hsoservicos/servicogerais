-- ═══════════════════════════════════════════════════════════════
-- Migration 005 — Address Fields on Tenants (Proximity Search)
-- ═══════════════════════════════════════════════════════════════
-- Permite que prestadores (tenants) registrem seu endereço/
-- município para que clientes possam buscar serviços próximos.

ALTER TABLE tenants
    ADD COLUMN zipcode VARCHAR(9) NULL AFTER whatsapp,
    ADD COLUMN address VARCHAR(500) NULL AFTER zipcode,
    ADD COLUMN neighborhood VARCHAR(100) NULL AFTER address,
    ADD COLUMN city VARCHAR(100) NULL AFTER neighborhood,
    ADD COLUMN state CHAR(2) NULL AFTER city,
    ADD COLUMN latitude DECIMAL(10, 8) NULL AFTER state,
    ADD COLUMN longitude DECIMAL(11, 8) NULL AFTER latitude,
    ADD INDEX idx_tenants_city (city),
    ADD INDEX idx_tenants_state (state);

COMMIT;