> **📦 ARQUIVADO** — Este documento foi substituído pelo `docs/planning/PLANEJAMENTO_MODERNO_PROJETO.md` (v2.0).
> **Data de arquivamento:** 27 de Julho de 2026
> **Motivo:** Documentos consolidados em plano estratégico único.
> **Consultar:** `docs/planning/PLANEJAMENTO_MODERNO_PROJETO.md` para versão atual.

# Documento de Requisitos do Produto (PRD) — Serviços Flex (PHP + Node.js API + Cloudflare Tunnel)

**Versão:** 3.0  
**Data:** 27 de Julho de 2026  
**Projeto:** Serviços Flex  
**Status:** Atualizado para Arquitetura Microsserviços/Containers Isolados com Cloudflare Tunnel  

---

## 1. Visão Geral do Produto

O **Serviços Flex** é uma plataforma SaaS web responsiva focada em profissionais autônomos, liberais e pequenas empresas [cite: 2]. O objetivo é simplificar e acelerar a criação, gestão, envio (via WhatsApp) e aprovação de orçamentos e propostas comerciais [cite: 2].

Nesta versão da arquitetura, cada componente da aplicação roda de forma **100% isolada e desacoplada em containers Docker dedicados**, facilitando atualizações zero-downtime, manutenção modular e substituição de serviços. A exposição e publicação web é realizada de forma segura através do **Cloudflare Tunnel (cloudflared)**, dispensando a abertura pública de portas (80/443) no firewall ou IP estático no servidor hospedeiro.

---

## 2. Requisitos Técnicos & Arquitetura em Containers Isolados

A aplicação é dividida nos seguintes serviços isolados:

1. **`flex_cloudflared` (Túnel Cloudflare):** Mantém a conexão criptografada de saída com a rede edge da Cloudflare, gerenciando roteamento e certificados SSL automaticamente.
2. **`flex_nginx` (Servidor Web / Gateway Frontend):** Servidor Nginx atuando como proxy reverso interno para rotear requisições entre o frontend e a API.
3. **`flex_frontend_php` (Camada Web de Apresentação):** Container PHP-FPM 8.2 executando as páginas HTML/PHP, formulários e lógica de apresentação [cite: 2].
4. **`flex_api_node` (Backend & API REST):** Container Node.js (Express.js) que executa a API REST, regras de negócio, geração nativa de PDF (`puppeteer`/`pdfkit`) e isolamento de Multi-tenancy [cite: 2].
5. **`flex_phpmyadmin` (Gerenciador do BD):** Container isolado para administração gráfica do banco de dados MySQL [cite: 2].
6. **`MySQL Externo`:** Banco de dados MySQL 8.0 rodando na máquina hospedeira (ou container dedicado de BD na rede interna do Docker) [cite: 2].

---

## 3. Diretrizes de Design & UX/UI

* **Paleta de Cores:**
  * Primária (Ação/Destaque): Azul (`#2563EB`) [cite: 2]
  * Fundo das Telas: Azul Claro (`#F0F7FF`) [cite: 2]
  * Ação Direta (WhatsApp / Aprovação): Verde (`#16A34A`) [cite: 2]
  * Sidebar e Navegação: Cinza Claro (`#F3F4F6`) [cite: 2]
  * Botões Secundários: Branco (`#FFFFFF`) [cite: 2]
* **Tipografia:** Fonte **Poppins** em toda a aplicação [cite: 2].
* **Layout:** Sidebar fixa à esquerda contendo a logo no topo e itens de navegação [cite: 2]. No mobile, a sidebar oculta-se e é acionada por botão hambúrguer no cabeçalho (`menu.js`) [cite: 2].
* **Componentes de UI:**
  * Calendário nativo em todos os campos de data, fechando após a seleção [cite: 2].
  * Ícones modernos em todos os botões e ações [cite: 2].
  * Botão de WhatsApp direcionando para o link da API do WhatsApp com mensagem pré-formatada [cite: 2].
* **Formatos Nacionais:**
  * Moeda: `R$ 0.000,00` [cite: 2] | Datas: `dd/mm/yyyy` [cite: 2]
  * Telefone Fixo: `(XX) XXXX-XXXX` [cite: 2] | Celular: `(XX) XXXXX-XXXX` [cite: 2]
  * CPF: `XXX.XXX.XXX-XX` [cite: 2] | CNPJ: `XXX.XXX.XXX/XXXX-XX` [cite: 2]

---

## 4. Regras de Negócio & Validações de Segurança

* **Publicação Segura via Cloudflare Tunnel:**
  * Proteção nativa contra ataques DDoS e ocultação do IP real do servidor.
  * Certificados HTTPS/SSL gerenciados de forma automatizada na borda da Cloudflare.
* **Isolamento Multi-tenant Estrito:** Validação por `tenant_id` realizada na API REST em Node.js [cite: 2].
* **Unicidade de Registros:** Proibida duplicidade de CPF, CNPJ ou E-mail [cite: 2].
* **Exclusão Lógica e Propostas:** Status `Excluida` preservando histórico na base [cite: 2]. Links públicos abertos permitem visualização e ações de Aprovar/Reprovar via API [cite: 2].

---

# Estrutura Modular do Projeto (Arquivos e Containers)

