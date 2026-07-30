# Estratégia de E-mail — ServiceSaaS

## 1. Análise de Necessidades

### 1.1 Tipos de E-mail

| Tipo | Prioridade | Volume Estimado | Exemplos |
|:-----|:----------:|:---------------:|:---------|
| **Transacionais** | 🔴 Alta | 100-500/mês | Reset de senha, confirmação de cadastro, notificação de lead |
| **Notificações** | 🟡 Média | 200-1000/mês | Proposta aprovada/rejeitada, pagamento confirmado, agendamento |
| **Marketing** | 🟢 Baixa | 0-5000/mês | Newsletters, promoções, lembretes (futuro) |

### 1.2 Público-Alvo

- **Prestadores (tenants):** ~500-2000 contatos — recebem leads, notificações de propostas
- **Clientes finais:** ~2000-10000 contatos — recebem proposal link, confirmações
- **Administradores:** ~5-10 contatos — recebem alertas do sistema

### 1.3 Requisitos Legais (LGPD)

- Consentimento explícito para marketing (já implementado via `lgpd_consent`)
- Opt-out em todos os e-mails de marketing
- Registro de consentimento no `audit_log`
- Criptografia em trânsito (TLS 1.2+)
- Dados de e-mail armazenados apenas no Brasil (quando possível)

---

## 2. Comparativo de Plataformas

### 2.1 Critérios de Avaliação

| Critério | Peso | Descrição |
|:---------|:----:|:-----------|
| Custo por e-mail | 20% | Preço por 1000 e-mails enviados |
| Deliverability | 25% | Taxa de entrega na caixa de entrada (não spam) |
| Facilidade de integração | 20% | SDK, documentação, tempo de setup |
| Infraestrutura no Brasil | 15% | Servidores locais, suporte em português |
| Transacional vs Marketing | 10% | Suporte a ambos os tipos |
| Recursos extras | 10% | Templates, analytics, webhooks, API |

### 2.2 Plataformas Avaliadas

| Plataforma | Custo/mês (10k emails) | Deliverability | SDK Node.js | Servidores BR | Nota Final |
|:-----------|:----------------------:|:--------------:|:-----------:|:-------------:|:----------:|
| **Brevo (Sendinblue)** | ~R$ 65 (gratuito até 300/dia) | ⭐⭐⭐⭐ | ✅ Sim | ✅ Sim (Brasil) | **9.2/10** |
| **SendGrid (Twilio)** | ~US$ 20 (gratuito até 100/dia) | ⭐⭐⭐⭐⭐ | ✅ Sim | ❌ EUA | **9.0/10** |
| **Amazon SES** | ~US$ 1 (pago por uso) | ⭐⭐⭐⭐ | ✅ SDK v3 | ✅ São Paulo | **8.8/10** |
| **Mailgun** | ~US$ 35 (50k emails) | ⭐⭐⭐⭐ | ✅ Sim | ❌ EUA | **8.0/10** |
| **Postmark** | ~US$ 15 (10k emails) | ⭐⭐⭐⭐⭐ | ✅ Sim | ❌ EUA | **8.5/10** |
| **Zoho Mail** | ~R$ 30 (5 caixas) | ⭐⭐⭐ | ❌ API REST | ❌ EUA | **6.5/10** |

### 2.3 Recomendação Primária: **Brevo (Sendinblue)**

**Motivos:**
1. **Infraestrutura local** — servidores no Brasil garantem melhor deliverability para e-mails .com.br
2. **Preço em BRL** — sem variação cambial, faturamento em reais
3. **Gratuito generoso** — 300 e-mails/dia gratuitos (9k/mês) — suficiente para MVP
4. **Transactional + Marketing** — mesma plataforma para ambos os tipos
5. **SDK Node.js oficial** — `@sendinblue/client` ou API REST
6. **LGPD compliance** — dados armazenados no Brasil, DPA disponível
7. **Templates visuais** — editor drag-and-drop para e-mails marketing

### 2.4 Recomendação Secundária: **SendGrid (Twilio)**

**Motivos:**
1. ✅ **Já integrado** — `email.service.js` já tem suporte a SendGrid
2. **Melhor deliverability internacional** — se a base de clientes crescer globalmente
3. **SDK maduro** — `@sendgrid/mail` é battle-tested
4. **Analytics avançados** — opens, clicks, bounces em tempo real
5. **Já configurado** — basta setar `EMAIL_PROVIDER=sendgrid` e `SENDGRID_API_KEY`

---

## 3. Implementação Recomendada

### 3.1 Arquitetura Final

