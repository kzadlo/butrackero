FROM php:7.3-apache
RUN apt-get update \
  && apt-get install -y libzip-dev zip \
  && docker-php-ext-install zip \
  && docker-php-ext-install pdo_mysql \
  && a2enmod rewrite ssl \
  && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN mkdir /etc/apache2/ssl \
  && openssl req -new -newkey rsa:4096 -days 365 -nodes -x509 \
    -subj "/C=PL/ST=Malopolska/L=Krakow/O=Dis/CN=butrackero.local" \
    -keyout /etc/apache2/ssl/apache.key \
    -out /etc/ssl/certs/apache.crt

RUN echo "ServerName butrackero" >> /etc/apache2/apache2.conf

RUN service apache2 restart
