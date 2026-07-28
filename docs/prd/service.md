> **📦 ARQUIVADO** — Este documento foi substituído pelo `docs/planning/PLANEJAMENTO_MODERNO_PROJETO.md` (v2.0).  
> **Data de arquivamento:** 27 de Julho de 2026  
> **Motivo:** Documentos consolidados em plano estratégico único.  
> **Consultar:** `docs/planning/PLANEJAMENTO_MODERNO_PROJETO.md` para versão atual.

Entendi perfeitamente todos os requisitos do projeto **Serviços Flex**.

Abaixo está o Product Requirement Document (PRD) estruturado, acompanhado das 3 opções de arquitetura, arquivos de containerização e os manuais de deploy.

---

# Documento de Requisitos do Produto (PRD) — Serviços Flex

## 1. Visão Geral do Produto

O **Serviços Flex** é uma plataforma SaaS web responsiva focada em profissionais autônomos, liberais e pequenas empresas. O objetivo é simplificar e acelerar a criação, gestão, envio (via WhatsApp) e aprovação de orçamentos e propostas comerciais.

---

## 2. Requisitos Técnicos & Infraestrutura

* **Ambiente de Execução:** Container Linux Debian/Ubuntu (Docker).
* **Servidor Web:** Nginx (Proxy Reverso).
* **Backend:** Node.js (JavaScript).
* **Banco de Dados:** MySQL externo ao container da aplicação.
* **Gerenciamento do BD:** phpMyAdmin em container auxiliar.
* **Sessões e Autenticação:** Sessões nativas com Node.js (ex: `express-session`), suporte a Multi-tenancy isolation.
* **Geração de Documentos:** Geração nativa de PDF profissional no backend Node.js (ex: `pdfkit` ou `puppeteer`).

---

## 3. Diretrizes de Design & UX/UI

* **Paleta de Cores:** Tons de Azul (Ação primária `#2563EB`, Fundo das telas `#F0F7FF`), Verde (WhatsApp / Aprovação `#16A34A`), Cinza Claro (Sidebar `#F3F4F6`), Branco (Botões secundários `#FFFFFF`).
* **Tipografia:** Fonte **Poppins** em toda a aplicação.
* **Layout:** Sidebar fixa à esquerda contendo a logo no topo e itens de navegação. No mobile, a sidebar oculta-se e é acionada por botão hambúrguer no cabeçalho.
* **Componentes de UI:**
* Calendário nativo em todos os campos de data, fechando após a seleção.
* Ícones modernos em todos os botões e ações.
* Botão de WhatsApp direcionando para o link da API do WhatsApp com mensagem pré-formatada.


* **Formatos Nacionais:**
* Moeda: `R$ 0.000,00`
* Datas: `dd/mm/yyyy`
* Telefone Fixo: `(XX) XXXX-XXXX`
* Telefone Celular: `(XX) XXXXX-XXXX`
* CPF: `XXX.XXX.XXX-XX`
* CNPJ: `XXX.XXX.XXX/XXXX-XX`


---

## 4. Regras de Negócio & Validações de Segurança

* **Multi-tenancy:** Garantia estrita de isolamento de dados por `tenant_id` (usuário).
* **Unicidade de Contas:** Proibida duplicidade de CPF, CNPJ ou E-mail no sistema.
* **Validação de Cadastro:** Validação do formato do e-mail e máscaras dinâmicas/validação para CPF/CNPJ e telefones.
* **Política de Senhas:** Mínimo de 8 caracteres alfanuméricos.
* **Exclusão Lógica de Propostas:** Propostas excluídas alteram o status para `Excluida`, mantendo o histórico no banco de dados.
* **Visualização Pública:** Qualquer pessoa pode acessar o link único de uma proposta pública para clicar em **Aprovar** ou **Reprovar**. Propostas em Rascunho não exibem status para o cliente final.

---

## 5. Mapeamento de Módulos & Funcionalidades

### Área Pública (Não Logada)

