FROM php:8.6-apache@sha256:0bf09a023befd33325642adcf8da2f818d7d8963014c07c26fc588f9badd4de9

RUN docker-php-ext-configure mysqli
RUN docker-php-ext-install mysqli
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
RUN echo "PassEnv DB_HOST DB_USERNAME DB_PASSWORD DB_DATABASE" >> /etc/apache2/conf-enabled/expose-env.conf

COPY . /var/www/html/whereisthenorthreally