```text
servicos-flex/
├── docker-compose.yml         # Orquestração de todos os containers isolados
├── .env                       # Variáveis de ambiente (Tokens do Cloudflare, BD, etc.)
├── nginx/
│   └── default.conf           # Roteamento interno entre Frontend PHP e API Node.js
├── web-frontend/              # Container PHP-FPM / HTML / JS
│   ├── Dockerfile
│   └── public/
│       ├── index.php          # Landing Page
│       ├── login.php
│       ├── dashboard.php
│       ├── propostas.php
│       └── js/menu.js
└── api-backend/               # Container API REST Node.js
    ├── Dockerfile
    ├── package.json
    ├── server.js
    └── services/pdfService.js
```

---

# Arquivo de Orquestração Docker (`docker-compose.yml`)

```yaml
services:
  # 1. Túnel Cloudflare (Acesso seguro sem abertura de portas)
  cloudflared:
    image: cloudflare/cloudflared:latest
    container_name: flex_cloudflared
    restart: always
    command: tunnel --no-autoupdate run
    environment:
      - TUNNEL_TOKEN=${CLOUDFLARE_TUNNEL_TOKEN}
    networks:
      - flex-network
    depends_on:
      - nginx

  # 2. Servidor Web Nginx (Gateway Interno)
  nginx:
    image: nginx:alpine
    container_name: flex_nginx
    restart: always
    volumes:
      - ./nginx/default.conf:/etc/nginx/conf.d/default.conf:ro
      - ./web-frontend/public:/var/www/html:ro
    depends_on:
      - web-app
      - api
    networks:
      - flex-network

  # 3. Frontend PHP-FPM (Unidade Isolada)
  web-app:
    build: ./web-frontend
    container_name: flex_frontend_php
    restart: always
    environment:
      - API_URL=http://api:3000/api/v1
    volumes:
      - ./web-frontend/public:/var/www/html
    networks:
      - flex-network

  # 4. API REST Node.js / Express (Unidade Isolada)
  api:
    build: ./api-backend
    container_name: flex_api_node
    restart: always
    environment:
      - NODE_ENV=production
      - PORT=3000
      - DB_HOST=172.17.0.1
      - DB_USER=${DB_USER}
      - DB_PASS=${DB_PASS}
      - DB_NAME=${DB_NAME}
      - JWT_SECRET=${JWT_SECRET}
    networks:
      - flex-network

  # 5. phpMyAdmin (Gerenciador do BD Isolado)
  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    container_name: flex_phpmyadmin
    restart: always
    ports:
      - "8080:80" # Acesso local/VPN para administração
    environment:
      PMA_HOST: 172.17.0.1
      PMA_PORT: 3306
    networks:
      - flex-network

networks:
  flex-network:
    driver: bridge
```

---

# Configuração do Proxy Nginx (`nginx/default.conf`)

```nginx
server {
    listen 80;
    server_name _;

    root /var/www/html;
    index index.php index.html;

    # Roteamento do Frontend PHP
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Processamento PHP-FPM no container 'web-app'
    location ~ \.php$ {
        fastcgi_pass web-app:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME /var/www/html$fastcgi_script_name;
        include fastcgi_params;
    }

    # Proxy para a API REST Node.js no container 'api'
    location /api/ {
        proxy_pass http://api:3000/api/;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_cache_bypass $http_upgrade;
    }
}
```

---

# Guia Completo de Deploy via Cloudflare Tunnel e Docker

### Passo 1: Preparar o Servidor Linux (Debian/Ubuntu)
```bash
# Atualizar sistema e instalar Docker
sudo apt update && sudo apt upgrade -y
curl -fsSL https://get.docker.com | bash
sudo systemctl enable --now docker
sudo apt install -y docker-compose-plugin git
```

### Passo 2: Criar e Configurar o Túnel no Cloudflare Zero Trust
1. Acesse o painel **Cloudflare Zero Trust** (`dash.cloudflare.com`).
2. Vá em **Networks > Tunnels** e clique em **Create a Tunnel**.
3. Escolha o nome do túnel (ex: `servicos-flex-tunnel`).
4. Na tela de instalação, copie o **TOKEN** gerado para o túnel.
5. Na aba **Public Hostnames**, configure o direcionamento:
   * **Subdomain / Domain:** `app.seudominio.com.br`
   * **Service:** `HTTP`
   * **URL:** `flex_nginx:80` *(Nome do container Nginx no Docker)*

### Passo 3: Configurar as Variáveis de Ambiente (`.env`)
No diretório raiz do projeto no servidor `/var/www/servicos-flex`, crie o arquivo `.env`:

```env
CLOUDFLARE_TUNNEL_TOKEN=seu_token_gerado_no_cloudflare_aqui
DB_USER=flex_user
DB_PASS=sua_senha_mysql
DB_NAME=servicos_flex
JWT_SECRET=sua_chave_secreta_jwt_super_segura
```

### Passo 4: Subir os Containers Isolados
```bash
cd /var/www/servicos-flex

# Build e execução em segundo plano
docker compose up -d --build
```

### Passo 5: Vantagens de Manutenção e Atualizações Independentes
Com essa arquitetura isolada, você pode atualizar ou reiniciar módulos individualmente sem derrubar toda a aplicação:

* **Atualizar apenas o Frontend PHP:**
  ```bash
  docker compose restart web-app
  ```
* **Atualizar apenas a API Node.js:**
  ```bash
  docker compose build api && docker compose up -d api
  ```
* **Verificar logs do Túnel Cloudflare:**
  ```bash
  docker compose logs -f cloudflared
  ```
