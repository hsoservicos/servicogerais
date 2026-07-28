# 🔬 Pesquisa Técnica: Integração Mercado Pago — ServiceSaaS

**Data:** 28 de Julho de 2026
**Autora:** Mary (Business Analyst)
**Projeto:** ServiceSaaS (Serviços Flex)
**Stack:** Node.js 20 + Express.js · MySQL 8.0 · Docker Compose

---

## Sumário Executivo

Esta pesquisa técnica valida a arquitetura de integração com o **Mercado Pago** planejada para o **Epic 5 (Pagamentos)** do ServiceSaaS. Foram analisados: SDK oficial, segurança de webhooks, geração de QR Code Pix, idempotência, estornos e a implementação existente no código-fonte.

**Status da Implementação Atual:** 🟡 Parcial — API Admin com queries de `transactions` existe, mas sem conexão real com Mercado Pago. Endpoints de pagamento e webhook não implementados.

---

## 1. Stack e Versões

| Componente | Planejado (PRD) | Recomendado (Pesquisa) | Observação |
|:---|---|:---|:---|
| SDK Node.js | `mercadopago` v2.1 | ✅ v2.1+ | Versão atual estável. Usar `MercadoPagoConfig` + recursos |
| Checkout | SDK JS v4.0 (Frontend) | ✅ Checkout Bricks (recomendado) | Bricks abstrai PCI compliance e UI Pix |
| Idempotência | `X-Idempotency-Key` + UNIQUE | ✅ UUID v4 por requisição | Padrão da indústria |
| Webhook | POST `/api/v1/payments/webhook` | ✅ HMAC-SHA256 via `x-signature` | Ver obrigatório |

---

## 2. Arquitetura do SDK (v2.1)

### 2.1. Inicialização

O SDK v2.1 usa arquitetura baseada em clientes. Diferente de versões antigas (v1.x), não se usa mais `mercadopago.configure()`:

```javascript
const { MercadoPagoConfig, Preference, Payment, PaymentRefund } = require('mercadopago');

const client = new MercadoPagoConfig({
  accessToken: process.env.MP_ACCESS_TOKEN,
  options: {
    timeout: 5000,
    idempotencyKey: 'unique-uuid-v4',
  },
});

// Recursos específicos
const preference = new Preference(client);
const payment = new Payment(client);
```

**🔴 Gap Crítico:** O PRD planeja `mercadopagoService.js` mas não especifica a arquitetura de cliente. **Necessário criar um wrapper que compartilhe a instância do cliente** em vez de criar nova instância por requisição.

### 2.2. Gerenciamento de Erros

```javascript
try {
  const result = await preference.create({ body });
} catch (err) {
  if (err.status === 400) {
    // Erro de validação — payload inválido
  } else if (err.status === 429) {
    // Rate limit — implementar retry com backoff
  } else {
    // Erro genérico MP
  }
}
```

**🟡 Recomendação:** Implementar **retry com exponential backoff** para erros 429 (rate limit) e 500 (servidor MP). Máximo de 3 tentativas.

---

## 3. Preferência de Pagamento (Story 5.2)

### 3.1. Criação de Preferência

```javascript
const body = {
  items: [
    {
      id: 'PROP-2026-0001',
      title: 'Corte Feminino',
      description: 'Corte com lavagem e finalização',
      quantity: 1,
      unit_price: 65.00,
      currency_id: 'BRL',
    },
    {
      title: 'Manicure Completa',
      quantity: 2,
      unit_price: 35.00,
      currency_id: 'BRL',
    },
  ],
  payer: {
    name: 'Carlos Silva',
    email: 'carlos@email.com',
  },
  external_reference: 'proposal_42', // ID da proposta no ServiceSaaS
  notification_url: 'https://seudominio.com.br/api/v1/payments/webhook',
  expires: true,
  expiration_date_from: new Date().toISOString(),
  expiration_date_to: new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString(), // 7 dias
  metadata: {
    tenant_id: 2,
    proposal_id: 42,
  },
};

const preference = await client.preference.create({ body });
// Resposta: { id: '123456789', init_point: 'https://www.mercadopago.com.br/...', sandbox_init_point: '...' }
```

### 3.2. Recomendações

| Aspecto | Recomendação |
|:---|---|
| `external_reference` | Usar formato `proposal_{id}` para rastreamento bidirecional |
| `notification_url` | Configurar no .env, diferente para staging vs produção |
| `expires` | Ativar com 7 dias para evitar preferências órfãs |
| `metadata` | Incluir `tenant_id` + `proposal_id` para correlação sem consulta extra |

