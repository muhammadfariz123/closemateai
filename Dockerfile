FROM php:8.2-apache

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
    sqlite3 \
    libpq-dev

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql pdo_pgsql pgsql mbstring exif pcntl bcmath gd pdo_sqlite

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Update Apache DocumentRoot to public folder
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# 1. Install Node modules and build frontend
COPY package.json package-lock.json* ./
RUN npm install

COPY vite.config.js ./
COPY resources/ resources/
COPY tailwind.config.js* postcss.config.js* ./
RUN npm run build

# 2. Install PHP dependencies
COPY composer.json composer.lock* ./
RUN composer install --no-scripts --no-autoloader

# 3. Copy the rest of the project
COPY . .

# Generate optimized autoload files
RUN composer dump-autoload --optimize

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Create a startup script
RUN echo '#!/bin/bash\n\
# Update Apache to listen on Render PORT\n\
sed -i "s/80/${PORT:-8000}/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf\n\
\n\
# Run migrations\n\
php artisan migrate --force\n\
php artisan storage:link || true\n\
\n\
# Start apache in foreground\n\
apache2-foreground' > /usr/local/bin/start.sh

RUN chmod +x /usr/local/bin/start.sh

CMD ["/usr/local/bin/start.sh"]