```
┌──────────────┐     ┌──────────────────┐     ┌──────────────────────┐
│  Aplicação    │────▶│ communication    │────▶│ email.service.js     │
│  (eventos)    │     │ .service.js      │     │                      │
│               │     │                  │     │  EMAIL_PROVIDER=     │
│ register ✓    │     │  Queue assíncrona│     │  brevo | sendgrid    │
│ proposal ✓    │     │  + preference    │     │                      │
│ lead ✓        │     │  filtering       │     │  ProviderResolver    │
│ schedule ✓    │     │                  │     │  ────────────────   │
└──────────────┘     └──────────────────┘     │  BrevoAdapter ◀── SDK│
                                              │  SendGridAdapter     │
                                              │  LogAdapter (dev)    │
                                              └──────────────────────┘
```

### 3.2 Provider Abstraction (Recomendado)

Criar um adapter pattern que permite trocar de provider sem alterar código de negócio:

```javascript
// services/email/provider.js
const providers = {
  brevo: require('./brevo.provider'),
  sendgrid: require('./sendgrid.provider'),
  log: require('./log.provider'),
};

function getProvider(name = process.env.EMAIL_PROVIDER || 'log') {
  return providers[name] || providers.log;
}
```

### 3.3 Configuração para Produção

```bash
# .env — Configuração Brevo (recomendado)
EMAIL_PROVIDER=brevo
BREVO_API_KEY=seu_token_aqui
BREVO_FROM_EMAIL=noreply@servicesaas.com.br
BREVO_FROM_NAME=ServiceSaaS

# .env — Configuração SendGrid (alternativa)
# EMAIL_PROVIDER=sendgrid
# SENDGRID_API_KEY=seu_token
# SENDGRID_FROM_EMAIL=noreply@servicesaas.com
```

---

## 4. Passos para Ativação

### 4.1 Setup Brevo (Recomendado)

```bash
# 1. Instalar SDK
npm install @getbrevo/brevo

# 2. Configurar .env
EMAIL_PROVIDER=brevo
BREVO_API_KEY=sua_chave_api
BREVO_FROM_EMAIL=noreply@servicesaas.com.br
BREVO_FROM_NAME=ServiceSaaS

# 3. Configurar DKIM/SPF no DNS
#   Adicionar registro TXT: brevo._domainkey → (valor fornecido pelo Brevo)
#   Adicionar registro SPF: v=spf1 include:spf.brevo.com ~all

# 4. Configurar webhook de bounce
#   POST /api/v1/webhooks/email/bounce → atualizar status do contato
```

### 4.2 Setup SendGrid (Já Integrado)

```bash
# 1. Configurar .env
EMAIL_PROVIDER=sendgrid
SENDGRID_API_KEY=seu_token
SENDGRID_FROM_EMAIL=noreply@servicesaas.com

# 2. Verificar remetente no SendGrid
#   Adicionar Sender Authentication (DKIM + SPF)

# 3. Criar templates dinâmicos (opcional)
#   SendGrid Dynamic Templates → Handlebars
```

### 4.3 DKIM/SPF Setup (Obrigatório)

```dns
# Registros DNS necessários para qualquer provedor

# SPF — autoriza o provedor a enviar em seu nome
v=spf1 include:spf.sendgrid.net ~all

# DKIM — assinatura criptográfica dos e-mails
# (valores fornecidos pelo provedor)

# DMARC — política de tratamento de falhas
v=DMARC1; p=quarantine; rua=mailto:dmarc@seudominio.com.br
```

---

## 5. Métricas e Monitoramento

| Métrica | Onde Ver | Alerta |
|:--------|:---------|:-------|
| Taxa de entrega | Dashboard do provedor | < 95% |
| Taxa de bounce | Webhook / API | > 2% |
| Taxa de spam | Painel do provedor | > 0.1% |
| Latência de entrega | Logs da aplicação | > 5 min |
| Opens (marketing) | Analytics do provedor | < 10% |

---

## 6. Conclusão

### Recomendação: **Brevo (Sendinblue)**

| Para | Por quê |
|:-----|:---------|
| **MVP / Início** | 300 e-mails/dia gratuitos, fácil setup |
| **Crescimento** | Preço competitivo em BRL, sem surpresa cambial |
| **LGPD** | Servidores no Brasil, DPA disponível |
| **Transacionais** | API rápida, SDK Node.js, templates |

### Fallback: **SendGrid**

| Para | Por quê |
|:-----|:---------|
| **Já integrado** | `email.service.js` já suporta |
| **Escala global** | Melhor deliverability internacional |
| **Enterprise** | SLA 99.9%, suporte 24/7 |

---

*Documento gerado em 30/07/2026 — ServiceSaaS*
