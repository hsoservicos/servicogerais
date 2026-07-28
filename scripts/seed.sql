-- ═══════════════════════════════════════════════════════════════
-- seed.sql — Dados de Teste (ServiceSaaS)
-- ═══════════════════════════════════════════════════════════════
-- Uso: make seed
-- NOTA: Execute apenas em ambiente de desenvolvimento!

USE servicos_flex;

-- ── Tenant de Teste ──────────────────────────────────────
INSERT IGNORE INTO tenants (id, name, slug, document_cpf, phone, whatsapp, active, plan)
VALUES (2, 'Maria Beleza Estética', 'maria-beleza', '123.456.789-00', '(11) 99999-0001', '(11) 99999-0001', TRUE, 'free');

-- ── Usuário Admin (senha: 12345678) ──────────────────────
-- Hash bcrypt rounds=12 gerado no container (bcrypt compareSync confirmado)
INSERT IGNORE INTO users (tenant_id, name, email, password_hash, role, active)
VALUES (2, 'Maria Silva', 'maria@beleza.com',
        '$2b$12$lxyfwqL/RnNzj4wSKqCtTOFNfewNt0HBY.L3lOUmoW5CD1Ti0aQwS',
        'admin', TRUE);

-- Limpar dados existentes do tenant 2 para garantir idempotência
DELETE FROM services WHERE tenant_id = 2;
DELETE FROM categories WHERE tenant_id = 2;
DELETE FROM clients WHERE tenant_id = 2;

-- ── Categorias de Exemplo (IDs explícitos 100-103 para referência segura) ──
INSERT INTO categories (id, tenant_id, name, description, icon, color) VALUES
(100, 2, 'Corte de Cabelo', 'Cortes femininos e masculinos', 'scissors', '#10B981'),
(101, 2, 'Manicure & Pedicure', 'Cuidados com unhas', 'sparkles', '#0284C7'),
(102, 2, 'Maquiagem', 'Maquiagem social e profissional', 'brush', '#D97706'),
(103, 2, 'Estética Facial', 'Tratamentos faciais', 'face', '#16A34A');

-- ── Serviços de Exemplo (category_id referenciando IDs 100-103) ──
INSERT INTO services (tenant_id, category_id, name, description, price, duration_minutes) VALUES
(2, 100, 'Corte Feminino', 'Corte personalizado com lavagem e finalização', 65.00, 60),
(2, 100, 'Corte Masculino', 'Corte máquina e tesoura com acabamento', 45.00, 40),
(2, 100, 'Escova', 'Escova modelada com finalizador', 55.00, 50),
(2, 101, 'Manicure Completa', 'Corte, lixa, esmaltação e hidratação', 35.00, 45),
(2, 101, 'Pedicure Completa', 'Corte, lixa, esmaltação e hidratação', 40.00, 50),
(2, 101, 'Unha em Gel', 'Alongamento de unhas em gel', 120.00, 90),
(2, 102, 'Maquiagem Social', 'Maquiagem para eventos e festas', 80.00, 60),
(2, 102, 'Maquiagem Noiva', 'Maquiagem profissional para noivas', 200.00, 90),
(2, 103, 'Limpeza de Pele', 'Limpeza profunda com extração', 90.00, 60),
(2, 103, 'Hidratação Facial', 'Hidratação com ativos específicos', 70.00, 45);

-- ── Clientes de Exemplo ──────────────────────────────────
INSERT IGNORE INTO clients (tenant_id, name, email, phone, whatsapp, city, state, active) VALUES
(2, 'Ana Oliveira', 'ana@email.com', '(11) 98888-0001', '(11) 98888-0001', 'São Paulo', 'SP', TRUE),
(2, 'Carlos Santos', 'carlos@email.com', '(11) 98888-0002', '(11) 98888-0002', 'São Paulo', 'SP', TRUE),
(2, 'Julia Costa', 'julia@email.com', '(11) 98888-0003', '(11) 98888-0003', 'Guarulhos', 'SP', TRUE),
(2, 'Pedro Almeida', 'pedro@email.com', '(11) 98888-0004', NULL, 'Osasco', 'SP', TRUE),
(2, 'Marina Souza', 'marina@email.com', '(11) 98888-0005', '(11) 98888-0005', 'São Paulo', 'SP', TRUE);

-- ═══════════════════════════════════════════════════════════════
-- 🏢 SUPER ADMIN (Epic 7 — Administração da Plataforma)
-- ═══════════════════════════════════════════════════════════════
-- Login: admin@servicesaas.com / 12345678 (mesma hash do seed, rounds=12)
-- Role: super_admin (acesso global sem tenant filter)

INSERT IGNORE INTO users (tenant_id, name, email, password_hash, role, active)
VALUES (1, 'Super Admin', 'admin@servicesaas.com',
        '$2b$12$lxyfwqL/RnNzj4wSKqCtTOFNfewNt0HBY.L3lOUmoW5CD1Ti0aQwS',
        'super_admin', TRUE);

COMMIT;
