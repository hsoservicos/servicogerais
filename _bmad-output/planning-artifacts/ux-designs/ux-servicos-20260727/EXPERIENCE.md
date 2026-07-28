---
name: 'ServiceSaaS'
status: final
sources:
  - '{planning_artifacts}/prd_servicos_flex_php_nodejs.md'
  - '{planning_artifacts}/prd_servicos_flex_cloudflare_docker.md'
  - '{planning_artifacts}/PLANEJAMENTO_MODERNO_PROJETO.md'
  - '{planning_artifacts}/architecture/architecture-servicos-20260727/ARCHITECTURE-SPINE.md'
  - 'layout/clienets_especificacao_landing_page_servicos_flex.md'
updated: 2026-07-27
---

# ServiceSaaS — Experience Spine

> Web responsivo (mobile-first). SaaS B2B para prestadores de serviço brasileiros. Duas personas: **Prestador** (usuário pagante, admin do sistema) e **Cliente Final** (aprovador/pagador de propostas). Visual identity reference: `DESIGN.md`.

## Foundation

**Form-factor:** Web responsivo — desktop primário (operação) com suporte total mobile (técnicos em campo).

**UI system:** Nenhum — implementação customizada com PHP + CSS + JS vanilla. `DESIGN.md` é a referência de identidade visual.

**Personas:**
- **Maria** (Prestadora) — Técnica de ar condicionado, MEI, 38 anos. Usa WhatsApp para tudo. Precisa criar propostas rápido e receber pagamento sem burocracia.
- **Carlos** (Cliente Final) — Proprietário de loja, 45 anos. Recebe proposta da Maria pelo WhatsApp, quer aprovar e pagar em 2 cliques sem criar conta.

## Information Architecture

| # | Superfície | Acesso | Propósito | Persona |
|---|---|---|---|---|
| 1 | **Landing Page** | Público (sem login) | Vitrine + busca de serviços | Cliente |
| 2 | **Login** | Público | Autenticação | Prestador |
| 3 | **Cadastro** | Público | Registro PF/PJ | Prestador |
| 4 | **Dashboard** | Privada (pós-login) | KPIs, follow-up, gráfico | Prestador |
| 5 | **Clientes** | Privada | CRUD de clientes | Prestador |
| 6 | **Produtos/Serviços** | Privada | Catálogo de itens | Prestador |
| 7 | **Propostas** | Privada | Listagem + CRUD mestre-detalhe | Prestador |
| 8 | **Proposta Pública** | Pública (token) | Visualizar + aprovar/reprovar/pagar | Cliente |
| 9 | **Financeiro** | Privada | Transações, extrato Mercado Pago | Prestador |
| 10 | **Configurações** | Privada | Perfil, senha, template WhatsApp | Prestador |

### Estrutura de Navegação (Sidebar)

```
[Logo] ServiceSaaS
───────────────
📊 Dashboard
📋 Propostas
👥 Clientes
📦 Produtos/Serviços
💰 Financeiro
⚙️ Configurações
───────────────
👤 Perfil
🚪 Sair
```

Topbar: busca global, notificações, avatar do usuário.

## Voice and Tone

| Situação | Tom | Exemplo |
|---|---|---|
| Ação principal | Direto, confiante | "Criar proposta" — não "Gostaria de criar uma nova proposta?" |
| Sucesso | Caloroso, breve | "Proposta enviada com sucesso!" |
| Erro | Honesto, sem jargão | "Não foi possível conectar ao Mercado Pago. Tente novamente." |
| Vazio (empty state) | Útil, encorajador | "Nenhum cliente cadastrado. Cliente em "+Adicionar" para começar." |
| Aprovação de proposta | Simples, claro | "Clique em Aprovar para aceitar esta proposta." |

**Nunca usar:** jargão técnico ("payload", "endpoint", "token"), linguagem informal excessiva ("show!", "topzera"), linguagem corporativa fria ("Dentro do escopo supracitado").

## Component Patterns

Comportamental. Especificações visuais em `DESIGN.md.Components`.