### 3.3. Idempotência (AR-05)

**Mecanismo de Dupla Camada:**

1. **Camada API:** Header `X-Idempotency-Key` com UUID v4 em toda requisição de criação
2. **Camada Banco:** UNIQUE INDEX `(tenant_id, proposal_id)` na tabela `transactions`

```sql
-- UNIQUE INDEX para idempotência
CREATE UNIQUE INDEX idx_transactions_proposal 
  ON transactions(tenant_id, proposal_id) 
  WHERE status != 'refunded';
```

```javascript
// Geração da chave de idempotência
const idempotencyKey = `${proposalId}-${Date.now()}`; // proposal_42-1722101234
const crypto = require('crypto');
const hash = crypto.createHash('sha256').update(idempotencyKey).digest('hex');

const preference = await client.preference.create({
  body,
  requestOptions: { idempotencyKey: hash },
});
```

**✅ Validação:** A abordagem de dupla camada está correta e alinhada com as melhores práticas do Mercado Pago.

---

## 4. Webhook IPN (Story 5.3) — Validação Detalhada

### 4.1. Arquitetura de Segurança

O fluxo de webhook tem **3 camadas de segurança**:

```
1. Recepção → Validação HMAC (x-signature header)
2. Verificação → GET /v1/payments/{data.id} (nunca confiar no payload)
3. Idempotência → UNIQUE CONSTRAINT no notification_id
```

### 4.2. Validação HMAC-SHA256

**⚠️ CRÍTICO: O PRD atual não menciona validação HMAC!** A pesquisa revelou que ela é **obrigatória** para produção.

```javascript
// Validação de assinatura do webhook
const crypto = require('crypto');

function validateWebhookSignature(req) {
  const signature = req.headers['x-signature'];
  const requestId = req.headers['x-request-id'];
  const dataId = req.query['data.id'];
  
  if (!signature || !requestId || !dataId) {
    return false;
  }
  
  // Parse do header: "ts=1234567890,v1=abcdef123456..."
  const parts = {};
  signature.split(',').forEach(pair => {
    const [key, value] = pair.split('=');
    parts[key.trim()] = value.trim();
  });
  
  const manifest = `id:${dataId};request-id:${requestId};ts:${parts.ts};`;
  const secret = process.env.MP_WEBHOOK_SECRET;
  const hash = crypto
    .createHmac('sha256', secret)
    .update(manifest)
    .digest('hex');
  
  // Comparação em tempo constante (previne timing attack)
  if (hash.length !== parts.v1.length) return false;
  return crypto.timingSafeEqual(Buffer.from(hash), Buffer.from(parts.v1));
}
```

### 4.3. Fluxo Completo do Webhook

```javascript
async function handleWebhook(req, res) {
  // 1. ACK imediato (MP espera resposta em < 22s)
  res.status(200).json({ received: true });
  
  // 2. Validar assinatura
  if (!validateWebhookSignature(req)) {
    console.warn('Webhook rejeitado: assinatura inválida', { correlationId });
    return;
  }
  
  // 3. Verificar se já foi processado (idempotência)
  const notificationId = req.body.id;
  const alreadyProcessed = await checkNotificationProcessed(notificationId);
  if (alreadyProcessed) return;
  
  // 4. Buscar status real do pagamento na API do MP
  const paymentId = req.body.data?.id;
  const paymentData = await payment.get({ id: paymentId });
  
  // 5. Atualizar transação conforme status real
  const statusMap = {
    approved: 'completed',
    rejected: 'cancelled',
    refunded: 'refunded',
    chargeback: 'chargeback',
    in_process: 'pending',
    pending: 'pending',
  };
  
  await updateTransaction(paymentId, {
    mp_status: paymentData.status,
    status: statusMap[paymentData.status] || 'unknown',
    paid_at: paymentData.status === 'approved' ? new Date() : null,
  });
  
  // 6. Se aprovado, atualizar proposta para "paid"
  if (paymentData.status === 'approved') {
    await updateProposalStatus(paymentData.external_reference, 'paid');
  }
}
```

### 4.4. Tratamento de Tipos de Evento

| Tipo | Ação | HTTP Response |
|:---|---|:---:|
| `payment` com `data.id` | Processar pagamento | 200 ✅ |
| `merchant_order` | Ignorar (log debug) | 200 ✅ |
| `stop_delivery_op_wh` | ⚠️ Ação imediata (alerta de fraude) | 200 ✅ |
| Payload sem `data.id` | Log warn | 400 ❌ |
| Assinatura inválida | Log error + rejeitar | 401 ❌ |

