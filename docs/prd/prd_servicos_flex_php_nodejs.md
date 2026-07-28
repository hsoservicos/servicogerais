> **📦 ARQUIVADO** — Este documento foi substituído pelo `docs/planning/PLANEJAMENTO_MODERNO_PROJETO.md` (v2.0).  
> **Data de arquivamento:** 27 de Julho de 2026  
> **Motivo:** Documentos consolidados em plano estratégico único.  
> **Consultar:** `docs/planning/PLANEJAMENTO_MODERNO_PROJETO.md` para versão atual.

# Documento de Requisitos do Produto (PRD) — Serviços Flex (PHP + Node.js REST API)

**Versão:** 2.0  
**Data:** 27 de Julho de 2026  
**Projeto:** Serviços Flex  
**Status:** Atualizado para Frontend PHP e API Node.js / Express  

---

## 1. Visão Geral do Produto

O **Serviços Flex** é uma plataforma SaaS web responsiva focada em profissionais autônomos, liberais e pequenas empresas [cite: 2]. O objetivo é simplificar e acelerar a criação, gestão, envio (via WhatsApp) e aprovação de orçamentos e propostas comerciais [cite: 2].

Nesta nova arquitetura, o Frontend/Camada Web de apresentação é desenvolvida com **PHP 8.2**, **HTML5**, **CSS3** e **JavaScript ES6+**, enquanto o Backend e as regras de negócio residem em uma **API REST em Node.js (Express)**.

---

## 2. Requisitos Técnicos & Infraestrutura

* **Ambiente de Execução:** Containers Linux Debian/Ubuntu (Docker) separando a camada Web (PHP + Nginx) e a API (Node.js).
* **Servidor Web / Proxy:** Nginx atuando como proxy reverso para PHP-FPM e Node.js.
* **Frontend Web:** PHP 8.2 (Renderização de views/templates e consumo da API via `cURL` / `Guzzle`), HTML5, CSS3, JavaScript.
* **API REST:** Node.js (Express.js) gerenciando regras de negócio, rotas da API, geração de PDF e persistência no BD.
* **Banco de Dados:** MySQL 8.0 externo aos containers da aplicação [cite: 2].
* **Gerenciamento do BD:** phpMyAdmin em container auxiliar [cite: 2].
* **Autenticação:** Tokens JWT (JSON Web Tokens) gerenciados pela API e mantidos na sessão PHP do usuário.
* **Geração de Documentos:** Geração de PDF nativo via Node.js (`pdfkit` / `puppeteer`) servido pela API.

---

## 3. Diretrizes de Design & UX/UI

* **Paleta de Cores:**
  * Primária (Ação/Destaque): Azul (`#2563EB`) [cite: 2]
  * Fundo das Telas: Azul Claro (`#F0F7FF`) [cite: 2]
  * Ação Direta (WhatsApp / Aprovação): Verde (`#16A34A`) [cite: 2]
  * Sidebar e Navegação: Cinza Claro (`#F3F4F6`) [cite: 2]
  * Botões Secundários: Branco (`#FFFFFF`) [cite: 2]
* **Tipografia:** Fonte **Poppins** em toda a aplicação [cite: 2].
* **Layout:** Sidebar fixa à esquerda contendo a logo no topo e itens de navegação [cite: 2]. No mobile, a sidebar oculta-se e é acionada por botão hambúrguer no cabeçalho (script em `menu.js`) [cite: 2].
* **Componentes de UI:**
  * Calendário nativo em todos os campos de data, fechando após a seleção [cite: 2].
  * Ícones modernos em todos os botões e ações [cite: 2].
  * Botão de WhatsApp direcionando para o link da API do WhatsApp com mensagem pré-formatada [cite: 2].
* **Formatos Nacionais:**
  * Moeda: `R$ 0.000,00` [cite: 2]
  * Datas: `dd/mm/yyyy` [cite: 2]
  * Telefone Fixo: `(XX) XXXX-XXXX` [cite: 2]
  * Telefone Celular: `(XX) XXXXX-XXXX` [cite: 2]
  * CPF: `XXX.XXX.XXX-XX` [cite: 2]
  * CNPJ: `XXX.XXX.XXX/XXXX-XX` [cite: 2]

