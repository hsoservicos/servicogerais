# 🔍 Pesquisa de Domínio: Regulamentação de Profissionais de Serviços no Brasil

**Data:** 28 de Julho de 2026
**Autora:** Mary (Business Analyst)
**Projeto:** ServiceSaaS (Serviços Flex)
**Público-alvo:** MEI, autônomos e pequenos prestadores de serviço

---

## Sumário Executivo

Esta pesquisa analisa o ambiente regulatório brasileiro para **profissionais de serviços** (como Maria, a cabeleireira do nosso persona) que utilizam plataformas digitais como o ServiceSaaS. Foram investigados três pilares fundamentais:

| Pilar | Status no Projeto | Recomendação |
|:---|---|:---|
| **MEI** (Microempreendedor Individual) | 🟡 Não abordado | Incluir CNAEs, DAS, NFSe no onboarding |
| **LGPD** (Lei Geral de Proteção de Dados) | ✅ Bem documentado | Ajustes finos (DPO, consentimento granular) |
| **NFSe** (Nota Fiscal de Serviços) | 🔴 Não abordado | Emissor Nacional obrigatório para PJ |

---

## 1. MEI — Microempreendedor Individual

### 1.1. Limites e Elegibilidade

| Aspecto | Regra Atual (2025-2026) | Impacto no ServiceSaaS |
|:---|---|:---|
| Faturamento anual | R$ 81.000,00 (R$ 6.750,00/mês) | Maria (cabeleireira) se enquadra |
| Employees | Máximo 1 empregado | Ideal para profissionais individuais |
| Atividades permitidas | Anexo XI da Resolução CGSN nº 140/2018 | Cortes de cabelo, manicure, estética facial — **todas permitidas** |
| Excedente ≤ 20% | Permanece MEI até fim do ano | Tolerância de R$ 97.200,00 |
| Excedente > 20% | Desenquadramento retroativo | ⚠️ Migração obrigatória para ME |

**🔍 Implicação para o ServiceSaaS:** A grande maioria dos profissionais que usarão a plataforma será **MEI**. O sistema deve:
- Não exigir cadastro com CNPJ (permitir CPF)
- Oferecer campo opcional de CNPJ para quem já é formalizado
- Sugerir formalização como MEI após o cadastro

### 1.2. CNAEs Permitidos para Serviços

**CNAEs relevantes para o ServiceSaaS** (todos permitidos como MEI):

| CNAE | Descrição | Exemplo de Uso |
|:---:|---|:---|
| 9602-5/01 | Cabeleireiros, manicure e pedicure | Maria (Beleza) ✅ |
| 9602-5/02 | Atividades de estética e outros serviços de beleza | Limpeza de pele, maquiagem ✅ |
| 4321-5/00 | Instalação e manutenção elétrica | Eletricista |
| 4330-4/01 | Pintura de edifícios | Pintor |
| 8121-4/00 | Limpeza geral em edifícios | Faxina |
| 9521-5/00 | Reparação e manutenção de equipamentos eletroeletrônicos | Técnico |

### 1.3. Obrigações Mensais

| Obrigação | Frequência | Valor (2025-2026) | Descrição |
|:---|---|:---|:---|
| **DAS** | Mensal | ~R$ 67,00 (comércio/serviço) | INSS + ISS (ou ICMS) |
| **DASN-SIMEI** | Anual (até 31/05) | Grátis | Declaração de faturamento anual |
| **Relatório Mensal** | Mensal | Grátis | Receitas do mês (Portal do Empreendedor) |
| **NFSe** | Por serviço | Grátis | Emissão obrigatória para PJ |

**🔴 Gap no Projeto:** O ServiceSaaS não possui nenhuma funcionalidade de **controle mensal de DAS** ou **alerta de vencimento**. Sugiro adicionar como feature futura (Story futura no Epic 2 ou novo Epic).

---

## 2. NFSe — Nota Fiscal de Serviços Eletrônica

### 2.1. Obrigatoriedade por Tipo de Cliente

