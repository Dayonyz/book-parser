FROM php:8.2-fpm

ARG PROJECT_PATH
ARG USER_DOCKER_ID
ARG GROUP_DOCKER_ID

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libc-client-dev \
    libkrb5-dev \
    libzip-dev \
    libpq-dev \
    libicu-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    gnupg2 \
    libpng-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    zip \
    unzip \
    cron \
    nano \
    graphviz \
    jq

# Node.js + npm
RUN curl -sL https://deb.nodesource.com/setup_18.x | bash
RUN apt-get install -y nodejs

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure imap --with-imap --with-kerberos --with-imap-ssl
RUN docker-php-ext-configure gd --with-freetype --with-jpeg

RUN docker-php-ext-install pdo_mysql intl mbstring zip gd soap exif bcmath imap pcntl posix

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

ADD .docker/php/php.ini /usr/local/etc/php/php.ini

RUN mkdir -p /.npm
RUN chmod -R 755 /.npm
RUN chown -R ${USER_DOCKER_ID}:${GROUP_DOCKER_ID} /.npm

RUN mkdir -p /var/www/.config/psysh
RUN chmod -R 755 /var/www/.config

# Composer
ENV COMPOSER_HOME=/var/www/.composer
ENV COMPOSER_CACHE_DIR=/var/www/.composer/cache
RUN mkdir -p $COMPOSER_CACHE_DIR && chmod -R 777 $COMPOSER_CACHE_DIR
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV PATH="$PATH:/var/www/.composer/vendor/bin"

RUN chown -R www-data:www-data ${PROJECT_PATH}
RUN chmod -R 755 ${PROJECT_PATH}

WORKDIR ${PROJECT_PATH}

USER "${USER_DOCKER_ID}:${GROUP_DOCKER_ID}"