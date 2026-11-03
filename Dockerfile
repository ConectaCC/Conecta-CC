# syntax=docker/dockerfile:1

FROM node:24-alpine AS frontend
WORKDIR /app/frontend
COPY frontend/package.json frontend/package-lock.json ./
RUN npm ci
COPY frontend/ ./
ARG VITE_SENTRY_DSN=""
ENV VITE_SENTRY_DSN=$VITE_SENTRY_DSN
RUN npm run build

FROM serversideup/php:8.4-fpm-nginx AS runtime
# intl: normalização de domínios com acento nos links de material (MaterialUrl)
USER root
RUN install-php-extensions intl
USER www-data
# migrations e caches rodam na inicialização do container (decisão 9)
ENV PHP_OPCACHE_ENABLE=1 \
    AUTORUN_ENABLED=true \
    SSL_MODE=off
WORKDIR /var/www/html
COPY --chown=www-data:www-data backend/composer.json backend/composer.lock ./
RUN composer install --no-dev --no-interaction --no-scripts --no-autoloader --prefer-dist
COPY --chown=www-data:www-data backend/ ./
COPY --chown=www-data:www-data --from=frontend /app/backend/public/spa ./public/spa
RUN composer dump-autoload --optimize --no-dev \
    && php artisan package:discover --ansi
