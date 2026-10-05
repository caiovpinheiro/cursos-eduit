#!/bin/sh
set -e
# Bind mount sobrescreve o código: garantir que o PHP (www-data) possa gravar cache, views e logs.
BASE="/var/www/html"
mkdir -p \
    "$BASE/storage/framework/sessions" \
    "$BASE/storage/framework/views" \
    "$BASE/storage/framework/cache" \
    "$BASE/storage/framework/cache/data" \
    "$BASE/storage/app/public" \
    "$BASE/storage/logs" \
    "$BASE/bootstrap/cache"

chown -R www-data:www-data "$BASE/storage" "$BASE/bootstrap/cache"
chmod -R ug+rwx "$BASE/storage" "$BASE/bootstrap/cache"

exec "$@"
