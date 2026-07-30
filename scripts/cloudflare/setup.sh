#!/bin/bash
# Cloudflare Tunnel Setup — ServiceSaaS
# Uso: ./setup.sh <seu-dominio.com.br>

set -e

DOMAIN="${1:-seudominio.com.br}"
echo "🔧 Configurando Cloudflare Tunnel para $DOMAIN"

# 1. Instalar cloudflared
if ! command -v cloudflared &> /dev/null; then
  echo "📦 Instalando cloudflared..."
  curl -L https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-amd64 -o /usr/local/bin/cloudflared
  chmod +x /usr/local/bin/cloudflared
fi

# 2. Autenticar
echo "🔑 Autentique no Cloudflare (abra o link):"
cloudflared tunnel login

# 3. Criar tunnel
TUNNEL_NAME="servicos-flex"
cloudflared tunnel create "$TUNNEL_NAME"

# 4. Configurar DNS
echo "🌐 Configure os registros DNS manualmente ou via API:"
echo "   CNAME app.$DOMAIN → $TUNNEL_NAME.cfargotunnel.com"
echo "   CNAME admin.$DOMAIN → $TUNNEL_NAME.cfargotunnel.com"

# 5. Copiar config
echo "📝 Copie config.yml para ~/.cloudflared/config.yml"
echo "   E ajuste os hostnames para seu domínio"

echo "✅ Setup concluído! Inicie com: cloudflared tunnel run $TUNNEL_NAME"
