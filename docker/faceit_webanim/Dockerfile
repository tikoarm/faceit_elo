FROM php:8.2-apache

# Установка расширений PHP
RUN docker-php-ext-install mysqli

# Копируем PHP-код
COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html/data && \
    chmod -R 755 /var/www/html/data

WORKDIR /var/www/html