---

## 4. Regras de Negócio & Validações de Segurança

* **Multi-tenancy Isolation:** Validação rigorosa por `tenant_id` em todos os endpoints da API REST em Node.js [cite: 2].
* **Unicidade de Contas:** Proibida duplicidade de CPF, CNPJ ou E-mail na base de dados [cite: 2].
* **Validação de Cadastro:** Formato de e-mail e máscaras dinâmicas no frontend (JavaScript) para CPF/CNPJ e telefones [cite: 2].
* **Política de Senhas:** Mínimo de 8 caracteres alfanuméricos [cite: 2].
* **Exclusão Lógica de Propostas:** Propostas excluídas alteram o status para `Excluida`, mantendo o histórico no banco de dados [cite: 2].
* **Visualização Pública:** Links públicos de propostas consomem endpoint aberto da API Node.js para exibir os itens e permitir Aprovar/Reprovar [cite: 2]. Propostas em rascunho não mostram o status na página pública [cite: 2].

---

## 5. Mapeamento de Módulos & Funcionalidades

### Área Pública (Não Logada)
1. **Criação de Conta (`register.php`):** Formulário dinâmico para Pessoa Física ou Jurídica com validação JS e envio via POST para a API REST [cite: 2].
2. **Login (`login.php`):** Autenticação que consome a API REST Node.js e salva o Token JWT na sessão PHP do usuário [cite: 2].
3. **Página Pública da Proposta (`proposta_publica.php`):** Renderização limpa com botões de Aprovado/Reprovado acionando endpoints da API [cite: 2].
4. **Landing Page para Clientes (`index.php`):** Vitrine e Wizard de solicitação com busca direta na API Node.js [cite: 2].

### Área Privada (Logada)
1. **Painel Gerencial / Dashboard (`dashboard.php`):**
   * Indicadores em cards (Total Clientes, Qtd. Propostas, Aprovadas, Pendentes, Excluídas) [cite: 2].
   * Gráfico financeiro dos últimos 6 meses (Chart.js consumindo dados JSON da API REST) [cite: 2].
   * Listas de acompanhamento (Follow-up do dia com botão WhatsApp, Propostas Pendentes e Excluídas) [cite: 2].
2. **Cadastro de Clientes (`clientes.php`):** CRUD via chamadas AJAX (JS) para a API Node.js [cite: 2].
3. **Cadastro de Produtos e Serviços (`produtos_servicos.php`):** Gerenciamento com campos formatados em Reais (`R$`) [cite: 2].
4. **Gerenciador de Propostas (`proposta_form.php`):**
   * Funcionamento Mestre-Detalhe manipulado em memória no cliente via JavaScript [cite: 2].
   * Adição e exclusão de itens sem refresh da página [cite: 2].
   * Envio final payload JSON único para a API Node.js para gravação atômica [cite: 2].
5. **Configurações (`configuracoes.php`):** Edição de perfil, alteração de senha e template customizado para envio no WhatsApp com substituição de tags (`#EMPRESA#`, `#CLIENTE#`, `#LINK#`, etc.) [cite: 2].

---

# Sugestões de Arquitetura do Projeto

### Opção 1: Arquitetura em Camadas Desacopladas (Recomendada)
> Frontend em PHP e Backend REST API em Node.js organizados em diretórios próprios.

```text
servicos-flex/
├── docker-compose.yml
├── nginx/
│   └── default.conf
├── web-frontend/             # Camada PHP / HTML / JS
│   ├── Dockerfile
│   ├── public/
│   │   ├── css/
│   │   ├── js/
│   │   │   └── menu.js
│   │   ├── index.php         # Landing Page
│   │   ├── login.php
│   │   ├── dashboard.php
│   │   ├── propostas.php
│   │   └── clientes.php
│   └── config/
│       └── api_client.php    # Cliente PHP/cURL para chamar a API Node.js
└── api-backend/              # API REST em Node.js
    ├── Dockerfile
    ├── package.json
    ├── server.js
    ├── config/
    │   └── database.js
    ├── controllers/
    ├── routes/
    └── services/
        └── pdfService.js
```

