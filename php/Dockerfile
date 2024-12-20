# Menggunakan image PHP-FPM sebagai dasar
FROM php:7.4-fpm

# Instalasi ekstensi PHP yang diperlukan (misalnya, untuk MySQL)
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Expose port 9000 untuk komunikasi PHP-FPM
EXPOSE 9000

