FROM composer as builder
WORKDIR /app/
COPY composer.* ./
COPY artisan .

RUN composer install --no-scripts

FROM node:16 as frontend

WORKDIR /usr/app

COPY . /usr/app
COPY --from=builder /app/vendor ./vendor

RUN npm install
RUN npm run build

FROM php:8.2-apache

WORKDIR /var/www/html

ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd \
    && docker-php-ext-install pdo_mysql

RUN pecl install redis-5.3.7 \
	&& docker-php-ext-enable redis

# Change document root
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
# Change Port
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

COPY . .
COPY --from=builder /app/vendor /var/www/html/vendor
COPY --from=builder /usr/bin/composer /usr/bin/composer

COPY --from=frontend /usr/app/node_modules /var/www/html/node_modules
COPY --from=frontend /usr/app/public/build /var/www/html/public/build

RUN chown -R www-data:www-data /var/www/html/storage
RUN chmod -R ug+rwx /var/www/html/storage

RUN echo "APP_KEY=" > .env
RUN php artisan key:generate

RUN a2enmod rewrite

# Configure PHP for development.
# Switch to the production php.ini for production operations.
# RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
# https://hub.docker.com/_/php#configuration
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"