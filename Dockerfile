# ── Bước 1: Build stage (PHP + Node.js) ──────────────────────────────────────
FROM php:8.3-apache AS base

# Cài thư viện hệ thống cần thiết
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libzip-dev unzip git curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Cài Node.js 20 (để build assets Vite cho Admin Blade)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs && apt-get clean

# Cài Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy toàn bộ source code
COPY . .

# Cài PHP dependencies
RUN composer install --optimize-autoloader --no-dev --no-interaction

# Cài Node dependencies và build assets cho Admin (Blade views)
RUN npm ci && npm run build

# Cấu hình quyền thư mục
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Cấu hình Apache trỏ vào /public của Laravel
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' \
    /etc/apache2/sites-available/000-default.conf \
    && a2enmod rewrite

# Thêm AllowOverride All để .htaccess hoạt động
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' \
    /etc/apache2/apache2.conf

EXPOSE 80

# Sao chép script khởi động
COPY start.sh /start.sh
RUN chmod +x /start.sh

CMD ["/start.sh"]
