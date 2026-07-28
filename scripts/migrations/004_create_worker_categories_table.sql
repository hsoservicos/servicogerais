-- ═══════════════════════════════════════════════════════════════
-- Migration 004 — Worker Categories Reference Table (LC 150)
-- ═══════════════════════════════════════════════════════════════
-- Base Legal: Lei Complementar nº 150/2015, CBO 2026
-- Fonte: Auditoria Hora do Lar (docs/auditoria/AUDITORIA_COMPLIANCE_DOMESTICO.md)
--
-- Esta tabela substitui o ENUM worker_category na tabela workers
-- como fonte de verdade das categorias profissionais. Futuramente,
-- workers.worker_category deve tornar-se FK para esta tabela.

CREATE TABLE IF NOT EXISTS worker_categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(60) NOT NULL COMMENT 'Código da categoria (ex: EMPREGADO_DOMESTICO_GERAL)',
    cbo_code VARCHAR(10) NOT NULL COMMENT 'Código CBO (ex: 5121-05)',
    name VARCHAR(120) NOT NULL COMMENT 'Nome exibível da categoria',
    legal_regime VARCHAR(30) NOT NULL DEFAULT 'LC_150_CLT' COMMENT 'Regime jurídico (LC_150_CLT, AUTONOMO_DIARISTA, AUTONOMO, MEI, RPA, CLT, ESTAGIO, VOLUNTARIO, etc)',
    max_weekly_frequency TINYINT UNSIGNED NULL COMMENT 'Máx. dias/semana (NULL = irrestrito, 2 = diarista)',
    description TEXT NULL COMMENT 'Atividades abrangidas',
    compliance_notes TEXT NULL COMMENT 'Alertas de compliance para o app',
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE INDEX idx_wcat_code (code),
    INDEX idx_wcat_cbo (cbo_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Seed: 9 Categorias Profissionais Domésticas ──────────────
INSERT INTO worker_categories (code, cbo_code, name, legal_regime, max_weekly_frequency, description, compliance_notes) VALUES
('EMPREGADO_DOMESTICO_GERAL', '5121-05', 'Empregada Doméstica Geral',        'LC_150_CLT',       NULL,
 'Limpeza geral, organização, lavar, passar e preparo de refeições triviais.',
 'Risco de acúmulo de função se forem exigidos cuidados especializados de idosos/crianças.'),
('DIARISTA',                   '5121-05', 'Diarista (Autônoma)',              'AUTONOMO_DIARISTA', 2,
 'Serviços esporádicos de limpeza e organização residencial.',
 '⚠️ Risco Crítico: Não ultrapassar 2 dias/semana no mesmo tomador/residência. Bloquear 3º agendamento e oferecer fluxo CLT.'),
('BABA',                       '5162-05', 'Babá / Cuidador Infantil',         'LC_150_CLT',       NULL,
 'Cuidados diretos com crianças, alimentação infantil, higiene e acompanhamento.',
 'Horas extras em viagens; adicionais noturnos e pernoite.'),
('CUIDADOR_IDOSOS',           '5162-10', 'Cuidador de Idosos',                'LC_150_CLT',       NULL,
 'Auxílio em atividades diárias, medicação oral supervisionada e higiene.',
 'Não substitui enfermeiro(a) técnico. Atenção para parametrização de jornada 12x36.'),
('COZINHEIRO',                 '5132-10', 'Cozinheiro(a) Doméstico',           'LC_150_CLT',       NULL,
 'Planejamento de cardápio, preparo de refeições elaboradas e conservação.',
 'Diferenciar do preparo de refeição trivial executado por arrumadeira geral.'),
('MOTORISTA',                  '5151-05', 'Motorista Particular',              'LC_150_CLT',       NULL,
 'Condução de veículo familiar, transporte de dependentes, manutenção básica.',
 'Controle rigoroso do tempo de espera e horas extras em trânsito.'),
('JARDINEIRO',                '6112-05', 'Jardineiro Residencial',            'LC_150_CLT',       NULL,
 'Manutenção de jardins, poda de plantas e adubação residencial.',
 'Apenas em residências sem fins lucrativos. Exigência de EPIs para insalubridade.'),
('CASEIRO',                    '5121-15', 'Caseiro / Zelador de Sítio',        'LC_150_CLT',       NULL,
 'Cuidado contínuo de imóvel residencial/recreativo familiar.',
 'Moradia no local não integra salário. Vedada a exploração comercial do imóvel.'),
('GOVERNANTA',                '5121-10', 'Governanta / Mordomo',              'LC_150_CLT',       NULL,
 'Gestão da rotina doméstica, supervisão de outros empregados, compras.',
 'Cargo de confiança/supervisão; gestão de fundo fixo da residência.');

COMMIT;