**🔴 Gap:** O PRD trata `merchant_order` como ignorável, mas a pesquisa mostra que `stop_delivery_op_wh` (alerta de fraude) requer **ação imediata** e **ACK obrigatório** (200). Sem o ACK, o MP não reenvia este tipo.

---

## 5. Pix QR Code (Story 5.4)

### 5.1. Geração via API

```javascript
const paymentData = await payment.create({
  body: {
    transaction_amount: 100.00,
    description: 'Corte Feminino',
    payment_method_id: 'pix',
    payer: { email: 'cliente@email.com' },
  },
});

// Dados do Pix na resposta:
const qrCodeBase64 = paymentData.point_of_interaction.transaction_data.qr_code_base64;
const qrCodeText = paymentData.point_of_interaction.transaction_data.qr_code; // BR Code copia e cola
```

### 5.2. Expiração do QR Code

**🟡 Prazo Padrão do MP:** Pix tem validade de **30 minutos** (não configurável via API simples). Após expirar, deve-se criar novo pagamento.

```javascript
// Verificar expiração
if (paymentData.date_of_expiration && new Date(paymentData.date_of_expiration) < new Date()) {
  // QR Code expirado — criar novo pagamento
  const newPayment = await payment.create({ /* ... */ });
}
```

### 5.3. Frontend com Bricks (Recomendado)

Em vez de renderizar manualmente o QR Code com `qrcode` library, usar o **Status Screen Brick** do Mercado Pago:

```html
<script src="https://sdk.mercadopago.com/js/v2"></script>
<script>
  const mp = new MercadoPago('PUBLIC_KEY', { locale: 'pt-BR' });
  mp.bricks().create('statusScreen', 'status-screen-brick', {
    initialization: { paymentId: 'PAYMENT_ID' },
    customization: { visual: { hideStatusDetails: false } },
  });
</script>
```

**Benefícios:** Renderiza automaticamente: QR Code, código copia e cola, timer de expiração, atualização automática quando pago.

---

## 6. Estorno (Story 5.5)

### 6.1. Implementação

```javascript
const { PaymentRefund } = require('mercadopago');

// Estorno total
await refund.create({ payment_id: paymentId });

// Estorno parcial
await refund.create({
  payment_id: paymentId,
  amount: 50.00, // apenas R$ 50,00 dos R$ 200,00
});
```

### 6.2. Regras de Negócio

| Regra | Detalhe |
|:---|---|
| Prazo máximo | 180 dias após aprovação |
| Estorno total | Liberado imediatamente. Proposta volta para `accepted` |
| Estorno parcial | Transação original permanece `approved` com `net_amount` ajustado. Criar nova transação de estorno |
| Múltiplos estornos | Permitido até o valor total ser atingido |
| Taxa MP | Taxa de processamento NÃO é estornada (MP retention) |

**🔴 Gap:** O PRD não menciona a **retenção da taxa MP** em estornos. O prestador (Maria) precisa ser informada que o valor estornado será **líquido da taxa** (ex: se pagou R$ 100 com taxa de R$ 4, o estorno será de R$ 96).

---

## 7. Análise da Arquitetura Existente

### 7.1. Admin Controller — Já Implementado

```javascript
// queries existentes em admin.controller.js que referenciam transactions:
const [revenueRow] = await query(
  "SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE status = 'completed'"
);
```

✅ As queries já estão preparadas para a tabela `transactions`. Ótimo!

### 7.2. Admin Routes — Pendente

```javascript
// admin.routes.js placeholders (PRÓXIMAS STORIES):
// PUT   /tenants/:id     → Atualizar tenant
// DELETE /tenants/:id    → Suspender tenant
// GET   /transactions    → Transações
// POST  /transactions/:id/refund → Estorno
```

🟡 Endpoints de transação e estorno existem como comentários mas **não foram implementados**.

### 7.3. Tabela `transactions` — Gap

🔴 **A tabela `transactions` não existe no `init.sql`** atual. A migration precisa ser criada. Sugestão de schema:

