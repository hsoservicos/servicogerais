-- ═══════════════════════════════════════════════════════════════
-- init.sql — Database Migration (ServiceSaaS)
-- ═══════════════════════════════════════════════════════════════
-- Executado automaticamente pelo docker-entrypoint-initdb.d

CREATE DATABASE IF NOT EXISTS servicos_flex
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE servicos_flex;

-- ═══════════════════════════════════════════════════════════════
-- 1. TENANTS (Multi-Tenancy - Raiz)
-- ═══════════════════════════════════════════════════════════════
CREATE TABLE IF NOT EXISTS tenants (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(100) UNIQUE,
    document_cpf VARCHAR(14) NULL,
    document_cnpj VARCHAR(18) NULL,
    phone VARCHAR(20) NULL,
    whatsapp VARCHAR(20) NULL,
    logo_url VARCHAR(500) NULL,
    active BOOLEAN DEFAULT TRUE,
    plan ENUM('free', 'basic', 'pro', 'enterprise') DEFAULT 'free',
    settings JSON NULL COMMENT 'Configurações específicas do tenant',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_tenants_active (active),
    INDEX idx_tenants_plan (plan)
) ENGINE=InnoDB;

-- ═══════════════════════════════════════════════════════════════
-- 2. USERS (Usuários do Sistema)
-- ═══════════════════════════════════════════════════════════════
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'admin', 'viewer') DEFAULT 'admin',
    avatar_url VARCHAR(500) NULL,
    active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE INDEX idx_users_email (email),
    INDEX idx_users_tenant (tenant_id),
    CONSTRAINT fk_users_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ═══════════════════════════════════════════════════════════════