---

# Configuração de Ambiente Docker (`docker-compose.yml`)

```yaml
services:
  # API REST - Node.js / Express
  api:
    build: ./api-backend
    container_name: flex_api_node
    restart: always
    environment:
      - NODE_ENV=production
      - PORT=3000
      - DB_HOST=172.17.0.1
      - DB_USER=flex_user
      - DB_PASS=flex_password
      - DB_NAME=servicos_flex
      - JWT_SECRET=sua_chave_secreta_jwt
    networks:
      - flex-network

  # Web Frontend - PHP-FPM
  web-app:
    build: ./web-frontend
    container_name: flex_frontend_php
    restart: always
    environment:
      - API_URL=http://api:3000/api/v1
    networks:
      - flex-network

  # Servidor Web - Nginx (Proxy Reverso para PHP e Node.js)
  nginx:
    image: nginx:alpine
    container_name: flex_nginx
    restart: always
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./nginx/default.conf:/etc/nginx/conf.d/default.conf:ro
      - ./web-frontend/public:/var/www/html:ro
    depends_on:
      - api
      - web-app
    networks:
      - flex-network

  # Gerenciador MySQL - phpMyAdmin
  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    container_name: flex_phpmyadmin
    restart: always
    ports:
      - "8080:80"
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

# Guia de Instalação e Deploy no Docker (Servidor Debian/Ubuntu)

### 1. Preparação do Servidor
```bash
# Atualizar pacotes do sistema
sudo apt update && sudo apt upgrade -y

# Instalar Docker e Docker Compose Plugin
sudo apt install -y ca-certificates curl gnupg lsb-release git
curl -fsSL https://get.docker.com | bash
sudo systemctl enable --now docker
sudo apt install -y docker-compose-plugin
```

### 2. Configuração do MySQL Externo
```bash
# Liberar bind-address caso o MySQL esteja no host
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
# bind-address = 0.0.0.0
sudo systemctl restart mysql
```

### 3. Subindo a Aplicação
```bash
cd /var/www/servicos-flex
docker compose up -d --build
```

---

# Guia de Instalação e Deploy no Apache (Modo Tradicional)

Para rodar o Frontend PHP e a API Node.js diretamente no servidor Apache:

### 1. Instalação do Apache, PHP e Node.js
```bash
sudo apt update
sudo apt install -y apache2 php libapache2-mod-php php-curl php-mysql curl
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
sudo npm install -g pm2
sudo a2enmod proxy proxy_http rewrite headers
sudo systemctl restart apache2
```

### 2. Execução da API Node.js
```bash
cd /var/www/servicos-flex/api-backend
npm install --production
pm2 start server.js --name "flex-api"
pm2 save
pm2 startup
```

### 3. Configuração do VirtualHost no Apache (`/etc/apache2/sites-available/servicos-flex.conf`)
```apache
<VirtualHost *:80>
    ServerName seu-dominio.com.br
    DocumentRoot /var/www/servicos-flex/web-frontend/public

    <Directory /var/www/servicos-flex/web-frontend/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    # Proxy para a API REST Node.js
    ProxyRequests Off
    ProxyPreserveHost On
    ProxyPass /api/ http://127.0.0.1:3000/api/
    ProxyPassReverse /api/ http://127.0.0.1:3000/api/

    ErrorLog ${APACHE_LOG_DIR}/servicos_flex_error.log
    CustomLog ${APACHE_LOG_DIR}/servicos_flex_access.log combined
</VirtualHost>
```

### 4. Ativação
```bash
sudo a2dissite 000-default.conf
sudo a2ensite servicos-flex.conf
sudo systemctl restart apache2
```
