-- Migration: Add communication preference flags
ALTER TABLE clients
  ADD COLUMN notify_email BOOLEAN DEFAULT TRUE AFTER email,
  ADD COLUMN notify_whatsapp BOOLEAN DEFAULT TRUE AFTER notify_email,
  ADD COLUMN notify_telegram BOOLEAN DEFAULT FALSE AFTER notify_whatsapp,
  ADD COLUMN telegram_chat_id VARCHAR(100) NULL AFTER notify_telegram;

ALTER TABLE tenants
  ADD COLUMN notify_email BOOLEAN DEFAULT TRUE AFTER whatsapp,
  ADD COLUMN notify_whatsapp BOOLEAN DEFAULT TRUE AFTER notify_email,
  ADD COLUMN notify_telegram BOOLEAN DEFAULT FALSE AFTER notify_whatsapp,
  ADD COLUMN telegram_chat_id VARCHAR(100) NULL AFTER notify_telegram;
