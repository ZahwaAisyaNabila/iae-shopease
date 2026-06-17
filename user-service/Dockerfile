# Gunakan image PHP resmi dengan ekstensi yang dibutuhkan Laravel
FROM php:8.2-fpm

# Atur working directory di dalam container
WORKDIR /var/www

# Install dependensi sistem dan ekstensi PHP yang diperlukan
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype61-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libonig-dev \
    libzip-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install ekstensi PHP untuk MySQL
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl

# Install Composer secara global
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Salin seluruh kode proyek ke dalam container
COPY . /var/www

# Berikan izin akses folder storage dan bootstrap/cache untuk Laravel
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Expose port 8000 untuk mengakses PHP-FPM / Artisan Server
EXPOSE 8000

# Perintah utama untuk menjalankan server Laravel
CMD php artisan serve --host=0.0.0.0 --port=8000
