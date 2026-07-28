# Correct Course — Change Proposal
## Incorporação de Compliance de Empregados Domésticos (LC 150/2015)

**Data:** 28/07/2026
**Origem:** Auditoria de Compliance (`docs/auditoria/AUDITORIA_COMPLIANCE_DOMESTICO.md`)
**Impacto:** Expansão do escopo do produto

---

## 1. Change Signal

A auditoria revelou que o sistema atual atende **apenas serviços autônomos avulsos** (proposta → pagamento). Para operar **empregados domésticos** regulados pela LC 150/2015, 8 módulos estão completamente ausentes:

| Módulo Faltante | Risco |
|:----------------|:------|
| Workers (CBO + 9 categorias) | Não é possível onboardar trabalhadores |
| Trava algorítmica de frequência (max 2d/sem) | Descaracterização CLT → passivo trabalhista |
| Ponto eletrônico geolocalizado (GPS + foto) | Violação Art. 12 LC 150 |
| Integração eSocial Doméstico | Passivo tributário (INSS, FGTS, Gilrat) |
| Engine de cálculos trabalhistas | Passivo salarial (HE, noturno, 12×36) |
| Certificação e background check | Risco à segurança de idosos/crianças |
| Incidentes/seguro + CAT | Responsabilidade civil |
| LGPD completo (portabilidade + eliminação) | Multa de até 2% do faturamento |

## 2. Decisões do Correct Course

| Decisão | Ação |
|:--------|:-----|
| **Estratégia** | Adotar modelo híbrido: manter módulo de serviços autônomos + adicionar módulo de empregados domésticos CLT como escopo separado |
| **PRD** | Atualizar v2.0 → v3.0: adicionar Seção de Compliance Doméstico, novos ADRs (ADR-010 a ADR-016), novo roadmap estendido |
| **Arquitetura** | Adicionar invariantes para workers, frequência, ponto eletrônico, eSocial |
| **Épicos** | Adicionar Épicos 8-11 para cobrir todos os 8 módulos faltantes |
| **Prioridade** | Workers + Trava de Frequência + Ponto Eletrônico = crítica (pré-requisito para qualquer operação CLT) |

## 3. Artefatos Modificados

| Artefato | Caminho | Ação |
|:---------|:--------|:-----|
| PRD | `docs/planning/PLANEJAMENTO_MODERNO_PROJETO.md` | Adicionar Seção 14 (Compliance Doméstico), novos ADRs, roadmap extendido |
| Architecture Spine | `_bmad-output/planning-artifacts/architecture/architecture-servicos-20260727/ARCHITECTURE-SPINE.md` | Novos invariantes AD-9 a AD-15 |
| Épicos | `_bmad-output/planning-artifacts/epics.md` | Épicos 8-11 com ~15 novas stories |
| AGENTS.md | `AGENTS.md` | Já atualizado com sessão de auditoria |

## 4. Novo Roadmap (versão estendida)

```
Semanas 1-13: Roadmap original (6 fases) — serviços autônomos
Semanas 10-18: Fase 7 — Workers + Frequência (paralelo com Fase 5)
Semanas 14-20: Fase 8 — Ponto Eletrônico + Jornada
Semanas 17-22: Fase 9 — eSocial Doméstico + Cálculos Trabalhistas
Semanas 20-24: Fase 10 — Certificação + Incidentes + Seguro
```

## 5. Ações Imediatas

| # | Ação | Responsável |
|:-:|:-----|:------------|
| 1 | Atualizar PRD com compliance doméstico | John (PM) |
| 2 | Atualizar Architecture Spine | Winston (Architect) |
| 3 | Atualizar Épicos e Stories | Mary (Analyst) |
| 4 | Gerar Sprint Planning | Amelia (Dev) |
| 5 | Criar primeira story (Workers DB) | Amelia (Dev) |