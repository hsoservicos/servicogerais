# 🚨 Plano de Resposta a Incidentes de Segurança

**Base Legal:** Resolução ANPD nº 15/2024 + Art. 46-48 da LGPD
**Última Atualização:** 2026-07-27

---

## 1. Objetivo

Estabelecer procedimentos para detecção, contenção, investigação e notificação de incidentes de segurança envolvendo dados pessoais, em conformidade com a LGPD e a Resolução ANPD nº 15/2024.

---

## 2. Definições

| Termo | Definição |
|:---|---|
| **Incidente** | Qualquer evento adverso confirmado que comprometa a confidencialidade, integridade ou disponibilidade de dados pessoais |
| **Violação** | Incidente que resulte em acesso, destruição, perda, alteração ou difusão não autorizada de dados pessoais |
| **ANPD** | Autoridade Nacional de Proteção de Dados |
| **Titular** | Pessoa física a quem pertencem os dados |

---

## 3. Classificação de Incidentes

| Nível | Descrição | Exemplo | Prazo ANPD |
|:---:|:---|---|:---:|
| 🔴 **Crítico** | Vazamento massivo de dados pessoais (> 1000 registros) | Exposição de tabela `clients` | 72h |
| 🟡 **Alto** | Acesso não autorizado a dados de tenant específico | Invasão de conta de prestador | 72h |
| 🟡 **Médio** | Indisponibilidade prolongada (> 2h) | DDoS, falha de servidor | 72h (se houver risco) |
| 🟢 **Baixo** | Tentativa de acesso bloqueada | Brute force contido pelo rate limit | Não notifica |

---

## 4. Fluxo de Resposta

### 4.1 Detecção (Fase 1)

**Canais de detecção:**
- ✅ Monitoramento de logs (Loki + Grafana)
- ✅ Alertas de uptime (API Down, Latência > 1s, Erro Rate > 5%)
- ✅ Usuários reportando atividade suspeita via `privacidade@seudominio.com.br`
- ✅ Cloudflare Security Events (WAF, DDoS)

### 4.2 Triagem (Fase 2) — ≤ 1h

```bash
# Comandos iniciais de investigação
docker logs flex_api_node --tail 100 --since 10m | grep ERROR
docker logs flex_nginx --tail 100 --since 10m | grep "4[0-9][0-9]\|5[0-9][0-9]"
docker logs flex_frontend_php --tail 100 --since 10m | grep "PHP Fatal error"
```

### 4.3 Contenção (Fase 3) — ≤ 2h

| Ação | Comando |
|:---|---|
| Isolar container comprometido | `docker stop <container>` |
| Bloquear IP ofensor | `docker exec flex_nginx sh -c "echo 'deny <IP>;' >> /etc/nginx/conf.d/block.conf && nginx -s reload"` |
| Rotacionar chaves | Atualizar JWT_SECRET, MERCADO_PAGO_ACCESS_TOKEN |
| Ativar modo de manutenção | `docker exec flex_nginx cp /etc/nginx/maintenance.conf /etc/nginx/default.conf` |

### 4.4 Investigação (Fase 4) — ≤ 24h

**Checklist de investigação:**

1. Quais dados foram acessados/exfiltrados?
2. Quantos titulares foram afetados?
3. Qual a causa raiz?
4. Quanto tempo durou o acesso não autorizado?
5. O incidente está contido?

### 4.5 Notificação (Fase 5) — ≤ 72h

#### Para a ANPD

**Template de notificação (Resolução ANPD nº 15/2024):**

```json
{
  "data_incidente": "2026-07-27T14:30:00Z",
  "natureza": "Acesso não autorizado a dados pessoais",
  "categorias_dados_afetados": [
    "Nomes", "CPF", "E-mails", "Telefones"
  ],
  "volume_estimado": 150,
  "circunstancias": "Falha de segurança em [causa]",
  "medidas_contencao": [
    "Container isolado",
    "Chaves rotacionadas",
    "Patch de segurança aplicado"
  ],
  "medidas_mitigacao": [
    "Usuários notificados",
    "Senhas resetadas"
  ],
  "contato": "privacidade@seudominio.com.br"
}
```

#### Para os Titulares

**Template de comunicação:**

```
ASSUNTO: Notificação de Incidente de Segurança — ServiceSaaS

Prezado [Nome],

Identificamos um incidente de segurança em nossa plataforma na data [Data].
Os seguintes dados podem ter sido afetados: [categorias].

Já tomamos as seguintes medidas:
- [Medida 1]
- [Medida 2]

Recomendamos que você:
- Altere sua senha
- Fique atento a comunicações suspeitas

Qualquer dúvida, contate: privacidade@seudominio.com.br

Atenciosamente,
Equipe ServiceSaaS
```

### 4.6 Correção (Fase 6) — ≤ 48h após notificação

1. Aplicar patch de segurança
2. Validar que vulnerabilidade foi fechada
3. Restaurar serviços do backup íntegro
4. Testar em staging antes de produção

### 4.7 Post-mortem (Fase 7) — ≤ 7 dias

Documentar:
- Timeline completa do incidente
- Causa raiz
- O que funcionou bem
- O que precisa melhorar
- Ações corretivas com prazos

---

## 5. Contatos de Emergência

| Função | Contato |
|:---|---|
| **DPO / Encarregado** | `privacidade@seudominio.com.br` |
| **Equipe Técnica** | `dev@seudominio.com.br` |
| **ANPD** | `https://www.gov.br/anpd` |

---

## 6. Testes e Simulados

| Tipo | Frequência | Responsável |
|:---|---|:---|
| Teste de backup/restore | Mensal | Equipe Técnica |
| Simulado de incidente | Trimestral | DPO + Equipe Técnica |
| Revisão do plano | Semestral | DPO |

---

*Documento mantido conforme Resolução ANPD nº 15/2024.*

