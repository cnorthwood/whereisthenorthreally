FROM php:8.5-apache@sha256:4cc63b3d4e05c40da73e7af5d39364ca2678ac07208919497bda3bbab975be95

RUN docker-php-ext-configure mysqli
RUN docker-php-ext-install mysqli
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
RUN echo "PassEnv DB_HOST DB_USERNAME DB_PASSWORD DB_DATABASE" >> /etc/apache2/conf-enabled/expose-env.conf

COPY . /var/www/html/whereisthenorthreally
