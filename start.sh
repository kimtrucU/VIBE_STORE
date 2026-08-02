#!/bin/bash
set -e

echo "🚀 Starting Vibe Fashion..."

# Chạy migration trong background để không block Apache khởi động
(php artisan migrate --force 2>&1 || true) &

# Cache cấu hình, routes, views để tăng hiệu suất
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Tạo storage symlink cho upload ảnh
php artisan storage:link || true

echo "✅ Setup hoàn tất. Khởi động Apache..."

# Fix port binding for Render (Render passes $PORT, usually 10000)
sed -i "s/80/${PORT:-80}/g" /etc/apache2/ports.conf
sed -i "s/:80/:${PORT:-80}/g" /etc/apache2/sites-available/000-default.conf

# Khởi động Apache ngay lập tức
apache2-foreground
