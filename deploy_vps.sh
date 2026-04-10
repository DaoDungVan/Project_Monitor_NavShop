#!/usr/bin/env bash
set -euo pipefail

APP_DIR="${APP_DIR:-/var/www/navshop}"
REPO_URL="${REPO_URL:-https://github.com/DaoDungVan/Project_Monitor_NavShop.git}"
BRANCH="${BRANCH:-main}"
DOMAIN="${DOMAIN:-navshop.navgund.io.vn}"
ENV_FILE="${ENV_FILE:-/etc/navshop.env}"

if [ -f "$ENV_FILE" ]; then
    set -a
    # shellcheck disable=SC1090
    . "$ENV_FILE"
    set +a
fi

DB_NAME="${DB_NAME:-navshop}"
DB_USER="${DB_USER:-navshop_user}"
DB_PASS="${DB_PASS:-}"
PHP_SOCK="${PHP_SOCK:-/run/php/php8.5-fpm.sock}"

if [ -z "$DB_PASS" ]; then
    DB_PASS="$(openssl rand -hex 18 2>/dev/null || date +%s%N)"
fi

git config --global --add safe.directory "$APP_DIR" >/dev/null 2>&1 || true

if ! command -v git >/dev/null 2>&1; then
    apt-get update
    apt-get install -y git
fi

if [ -d "$APP_DIR/.git" ]; then
    git -C "$APP_DIR" fetch origin "$BRANCH"
    git -C "$APP_DIR" reset --hard "origin/$BRANCH"
else
    if [ -d "$APP_DIR" ] && [ -n "$(find "$APP_DIR" -mindepth 1 -maxdepth 1 -print -quit 2>/dev/null)" ]; then
        mv "$APP_DIR" "${APP_DIR}.backup.$(date +%Y%m%d%H%M%S)"
    fi

    mkdir -p "$(dirname "$APP_DIR")"
    git clone --branch "$BRANCH" "$REPO_URL" "$APP_DIR"
fi

cat > "$APP_DIR/.env" <<ENV
DB_HOST=localhost
DB_NAME=$DB_NAME
DB_USER=$DB_USER
DB_PASS=$DB_PASS
APP_DEBUG=false
ENV

install -m 600 /dev/null "$ENV_FILE"
cat > "$ENV_FILE" <<ENV
DB_HOST=localhost
DB_NAME=$DB_NAME
DB_USER=$DB_USER
DB_PASS=$DB_PASS
APP_DEBUG=false
ENV

mysql -u root <<SQL
CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';
ALTER USER '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';
GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';
FLUSH PRIVILEGES;
SQL

TABLE_COUNT="$(mysql -N -B -u root -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$DB_NAME';")"
if [ "$TABLE_COUNT" = "0" ]; then
    mysql -u root "$DB_NAME" < "$APP_DIR/database.sql"
fi

cat > /etc/nginx/sites-available/navshop <<NGINX
server {
    listen 80;
    listen [::]:80;
    server_name $DOMAIN;

    root $APP_DIR;
    index index.php index.html index.htm;

    client_max_body_size 20M;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~* ^/uploads/.*\\.php\$ {
        deny all;
    }

    location ~ \\.php\$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:$PHP_SOCK;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ^~ /config/ {
        deny all;
    }

    location ^~ /middleware/ {
        deny all;
    }

    location ~* \\.(sql|zip|sh|bak|env)\$ {
        deny all;
    }

    location ~ /\\. {
        deny all;
    }
}
NGINX

ln -sfn /etc/nginx/sites-available/navshop /etc/nginx/sites-enabled/navshop
chown -R www-data:www-data "$APP_DIR"
find "$APP_DIR" -type d -exec chmod 755 {} \;
find "$APP_DIR" -type f -exec chmod 644 {} \;
find "$APP_DIR/uploads" -type d -exec chmod 775 {} \; 2>/dev/null || true
chmod 640 "$APP_DIR/.env"

nginx -t
systemctl reload nginx

if command -v certbot >/dev/null 2>&1; then
    certbot --nginx -d "$DOMAIN" --non-interactive --agree-tos --register-unsafely-without-email --redirect || true
fi

echo "Deploy done: https://$DOMAIN"