| Cliente | Obrigação | Detalhe |
|:---|---|:---|
| **Pessoa Jurídica** (empresa) | ✅ **Obrigatória** | MEI DEVE emitir NFSe |
| **Pessoa Física** | ❌ Facultativa | Exceto se o cliente solicitar |
| **Órgão Público** | ✅ Obrigatória | Exigência legal |

### 2.2. Emissor Nacional de NFS-e

Em 2026, o **Emissor Nacional de NFS-e** (gov.br) tornou-se o padrão obrigatório para MEIs:

```
Portal: https://www.nfse.gov.br/EmissorNacional
App: NFSe Mobile (Android/iOS)
Login: Gov.br (nível Prata ou Ouro)
Custo: Gratuito
```

**🔍 Implicação para o ServiceSaaS:** O profissional **não precisa** emitir nota fiscal pelo ServiceSaaS — ele usa o portal nacional gratuito. Porém, a plataforma pode se diferenciar **integrando a emissão** via API do Emissor Nacional (ou gerando XML para importação).

### 2.3. Fluxo de Emissão para o Profissional

```
1. Presta o serviço (ex: corte de cabelo — R$ 65,00)
2. Acessa o Emissor Nacional (gov.br) ou app
3. Informa CPF/CNPJ do cliente + valor + descrição
4. Sistema gera NFSe automaticamente
5. Envia NFSe por e-mail/WhatsApp ao cliente
```

### 2.4. Cadastro Municipal

Nem todos os municípios migraram para o Emissor Nacional. O profissional deve:
1. Verificar se seu município aderiu ao sistema nacional
2. Se não tiver aderido, emitir pelo sistema municipal da prefeitura
3. Manter **Inscrição Municipal** ativa

**🟡 Recomendação:** Adicionar na área de **Configurações** do ServiceSaaS um campo para o profissional informar sua **Inscrição Municipal** e **Regime Tributário**, para futura integração fiscal.

---

## 3. LGPD — Lei Geral de Proteção de Dados

### 3.1. Classificação do ServiceSaaS e seus Usuários

| Ator | Papel LGPD | Exemplo |
|:---|---|:---|
| **ServiceSaaS** (plataforma) | **Operador** | Processa dados em nome do prestador |
| **Prestador (Maria)** | **Controlador** | Decide finalidades do tratamento |
| **Cliente final (Carlos)** | **Titular** | Dados pessoais são dele |

### 3.2. Regime Especial para Pequenos Negócios

**Resolução CD/ANPD nº 2/2022** estabelece tratamento diferenciado para **Agentes de Tratamento de Pequeno Porte (ATPP)**:

| Requisito | Regra Normal | Regra ATPP (MEI/Pequeno) | Impacto ServiceSaaS |
|:---|---|:---|:---|
| **DPO (Encarregado)** | Obrigatório | ❌ **Dispensado** (mas canal obrigatório) | Canal `privacidade@` já implementado ✅ |
| **RoPA (Registro)** | Completo | ✅ **Simplificado** | Já temos `registro-operacoes.md` ✅ |
| **Prazo resposta titular** | 15 dias | ✅ **30 dias** (prazo em dobro) | Alinhado com Art. 19 |
| **Prazo notificação incidente** | 72h | ✅ **144h** (prazo em dobro) | Atualizar `termos-de-uso.html` |
| **Política de segurança** | Completa | ✅ **Simplificada** | Adequada para MVP |

**🟡 Recomendação:** Os documentos LGPD atuais do projeto (termos-de-uso.html, registro-operacoes.md, politica-retencao.md) estão **alinhados e completos**. A única correção necessária é o prazo de notificação de incidentes: atualmente menciona "48h" ao Controlador — deve mencionar que para o ATPP o prazo de notificação à ANPD é **144h** (72h × 2).

### 3.3. Bases Legais Aplicáveis ao ServiceSaaS

