# Especificação Técnica e Funcional: Landing Page para Clientes Finais (Serviços Flex)

**Versão:** 1.0  
**Data:** 27 de Julho de 2026  
**Projeto:** Serviços Flex  
**Status:** Aprovado para Desenvolvimento  

---

## 1. Visão Geral do Documento

Este documento especifica os requisitos técnicos, funcionais e de design para a implementação da **Landing Page pública** do projeto **Serviços Flex**. O objetivo principal da Landing Page é atuar como vitrine e canal de captação de demandas para clientes finais, conectando-os aos profissionais liberais, autônomos e pequenas empresas (tenants) cadastrados na plataforma.

---

## 2. Objetivos da Landing Page

1. **Atração e Conversão:** Proporcionar uma interface fluida, moderna e responsiva para que clientes encontrem serviços e produtos rapidamente.
2. **Distribuição de Demandas:** Permitir que o cliente preencha uma solicitação rápida (Wizard) que direcione para profissionais da categoria correspondente.
3. **Contato Direto via WhatsApp:** Facilitar o contato direto entre o cliente e o profissional selecionado.
4. **Captação de Novos Profissionais:** Manter ponto de conversão para novos prestadores de serviço se cadastrarem no SaaS.

---

## 3. Arquitetura de Software e Integrações

### 3.1 Pilha Tecnológica
* **Frontend:** HTML5, CSS3, JavaScript (ES6+ nativo), Fonte Poppins, Ícones Modernos (Lucide / FontAwesome).
* **Backend:** Node.js (Express framework).
* **Banco de Dados:** MySQL (Consultas otimizadas com indexação por categoria/serviço e região).
* **Servidor Web:** Nginx (Proxy Reverso e entrega de arquivos estáticos).

### 3.2 Rotas e Endpoints Necessários

| Método | Endpoint | Descrição |
| :--- | :--- | :--- |
| `GET` | `/` | Renderização do HTML principal da Landing Page |
| `GET` | `/api/v1/public/services/search` | Autocomplete e busca de produtos/serviços cadastrados |
| `GET` | `/api/v1/public/professionals` | Listagem filtrada de profissionais por serviço e localização |
| `POST` | `/api/v1/public/leads` | Registro da solicitação de orçamento enviada pelo cliente |

---

## 4. Layout, Design System e UX

### 4.1 Paleta de Cores
* **Cor Primária (Ação/Destaque):** Azul (#2563EB)
* **Cor Secundária (Fundo das Telas):** Tom de Azul Claro (#F0F7FF)
* **Ação Direta (WhatsApp / Aprovação):** Verde (#16A34A / #25D366)
* **Neutros / Elementos Auxiliares:** Cinza Claro (#F3F4F6) e Branco (#FFFFFF)
* **Texto:** Cinza Escuro (#1F2937)

### 4.2 Tipografia e Responsividade
* **Fonte:** `Poppins`, sans-serif (Google Fonts).
* **Mobile First:** Escondimento do menu tradicional em telas pequenas, acionado via botão hambúrguer no cabeçalho superior.
* **Máscaras Nativas JS:**
  * Telefone Fixo: `(XX) XXXX-XXXX`
  * Celular: `(XX) XXXXX-XXXX`

---

## 5. Mapeamento das Seções e Funcionalidades

### 5.1 Cabeçalho (Header & Navigation)
* Logo **Serviços Flex** no canto esquerdo.
* Menu de Navegação: *Início*, *Como Funciona*, *Categorias*, *Para Profissionais*.
* Botão de Ação (CTA): **"Área do Profissional (Login)"** e **"Cadastre-se"**.
* Suporte a navegação responsiva mobile via `menu.js`.

### 5.2 Hero Section (Busca Principal)
* **Título:** *"Encontre os melhores profissionais e solicite orçamentos em segundos."*
* **Subtítulo:** *"Conectamos você a profissionais liberais, autônomos e empresas qualificadas perto de você."*
* **Barra de Busca Dinâmica:**
  * Campo input: *"O que você precisa?"* (Autocomplete com `produtos_servicos`).
  * Campo select/input: *"Cidade/Região"*.
  * Botão de busca azul com ícone de lupa.

### 5.3 Wizard de Solicitação em 3 Passos
Formulário guiado em modal ou seção dedicada para especificar a necessidade:
1. **Passo 1 (Serviço/Produto):** Seleção do tipo e descrição do que precisa.
2. **Passo 2 (Data e Detalhes):** Seleção de data desejada (com componente de calendário nativo que fecha ao selecionar) e detalhes adicionais.
3. **Passo 3 (Contato):** Nome completo, e-mail e número de celular (com máscara automática).
4. **Finalização:** Registro do lead e redirecionamento para a lista de profissionais compatíveis.

### 5.4 Vitrine de Categorias e Profissionais
* Grid de cards contendo:
  * Nome do Profissional / Empresa.
  * Foto / Logo do Tenant.
  * Principais Serviços / Produtos oferecidos com valores base.
  * Botão verde: **"Chamar no WhatsApp"** (Gera link `https://wa.me/...` com mensagem pré-formatada).

### 5.5 Seção "Como Funciona"
Card simples com 3 etapas ilustradas por ícones:
1. **Pesquise ou Solicite:** Diga qual serviço você precisa.
2. **Receba o Contato:** Profissionais visualizam sua solicitação e entram em contato.
3. **Aprove a Proposta:** Receba um link exclusivo da proposta comercial e aprova com 1 clique.

---

## 6. Modelo de Dados (Apoio à Landing Page)

```sql
-- Tabela de Leads / Solicitações enviadas pela Landing Page
CREATE TABLE IF NOT EXISTS public_leads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_name VARCHAR(150) NOT NULL,
    client_email VARCHAR(150) NOT NULL,
    client_phone VARCHAR(20) NOT NULL,
    service_description TEXT NOT NULL,
    desired_date DATE,
    city VARCHAR(100),
    status ENUM('pending', 'contacted', 'closed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## 7. Estrutura de Arquivos Sugerida

```text
servicos-flex/
├── public/
│   ├── index.html            # Landing Page do Cliente Final
│   ├── css/
│   │   └── landing.css       # Estilos específicos da Landing Page
│   └── js/
│       ├── landing.js        # Lógica de busca, autocomplete e wizard
│       └── menu.js           # Menu responsivo mobile
├── src/
│   ├── controllers/
│   │   └── publicController.js # Lógica de busca e recebimento de leads
│   └── routes/
│       └── publicRoutes.js   # Rotas abertas para a Landing Page
```

---

## 8. Critérios de Aceite

- [ ] A Landing Page deve carregar em menos de 2 segundos em conexões 4G/3G.
- [ ] O componente de calendário deve abrir nativamente e fechar automaticamente após a escolha da data.
- [ ] Todas as máscaras de telefone (fixo e celular) devem ser aplicadas em tempo de digitação.
- [ ] O clique no botão do WhatsApp deve abrir a aplicação com o número do profissional e mensagem preenchida contendo os dados da busca.
- [ ] A página deve passar nos testes de responsividade em dispositivos mobile (iOS e Android).
