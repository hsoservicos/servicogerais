#!/bin/bash
# ═══════════════════════════════════════════════════════════════
# scripts/seed-admin.sh — Seed Super Admin User
# ═══════════════════════════════════════════════════════════════
# Cria um usuário super_admin para acesso ao painel administrativo.
# Uso: bash scripts/seed-admin.sh
#
# Métodos disponíveis:
#   1. SQL — Insere diretamente no MySQL (hash bcrypt conhecido)
#   2. API — Cria via POST /api/v1/auth/register + promove role
# ═══════════════════════════════════════════════════════════════

set -e

# ── Configurações ─────────────────────────────────────────────
ADMIN_NAME="${ADMIN_NAME:-Admin ServiceSaaS}"
ADMIN_EMAIL="${ADMIN_EMAIL:-admin@servicesaas.com.br}"
ADMIN_PASSWORD="${ADMIN_PASSWORD:-admin123}"
ADMIN_PHONE="${ADMIN_PHONE:-(11) 99999-0000}"

# ── 1. Método SQL ─────────────────────────────────────────────
seed_sql() {
    echo "📦 Método SQL: Criando admin user diretamente no MySQL..."

    # Gera hash bcrypt da senha usando Node.js (disponível no container API)
    echo "   🔑 Gerando hash bcrypt para a senha..."
    HASH=$(docker exec flex_api_node node -e "
        const bcrypt = require('bcrypt');
        bcrypt.hash('${ADMIN_PASSWORD}', 12).then(h => console.log(h));
    ")

    if [ -z "$HASH" ]; then
        echo "❌ Falha ao gerar hash bcrypt. Verifique se o container API está rodando."
        echo "   Comando: docker exec flex_api_node node -e \"require('bcrypt').hash('senha',12).then(console.log)\""
        exit 1
    fi
    echo "   ✅ Hash gerado com sucesso"

    # Usando printf em vez de heredoc para evitar que os $ do hash
    # bcrypt sejam interpretados como expansão de variáveis pelo shell.
    printf "INSERT IGNORE INTO tenants (id, name, slug, active, plan)
VALUES (1, 'ServiceSaaS Admin', 'servicesaas', TRUE, 'enterprise');

INSERT INTO users (tenant_id, name, email, password_hash, role, active)
VALUES (1, '%s', '%s', '%s', 'super_admin', TRUE)
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    password_hash = VALUES(password_hash),
    role = 'super_admin',
    active = TRUE;

SELECT id, name, email, role FROM users WHERE email = '%s';" \
      "$ADMIN_NAME" "$ADMIN_EMAIL" "$HASH" "$ADMIN_EMAIL" | \
    docker exec -i flex_mysql mysql -uroot -proot servicos_flex

    echo "✅ Admin criado via SQL!"
    echo "   E-mail: ${ADMIN_EMAIL}"
    echo "   Senha:  ${ADMIN_PASSWORD}"
    echo "   Role:   super_admin"
}

# ── 2. Método API ─────────────────────────────────────────────
seed_api() {
    echo "📦 Método API: Criando admin user via API..."

    # Passo 1: Registrar
    RESPONSE=$(curl -s -X POST http://localhost:8080/api/v1/auth/register \
        -H 'Content-Type: application/json' \
        -d "{
            \"companyName\": \"${ADMIN_NAME}\",
            \"email\": \"${ADMIN_EMAIL}\",
            \"password\": \"${ADMIN_PASSWORD}\",
            \"phone\": \"${ADMIN_PHONE}\"
        }")

    echo "   Resposta: $RESPONSE"

    # Passo 2: Promover para super_admin
    docker exec flex_mysql mysql -uroot -proot servicos_flex \
        -e "UPDATE users SET role = 'super_admin' WHERE email = '${ADMIN_EMAIL}';"

    # Passo 3: Verificar
    RESULT=$(docker exec flex_mysql mysql -uroot -proot servicos_flex \
        -e "SELECT id, name, email, role FROM users WHERE email = '${ADMIN_EMAIL}';" 2>&1)

    echo ""
    echo "✅ Admin criado via API!"
    echo "   $RESULT"
    echo "   Senha:  ${ADMIN_PASSWORD}"
}

# ── 3. Verificar Status ───────────────────────────────────────
check_status() {
    echo "📋 Verificando admin users existentes..."
    docker exec flex_mysql mysql -uroot -proot servicos_flex \
        -e "SELECT id, name, email, role, active FROM users WHERE role = 'super_admin';" 2>&1

    echo ""
    echo "📋 Testando login via API..."
    curl -s -X POST http://localhost:8080/api/v1/auth/login \
        -H 'Content-Type: application/json' \
        -d "{\"email\": \"${ADMIN_EMAIL}\", \"password\": \"${ADMIN_PASSWORD}\"}" | python -c "
import sys, json
try:
    d = json.load(sys.stdin)
    if d.get('token'):
        print('✅ Login OK — Token JWT obtido!')
        print(f'   User: {d.get(\"user\", {}).get(\"name\", \"?\")}')
        print(f'   Role: {d.get(\"user\", {}).get(\"role\", \"?\")}')
    else:
        print(f'❌ Login falhou: {d.get(\"message\", \"Erro desconhecido\")}')
except Exception as e:
    print(f'❌ Erro: {e}')
" 2>&1 || echo "   ⚠️  Python não disponível para verificação. Teste manual: curl -X POST http://localhost:8080/api/v1/auth/login -H 'Content-Type: application/json' -d '{\"email\":\"${ADMIN_EMAIL}\",\"password\":\"${ADMIN_PASSWORD}\"}'"
}

# ── Main ──────────────────────────────────────────────────────
echo "═══════════════════════════════════════════════════════"
echo "  🔐 Seed Super Admin — ServiceSaaS"
echo "═══════════════════════════════════════════════════════"
echo ""

case "${1:-all}" in
    sql)
        seed_sql
        ;;
    api)
        seed_api
        ;;
    check)
        check_status
        ;;
    all)
        seed_api
        echo ""
        echo "────────── Verificação ──────────"
        check_status
        ;;
    *)
        echo "Uso: bash scripts/seed-admin.sh [sql|api|check|all]"
        echo ""
        echo "  sql   — Insere direto no MySQL"
        echo "  api   — Cria via API + promove role"
        echo "  check — Verifica admin existentes + testa login"
        echo "  all   — API + verificação (padrão)"
        exit 1
        ;;
esac
