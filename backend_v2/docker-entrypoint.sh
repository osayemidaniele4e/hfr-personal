#!/bin/sh
set -e

# Render (and other hosts) set PORT; the stock php:apache image only listens on 80.
if [ -n "$PORT" ] && [ "$PORT" != "80" ]; then
  echo "[entrypoint] Configuring Apache to listen on ${PORT}"
  if [ -f /etc/apache2/ports.conf ]; then
    sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
  fi
  for f in /etc/apache2/sites-enabled/*.conf; do
    if [ -f "$f" ]; then
      sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" "$f"
    fi
  done
fi

# Symlink public/storage; ignore if it already exists
php artisan storage:link 2>/dev/null || true

# Render Free tier: pre-deploy command is unavailable in the dashboard. Set
# RUN_MIGRATIONS_ON_START=true in Environment to run migrations on each deploy/start.
if [ "${RUN_MIGRATIONS_ON_START:-}" = "true" ]; then
  echo "[entrypoint] RUN_MIGRATIONS_ON_START: php artisan migrate --force"
  php artisan migrate --force
fi

exec apache2-foreground