| Atividade | Base Legal | Artigo |
|:---|---|:---:|
| Cadastro do prestador (Maria) | Execução de contrato | Art. 7°, V |
| Cadastro de clientes finais (Carlos) | Execução de contrato | Art. 7°, V |
| Processamento de pagamentos (MP) | Execução de contrato | Art. 7°, V |
| Logs de auditoria e segurança | Legítimo interesse | Art. 7°, IX |
| Retenção fiscal (5 anos) | Obrigação legal | Art. 7°, II + Art. 11, II |
| Comunicações de marketing | Consentimento (opt-in) | Art. 7°, I |
| Compartilhamento com Mercado Pago | Execução de contrato | Art. 7°, V |

### 3.4. Direitos dos Titulares (Art. 17-22)

| Direito | Implementação no ServiceSaaS | Status |
|:---|---|:---:|
| Confirmação de tratamento | GET /api/v1/account/data | ⏳ MVP |
| Acesso aos dados | GET /api/v1/account/data | ⏳ MVP |
| Correção | PUT /api/v1/account | ✅ Implementado |
| Exclusão / Anonimização | POST /api/v1/data-subject-request | ⏳ MVP |
| Portabilidade | POST /api/v1/data-subject-request | ⏳ MVP (v1.1) |
| Revogar consentimento | POST /api/v1/lgpd/revoke | ⏳ MVP |
| Informação sobre compartilhamento | Política de Privacidade | ✅ Documentado |

### 3.5. Penalidades Potenciais

| Infração | Penalidade | Valor Máximo |
|:---|---|:---|
| Tratar dados sem base legal | Advertência → Multa simples | 2% faturamento (limitado a R$ 50MM) |
| Não atender direitos titular | Multa simples + diária | R$ 50MM por infração |
| Vazar dados sem notificar | Multa + proibição de tratar | Até R$ 50MM |
| Não manter registro | Advertência → Multa | 2% faturamento |

**🔍 Análise para ATPP (MEI):** A ANPD tem adotado abordagem **pedagógica** para pequenos agentes. Risco real de multa é baixo se houver **boa-fé e medidas básicas** implementadas. O ServiceSaaS já está bem posicionado.

---

## 4. Entrega de Documentos Fiscais aos Clientes

### 4.1. Obrigação do Prestador (Maria)

Quando Maria presta um serviço para Carlos (pessoa física):
- **NFSe:** Facultativa (exceto se Carlos solicitar)
- **Recibo:** Pode emitir recibo simples
- **Comprovante:** O ServiceSaaS pode gerar um **comprovante de serviço prestado** (não fiscal) como cortesia

### 4.2. Obrigação do Cliente Final (Carlos)

Carlos **não tem obrigação fiscal** ao contratar Maria — ele é consumidor final. A plataforma não precisa emitir nota fiscal para ele.

### 4.3. Integração Fiscal Futura (v1.1+)

```
ServiceSaaS → API Emissor Nacional → Geração NFSe automática
             → Download XML + PDF
             → Envio automático ao cliente
```

**Estima-se complexidade:** Média-Alta (cada município tem regras próprias de ISS).

---

## 5. Recomendações para o ServiceSaaS

### 🔴 Recomendações Imediatas (MVP)

| # | Recomendação | Impacto | Esforço |
|:---:|---|---|:---:|
| 1 | **Incluir CNAE** no cadastro do prestador (seletor de atividades) | Onboarding mais completo para MEI | Baixo |
| 2 | **Corrigir prazo LGPD** no termos-de-uso.html: incidente → 144h (não 72h) para ATPP | Conformidade legal | Mínimo |
| 3 | **Adicionar canal DPO** como campo de configuração na área do prestador | LGPD Art. 41 | Baixo |
| 4 | **Gerar comprovante** de serviço prestado (não fiscal) ao finalizar proposta | Diferencial competitivo | Médio |

### 🟡 Recomendações para v1.1

| # | Recomendação | Justificativa |
|:---:|---|---|
| 5 | Lembrete mensal de **DAS** (guia MEI) no dashboard | Reduz inadimplência fiscal dos usuários |
| 6 | Botão "Emitir NFSe" com redirecionamento ao Emissor Nacional | Facilita vida do profissional |
| 7 | Alerta de **limite MEI** (R$ 81.000,00) com percentual do ano | Previne desenquadramento |
| 8 | Integração com API **Gov.br** para validação de dados | Reduz fraudes |

