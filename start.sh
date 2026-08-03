#!/bin/bash

echo "🚀 Starting Vibe Fashion..."

# Render cung cấp biến $PORT (thường là 10000)
APP_PORT="${PORT:-80}"
echo "📡 Binding Apache to port $APP_PORT"

# Cấu hình Apache lắng nghe đúng port của Render
echo "Listen $APP_PORT" > /etc/apache2/ports.conf

# Cập nhật VirtualHost sang đúng port
sed -i "s/<VirtualHost \*:[0-9]*>/<VirtualHost *:${APP_PORT}>/" \
    /etc/apache2/sites-available/000-default.conf

# Cache config (|| true để không crash nếu lỗi nhỏ)
php artisan config:cache || true
php artisan route:cache  || true
php artisan view:cache   || true
php artisan storage:link || true

# Chạy migration trong background, không block Apache
php artisan migrate --force &

echo "✅ Starting Apache on port $APP_PORT..."
exec apache2-foreground
