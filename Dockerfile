FROM php:8.6-apache@sha256:17e914da4f48db2a395f3a9e69206cd311b2d8dfdebc802477a31a69a246ecff

RUN docker-php-ext-configure mysqli
RUN docker-php-ext-install mysqli
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
RUN echo "PassEnv DB_HOST DB_USERNAME DB_PASSWORD DB_DATABASE" >> /etc/apache2/conf-enabled/expose-env.conf

# k3s pod network: Traefik's pod IP changes on every restart, so trust the whole range.
# %a rather than %h so the access log shows the address mod_remoteip resolved.
RUN a2enmod remoteip && cat >/etc/apache2/conf-enabled/remoteip.conf <<'EOF'
RemoteIPHeader X-Forwarded-For
RemoteIPInternalProxy 10.42.0.0/16
LogFormat "%a %l %u %t \"%r\" %>s %O \"%{Referer}i\" \"%{User-Agent}i\"" combined
EOF

COPY . /var/www/html/whereisthenorthreally