-- 3. CLIENTS (Clientes dos Prestadores)
-- ═══════════════════════════════════════════════════════════════
CREATE TABLE IF NOT EXISTS clients (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NULL,
    document_cpf VARCHAR(14) NULL,
    document_cnpj VARCHAR(18) NULL,
    phone VARCHAR(20) NULL,
    whatsapp VARCHAR(20) NULL,
    address VARCHAR(500) NULL,
    city VARCHAR(100) NULL,
    state CHAR(2) NULL,
    notes TEXT NULL COMMENT 'Observações sobre o cliente',
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_clients_tenant (tenant_id),
    INDEX idx_clients_name (name),
    INDEX idx_clients_active (active),
    CONSTRAINT fk_clients_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ═══════════════════════════════════════════════════════════════
-- 4. CATEGORIES (Categorias de Serviços)
-- ═══════════════════════════════════════════════════════════════
CREATE TABLE IF NOT EXISTS categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    description VARCHAR(300) NULL,
    icon VARCHAR(50) NULL,
    color VARCHAR(7) NULL COMMENT 'Hex color like #10B981',
    active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_categories_tenant (tenant_id),
    CONSTRAINT fk_categories_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ═══════════════════════════════════════════════════════════════
-- 5. SERVICES (Catálogo de Serviços/Produtos)
-- ═══════════════════════════════════════════════════════════════
CREATE TABLE IF NOT EXISTS services (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT UNSIGNED NOT NULL,
    category_id INT UNSIGNED NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    duration_minutes INT NULL COMMENT 'Duração estimada em minutos',
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_services_tenant (tenant_id),
    INDEX idx_services_category (category_id),
    INDEX idx_services_active (active),
    CONSTRAINT fk_services_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    CONSTRAINT fk_services_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ═══════════════════════════════════════════════════════════════
-- 6. PROPOSALS (Propostas / Orçamentos)
-- ═══════════════════════════════════════════════════════════════
CREATE TABLE IF NOT EXISTS proposals (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT UNSIGNED NOT NULL,
    client_id INT UNSIGNED NULL,
    number VARCHAR(20) NOT NULL COMMENT 'Número sequencial da proposta (ex: PROP-2026-0001)',
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    status ENUM('draft', 'sent', 'viewed', 'accepted', 'rejected', 'cancelled', 'paid') DEFAULT 'draft',
    valid_until DATE NULL,
    public_token VARCHAR(36) NULL COMMENT 'UUID v4 para acesso público (Story 6.3)',
    payment_terms TEXT NULL,
    notes TEXT NULL,
    sent_at TIMESTAMP NULL,
    accepted_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_proposals_tenant (tenant_id),
    INDEX idx_proposals_client (client_id),
    INDEX idx_proposals_status (status),
    UNIQUE INDEX idx_proposals_number_tenant (tenant_id, number),
    UNIQUE INDEX idx_proposals_public_token (public_token),
    CONSTRAINT fk_proposals_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    CONSTRAINT fk_proposals_client FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ═══════════════════════════════════════════════════════════════
-- 7. PROPOSAL ITEMS (Itens da Proposta)
-- ═══════════════════════════════════════════════════════════════
CREATE TABLE IF NOT EXISTS proposal_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    proposal_id INT UNSIGNED NOT NULL,
    description VARCHAR(500) NOT NULL,
    quantity DECIMAL(10,2) NOT NULL DEFAULT 1,
    unit_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total_price DECIMAL(10,2) GENERATED ALWAYS AS (quantity * unit_price) STORED,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_items_proposal (proposal_id),
    CONSTRAINT fk_items_proposal FOREIGN KEY (proposal_id) REFERENCES proposals(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ═══════════════════════════════════════════════════════════════
-- 8. TRANSACTIONS (Transações Financeiras - Mercado Pago)
-- ═══════════════════════════════════════════════════════════════
CREATE TABLE IF NOT EXISTS transactions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT UNSIGNED NOT NULL,
    proposal_id INT UNSIGNED NULL,
    mp_id VARCHAR(100) NULL COMMENT 'ID da transação no Mercado Pago',
    mp_status VARCHAR(30) NULL COMMENT 'Status do MP (approved, pending, rejected)',
    amount DECIMAL(10,2) NOT NULL,
    fee DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Taxa do MP',
    net_amount DECIMAL(10,2) GENERATED ALWAYS AS (amount - fee) STORED,
    payment_method VARCHAR(50) NULL COMMENT 'credit_card, pix, boleto',
    status ENUM('pending', 'processing', 'completed', 'refunded', 'cancelled') DEFAULT 'pending',
    paid_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_transactions_tenant (tenant_id),
    UNIQUE INDEX idx_transactions_mp (mp_id),
    INDEX idx_transactions_status (status),
    CONSTRAINT fk_transactions_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    CONSTRAINT fk_transactions_proposal FOREIGN KEY (proposal_id) REFERENCES proposals(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ═══════════════════════════════════════════════════════════════
-- 9. AUDIT LOG (Rastreabilidade LGPD)
-- ═══════════════════════════════════════════════════════════════
CREATE TABLE IF NOT EXISTS audit_log (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT UNSIGNED NULL,
    user_id INT UNSIGNED NULL,
    action VARCHAR(50) NOT NULL COMMENT 'create, read, update, delete, export, login',
    entity_type VARCHAR(50) NOT NULL COMMENT 'client, proposal, service, user, tenant',
    entity_id INT UNSIGNED NULL,
    metadata JSON NULL COMMENT 'Payload da ação (dados alterados)',
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(500) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_audit_tenant (tenant_id),
    INDEX idx_audit_user (user_id),
    INDEX idx_audit_action (action),
    INDEX idx_audit_entity (entity_type, entity_id),
    INDEX idx_audit_created (created_at)
) ENGINE=InnoDB;

-- ═══════════════════════════════════════════════════════════════
-- 🔐 LGPD — Tabelas de Consentimento e Privacidade
-- ═══════════════════════════════════════════════════════════════

CREATE TABLE IF NOT EXISTS lgpd_consent (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    consent_type ENUM('opt-in', 'communications', 'terms') NOT NULL,
    granted BOOLEAN NOT NULL DEFAULT TRUE,
    ip_address VARCHAR(45) NULL,
    granted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    revoked_at TIMESTAMP NULL,
    INDEX idx_lgpd_user (user_id),
    INDEX idx_lgpd_type (consent_type),
    CONSTRAINT fk_lgpd_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ═══════════════════════════════════════════════════════════════
-- 10. PUBLIC LEADS (Captura de Leads — Epic 6)
-- ═══════════════════════════════════════════════════════════════
CREATE TABLE IF NOT EXISTS public_leads (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT UNSIGNED NULL COMMENT 'Opcional — atribuído após match com prestador',
    service_name VARCHAR(255) NOT NULL COMMENT 'Serviço selecionado no wizard',
    description TEXT NULL COMMENT 'Descrição do que precisa',
    desired_date DATE NULL,
    desired_time TIME NULL,
    zipcode VARCHAR(9) NULL,
    address VARCHAR(500) NULL,
    city VARCHAR(100) NULL,
    state CHAR(2) NULL,
    reference VARCHAR(255) NULL,
    photo_urls JSON NULL COMMENT 'URLs de fotos enviadas',
    customer_name VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    customer_email VARCHAR(255) NULL,
    lgpd_consent_marketing BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Aceita ser contactado',
    lgpd_consent_terms BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Aceita termos de uso',
    status ENUM('new', 'contacted', 'converted', 'archived') DEFAULT 'new',
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_public_leads_status (status),
    INDEX idx_public_leads_tenant (tenant_id),
    INDEX idx_public_leads_created (created_at),
    CONSTRAINT fk_public_leads_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ═══════════════════════════════════════════════════════════════
-- 🔐 ADMIN — Gestão de Plataforma
-- ═══════════════════════════════════════════════════════════════

CREATE TABLE IF NOT EXISTS admin_audit_log (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_user_id INT UNSIGNED NOT NULL,
    action VARCHAR(50) NOT NULL,
    target_type VARCHAR(50) NOT NULL COMMENT 'tenant, user, transaction, plan',
    target_id INT UNSIGNED NULL,
    details JSON NULL,
    ip_address VARCHAR(45) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_admin_audit_action (action),
    INDEX idx_admin_audit_target (target_type, target_id),
    INDEX idx_admin_audit_created (created_at)
) ENGINE=InnoDB;

-- ═══════════════════════════════════════════════════════════════
-- ⚙️ Dados Iniciais
-- ═══════════════════════════════════════════════════════════════

-- Super Admin Padrão (senha: admin123)
-- Hash gerado com bcrypt (salt rounds 12)
INSERT IGNORE INTO tenants (id, name, slug, active, plan)
VALUES (1, 'ServiceSaaS Admin', 'servicesaas', TRUE, 'enterprise');

-- Nota: O super admin é criado via aplicação.
-- Para primeiro acesso, use o endpoint POST /api/v1/auth/register
-- ou insira manualmente com o hash gerado.

COMMIT;