1. **Criação de Conta:**
* Seleção de Tipo: Pessoa Física (Nome, CPF) ou Jurídica (Nome da Empresa, CNPJ).
* Pessoa de Contato, E-mail, Telefone Fixo (opcional), WhatsApp, Senha e Confirmação de Senha.


2. **Login:** E-mail e Senha.
3. **Página Pública da Proposta:** Visualização limpa dos itens com botões de Aprovação/Reprovação.
4. **Vitrine e Solicitação do Cliente:** Cadastro de clientes para solicitar serviços/produtos diretamente e visualizar profissionais disponíveis.

### Área Privada (Logada)

1. **Painel Gerencial (Dashboard):**
* Indicadores: Total de Clientes, Qtd. Propostas, Qtd. Aprovadas, Valor Aprovadas, Qtd. Pendentes, Propostas Excluídas.
* Gráfico: Histórico financeiro mensal de propostas aprovadas (últimos 6 meses).
* Painéis de acompanhamento: Follow-up do dia (com acionamento de WhatsApp direto), Propostas Pendentes e Propostas Excluídas.


2. **Cadastro de Clientes:** Módulo completo de CRUD.
3. **Cadastro de Produtos e Serviços:** Classificação por tipo, descrição e valor unitário.
4. **Gerenciador de Propostas (Mestre-Detalhe):**
* Interface reativa em memória antes da gravação final.
* Inclusão/Remoção contínua de itens (produtos/serviços).
* Geração e envio por WhatsApp utilizando template customizável.
* Redirecionamento automático para consulta após salvar.


5. **Configurações:** Edição de perfil, alteração de senha e template dinâmico para mensagem do WhatsApp (Tags: `#EMPRESA#`, `#CLIENTE#`, `#PROPOSTA#`, `#VALOR#`, `#DATA#`, `#LINK#`).

---

# Sugestões de Arquitetura do Projeto

Aqui estão 3 opções de organização do código para você escolher a que melhor atende ao seu fluxo de trabalho:

### Opção 1: Estrutura Monolítica Plana (Arquivos na mesma pasta)

> Recomendada para prototipagem rápida e projetos simples sem abstração de pastas.

```text
servicos-flex/
├── docker-compose.yml
├── Dockerfile
├── nginx.conf
├── server.js
├── database.js
├── auth.js
├── menu.js
├── routes.js
├── pdf-generator.js
├── index.html
├── login.html
├── dashboard.html
├── propostas.html
├── clientes.html
├── public-proposal.html
├── style.css
└── package.json

```

---

### Opção 2: Estrutura em Camadas (MVC Tradicional)

> Recomendada para facilidade de manutenção e separação de responsabilidades.

```text
servicos-flex/
├── docker-compose.yml
├── Dockerfile
├── nginx/
│   └── nginx.conf
├── src/
│   ├── config/
│   │   └── database.js
│   ├── controllers/
│   ├── models/
│   ├── routes/
│   ├── services/
│   │   └── pdfService.js
│   └── app.js
├── public/
│   ├── css/
│   ├── js/
│   │   └── menu.js
│   └── views/
└── package.json

```

---

### Opção 3: Estrutura Modular por Domínio

> Recomendada para escalabilidade e isolamento de funcionalidades futuras.

```text
servicos-flex/
├── docker-compose.yml
├── Dockerfile
├── config/
├── modules/
│   ├── auth/
│   ├── clients/
│   ├── proposals/
│   └── dashboard/
├── shared/
│   ├── utils/
│   └── middlewares/
├── public/
│   └── js/
│       └── menu.js
└── package.json

```

---

# Configuração de Ambiente Docker

Abaixo está o arquivo `docker-compose.yml` configurado com container de aplicação, Nginx, phpMyAdmin e integração para MySQL externo.

### `docker-compose.yml`