| Componente | Uso | Regras de comportamento |
|---|---|---|
| **Botão Primário** | Ações principais | Desabilitado se formulário inválido. Loading spinner se operação > 500ms. |
| **Badge de Status** | Propostas, transações | Verde (aprovado/pago), Âmbar (pendente), Azul (andamento), Vermelho (cancelado). |
| **Card de KPI** | Dashboard | Animação de contagem no valor (0 → final em 600ms). Tooltip com detalhes no hover. |
| **Modal** | CRUD, pagamento | Fecha ao clicar fora. Botão de X no canto. Scroll interno se conteúdo longo. |
| **Tabela** | Listagens | Headers fixos. Paginação (20 itens). Ordenação por coluna clicável. Hover row. |
| **Wizard** | Solicitação de serviço (LP) | 3 passos com barra de progresso. Botão "Próximo" desabilitado se campo obrigatório vazio. |
| **Autocomplete** | Busca de serviços | Debounce de 300ms. Mínimo 3 caracteres para disparar busca. Dropdown com 5 resultados. |
| **Mestre-Detalhe** | Formulário de proposta | Tabela de itens no cliente (JS reativo). Cálculo automático em tempo real. Botão "Adicionar Item" inline. |
| **Botão WhatsApp** | Contato com cliente | Abre `wa.me` em nova aba com mensagem pré-formatada. Template substitui `#tags#`. |

## State Patterns

| Estado | Superfície | Tratamento |
|---|---|---|
| **Carregando** | Todas | Skeleton loader (`{colors.border}` animado). Nunca spinner sozinho. |
| **Vazio** | Clientes, Produtos, Propostas | Ilustração + texto útil + CTA. "Nenhuma proposta ainda. Crie sua primeira!" |
| **Erro de rede** | Formulários | Toast/Banner no topo: "Sem conexão. Seus dados estão salvos." Inputs mantêm valores. |
| **Erro de formulário** | Inputs | Borda vermelha + mensagem abaixo do campo. Never alert(). |
| **Sucesso** | Ações | Toast verde no canto superior direito. "Proposta salva!" Desaparece em 3s. |
| **404** | Rotas inválidas | Página com ilustração + "Página não encontrada" + botão "Voltar ao Dashboard". |
| **Sessão expirada** | Rotas privadas | Modal: "Sua sessão expirou. Faça login novamente." Redireciona ao login. |

## Interaction Primitives

- **Click/tap** para ações primárias. **Enter** em formulários para submeter.
- **Hover** em rows de tabela muda fundo para `{colors.primary-50}`.
- **Focus** visível em todos os inputs com anel de `2px solid {colors.primary-500}`.
- **Scroll infinito** em nenhum lugar — paginação com controles.
- **Drag-and-drop** não usado no MVP. Input de data nativo HTML.
- **Banned:** carrosséis automáticos, modais que abrem outros modais, confirmações desnecessárias ("Tem certeza?" para ações não-destrutivas).

## Accessibility Floor

Comportamental. Contraste visual em `DESIGN.md`.

- **Teclado:** Todos os botões, links e inputs navegáveis por Tab. Foco visível com anel de `2px solid {primary-500}`.
- **Leitores de tela:** `aria-label` em botões de ícone, `aria-expanded` em menus, `role` em componentes dinâmicos.
- **Contraste mínimo:** 4.5:1 para texto normal, 3:1 para texto grande (WCAG AA) conforme tokens `{colors.ink-primary}` e `{colors.ink-secondary}`.
- **Touch targets ≥ 44px** em mobile, conforme `{spacing.6}` como referência.
- **Reduced motion:** Respeitar `prefers-reduced-motion`. Animações opcionais.

## Key Flows

### Flow 1 — Maria cria e envia uma proposta (Prestadora)

