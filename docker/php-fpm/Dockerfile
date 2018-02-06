FROM php:7.0-fpm

EXPOSE 9000

RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    git \
    sudo \

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /usr/src/silex

ADD ./composer.json /usr/src/silex

#RUN composer install --prefer-dist --working-dir=/usr/src/silex

VOLUME ./../silex_api
