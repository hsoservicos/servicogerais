# Política de Retenção de Dados — ServiceSaaS

## Prazos de Retenção

| Categoria | Tipo de Dado | Prazo | Fundamento |
|:----------|:-------------|:-----:|:-----------|
| Cadastro | Dados cadastrais (nome, email, CPF) | 5 anos após inatividade | Obrigação fiscal (arts. 173, 174 CTN) |
| Financeiro | Transações, notas fiscais | 5 anos | Obrigação fiscal (art. 173 CTN) |
| Trabalhista | Contratos CLT, DAE, ponto | 5 anos após fim do vínculo | Obrigação trabalhista (art. 11 CLT) |
| Propostas | Propostas, itens, aprovações | 5 anos | Prescrição contratual (art. 206 CC) |
| Auditoria | Logs de acesso (admin_audit_log) | 5 anos | LGPD (art. 37) |
| Navegação | IP, user-agent, logs de requisição | 90 dias | Minimização de dados (art. 27 LGPD) |
| Consentimento | Registros de consentimento | 5 anos após revogação | Comprovação LGPD (art. 8 §6º) |

## Procedimentos

### Baixa Automática
- Dados de navegação: purge automático via script semanal
- Usuários inativos >5 anos: notificação → anonimização

### Baixa sob Demanda
- Direito de exclusão (art. 18 LGPD): fila de 15 dias → anonimização
- Anonimização mantém registros operacionais sem PII

### Exceções
- Dados necessários para defesa em processo judicial
- Dados anonimizados (não são considerados dados pessoais)