1. Maria acessa o dashboard. Vê KPIs do mês + follow-up de propostas pendentes.
2. Clica em "Nova Proposta" no card de atalho ou na sidebar > Propostas > Nova.
3. Seleciona Carlos na lista de clientes (autocomplete com 2 caracteres).
4. Adiciona itens: seleciona "Manutenção Preventiva AR" (preço R$ 250,00), quantidade 1. Total calculado automaticamente.
5. Adiciona mais um item: "Instalação" (R$ 150,00). Total atualizado para R$ 400,00.
6. **Clímax:** Clica em "Salvar e Enviar WhatsApp". A proposta é salva (status: sent). O WhatsApp abre com link público da proposta e mensagem: "Olá Carlos! Segue proposta #OS-2026-001 no valor de R$ 400,00. Aprove aqui: [link]"
7. Maria volta ao dashboard. O card de follow-up agora mostra a proposta como "Enviada há 2 min".

**Falha:** WhatsApp não abre → Maria copia o link manualmente. Toast: "Link copiado para a área de transferência."

### Flow 2 — Carlos aprova e paga a proposta (Cliente Final)

1. Carlos recebe o WhatsApp de Maria com o link da proposta.
2. Clica no link. Abre `proposta_publica.php?token=UUID` no navegador.
3. Vê: logo da Maria, número da proposta, itens (descrição + qtd + valor), total R$ 400,00.
4. Botões: **Aprovar** (verde) / **Reprovar** (vermelho).
5. Clica em "Aprovar". Status muda para "Aprovada". Botão "Pagar com Mercado Pago" aparece.
6. **Clímax:** Clica em "Pagar com Mercado Pago". Checkout Bricks abre com opções: Pix (QR Code), Cartão, Boleto. Carlos escolhe Pix, escaneia o QR Code com o celular e paga.
7. Maria recebe notificação no dashboard: proposta paga, valor disponível.

**Falha:** Pagamento recusado → Carlos vê mensagem clara do MP + botão "Tentar outro método".

### Flow 3 — Maria consulta o dashboard (Prestadora)

1. Maria faz login. Dashboard carrega em < 2s.
2. Cards de KPI: 45 Clientes · 28 Propostas este mês · R$ 12.450,00 Aprovadas · 3 Pendentes.
3. Gráfico Chart.js mostra últimos 6 meses com tooltip interativo.
4. **Clímax:** Lista de follow-up mostra 2 propostas enviadas há 3 dias sem resposta. Maria clica no ícone WhatsApp ao lado de cada uma — envia lembrete automático.
5. Abaixo, tabela de "Últimas Propostas" com status, valor e ações rápidas.

**Falha:** Dados não carregam → Skeleton visível por até 5s. Se falhar, toast de erro + botão "Tentar novamente".

### Flow 4 — Novo cliente chega pela Landing Page (Cliente)

1. Carlos acessa a Landing Page pelo Google.
2. Vê: "Encontre os melhores profissionais e solicite orçamentos em segundos."
3. Digita "Ar condicionado" no campo de busca. Autocomplete sugere serviços.
4. Wizard de 3 passos abre:
   - **Passo 1:** Seleciona "Manutenção de Ar Condicionado". Descreve "Aparelho de 12k BTUs não está gelando."
   - **Passo 2:** Seleciona data "25/08/2026" no calendário nativo.
   - **Passo 3:** Preenche nome, WhatsApp, cidade.
5. **Clímax:** Clica em "Solicitar Orçamento". Lead registrado. Página redireciona para lista de profissionais disponíveis com botão "Chamar no WhatsApp".

## Inspiration & Anti-patterns

- **Lifted from Conta Azul:** Sidebar escura com navegação clara, foco em finanças de pequenas empresas brasileiras.
- **Lifted from Nibo:** Cards de KPI com contagem animada, dashboard enxuto sem excesso de informações.
- **Rejected — Dashboard superlotado (Pipefy, Trello):** Uma tela com 20 widgets. ServiceSaaS mostra apenas o essencial: KPIs + follow-up + gráfico.
- **Rejected — Mobile-first que sacrifica desktop (Nubank):** A operação principal é no desktop (criação de propostas). Mobile é consulta e aprovação.
- **Rejected — Gamificação (streaks, badges):** Prestador de serviço não quer game — quer dinheiro no bolso. A motivação é financeira, não lúdica.
