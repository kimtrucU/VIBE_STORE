#!/bin/bash

echo "🚀 Starting Vibe Fashion..."

# Render yêu cầu app lắng nghe trên $PORT (mặc định 10000)
APP_PORT="${PORT:-8080}"

# Cache cấu hình, routes, views (không block nếu lỗi)
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Tạo storage symlink
php artisan storage:link || true

# Chạy migrate trong nền
php artisan migrate --force &

echo "✅ Khởi động PHP server trên port $APP_PORT..."

# Dùng php artisan serve để đảm bảo lắng nghe đúng port Render yêu cầu
exec php artisan serve --host=0.0.0.0 --port="$APP_PORT"
