# ServiceSaaS — Product Context

## Platform
Web application (PHP + Node.js REST API), responsive (mobile-first), multi-tenant.

## Users

### Primary: Prestador de Serviço (Professional)
- **Who:** Autônomos e micro-empresas (MEI) do setor de serviços — beleza, manutenção, consultoria, eventos, reformas.
- **Situation:** Usam WhatsApp e caderninho para orçar. Precisam de propostas profissionais rápidas e gestão financeira simples.
- **Needs:** Cadastrar clientes, criar orçamentos em 2 minutos, enviar via WhatsApp, receber pagamento via Pix.

### Secondary: Cliente Final (End Customer)
- **Who:** Pessoa física contratando serviços.
- **Situation:** Quer orçamento rápido e pagar sem burocracia.
- **Needs:** Visualizar proposta no celular, aprovar ou rejeitar com 1 clique, pagar via Pix.

### Tertiary: Administrador da Plataforma
- **Who:** Equipe ServiceSaaS (comercial + suporte).
- **Needs:** Gerenciar tenants, visualizar métricas globais, auditar ações.

## Positioning

**ServiceSaaS é a plataforma que profissionais autônomos usam para transformar o caos de orçamentos manuais em propostas profissionais com 1 clique — e receber pagamento no mesmo dia via Pix.**

## Design Principles

1. **Ação sobre Informação** — Cada tela tem um propósito claro e um próximo passo óbvio. Nada de dashboards contemplativos.
2. **Profissional sem ser Frio** — Azul (#2563EB) como cor primária. Confiável, moderno, profissional.
3. **Mobile-First** — O profissional está na rua, no celular. Tudo funciona em qualquer tela.
4. **Confiança com Solidez** — Sidebar escura (#0F172A) ancora o sistema com autoridade. Dados financeiros com tratamento sério.
5. **Velocidade é Feature** — Criar proposta em < 2 min. Pagamento Pix confirmado em segundos. Sem carregamentos desnecessários.

## Anti-References

- Não usar Inter, Arial ou system-ui (usar Poppins)
- Não fazer sidebar clara — a navegação principal deve usar fundo escuro com contraste máximo
- Não usar tons de azul como cor principal (o verde esmeralda é a identidade)
- Não criar dashboards com gráficos sem ação (cada KPI deve ter um próximo passo)
- Não usar borders/purple gradients (evitar estética "genérica SaaS")

## Evidence on Hand

- Docker Compose com 5 containers (nginx, php, api, pma, mysql)
- API Node.js + Express com 10+ módulos
- Frontend PHP com Tailwind CSS via CDN
- 3 tenants de teste no seed.sql
- Integração Mercado Pago (configurada em produção)
- 7 épicos implementados, 28 stories criadas no GitHub
