FROM php:8.2-cli

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm \
    libsqlite3-dev \
    sqlite3

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd pdo_sqlite

# Dapatkan Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Install dependencies PHP & Node (Optimized for caching)
# Copy package files first
COPY composer.json composer.lock* ./
COPY package.json package-lock.json* ./

# Install packages before copying source code
RUN composer install --no-scripts --no-autoloader
RUN npm install

# Copy file project
COPY . .

# Generate optimized autoload files and build assets
RUN composer dump-autoload --optimize
RUN npm run build

# Setup Database SQLite (Khusus untuk Demo)
RUN mkdir -p database
RUN touch database/database.sqlite
RUN php artisan migrate --force

# Set permission
RUN chmod -R 775 storage bootstrap/cache database

# Eksekusi server untuk produksi/Render
CMD php artisan migrate --force && php artisan storage:link && php -S 0.0.0.0:${PORT:-8000} -t public
