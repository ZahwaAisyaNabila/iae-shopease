# Build stage
FROM php:8.3-fpm-alpine AS builder

WORKDIR /app

# Install PHP extensions and build tools
RUN apk add --no-cache \
    build-base \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libzip-dev \
    git \
    curl \
    nodejs \
    npm

RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) \
    gd \
    zip \
    pdo \
    pdo_sqlite \
    redis \
    opcache

# Install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy dependency files
COPY composer.json composer.lock package.json ./

# Install PHP and Node dependencies
RUN composer install --no-dev --optimize-autoloader && \
    npm install --omit=dev

# Copy application code
COPY . .

# Build frontend assets
RUN npm run build

# Production stage
FROM php:8.3-fpm-alpine

WORKDIR /app

# Install runtime dependencies only
RUN apk add --no-cache \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libzip \
    redis

RUN docker-php-ext-install -j$(nproc) \
    gd \
    zip \
    pdo \
    pdo_sqlite \
    redis \
    opcache

# Copy built application from builder
COPY --from=builder /app /app

# Create non-root user
RUN addgroup -g 1000 laravel && \
    adduser -D -u 1000 -G laravel laravel

# Set permissions
RUN chown -R laravel:laravel /app && \
    chmod -R 755 /app/storage && \
    chmod -R 755 /app/bootstrap/cache

USER laravel

EXPOSE 9000

CMD ["php-fpm"]
