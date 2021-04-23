ARG PHP_VERSION="7.4"
FROM php:${PHP_VERSION}-fpm

LABEL maintainer="github@rgroot.nl"

RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip

RUN php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer > /dev/null

RUN chmod -R 777 /home

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www

CMD ["php-fpm", "-F"]