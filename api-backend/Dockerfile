# ═══════════════════════════════════════════════════════════════
# Dockerfile — Node.js 20 (API ServiceSaaS)
# ═══════════════════════════════════════════════════════════════

FROM node:20-alpine

# Diretório de trabalho
WORKDIR /usr/src/app

# ── Dockerfile de DESENVOLVIMENTO ─────────────────────────
# Instala devDependencies (nodemon, jest, eslint) para hot-reload e testes.
# Para produção, use multi-stage build com npm ci --only=production

# Copiar dependências primeiro (cache layer)
COPY package.json ./
RUN npm install

# Copiar código-fonte
COPY . .

# Criar usuário não-root para segurança
RUN addgroup -S appgroup && adduser -S appuser -G appgroup
USER appuser

# Expor porta da API
EXPOSE 3000

# Healthcheck
# Usa node -e (nativo) em vez de wget (BusyBox pode não ter --spider)
# 127.0.0.1 evita resolução IPv6 (localhost → ::1 no Alpine)
HEALTHCHECK --interval=30s --timeout=10s --start-period=15s --retries=3 \
    CMD node -e "require('http').get('http://127.0.0.1:3000/health',r=>process.exit(r.statusCode===200?0:1)).on('error',()=>process.exit(1))"

CMD ["node", "server.js"]
