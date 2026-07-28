# 🚀 ServiceSaaS — Guia de Desenvolvimento

**Documentado por:** Paige (Technical Writer)
**Data:** 28 de Julho de 2026

---

## 1. Pré-requisitos

| Ferramenta | Versão Mínima | Verificação |
|:---|---|:---|
| Docker | 24+ | `docker --version` |
| Docker Compose | 2.20+ | `docker compose version` |
| Git | — | `git --version` |
| Node.js | 20 LTS | `node --version` |
| Make | — | `make --version` |

---

## 2. Setup do Ambiente

### 2.1. Primeira Execução

```bash
# 1. Clone o repositório
git clone <repo-url> servicos-flex
cd servicos-flex

# 2. Configure variáveis de ambiente
cp .env.example .env
# Edite .env se necessário (padrão já funciona para dev)

# 3. Suba os containers
make setup
# Equivalente a: docker compose up -d --build

# 4. Verifique se tudo está rodando
docker compose ps
# Todos os 5 containers devem estar "Up"

# 5. Popule com dados de exemplo
make seed

# 6. Acesse
open http://localhost:8080
```

### 2.2. Containers

```bash
# Status
docker compose ps

# Logs de um serviço específico
docker compose logs -f api
docker compose logs -f php
docker compose logs -f nginx

# Reiniciar um serviço
docker compose restart api

# Parar tudo
docker compose down

# Reconstruir e subir
docker compose up -d --build
```

### 2.3. Comandos Make

| Comando | Descrição |
|:---|---|
| `make setup` | Sobe todos os containers |
| `make seed` | Popula banco com dados de exemplo |
| `make logs` | Logs de todos os serviços |
| `make down` | Para todos os containers |
| `make rebuild` | Reconstrói e sobe containers |

---

## 3. Variáveis de Ambiente

### 3.1. `.env` (Desenvolvimento)

```env
# Database
DB_HOST=mysql
DB_USER=servicos
DB_PASS=servicos_pass
DB_NAME=servicos_flex

# JWT
JWT_SECRET=servicos-flex-jwt-secret-dev-2026
JWT_EXPIRES_IN=24h

# Mercado Pago (futuro)
MP_ACCESS_TOKEN=
MP_PUBLIC_KEY=
MP_WEBHOOK_SECRET=

# Email (stub)
SMTP_HOST=
SMTP_PORT=
SMTP_USER=
SMTP_PASS=
```

---

## 4. Estrutura para Desenvolvimento

### 4.1. Criar um Novo Módulo na API

```
api-backend/modules/
└── meu-modulo/
    ├── meu-modulo.controller.js   ← Lógica de negócio
    └── meu-modulo.routes.js       ← Definição de rotas
```

**Registrar no server.js:**
```javascript
const meuModuloRoutes = require('./modules/meu-modulo/meu-modulo.routes');
app.use('/api/v1/meu-modulo', meuModuloRoutes);
```

### 4.2. Criar um Novo Template PHP

```
web-frontend/templates/
└── meu-template.php               ← HTML + PHP
```

**Registrar no index.php:**
```php
$allowedPages['meu-template'] = 'Meu Template';
```

### 4.3. Padrões de Código

```javascript
// API: sempre usar async/await + next(error)
async function list(req, res, next) {
  try {
    const results = await query('SELECT * FROM table WHERE tenant_id = ?', [req.tenantId]);
    res.json({ data: results, correlationId: req.correlationId });
  } catch (err) {
    next(err);
  }
}
```

```php
// Frontend: sempre escapar output
<h1><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h1>

// Chamadas à API via fetch()
fetch('/api/v1/endpoint', {
  headers: { 'Authorization': 'Bearer ' + jwtToken }
})
```

---

## 5. Banco de Dados

### 5.1. ⚠️ Atenção: Tabela `transactions` Não Existe

