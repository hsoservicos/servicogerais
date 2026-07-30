# Registro de Operações com Dados Pessoais — ServiceSaaS

## Controlador
- **Nome:** ServiceSaaS Tecnologia Ltda
- **DPO:** privacidade@servicesaas.com
- **Base Legal:** Lei 13.709/2018 (LGPD)

## Inventário de Dados

### Dados Coletados

| Tipo | Dado | Finalidade | Base Legal | Retenção |
|:-----|:-----|:-----------|:-----------|:---------|
| Identificação | Nome completo | Identificação do usuário | Execução de contrato | 5 anos |
| Identificação | CPF/CNPJ | Faturamento, validação fiscal | Obrigação legal | 5 anos |
| Contato | E-mail | Comunicação, recuperação de senha | Execução de contrato | 5 anos |
| Contato | Telefone/WhatsApp | Contato comercial | Interesse legítimo | 5 anos |
| Financeiro | Dados de pagamento MP | Processamento de pagamentos | Execução de contrato | 5 anos |
| Trabalhista | RG, CBO, categoria | Compliance trabalhista LC 150 | Obrigação legal | 5 anos após fim vínculo |
| Geolocalização | Endereço, CEP | Busca por proximidade | Execução de contrato | 5 anos |
| Navegação | IP, user-agent | Segurança, auditoria | Interesse legítimo | 90 dias |

### Fluxos de Dados

1. **Cadastro** → Usuário → API (Node.js) → MySQL
2. **Proposta** → Prestador → API → Cliente (via WhatsApp)
3. **Pagamento** → Cliente → Mercado Pago (processador) → Webhook → API
4. **Exportação** → API → Usuário (download JSON)
5. **Deleção** → Usuário → API → Fila 15 dias → Anonimização

### Compartilhamento

| Parceiro | Dados | Finalidade |
|:---------|:------|:-----------|
| Mercado Pago | Itens, valor, pagador | Processamento de pagamentos |
| WhatsApp | Link da proposta | Compartilhamento com cliente |

## Medidas de Segurança

- Senhas: bcrypt (12 rounds)
- Transporte: HTTPS (planejado via Cloudflare)
- API: Helmet, CORS, Rate Limiting
- Auditoria: admin_audit_log + audit_log
- Isolamento multi-tenant via tenant_id