```yaml
version: '3.8'

services:
  app:
    build: .
    container_name: servicos_flex_app
    restart: always
    environment:
      - NODE_ENV=production
      - PORT=3000
      - DB_HOST=172.17.0.1 # IP do host Linux onde o MySQL externo está rodando
      - DB_USER=flex_user
      - DB_PASS=flex_password
      - DB_NAME=servicos_flex
    networks:
      - flex-network

  web:
    image: nginx:alpine
    container_name: servicos_flex_nginx
    restart: always
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./nginx.conf:/etc/nginx/conf.d/default.conf:ro
    depends_on:
      - app
    networks:
      - flex-network

  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    container_name: servicos_flex_phpmyadmin
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

Execute os comandos no terminal do servidor Linux:

```bash
# Atualizar pacotes do sistema
sudo apt update && sudo apt upgrade -y

# Instalar dependências e Docker
sudo apt install -y ca-certificates curl gnupg lsb-release git
curl -fsSL https://get.docker.com | bash

# Habilitar e iniciar o serviço do Docker
sudo systemctl enable --now docker

# Instalar o Docker Compose CLI Plugin
sudo apt install -y docker-compose-plugin

```

### 2. Configuração do MySQL Externo

Se o MySQL estiver instalado no host Linux (fora do Docker), certifique-se de permitir a conexão do container:

```bash
# Editar o arquivo de configuração do MySQL
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf

# Altere bind-address para aceitar conexões locais do docker
# bind-address = 0.0.0.0

sudo systemctl restart mysql

```

### 3. Subindo a Aplicação

```bash
# Clone ou envie os arquivos para a pasta do servidor
cd /var/www/servicos-flex

# Subir os containers em segundo plano
docker compose up -d --build

```

### 4. Acessos

* **Aplicação Web:** `http://seu-ip-ou-dominio`
* **phpMyAdmin:** `http://seu-ip-ou-dominio:8080`

---

# Guia de Instalação e Deploy no Servidor Web Apache

Caso prefira executar a aplicação em um servidor Linux utilizando o Apache como servidor web e Proxy Reverso para o Node.js:

### 1. Instalação do Apache, Node.js e Módulos

```bash
# Instalar Apache e Node.js (v20 LTS)
sudo apt update
sudo apt install -y apache2 curl
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Habilitar módulos de Proxy Reverso no Apache
sudo a2enmod proxy proxy_http rewrite headers
sudo systemctl restart apache2

```

### 2. Configuração da Aplicação Node.js

```bash
# Instalar gerenciador de processos PM2 para manter a aplicação online
sudo npm install -g pm2

# Navegar até o diretório do projeto
cd /var/www/servicos-flex
npm install --production

# Iniciar a aplicação na porta 3000 com o PM2
pm2 start server.js --name "servicos-flex"
pm2 save
pm2 startup

```

### 3. Configuração do VirtualHost no Apache

Crie o arquivo de configuração da sua aplicação:

```bash
sudo nano /etc/apache2/sites-available/servicos-flex.conf

```

Adicione o seguinte conteúdo:

```apache
<VirtualHost *:80>
    ServerName seu-dominio.com.br
    ServerAdmin admin@seu-dominio.com.br

    # Configuração de Proxy Reverso para a aplicação Node.js
    ProxyRequests Off
    ProxyPreserveHost On
    ProxyVia Full

    <Proxy *>
        Require all granted
    </Proxy>

    ProxyPass / http://127.0.0.1:3000/
    ProxyPassReverse / http://127.0.0.1:3000/

    ErrorLog ${APACHE_LOG_DIR}/servicos_flex_error.log
    CustomLog ${APACHE_LOG_DIR}/servicos_flex_access.log combined
</VirtualHost>

```

### 4. Ativação do Site

```bash
# Ativar o novo site e reiniciar o Apache
sudo a2dissite 000-default.conf
sudo a2ensite servicos-flex.conf
sudo apache2ctl configtest
sudo systemctl restart apache2

```

Qual das 3 opções de estrutura do projeto você prefere seguir para darmos início ao desenvolvimento?
