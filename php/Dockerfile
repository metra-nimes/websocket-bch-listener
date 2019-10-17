FROM php:7.3-fpm

RUN apt-get -qq update && apt-get -qq install \
  curl \
  git \
  zip \
  > /dev/null

RUN pecl install swoole \
    && docker-php-ext-enable swoole

RUN curl --silent --show-error https://getcomposer.org/installer | php

ENV PHPREDIS_VERSION 3.0.0

RUN mkdir -p /usr/src/php/ext/redis \
    && curl -L https://github.com/phpredis/phpredis/archive/$PHPREDIS_VERSION.tar.gz | tar xvz -C /usr/src/php/ext/redis --strip 1 \
    && echo 'redis' >> /usr/src/php-available-exts \
    && docker-php-ext-install redis
RUN mkdir /server
