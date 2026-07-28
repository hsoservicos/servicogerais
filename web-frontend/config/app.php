<?php
/**
 * ═══════════════════════════════════════════════════════════════
 * config/app.php — Application Configuration (ServiceSaaS)
 * ═══════════════════════════════════════════════════════════════
 */

// ── Environment ──────────────────────────────────────────
define('ENV', getenv('APP_ENV') ?: 'development');
define('DEBUG', ENV === 'development');
define('APP_NAME', 'ServiceSaaS');

// ── API ──────────────────────────────────────────────────
// URL relativa via nginx (browser acessa localhost:8080/api/v1/...)
// Em produção, alterar para o domínio real: https://api.seudominio.com/api/v1
define('API_BASE_URL', getenv('API_BASE_URL') ?: '/api/v1');
define('API_TIMEOUT', 30);

// ── Session ──────────────────────────────────────────────
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', ENV === 'production');
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.use_strict_mode', 1);
ini_set('session.gc_maxlifetime', 86400); // 24h

session_start();

// ── Error Reporting ─────────────────────────────────────
if (DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// ── Funções Auxiliares ──────────────────────────────────

/**
 * Obtém o token JWT da sessão PHP
 */
function getToken(): ?string {
    return $_SESSION['jwt'] ?? null;
}

/**
 * Define o token JWT na sessão (após login/register)
 */
function setToken(string $token): void {
    $_SESSION['jwt'] = $token;
}

/**
 * Remove o token da sessão (logout)
 */
function clearToken(): void {
    unset($_SESSION['jwt']);
    session_destroy();
}

/**
 * Verifica se o usuário está autenticado
 */
function isAuthenticated(): bool {
    return isset($_SESSION['jwt']);
}

/**
 * Retorna o ID do tenant da sessão
 */
function getTenantId(): ?string {
    return $_SESSION['tenant_id'] ?? null;
}

/**
 * Retorna dados do usuário da sessão
 */
function getUser(): ?array {
    if (!isset($_SESSION['user'])) {
        return null;
    }
    return $_SESSION['user'];
}
