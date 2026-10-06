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

# Copy file project
COPY . .

# Install dependencies PHP & Node
RUN composer install --optimize-autoloader --no-dev
RUN npm install
RUN npm run build

# Setup Database SQLite (Khusus untuk Demo)
RUN mkdir -p database
RUN touch database/database.sqlite
RUN php artisan migrate --force

# Set permission
RUN chmod -R 775 storage bootstrap/cache database

# Eksekusi server untuk produksi/Render
CMD php artisan migrate --force && php artisan storage:link && php -S 0.0.0.0:${PORT:-8000} -t public
