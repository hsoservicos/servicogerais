# 📜 Política de Retenção de Dados — ServiceSaaS

**Base Legal:** Art. 15-16 da LGPD + Código Tributário Nacional
**Última Atualização:** 2026-07-27

---

## 1. Princípios

1. **Minimização:** Coletar apenas dados estritamente necessários
2. **Proporcionalidade:** Reter apenas pelo tempo necessário à finalidade
3. **Legalidade:** Observar prazos legais de retenção fiscal e regulatória
4. **Transparência:** Informar titulares sobre os prazos aplicáveis

---

## 2. Tabela de Prazos

### 2.1 Dados de Usuários (Prestadores)

| Dado | Prazo Máximo | Base Legal | Ação ao Término |
|:---|---|:---|:---|
| Nome, e-mail, CPF/CNPJ | Até exclusão da conta + 90 dias | Art. 15 | Anonimização |
| Senha (hash) | Até alteração ou exclusão | Necessidade técnica | Sobrescrita |
| Logotipo, dados de perfil | Até exclusão | Art. 15 | Exclusão |
| Histórico de login | 90 dias (Loki) + 5 anos (S3) | Art. 37 | Archive glacial |

### 2.2 Dados de Clientes Finais

| Dado | Prazo Máximo | Base Legal | Ação ao Término |
|:---|---|:---|:---|
| Nome, documentos, contato | 90 dias após inativação | Art. 15 | Anonimização |
| Endereço, observações | 90 dias após inativação | Art. 15 | Anonimização |
| Propostas vinculadas | 5 anos | Art. 16, I + CTN | Archive |

### 2.3 Dados de Propostas

| Dado | Prazo Máximo | Base Legal | Ação ao Término |
|:---|---|:---|:---|
| Propostas (aprovadas/pagas) | 5 anos | Art. 16, I + CTN | Archive glacial |
| Propostas (canceladas/rejeitadas) | 90 dias | Art. 15 | Anonimização |
| Propostas (rascunho) | 30 dias sem edição | Boa prática | Exclusão |

### 2.4 Dados de Pagamento

| Dado | Prazo Máximo | Base Legal | Ação ao Término |
|:---|---|:---|:---|
| Transações (aprovadas) | 5 anos | Art. 16, I + CTN | Archive glacial |
| Transações (rejeitadas/canceladas) | 1 ano | Art. 15 | Anonimização |
| Dados de pagamento (MP) | Conforme política do MP | — | Gerenciado pelo MP |

### 2.5 Logs e Auditoria

| Dado | Prazo Máximo | Base Legal | Ação ao Término |
|:---|---|:---|:---|
| Logs de sistema (Loki) | 90 dias | Boa prática | Deletar |
| Audit logs (S3) | 5 anos | Art. 37 + LGPD Art. 38 | Archive glacial |
| Access logs (Nginx) | 90 dias | Segurança | Deletar |
| Error tracking | 30 dias | Debug | Deletar |

---

## 3. Procedimento de Eliminação

### 3.1 Anonimização

Dados são considerados anonimizados quando não podem mais ser associados a um indivíduo:

```sql
-- Exemplo: anonimização de cliente
UPDATE clients SET
    name = CONCAT('Cliente Anônimo #', id),
    document_cpf = NULL,
    document_cnpj = NULL,
    email = NULL,
    phone = NULL,
    whatsapp = NULL,
    address = NULL,
    notes = 'Dados anonimizados em [data]'
WHERE id = ? AND active = false
  AND updated_at < DATE_SUB(NOW(), INTERVAL 90 DAY);
```

### 3.2 Exclusão Física

Registros sem obrigação legal de retenção podem ser excluídos fisicamente:

```sql
DELETE FROM proposals
WHERE status = 'cancelled'
  AND created_at < DATE_SUB(NOW(), INTERVAL 90 DAY);
```

### 3.3 Archive Glacial

Dados com obrigação fiscal de 5 anos são movidos para cold storage:

1. Exportação JSON dos dados
2. Compressão (gzip)
3. Upload para S3-compatible com lifecycle `glacial` após 90 dias
4. Exclusão da tabela ativa

---

## 4. Exceções

| Situação | Exceção | Fundamento |
|:---|---|:---|
| **Obrigação fiscal** | Retenção de 5 anos | Art. 16, I + CTN |
| **Processo judicial** | Retenção até decisão final | Art. 16, II |
| **Investigação ANPD** | Retenção até conclusão | Art. 16, III |
| **Exercício regular de direitos** | Retenção até prescrição | Art. 16, IV |

---

## 5. Revisão

Esta política deve ser revisada:
- Anualmente
- Quando houver mudança na legislação
- Quando novos tipos de dados forem coletados

---

*Documento mantido conforme Arts. 15-16 da LGPD.*