```sql
CREATE TABLE IF NOT EXISTS transactions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tenant_id INT NOT NULL,
  proposal_id INT NOT NULL,
  
  -- Mercado Pago
  mp_preference_id VARCHAR(100) NULL,
  mp_payment_id VARCHAR(100) NULL,
  mp_status VARCHAR(50) NULL,
  mp_notification_id VARCHAR(100) NULL,
  
  -- Financeiro
  amount DECIMAL(10,2) NOT NULL,
  fee DECIMAL(10,2) DEFAULT 0.00,
  net_amount DECIMAL(10,2) GENERATED ALWAYS AS (amount - fee) STORED,
  payment_method VARCHAR(20) NULL,          -- pix, credit_card, ticket (boleto)
  
  -- Status
  status ENUM('pending', 'completed', 'cancelled', 'refunded', 'chargeback') DEFAULT 'pending',
  paid_at TIMESTAMP NULL,
  
  -- Metadados
  metadata JSON NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  FOREIGN KEY (tenant_id) REFERENCES tenants(id),
  FOREIGN KEY (proposal_id) REFERENCES proposals(id),
  
  UNIQUE INDEX idx_transactions_mp (mp_payment_id),
  INDEX idx_transactions_tenant (tenant_id),
  INDEX idx_transactions_status (status)
);
```

---

## 8. Recomendações Finais

### 🔴 Recomendações Críticas (Implementar Antes do Deploy)

| # | Recomendação | Impacto | FR |
|:---:|---|---|:---:|
| 1 | **Adicionar validação HMAC-SHA256** no webhook handler | **Segurança** — sem isso, qualquer requisição POST pode fingir ser MP | FR-051 |
| 2 | **Criar tabela `transactions`** no `init.sql` | **Funcional** — sem ela, os dados de pagamento não persistem | FR-050 |
| 3 | **Tratar `stop_delivery_op_wh`** como alerta de fraude com ACK obrigatório | **Segurança** — sem ACK o MP bloqueia notificações | FR-051 |
| 4 | **Adicionar `net_amount`** à resposta de estorno para informar taxa retida | **UX** — prestador precisa saber valor real do estorno | FR-053 |

### 🟡 Recomendações Importantes (v1.0)

| # | Recomendação | Justificativa |
|:---:|---|---|
| 5 | **Usar Checkout Bricks** em vez de renderizar QR Code manualmente | Reduz complexidade frontend em ~40%, garante expiração automática |
| 6 | **Configurar `notification_url`** por ambiente (dev/staging/prod) | Evita webhooks falsos durante desenvolvimento |
| 7 | **Implementar retry com backoff** para erros 429 e 500 do MP | Resiliência contra instabilidades do gateway |
| 8 | **Adicionar `metadata`** nas preferências com tenant_id + proposal_id | Rastreabilidade sem consultas extras ao banco |

### 🟢 Boas Práticas (v1.1+)

| # | Prática | Benefício |
|:---:|---|---|
| 9 | Dashboard de transações no admin | Visibilidade financeira para equipe ServiceSaaS |
| 10 | Notificação push ao prestador quando pagamento confirmado | Experiência em tempo real |
| 11 | Relatório mensal de taxas MP | Transparência com prestadores |
| 12 | Cache local de `access_token` com renovação automática | Performance |

---

## 9. Roadmap de Implementação Sugerido

```
FASE 1 — Setup (Story 5.1) ~ 2h
└── Criar tabela transactions → mercadopagoService.js → .env.example

FASE 2 — Preferência + Cobrar (Story 5.2) ~ 3h
└── POST /payments/create-preference → Botão "Cobrar" na proposta

FASE 3 — Webhook (Story 5.3) ~ 3h
└── Validação HMAC → Handler idempotente → Atualização status

FASE 4 — Pix (Story 5.4) ~ 2h
└── QR Code → Copia e Cola → Expiração → Bricks frontend

FASE 5 — Estorno (Story 5.5) ~ 1h
└── POST /payments/:id/refund → UI modal → Histórico
```

---

## 10. Referências

- [Mercado Pago SDK Node.js (GitHub)](https://github.com/mercadopago/sdk-nodejs)
- [Mercado Pago Developers — Documentação Oficial](https://www.mercadopago.com.br/developers/en/docs)
- [Mercado Pago — Checkout Bricks](https://www.mercadopago.com.br/developers/en/docs/checkout-bricks/landing)
- [Mercado Pago — Webhooks/IPN](https://www.mercadopago.com.br/developers/en/docs/checkout-api-orders/optional-notifications)
- [Mercado Pago — Pix Integration](https://www.mercadopago.com.br/developers/en/docs/checkout-api/how-tos/pix-integration)

---

*Relatório gerado por Mary (Business Analyst) — 28 de Julho de 2026*
