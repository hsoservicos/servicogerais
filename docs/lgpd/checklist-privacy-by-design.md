# 🛡️ Checklist Privacy by Design — ServiceSaaS

**Base Legal:** Art. 46-50 da LGPD
**Uso:** Preencher para cada nova funcionalidade antes da implementação

---

## 1. Identificação

| Campo | Resposta |
|:---|---|
| **Feature** | |
| **PRD/Story ID** | |
| **Responsável** | |
| **Data** | |

---

## 2. Checklist de Privacidade

### 2.1 Coleta de Dados

| # | Pergunta | Sim | Não | N/A | Observação |
|:---:|---|---|:---:|:---:|:---:|:---|
| 1 | Esta feature coleta **novos dados pessoais**? | ☐ | ☐ | ☐ | |
| 2 | Se sim, cada campo coletado é **estritamente necessário**? | ☐ | ☐ | ☐ | |
| 3 | Existe **base legal** para cada dado coletado? | ☐ | ☐ | ☐ | |
| 4 | O titular será **informado** sobre a coleta? | ☐ | ☐ | ☐ | |
| 5 | Dados **sensíveis** (raça, religião, saúde, etc.) estão sendo evitados? | ☐ | ☐ | ☐ | |

### 2.2 Armazenamento

| # | Pergunta | Sim | Não | N/A | Observação |
|:---:|---|---|:---:|:---:|:---:|:---|
| 6 | Os dados serão armazenados em **tabela existente ou nova**? | ☐ | ☐ | ☐ | |
| 7 | A criptografia em repouso (MySQL TDE) cobre esta tabela? | ☐ | ☐ | ☐ | |
| 8 | O prazo de **retenção** está definido? | ☐ | ☐ | ☐ | |
| 9 | Haverá **backup** destes dados? | ☐ | ☐ | ☐ | |

### 2.3 Processamento

| # | Pergunta | Sim | Não | N/A | Observação |
|:---:|---|---|:---:|:---:|:---:|:---|
| 10 | O **tenant_id** está presente em toda query? | ☐ | ☐ | ☐ | |
| 11 | Há **prepared statements** para queries SQL? | ☐ | ☐ | ☐ | |
| 12 | O output HTML tem **escape** (htmlspecialchars)? | ☐ | ☐ | ☐ | |
| 13 | Há **rate limiting** aplicável? | ☐ | ☐ | ☐ | |

### 2.4 Compartilhamento

| # | Pergunta | Sim | Não | N/A | Observação |
|:---:|---|---|:---:|:---:|:---:|:---|
| 14 | Esta feature **compartilha dados com terceiros**? | ☐ | ☐ | ☐ | |
| 15 | Se sim, o terceiro tem contrato/DPA adequado? | ☐ | ☐ | ☐ | |
| 16 | O titular é **informado** sobre o compartilhamento? | ☐ | ☐ | ☐ | |

### 2.5 Direitos dos Titulares

| # | Pergunta | Sim | Não | N/A | Observação |
|:---:|---|---|:---:|:---:|:---:|:---|
| 17 | O titular pode **acessar** os dados desta feature? | ☐ | ☐ | ☐ | |
| 18 | O titular pode **corrigir** os dados? | ☐ | ☐ | ☐ | |
| 19 | O titular pode **excluir** os dados? | ☐ | ☐ | ☐ | |
| 20 | A exclusão respeita retenção fiscal de 5 anos? | ☐ | ☐ | ☐ | |

---

## 3. Decisão

| Resultado | Ação |
|:---|---|
| ✅ **Aprovado** | Todos os itens obrigatórios atendidos |
| ⚠️ **Aprovado com ressalvas** | Itens não críticos pendentes, plano de correção anexado |
| ❌ **Reprovado** | Itens críticos não atendidos. Revisar antes de implementar |

**Parecer final:** _________________________________________________

**Data da revisão:** ____/____/________

**Assinatura do responsável:** ______________________________________
