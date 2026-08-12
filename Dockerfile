FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql
RUN a2enmod rewrite

RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf
RUN printf '<Directory /var/www/html/>\n    AllowOverride All\n</Directory>\n' >> /etc/apache2/apache2.conf

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
COPY . .
RUN composer install

EXPOSE 80
