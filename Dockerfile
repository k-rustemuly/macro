FROM php:8.1-apache
RUN a2enmod rewrite
RUN apt-get update
RUN apt-get install -y git libzip-dev zip unzip npm
RUN docker-php-ext-install pdo pdo_mysql zip
RUN docker-php-ext-install mysqli
