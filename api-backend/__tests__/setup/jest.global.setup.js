const mysql = require('mysql2');
const path = require('path');
const fs = require('fs');

const TEST_DB = process.env.TEST_DB_NAME || 'servicos_flex_test';

module.exports = async function globalSetup() {
  const host = process.env.TEST_DB_HOST || process.env.DB_HOST || 'localhost';
  const port = parseInt(process.env.TEST_DB_PORT || process.env.DB_PORT, 10) || 3306;
  const user = process.env.TEST_DB_USER || process.env.DB_USER || 'root';
  const password = process.env.TEST_DB_PASSWORD || process.env.DB_PASSWORD || 'root';

  const conn = mysql.createConnection({ host, port, user, password, multipleStatements: true });
  const connect = () => new Promise((resolve, reject) => {
    conn.connect((err) => { if (err) reject(err); else resolve(); });
  });
  const queryPromise = (sql) => new Promise((resolve, reject) => {
    conn.query(sql, (err, result) => { if (err) reject(err); else resolve(result); });
  });

  await connect();
  await queryPromise(`DROP DATABASE IF EXISTS \`${TEST_DB}\``);
  await queryPromise(`CREATE DATABASE \`${TEST_DB}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci`);

  const initSqlPath = path.resolve(__dirname, '../../../scripts/init.sql');
  let initSql = fs.readFileSync(initSqlPath, 'utf8');
  initSql = initSql
    .replace(/CREATE DATABASE IF NOT EXISTS servicos_flex/g, 'CREATE DATABASE IF NOT EXISTS `' + TEST_DB + '`')
    .replace(/USE servicos_flex;/g, 'USE `' + TEST_DB + '`;');
  await queryPromise(initSql);

  const migrationSql = `
    ALTER TABLE users ADD COLUMN reset_token VARCHAR(255) NULL;
    ALTER TABLE users ADD COLUMN reset_token_expires DATETIME NULL;
    ALTER TABLE tenants ADD COLUMN zipcode VARCHAR(9) NULL;
    ALTER TABLE tenants ADD COLUMN address VARCHAR(500) NULL;
    ALTER TABLE tenants ADD COLUMN neighborhood VARCHAR(100) NULL;
    ALTER TABLE tenants ADD COLUMN city VARCHAR(100) NULL;
    ALTER TABLE tenants ADD COLUMN state CHAR(2) NULL;
    ALTER TABLE tenants ADD COLUMN latitude DECIMAL(10,8) NULL;
    ALTER TABLE tenants ADD COLUMN longitude DECIMAL(11,8) NULL;
    ALTER TABLE clients ADD COLUMN zipcode VARCHAR(9) NULL;
    ALTER TABLE clients ADD COLUMN notify_email BOOLEAN DEFAULT TRUE;
    ALTER TABLE clients ADD COLUMN notify_whatsapp BOOLEAN DEFAULT TRUE;
    ALTER TABLE clients ADD COLUMN notify_telegram BOOLEAN DEFAULT FALSE;
    ALTER TABLE clients ADD COLUMN telegram_chat_id VARCHAR(100) NULL;
    ALTER TABLE tenants ADD COLUMN notify_email BOOLEAN DEFAULT TRUE;
    ALTER TABLE tenants ADD COLUMN notify_whatsapp BOOLEAN DEFAULT TRUE;
    ALTER TABLE tenants ADD COLUMN notify_telegram BOOLEAN DEFAULT FALSE;
    ALTER TABLE tenants ADD COLUMN telegram_chat_id VARCHAR(100) NULL;

    CREATE TABLE IF NOT EXISTS workers (
      id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      tenant_id INT UNSIGNED NOT NULL,
      name VARCHAR(255) NOT NULL, email VARCHAR(255) NULL,
      cpf VARCHAR(14) NOT NULL, rg VARCHAR(20) NULL,
      cbo_code VARCHAR(10) NOT NULL,
      worker_category ENUM('EMPREGADO_DOMESTICO_GERAL','DIARISTA','BABA','CUIDADOR_IDOSOS','COZINHEIRO','MOTORISTA','JARDINEIRO','CASEIRO','GOVERNANTA') NOT NULL,
      phone VARCHAR(20) NULL, whatsapp VARCHAR(20) NULL, pix_key VARCHAR(100) NULL, address JSON NULL,
      avatar_url VARCHAR(500) NULL,
      background_check_status ENUM('PENDING','APPROVED','REJECTED') DEFAULT 'PENDING',
      background_check_date TIMESTAMP NULL, background_check_provider VARCHAR(100) NULL, active BOOLEAN DEFAULT TRUE,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      UNIQUE INDEX idx_workers_cpf (cpf), INDEX idx_workers_tenant (tenant_id),
      CONSTRAINT fk_workers_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;

    CREATE TABLE IF NOT EXISTS worker_certifications (
      id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      worker_id INT UNSIGNED NOT NULL,
      certification_type ENUM('CUIDADOR_IDOSOS','APH','BABA','COZINHA','JARDINAGEM','PRIMEIROS_SOCORROS','OUTRO') NOT NULL,
      title VARCHAR(255) NOT NULL, issuer VARCHAR(255) NULL,
      issue_date DATE NULL, expiry_date DATE NULL, document_url VARCHAR(500) NULL,
      verified BOOLEAN DEFAULT FALSE, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      INDEX idx_cert_worker (worker_id),
      CONSTRAINT fk_cert_worker FOREIGN KEY (worker_id) REFERENCES workers(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;

    CREATE TABLE IF NOT EXISTS service_schedules (
      id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      tenant_id INT UNSIGNED NOT NULL, worker_id INT UNSIGNED NOT NULL, client_id INT UNSIGNED NOT NULL,
      service_category ENUM('EMPREGADO_DOMESTICO_GERAL','DIARISTA','BABA','CUIDADOR_IDOSOS','COZINHEIRO','MOTORISTA','JARDINEIRO','CASEIRO','GOVERNANTA') NOT NULL,
      regime ENUM('AUTONOMO_DIARISTA','LC_150_CLT') NOT NULL,
      scheduled_date DATE NOT NULL, start_time TIME NULL, end_time TIME NULL,
      status ENUM('scheduled','confirmed','in_progress','completed','cancelled') DEFAULT 'scheduled',
      hourly_rate DECIMAL(10,2) NULL, total_amount DECIMAL(10,2) NULL, transport_voucher DECIMAL(10,2) DEFAULT 0.00, notes TEXT NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      INDEX idx_sched_tenant (tenant_id), INDEX idx_sched_worker (worker_id), INDEX idx_sched_client (client_id),
      INDEX idx_sched_date (scheduled_date), INDEX idx_sched_status (status),
      CONSTRAINT fk_sched_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
      CONSTRAINT fk_sched_worker FOREIGN KEY (worker_id) REFERENCES workers(id) ON DELETE CASCADE,
      CONSTRAINT fk_sched_client FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;

    CREATE TABLE IF NOT EXISTS worker_categories (
      id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      code VARCHAR(60) NOT NULL, cbo_code VARCHAR(10) NOT NULL, name VARCHAR(120) NOT NULL,
      legal_regime VARCHAR(30) NOT NULL DEFAULT 'LC_150_CLT',
      max_weekly_frequency TINYINT UNSIGNED NULL,
      description TEXT NULL, compliance_notes TEXT NULL, active BOOLEAN DEFAULT TRUE,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      UNIQUE INDEX idx_wcat_code (code)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    INSERT IGNORE INTO worker_categories (code, cbo_code, name, legal_regime, max_weekly_frequency) VALUES
    ('EMPREGADO_DOMESTICO_GERAL','5121-05','Empregada Domestica Geral','LC_150_CLT',NULL),
    ('DIARISTA','5121-05','Diarista (Autonoma)','AUTONOMO_DIARISTA',2),
    ('BABA','5162-05','Baba','LC_150_CLT',NULL),
    ('CUIDADOR_IDOSOS','5162-10','Cuidador de Idosos','LC_150_CLT',NULL),
    ('COZINHEIRO','5132-10','Cozinheiro(a)','LC_150_CLT',NULL),
    ('MOTORISTA','5151-05','Motorista','LC_150_CLT',NULL),
    ('JARDINEIRO','6112-05','Jardineiro','LC_150_CLT',NULL),
    ('CASEIRO','5121-15','Caseiro','LC_150_CLT',NULL),
    ('GOVERNANTA','5121-10','Governanta','LC_150_CLT',NULL);

    CREATE TABLE IF NOT EXISTS plans (
      id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      slug VARCHAR(50) NOT NULL UNIQUE, name VARCHAR(100) NOT NULL,
      description VARCHAR(500) NULL, price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
      limits JSON NULL, features JSON NULL, active BOOLEAN DEFAULT TRUE, sort_order INT DEFAULT 0,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;

    INSERT IGNORE INTO plans (slug, name, description, price, limits, features, sort_order) VALUES
    ('free','Gratis','Para quem esta comecando',0.00,'{"max_clients":10,"max_proposals":5,"max_users":1,"max_services":10}','["clientes","propostas","catalogo","whatsapp"]',1),
    ('basic','Basico','Para profissionais autonomos',29.90,'{"max_clients":50,"max_proposals":30,"max_users":2,"max_services":30}','["clientes","propostas","catalogo","whatsapp","relatorios"]',2),
    ('pro','Profissional','Para pequenas empresas',79.90,'{"max_clients":200,"max_proposals":100,"max_users":5,"max_services":100}','["clientes","propostas","catalogo","whatsapp","relatorios","dashboard_avancado","api"]',3),
    ('enterprise','Enterprise','Para grandes operacoes',199.90,'{"max_clients":-1,"max_proposals":-1,"max_users":20,"max_services":-1}','["clientes","propostas","catalogo","whatsapp","relatorios","dashboard_avancado","api","suporte_premium","personalizacao"]',4);

    CREATE TABLE IF NOT EXISTS domestic_agreements (
      id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      worker_id INT UNSIGNED NOT NULL, tenant_id INT UNSIGNED NOT NULL,
      contract_type ENUM('CLT_DOMESTICO') NOT NULL DEFAULT 'CLT_DOMESTICO',
      regime ENUM('AUTONOMO_DIARISTA','LC_150_CLT') NOT NULL DEFAULT 'LC_150_CLT',
      status ENUM('ACTIVE','TERMINATED','SUSPENDED') NOT NULL DEFAULT 'ACTIVE',
      salary DECIMAL(10,2) NOT NULL, transport_voucher DECIMAL(10,2) DEFAULT 0.00,
      food_allowance DECIMAL(10,2) DEFAULT 0.00, weekly_frequency_days TINYINT DEFAULT 5,
      start_date DATE NOT NULL, termination_date DATE NULL, termination_reason VARCHAR(500) NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      UNIQUE INDEX idx_agreement_worker (worker_id), INDEX idx_agreement_tenant (tenant_id),
      CONSTRAINT fk_agreement_worker FOREIGN KEY (worker_id) REFERENCES workers(id) ON DELETE CASCADE,
      CONSTRAINT fk_agreement_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;

    CREATE TABLE IF NOT EXISTS incidents (
      id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      tenant_id INT UNSIGNED NOT NULL,
      worker_id INT UNSIGNED NULL,
      type ENUM('ACCIDENT','EMERGENCY','DAMAGE','HEALTH','SECURITY','OTHER') NOT NULL,
      severity ENUM('LOW','MEDIUM','HIGH','CRITICAL') DEFAULT 'MEDIUM',
      status ENUM('OPEN','INVESTIGATING','RESOLVED','CLOSED') DEFAULT 'OPEN',
      description TEXT NOT NULL,
      gps_latitude DECIMAL(10,8) NULL, gps_longitude DECIMAL(11,8) NULL,
      occurred_at DATETIME NULL, protocol VARCHAR(30) NOT NULL UNIQUE,
      cat_number VARCHAR(30) NULL, cat_type VARCHAR(20) NULL,
      cat_issuing_agency VARCHAR(100) NULL, cat_issued_at DATETIME NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      INDEX idx_inc_tenant (tenant_id), INDEX idx_inc_status (status),
      INDEX idx_inc_type (type), INDEX idx_inc_severity (severity),
      CONSTRAINT fk_inc_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
      CONSTRAINT fk_inc_worker FOREIGN KEY (worker_id) REFERENCES workers(id) ON DELETE SET NULL
    ) ENGINE=InnoDB;
  `;
  await queryPromise(migrationSql);

  conn.end();
  console.log(`[TEST] Banco de testes '${TEST_DB}' criado.`);
};
