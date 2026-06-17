FROM php:8.2-fpm as base

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd
RUN pecl install redis && docker-php-ext-enable redis

# Set working directory
WORKDIR /var/www

# Copy Composer installer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ============ Development Stage ============
FROM base as development

# Copy application files
COPY . /var/www

# Install composer dependencies (including dev)
RUN composer install --no-interaction

# Set permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Install Node.js for Vite
RUN apt-get update && apt-get install -y nodejs npm && rm -rf /var/lib/apt/lists/*

# Install npm dependencies
RUN npm install

EXPOSE 9000
CMD ["php-fpm"]

# ============ Production Stage ============
FROM base as production

# Copy application files
COPY . /var/www

# Install composer dependencies (production only)
RUN composer install --no-dev --no-interaction --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Build front-end assets (Node.js needed for this step)
RUN apt-get update && apt-get install -y nodejs npm && rm -rf /var/lib/apt/lists/*
COPY package.json package-lock.json* /var/www/
RUN npm install --production && npm run build && npm prune --production

# Remove Node.js after build (reduces final image size)
RUN apt-get remove -y nodejs npm && rm -rf /var/lib/apt/lists/*

EXPOSE 9000
CMD ["php-fpm"]
