-- ═══════════════════════════════════════════════════════════════
-- Migration 002: Create transactions table (Epic 5 — Mercado Pago)
-- ═══════════════════════════════════════════════════════════════
-- Story 5.1 — Setup SDK MP + Tabela transactions
-- Schema baseado na pesquisa técnica de Mary (Business Analyst)
-- ═══════════════════════════════════════════════════════════════

-- ── 1. Criar tabela transactions ──────────────────────────
CREATE TABLE IF NOT EXISTS transactions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tenant_id INT NOT NULL,
  proposal_id INT NOT NULL,
  
  -- Mercado Pago identifiers
  mp_preference_id VARCHAR(100) NULL COMMENT 'ID da preferência no MP',
  mp_payment_id VARCHAR(100) NULL COMMENT 'ID do pagamento no MP',
  mp_status VARCHAR(50) NULL COMMENT 'Status retornado pelo MP',
  mp_notification_id VARCHAR(100) NULL COMMENT 'ID da notificação webhook (idempotência)',
  
  -- Financial data
  amount DECIMAL(10,2) NOT NULL COMMENT 'Valor total da transação',
  fee DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Taxa do Mercado Pago',
  net_amount DECIMAL(10,2) GENERATED ALWAYS AS (amount - fee) STORED COMMENT 'Valor líquido (após taxa)',
  payment_method VARCHAR(20) NULL COMMENT 'pix, credit_card, ticket (boleto)',
  
  -- Status
  status ENUM('pending', 'completed', 'cancelled', 'refunded', 'chargeback') DEFAULT 'pending',
  paid_at TIMESTAMP NULL COMMENT 'Data/hora da confirmação do pagamento',
  
  -- Metadata (JSON com dados extras do MP)
  metadata JSON NULL,
  
  -- Timestamps
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  -- Foreign Keys
  FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE RESTRICT,
  FOREIGN KEY (proposal_id) REFERENCES proposals(id) ON DELETE RESTRICT,
  
  -- Indexes
  UNIQUE INDEX idx_transactions_mp_payment (mp_payment_id),
  INDEX idx_transactions_tenant (tenant_id),
  INDEX idx_transactions_proposal (proposal_id),
  INDEX idx_transactions_status (status),
  INDEX idx_transactions_notification (mp_notification_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Transações de pagamento processadas via Mercado Pago';

-- ── 2. Adicionar coluna status='paid' na tabela proposals ──
-- (já existe no schema atual? Verificar init.sql)
-- NOTE: Se proposals já tem status 'paid' no ENUM, ignorar.
-- Se não tiver, descomentar a linha abaixo:
-- ALTER TABLE proposals MODIFY COLUMN status ENUM('draft','sent','viewed','accepted','rejected','cancelled','paid') NOT NULL DEFAULT 'draft';
