-- ═══════════════════════════════════════════════════════════════
-- Migration 006: Create plans table for Epic 7 (Story 7.3)
-- ═══════════════════════════════════════════════════════════════
-- Gerencia os planos disponíveis na plataforma.
-- Os planos existentes (free, basic, pro, enterprise) são seedados
-- como registros iniciais. O campo limits armazena em JSON os
-- limites de cada plano (ex: max_clients, max_proposals, etc).
-- ═══════════════════════════════════════════════════════════════

CREATE TABLE IF NOT EXISTS plans (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(50) NOT NULL UNIQUE COMMENT 'Identificador único (ex: free, basic, pro, enterprise)',
    name VARCHAR(100) NOT NULL COMMENT 'Nome amigável (ex: Grátis, Básico, Profissional, Enterprise)',
    description VARCHAR(500) NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Preço mensal em R$',
    limits JSON NULL COMMENT 'JSON com limites: max_clients, max_proposals, max_users, etc',
    features JSON NULL COMMENT 'JSON com lista de recursos disponíveis',
    active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_plans_active (active),
    INDEX idx_plans_sort (sort_order)
) ENGINE=InnoDB;

-- Seed: planos padrão
INSERT IGNORE INTO plans (slug, name, description, price, limits, features, sort_order) VALUES
('free', 'Grátis', 'Para quem está começando', 0.00,
 '{"max_clients":10,"max_proposals":5,"max_users":1,"max_services":10}',
 '["clientes","propostas","catalogo","whatsapp"]', 1),
('basic', 'Básico', 'Para profissionais autônomos', 29.90,
 '{"max_clients":50,"max_proposals":30,"max_users":2,"max_services":30}',
 '["clientes","propostas","catalogo","whatsapp","relatorios"]', 2),
('pro', 'Profissional', 'Para pequenas empresas', 79.90,
 '{"max_clients":200,"max_proposals":100,"max_users":5,"max_services":100}',
 '["clientes","propostas","catalogo","whatsapp","relatorios","dashboard_avancado","api"]', 3),
('enterprise', 'Enterprise', 'Para grandes operações', 199.90,
 '{"max_clients":-1,"max_proposals":-1,"max_users":20,"max_services":-1}',
 '["clientes","propostas","catalogo","whatsapp","relatorios","dashboard_avancado","api","suporte_premium","personalizacao"]', 4);
