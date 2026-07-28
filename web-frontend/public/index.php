<?php
/**
 * ═══════════════════════════════════════════════════════════════
 * public/index.php — Entry Point (ServiceSaaS Frontend)
 * ═══════════════════════════════════════════════════════════════
 * Roteamento básico via query string
 */

require_once __DIR__ . '/../config/app.php';

// ── Action Handler: Store Token (via AJAX do login/register) ──
$action = $_GET['action'] ?? null;

if ($action === 'store-token' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['token'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Token não fornecido']);
        exit;
    }
    
    setToken($input['token']);
    
    if (isset($input['user'])) {
        $_SESSION['tenant_id'] = $input['user']['tenantId'] ?? null;
        $_SESSION['user'] = $input['user'];
    }
    
    echo json_encode(['success' => true, 'message' => 'Token armazenado na sessão']);
    exit;
}

// ── Page Routing ────────────────────────────────────────────────
$page = $_GET['page'] ?? 'home';

$allowedPages = [
    'home', 'login', 'register', 'dashboard', 'clients', 'categories',
    'services', 'proposals', 'leads', 'workers', 'transactions', 'forgot-password', 'reset-password', 'logout',
    // Epic 6 — Landing
    'solicitar', 'public-proposal',
    // Epic 7 — Admin
    'admin-login', 'admin-dashboard', 'admin-tenants',
    'admin-financeiro', 'admin-audit',
    // Epic 9 — Profile / Settings
    'tenant-profile',
];
$page = in_array($page, $allowedPages) ? $page : 'home';

// ── Logout handler (before any output!) ────────────────────────
if ($page === 'logout') {
    clearToken();
    header('Location: ?page=home');
    exit;
}

$pageTitle = match ($page) {
    'home'      => APP_NAME . ' — Sua plataforma de serviços',
    'login'     => 'Login — ' . APP_NAME,
    'register'  => 'Cadastro — ' . APP_NAME,
    'dashboard' => 'Dashboard — ' . APP_NAME,
    'clients'   => 'Clientes — ' . APP_NAME,
    'categories' => 'Categorias — ' . APP_NAME,
    'services'  => 'Serviços — ' . APP_NAME,
    'proposals' => 'Propostas — ' . APP_NAME,
    'leads' => 'Leads — ' . APP_NAME,
    'workers'   => 'Trabalhadores — ' . APP_NAME,
    'transactions' => 'Financeiro — ' . APP_NAME,
    'solicitar' => 'Solicitar Orçamento — ' . APP_NAME,
    'public-proposal' => 'Proposta — ' . APP_NAME,
    'forgot-password' => 'Recuperar Senha — ' . APP_NAME,
    'reset-password'  => 'Redefinir Senha — ' . APP_NAME,
    'logout'    => 'Saindo... — ' . APP_NAME,
    // Epic 7 — Admin
    'admin-login'     => 'Admin Login — ' . APP_NAME,
    'admin-dashboard' => 'Admin Dashboard — ' . APP_NAME,
    'admin-tenants'   => 'Admin Tenants — ' . APP_NAME,
    'admin-financeiro' => 'Admin Financeiro — ' . APP_NAME,
    'admin-audit'     => 'Admin Auditoria — ' . APP_NAME,
    // Epic 9 — Profile
    'tenant-profile'  => 'Meu Perfil — ' . APP_NAME,
};

require_once __DIR__ . '/../templates/partials/header.php';

$pageFile = __DIR__ . "/../templates/{$page}.php";
if (file_exists($pageFile)) {
    require_once $pageFile;
} else {
    require_once __DIR__ . '/../templates/home.php';
}

require_once __DIR__ . '/../templates/partials/footer.php';
