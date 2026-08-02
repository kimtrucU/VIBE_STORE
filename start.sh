#!/bin/bash
set -e

echo "🚀 Starting Vibe Fashion..."

# Chạy migration để cập nhật DB schema
php artisan migrate --force

# Cache cấu hình, routes, views để tăng hiệu suất
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Tạo storage symlink cho upload ảnh
php artisan storage:link || true

echo "✅ Setup hoàn tất. Khởi động Apache..."

# Khởi động Apache
apache2-foreground
