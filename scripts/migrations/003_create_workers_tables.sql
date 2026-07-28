-- ═══════════════════════════════════════════════════════════════
-- Migration 003 — Workers, Certifications & Schedules (LC 150)
-- ═══════════════════════════════════════════════════════════════
-- Executar após init.sql
-- Base Legal: Lei Complementar nº 150/2015, CBO 2026

-- ── 1. WORKERS (Trabalhadores Domésticos) ───────────────────
CREATE TABLE IF NOT EXISTS workers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NULL,
    cpf VARCHAR(14) NOT NULL,
    rg VARCHAR(20) NULL,
    cbo_code VARCHAR(10) NOT NULL COMMENT 'Código CBO (ex: 5121-05)',
    worker_category ENUM(
        'EMPREGADO_DOMESTICO_GERAL', 'DIARISTA', 'BABA',
        'CUIDADOR_IDOSOS', 'COZINHEIRO', 'MOTORISTA',
        'JARDINEIRO', 'CASEIRO', 'GOVERNANTA'
    ) NOT NULL,
    phone VARCHAR(20) NULL,
    whatsapp VARCHAR(20) NULL,
    pix_key VARCHAR(100) NULL,
    address JSON NULL,
    avatar_url VARCHAR(500) NULL,
    background_check_status ENUM('PENDING', 'APPROVED', 'REJECTED') DEFAULT 'PENDING',
    background_check_date TIMESTAMP NULL,
    background_check_provider VARCHAR(100) NULL,
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE INDEX idx_workers_cpf (cpf),
    INDEX idx_workers_tenant (tenant_id),
    INDEX idx_workers_category (worker_category),
    INDEX idx_workers_active (active),
    INDEX idx_workers_name (name),
    CONSTRAINT fk_workers_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── 2. WORKER CERTIFICATIONS ────────────────────────────────
CREATE TABLE IF NOT EXISTS worker_certifications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    worker_id INT UNSIGNED NOT NULL,
    certification_type ENUM(
        'CUIDADOR_IDOSOS', 'APH', 'BABA', 'COZINHA',
        'JARDINAGEM', 'PRIMEIROS_SOCORROS', 'OUTRO'
    ) NOT NULL,
    title VARCHAR(255) NOT NULL,
    issuer VARCHAR(255) NULL,
    issue_date DATE NULL,
    expiry_date DATE NULL,
    document_url VARCHAR(500) NULL,
    verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_cert_worker (worker_id),
    CONSTRAINT fk_cert_worker FOREIGN KEY (worker_id) REFERENCES workers(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── 3. SERVICE SCHEDULES (Agendamentos com Controle de Frequência) ──
CREATE TABLE IF NOT EXISTS service_schedules (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT UNSIGNED NOT NULL,
    worker_id INT UNSIGNED NOT NULL,
    client_id INT UNSIGNED NOT NULL,
    service_category ENUM(
        'EMPREGADO_DOMESTICO_GERAL', 'DIARISTA', 'BABA',
        'CUIDADOR_IDOSOS', 'COZINHEIRO', 'MOTORISTA',
        'JARDINEIRO', 'CASEIRO', 'GOVERNANTA'
    ) NOT NULL,
    regime ENUM('AUTONOMO_DIARISTA', 'LC_150_CLT') NOT NULL,
    scheduled_date DATE NOT NULL,
    start_time TIME NULL,
    end_time TIME NULL,
    status ENUM('scheduled', 'confirmed', 'in_progress', 'completed', 'cancelled') DEFAULT 'scheduled',
    hourly_rate DECIMAL(10,2) NULL,
    total_amount DECIMAL(10,2) NULL,
    transport_voucher DECIMAL(10,2) DEFAULT 0.00,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_sched_tenant (tenant_id),
    INDEX idx_sched_worker (worker_id),
    INDEX idx_sched_client (client_id),
    INDEX idx_sched_date (scheduled_date),
    INDEX idx_sched_status (status),
    CONSTRAINT fk_sched_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    CONSTRAINT fk_sched_worker FOREIGN KEY (worker_id) REFERENCES workers(id) ON DELETE CASCADE,
    CONSTRAINT fk_sched_client FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
) ENGINE=InnoDB;

COMMIT;