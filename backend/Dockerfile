
FROM php:8.1-apache
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && docker-php-ext-install mysqli zip

WORKDIR /var/www/html
COPY backend/ .
COPY backend/php.ini /usr/local/etc/php/

ENV PORT=80
EXPOSE $PORT