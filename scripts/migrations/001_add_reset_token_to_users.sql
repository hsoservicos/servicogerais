-- ═══════════════════════════════════════════════════════════════
-- Migration 001: Add Reset Token columns to users table
-- ═══════════════════════════════════════════════════════════════
-- Story 1.5 — Recuperação de Senha
-- Adiciona colunas para gerenciar tokens de reset de senha
-- ═══════════════════════════════════════════════════════════════

USE servicos_flex;

ALTER TABLE users
  ADD COLUMN reset_token VARCHAR(255) NULL AFTER password_hash,
  ADD COLUMN reset_token_expires TIMESTAMP NULL AFTER reset_token,
  ADD INDEX idx_users_reset_token (reset_token);
