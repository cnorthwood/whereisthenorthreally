FROM php:8.5-apache@sha256:0a785eab8b7eb4836f9c6093771c8ea15ff8ba7269444f3da70a1d33d15fe664

RUN docker-php-ext-configure mysqli
RUN docker-php-ext-install mysqli
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
RUN echo "PassEnv DB_HOST DB_USERNAME DB_PASSWORD DB_DATABASE" >> /etc/apache2/conf-enabled/expose-env.conf

COPY . /var/www/html/whereisthenorthreally
