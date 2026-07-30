FROM php:8.3-apache

# Устанавливаем необходимые расширения PHP
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Включаем mod_rewrite
RUN a2enmod rewrite

# Копируем проект
COPY . /var/www/html/

# Права доступа
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80