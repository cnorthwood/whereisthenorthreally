FROM php:8.5-apache@sha256:08af36df144462d84c7f16190557eec06cb9f656882685610b712a15eec54d46

RUN docker-php-ext-configure mysqli
RUN docker-php-ext-install mysqli
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
RUN echo "PassEnv DB_HOST DB_USERNAME DB_PASSWORD DB_DATABASE" >> /etc/apache2/conf-enabled/expose-env.conf

COPY . /var/www/html/whereisthenorthreally
