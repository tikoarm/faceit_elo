FROM php:8.2-apache

# Установка расширений PHP
RUN docker-php-ext-install mysqli

# Копируем PHP-код
COPY . /var/www/html/

WORKDIR /var/www/html