A tabela `transactions` é **referenciada no código** (`admin.controller.js`, `tenant.middleware.js`) mas **não foi criada** no `init.sql`. Antes de implementar o Epic 5 (Mercado Pago), é necessário:

1. Criar a migration `scripts/migrations/002_create_transactions_table.sql`
2. Executar `docker compose exec -T mysql mysql ... < 002_create_transactions_table.sql`

Consulte o schema recomendado na [Pesquisa Técnica MP](research/technical-mercado-pago-integration-research.md), seção 7.3.

### 5.2. Migrations

As migrations ficam em `scripts/migrations/` e são numeradas:

```
scripts/migrations/
├── 001_add_reset_token_to_users.sql
└── ...
```

**Para criar uma nova migration:**
```sql
-- scripts/migrations/002_add_transactions_table.sql
CREATE TABLE IF NOT EXISTS transactions (
  -- ... schema aqui
);
```

**Para aplicar:**
```bash
docker compose exec -T mysql mysql -u servicos -pservicos_pass servicos_flex < scripts/migrations/002_add_transactions_table.sql
```

### 5.2. Seed

```bash
make seed
# Executa scripts/seed.sql com dados de exemplo
```

### 5.3. Acesso Direto

```bash
# Via phpMyAdmin
open http://localhost:8081

# Via linha de comando
docker compose exec mysql mysql -u servicos -pservicos_pass servicos_flex
```

---

## 6. Testes

### 6.1. Atualmente

```bash
# Não há testes automatizados configurados
# Testes manuais via curl/Postman/navegador
```

### 6.2. Testes via Curl (Exemplo — Windows)

```bash
:: Salvar token em arquivo para evitar problema de aspas no Windows
curl -s -X POST http://localhost:8080/api/v1/auth/login ^
  -H "Content-Type: application/json" ^
  -d "{\"email\":\"maria@beleza.com\",\"password\":\"12345678\"}" > login_response.json
:: Extrair token usando Python
python -c "import json; f=open('login_response.json'); print(json.load(f)['token'])" > token.txt
set /p TOKEN=<token.txt

:: Listar clientes
curl -s http://localhost:8080/api/v1/clients -H "Authorization: Bearer %TOKEN%"
```

---

## 7. Troubleshooting

### 7.1. Container API unhealthy

```bash
# Verificar health check
docker compose ps api

# Logs
docker compose logs api

# Causa comum: banco não pronto ainda (esperar 10s após setup)
# Solução: docker compose restart api
```

### 7.2. Banco sem dados

```bash
make seed
# Verificar: docker compose exec mysql mysql -u servicos -pservicos_pass servicos_flex -e "SELECT COUNT(*) FROM clients"
```

### 7.3. Porta 8080 em uso

```bash
# No docker-compose.yml, altere a porta:
# ports: "8081:80"  (em vez de 8080)
docker compose up -d
```

### 7.4. Permissão de execução

```bash
# No Windows, certificar-se de que os arquivos .sh têm permissão
# No Linux/Mac:
chmod +x scripts/*.sh
```

---

## 8. Roadmap de Desenvolvimento

```
SESSÃO ATUAL
├── 📝 Epic 2: Criar stories de Clientes e Catálogo
├── 📝 Epic 3: Criar stories de Propostas
└── 🏁 Step 4: Validação final de todos os épicos

PRÓXIMA SESSÃO
├── 🔴 Epic 5: Implementar Mercado Pago
│   ├── Story 5.1: Setup SDK + Tabela transactions
│   ├── Story 5.2: Preferência de pagamento
│   ├── Story 5.3: Webhook IPN
│   ├── Story 5.4: QR Code Pix
│   └── Story 5.5: Estorno
├── 🟡 Epic 7: Completar Admin (financeiro + auditoria)
└── 🟢 Testes automatizados + CI/CD
```

---

*Documento gerado por Paige (Technical Writer) em 28 de Julho de 2026*
