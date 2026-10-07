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

# 1. Install Node modules and build frontend FIRST
COPY package.json package-lock.json* ./
RUN npm install

COPY vite.config.js ./
COPY resources/ resources/
# We also need public/ for vite build sometimes, but usually just resources/ is enough.
# Let's run build now. If it fails due to missing files, we might need tailwind.config.js etc if they exist.
# Wait, let's just copy everything that might be needed for frontend.
COPY tailwind.config.js* postcss.config.js* ./
RUN npm run build

# 2. Install PHP dependencies
COPY composer.json composer.lock* ./
RUN composer install --no-scripts --no-autoloader

# 3. Copy the rest of the project (PHP files, etc)
COPY . .

# Generate optimized autoload files
RUN composer dump-autoload --optimize

# Setup Database SQLite (Khusus untuk Demo)
RUN mkdir -p database
RUN touch database/database.sqlite
RUN php artisan migrate --force

# Set permission
RUN chmod -R 775 storage bootstrap/cache database

# Eksekusi server untuk produksi/Render
CMD touch database/database.sqlite && php artisan migrate --force && php artisan storage:link && php -S 0.0.0.0:${PORT:-8000} -t public