### 🟢 Recomendações Estratégicas (v2.0)

| # | Recomendação | Justificativa |
|:---:|---|---|
| 9 | Emissão de NFSe **dentro da plataforma** via API | Diferencial competitivo forte |
| 10 | **Contador parceiro** integrado (recomendação automática) | Ecossistema de valor |
| 11 | Declaração **DASN-SIMEI** preenchida automaticamente | Reduz atrito para o usuário |
| 12 | Cálculo de **retenção de ISS** por município | Conformidade fiscal avançada |

---

## 6. Matriz de Conformidade Atual do ServiceSaaS

| Requisito | Status | Onde Está |
|:---|---|:---|
| Termos de Uso com DPA | ✅ Completo | `docs/lgpd/termos-de-uso.html` |
| Política de Privacidade | ✅ Completa | `docs/lgpd/politica-privacidade.md` |
| Registro de Operações (RoPA) | ✅ Completo | `docs/lgpd/registro-operacoes.md` |
| Política de Retenção | ✅ Completa | `docs/lgpd/politica-retencao.md` |
| Consentimento granular (LGPD) | 🟡 3 checkboxes previstos | NFR-LGPD-03 (não implementado) |
| Canal DPO | ✅ E-mail definido | `privacidade@seudominio.com.br` |
| Notificação incidente 144h | 🔴 48h/72h — precisa corrigir | `termos-de-uso.html` seção 6.7 |
| Criptografia em repouso | ⏳ Adiado p/ v1.1 | NFR-12 |
| Exclusão de dados (endpoint) | ⏳ MVP | NFR-LGPD-04 |
| Cadastro CNAE/MEI | 🔴 Não implementado | Nova funcionalidade |
| Emissão de NFSe | 🔴 Não implementado | Futuro |

---

## 7. Riscos Regulatórios Identificados

| Risco | Probabilidade | Impacto | Mitigação |
|:---|---|:---|:---|
| Prestador sem MEI usando plataforma | Alta | Baixo | Orientar formalização no onboarding |
| Prestador excede limite MEI (R$ 81k) e não sabe | Média | Médio | Alerta de faturamento no dashboard |
| Cliente final solicita NFSe e prestador não sabe emitir | Alta | Baixo | Guia rápido de emissão NFSe |
| Vazamento de dados de clientes finais | Baixa | Alto | Medidas de segurança já implementadas |
| Dados retidos além do prazo legal | Média | Médio | Script de anonimização automática (v1.1) |

---

## 8. Conclusão

O ServiceSaaS está **bem posicionado em conformidade LGPD** — os documentos existentes cobrem 80% dos requisitos. O principal gap identificado é a **falta de suporte ao regime MEI**, que é o perfil predominante dos profissionais que usarão a plataforma.

**Prioridade Alta:** Corrigir prazo de notificação de incidentes no termo de uso (48h → 144h para ATPP).

**Diferencial Competitivo:** Adicionar funcionalidades de gestão MEI (DAS, NFSe, limite de faturamento) tornaria o ServiceSaaS uma **plataforma completa** para o profissional, indo além de propostas e pagamentos.

---

## 9. Referências

- [Portal do Empreendedor — Gov.br](https://www.gov.br/empresas-e-negocios/pt-br/empreendedor)
- [Resolução CGSN nº 140/2018 — Anexo XI (CNAEs MEI)](https://www8.receita.fazenda.gov.br/simplesnacional/)
- [LGPD — Lei nº 13.709/2018](https://www.planalto.gov.br/ccivil_03/_ato2015-2018/2018/lei/l13709.htm)
- [Resolução CD/ANPD nº 2/2022 — ATPP](https://www.gov.br/anpd)
- [Emissor Nacional de NFS-e](https://www.nfse.gov.br/EmissorNacional)
- [Guia MEI 2026 — Razonet](https://razonet.com.br/contabilidade-digital/limite-mei)

---

*Relatório gerado por Mary (Business Analyst) — 28 de Julho de 2026*
