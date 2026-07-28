# Impeccable — Documentação de Instalação

## 📋 Resumo

**Impeccable** (v3.4.0) é uma ferramenta de design guidance para agentes de IA criada por Paul Bakaus. Ela adiciona um vocabulário compartilhado de 23 comandos de design, um detector determinístico com 60+ regras e hooks de design em tempo real para melhorar a qualidade visual do código gerado por IA.

**Repositório:** https://github.com/pbakaus/impeccable
**Site oficial:** https://impeccable.style/
**NPM:** `npx impeccable`

---

## 🚀 Instalação

### 1. Instalação Global via CLI

```bash
# A partir da raiz do projeto
npx impeccable install --scope=global --providers=claude --yes
```

Isso instala:
- **Skill:** `~/.claude/skills/impeccable/` — comando `/impeccable` disponível no Claude Code
- **Scripts:** `scripts/hook.mjs`, `scripts/hook-before-edit.mjs`, `scripts/hook-admin.mjs`, `scripts/live-server.mjs`
- **Detector CLI:** `npx impeccable detect` — análise standalone sem LLM

### 2. Verificação da Instalação

```bash
# Verificar versão
npx impeccable --version
# → 3.4.0

# Verificar skill instalada
ls ~/.claude/skills/impeccable/
# → SKILL.md  reference/  scripts/

# Verificar comandos disponíveis
npx impeccable --help
```

### 3. Estrutura do Projeto

Os seguintes arquivos foram criados/configurados:

```
📁 projeto/
├── 📄 PRODUCT.md          # Contexto estratégico do produto (Impeccable)
├── 📄 DESIGN.md           # Sistema de design formal (Impeccable YAML)
├── 📄 .gitignore          # → Regras para arquivos efêmeros do Impeccable
└── 📁 .impeccable/
    ├── 📄 config.json     # Configuração compartilhada do projeto
    ├── 📁 critique/       # Relatórios de review
    └── 📁 live/           # Estado do live-mode
```

---

## 🎯 Comandos Disponíveis

### Comandos de Design (via AI Harness)

| Comando | O que faz |
|:---|---|
| `/impeccable craft` | Fluxo completo shape-then-build com iteração visual |
| `/impeccable init` | Setup inicial: contexto de design, PRODUCT.md, DESIGN.md |
| `/impeccable document` | Gera DESIGN.md a partir do código existente |
| `/impeccable extract` | Extrai componentes e tokens para o design system |
| `/impeccable shape` | Planeja UX/UI antes de escrever código |
| `/impeccable critique` | Revisão de design UX (hierarquia, clareza, ressonância) |
| `/impeccable audit` | Verificações técnicas (a11y, performance, responsivo) |
| `/impeccable polish` | Passada final, alinhamento com design system |
| `/impeccable bolder` | Amplifica designs sem graça |
| `/impeccable quieter` | Suaviza designs muito ousados |
| `/impeccable distill` | Reduz à essência |
| `/impeccable harden` | Tratamento de erros, i18n, edge cases |
| `/impeccable onboard` | Fluxos de primeira execução, empty states |
| `/impeccable animate` | Adiciona movimento proposital |
| `/impeccable colorize` | Introduz cor estratégica |
| `/impeccable typeset` | Ajusta fontes, hierarquia, sizing |
| `/impeccable layout` | Ajusta layout, espaçamento, ritmo visual |
| `/impeccable delight` | Adiciona momentos de alegria |
| `/impeccable overdrive` | Efeitos tecnicamente extraordinários |
| `/impeccable clarify` | Melhora cópia UX confusa |
| `/impeccable adapt` | Adapta para diferentes dispositivos |
| `/impeccable optimize` | Melhorias de performance |
| `/impeccable live` | Modo de variante visual: itera no browser |

### CLI Standalone (Detector)

```bash
# Escanear diretório
npx impeccable detect src/

# Escanear arquivo
npx impeccable detect index.html

# Escanear URL (usa Puppeteer)
npx impeccable detect https://example.com

# Output JSON (amigável para CI)
npx impeccable detect --json .

# Escopo específico
npx impeccable detect --scope=type,layout src/

# Modo silencioso (só contagem de falhas)
npx impeccable detect --quiet src/
```

### Atalhos (Pinning)

Para comandos usados com frequência:

```bash
/impeccable pin audit    # Cria atalho /audit
/impeccable pin polish   # Cria atalho /polish
```

---

## 📄 Arquivos de Contexto de Design

### PRODUCT.md (Estratégia)

Contém contexto de alto nível do produto:
- **Platform:** Web app (PHP + Node.js), mobile-first, multi-tenant
- **Users:** Prestador (primário), Cliente Final (secundário), Admin (terciário)
- **Positioning:** Propostas profissionais com 1 clique + Pix
- **Design Principles:** 5 princípios de design
- **Anti-References:** O que evitar (Inter, sidebar clara, gradients roxos)
- **Evidence on Hand:** Stack atual, módulos, integrações

### DESIGN.md (Sistema Visual)

Contém especificações visuais técnicas em formato YAML:
- **Colors:** Paleta completa (#10B981 primary, #0F172A sidebar, status colors)
- **Typography:** Poppins (sans), JetBrains Mono (mono), escala completa
- **Components:** Botões, cards, inputs, badges, sidebar, tabela, modal
- **Elevation:** Sombras (sm/md/xl)
- **Motion:** Transições (duration-200/300), scale feedback, fade-in
- **Anti-Patterns:** 10 regras do que não fazer

---

## 🔧 Integração com o Projeto ServiceSaaS

### Como usar no desenvolvimento

1. **Antes de criar uma tela:**
   ```bash
   /impeccable shape "tela de login"
   ```

2. **Durante o desenvolvimento:**
   ```bash
   /impeccable audit a página de login
   ```

3. **Antes do deploy:**
   ```bash
   /impeccable polish a página de checkout
   /impeccable harden o formulário de cadastro
   ```

4. **Revisão de design:**
   ```bash
   /impeccable critique o dashboard
   ```

### No pipeline CI/CD

```bash
npx impeccable detect --json web-frontend/templates/ --quiet
```

Pode ser integrado ao GitHub Actions como quality gate.

---

## 📊 Resultado dos Testes

| Teste | Resultado |
|:---|---:|
| Instalação global | ✅ v3.4.0 |
| Skills (Claude Code) | ✅ `~/.claude/skills/impeccable/` |
| Detector CLI | ✅ Funcional |
| PRODUCT.md | ✅ Criado |
| DESIGN.md | ✅ Criado |
| .gitignore | ✅ Atualizado |
| Detector em templates | ✅ 0 anti-patterns encontrados |

---

## 🔗 Links Úteis

- **Repositório:** https://github.com/pbakaus/impeccable
- **Documentação:** https://impeccable.style/
- **Tutorial Getting Started:** https://impeccable.style/tutorials/getting-started
- **Caso de Uso (Neo Mirai):** https://impeccable.style/case-studies/neo-mirai
- **NPM:** `npx impeccable`
