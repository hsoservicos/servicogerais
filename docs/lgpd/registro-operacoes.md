# 📋 Registro das Operações de Tratamento de Dados Pessoais

> **LGPD — Lei Geral de Proteção de Dados (Lei nº 13.709/2018)**
> **Controlador:** Prestador de Serviço (cliente do ServiceSaaS)
> **Operador:** ServiceSaaS (Bueno Tecnologia Ltda)
> **Atualização:** Julho/2026

---

## 1. Mapeamento de Dados Pessoais

| # | Dado | Categoria | Finalidade | Base Legal | Retenção | Compartilhamento |
|:---:|---|:---:|---|:---:|:---:|:---:|
| 1 | Nome completo | Identificação | Cadastro e identificação do prestador | Art. 7°, V (execução de contrato) | 5 anos fiscais | — |
| 2 | CPF | Identificação fiscal | Faturamento e obrigações fiscais | Art. 7°, V + Art. 11, II (obrigação legal) | 5 anos fiscais | Contador do prestador |
| 3 | CNPJ | Identificação fiscal | Faturamento e obrigações fiscais | Art. 7°, V + Art. 11, II (obrigação legal) | 5 anos fiscais | Contador do prestador |
| 4 | E-mail | Contato | Comunicação operacional e recuperação de senha | Art. 7°, V (execução de contrato) | 90 dias após cancelamento | — |
| 5 | Telefone / WhatsApp | Contato | Comunicação com clientes do prestador | Art. 7°, V (execução de contrato) | 90 dias após cancelamento | — |
| 6 | Endereço | Identificação | Faturamento e entrega de serviços | Art. 7°, V (execução de contrato) | 5 anos fiscais | — |
| 7 | Nome de clientes | Identificação | Execução do serviço contratado | Art. 7°, V (execução de contrato) | 90 dias após cancelamento | — |
| 8 | Dados bancários | Financeiro | Recebimento de pagamentos (Mercado Pago) | Art. 7°, V (execução de contrato) | 5 anos fiscais | Mercado Pago (instituição de pagamento) |
| 9 | Logs de acesso | Segurança | Auditoria e segurança da informação | Art. 7°, IX (legítimo interesse) | 90 dias (logs) / 5 anos (audit) | — |
| 10 | Consentimento LGPD | Compliance | Registro de consentimento do titular | Art. 7°, I (consentimento) | 5 anos | — |

---

## 2. Base Legal por Atividade

| Atividade | Base Legal | Artigos |
|:---|---|:---:|
| Cadastro de prestador | Execução de contrato | Art. 7°, V |
| Processamento de pagamentos | Execução de contrato + obrigação legal | Art. 7°, V + Art. 11, II |
| Envio de comunicações | Consentimento | Art. 7°, I (opt-in) |
| Compartilhamento com MP | Execução de contrato | Art. 7°, V |
| Logs de auditoria | Legítimo interesse | Art. 7°, IX |
| Cumprimento fiscal | Obrigação legal | Art. 11, II |

---

## 3. Medidas de Segurança

| Medida | Status | Descrição |
|:---|---|:---|
| Criptografia em trânsito | ✅ | HTTPS via Cloudflare SSL/TLS (NFR-11) |
| Prepared Statements | ✅ | mysql2 `?` params — SQLi prevention (NFR-07) |
| Hash de senhas | ✅ | bcrypt rounds=12 (NFR-10) |
| JWT com expiração | ✅ | 24h, Authorization Bearer (NFR-10) |
| Rate limiting | ✅ | express-rate-limit (NFR-09) |
| Output escaping | ✅ | htmlspecialchars no PHP (NFR-08) |
| Isolamento multi-tenant | ✅ | tenant_id injetado via middleware (NFR-15) |
| Session segura | ✅ | httpOnly, SameSite=Lax, strict mode |
| Criptografia em repouso | ⏳ | MySQL TDE — v1.1 (NFR-12) |
| Backup criptografado | ⏳ | v1.1 |

---

## 4. Direitos dos Titulares (Art. 18 LGPD)

| Direito | Implementação |
|:---|---|
| Confirmação de tratamento | GET /api/v1/account/data |
| Acesso aos dados | GET /api/v1/account/data |
| Correção | PUT /api/v1/account |
| Anonimização/exclusão | POST /api/v1/data-subject-request (em desenvolvimento) |
| Portabilidade | POST /api/v1/data-subject-request (em desenvolvimento) |
| Revogação de consentimento | POST /api/v1/lgpd/revoke (em desenvolvimento) |

**Canal DPO:** privacidade@servicesaas.com.br

---

## 5. Incidentes

| Data | Tipo | Descrição | Status |
|:---:|:---|---|:---:|
| — | — | Nenhum incidente registrado | ✅ |

**Procedimento:** Em caso de incidente, notificar ANPD em até 72h (Resolução ANPD nº 15/2024).
