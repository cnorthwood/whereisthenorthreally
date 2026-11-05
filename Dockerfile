FROM php:8.5-apache@sha256:304eac8c3e14c194d05e1d67e756b641051d31d6848dd74db47dfae95f3142b8

RUN docker-php-ext-configure mysqli
RUN docker-php-ext-install mysqli
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
RUN echo "PassEnv DB_HOST DB_USERNAME DB_PASSWORD DB_DATABASE" >> /etc/apache2/conf-enabled/expose-env.conf

COPY . /var/www/html/whereisthenorthreally
