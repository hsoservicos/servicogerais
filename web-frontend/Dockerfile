# ═══════════════════════════════════════════════════════════════
# Dockerfile — PHP-FPM 8.2 (Frontend ServiceSaaS)
# ═══════════════════════════════════════════════════════════════

FROM php:8.2-fpm-alpine

# ── System Dependencies ─────────────────────────────────
RUN apk add --no-cache \
    nginx \
    curl \
    git \
    unzip \
    oniguruma-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev

# ── PHP Extensions ──────────────────────────────────────
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    mbstring \
    zip \
    gd

# ── Composer ────────────────────────────────────────────
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# ── Diretório de Trabalho ───────────────────────────────
WORKDIR /var/www

# ── Permissões ──────────────────────────────────────────
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www

# ── Healthcheck (PHP-FPM não serve HTTP — usar pgrep) ──
HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
    CMD pgrep php-fpm || exit 1

CMD ["php-fpm"]
