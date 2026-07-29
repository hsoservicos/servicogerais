-- Migration 007 — Domestic Agreements (CLT Contracts)
CREATE TABLE IF NOT EXISTS domestic_agreements (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    worker_id INT UNSIGNED NOT NULL,
    tenant_id INT UNSIGNED NOT NULL,
    contract_type ENUM('CLT_DOMESTICO') NOT NULL DEFAULT 'CLT_DOMESTICO',
    regime ENUM('AUTONOMO_DIARISTA', 'LC_150_CLT') NOT NULL DEFAULT 'LC_150_CLT',
    status ENUM('ACTIVE', 'TERMINATED', 'SUSPENDED') NOT NULL DEFAULT 'ACTIVE',
    salary DECIMAL(10,2) NOT NULL,
    transport_voucher DECIMAL(10,2) DEFAULT 0.00,
    food_allowance DECIMAL(10,2) DEFAULT 0.00,
    weekly_frequency_days TINYINT DEFAULT 5,
    start_date DATE NOT NULL,
    termination_date DATE NULL,
    termination_reason VARCHAR(500) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE INDEX idx_agreement_worker (worker_id),
    INDEX idx_agreement_tenant (tenant_id),
    INDEX idx_agreement_status (status),
    CONSTRAINT fk_agreement_worker FOREIGN KEY (worker_id) REFERENCES workers(id) ON DELETE CASCADE,
    CONSTRAINT fk_agreement_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
) ENGINE=InnoDB;
