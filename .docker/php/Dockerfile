FROM php:8.2-fpm

ARG PROJECT_PATH
ARG USER_DOCKER_ID
ARG GROUP_DOCKER_ID

# OS packages
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libzip-dev \
    libpq-dev \
    libicu-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    gnupg2 \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    zip \
    unzip \
    cron \
    nano \
    graphviz \
    jq && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Node.js + npm
RUN curl -sL https://deb.nodesource.com/setup_18.x | bash && \
    apt-get install -y nodejs && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Setup clear
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install \
        pdo_mysql \
        intl \
        mbstring \
        zip \
        gd \
        soap \
        exif \
        bcmath \
        pcntl \
        posix

# Xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Custom php.ini
ADD .docker/php/php.ini /usr/local/etc/php/php.ini

# Access fro .npm
RUN mkdir -p /.npm && \
    chmod -R 755 /.npm && \
    chown -R ${USER_DOCKER_ID}:${GROUP_DOCKER_ID} /.npm

# HOME for psysh
ENV HOME=/var/www

# Config psysh
RUN mkdir -p /var/www/.config/psysh && \
    chmod -R 775 /var/www/.config && \
    chown -R ${USER_DOCKER_ID}:${GROUP_DOCKER_ID} /var/www/.config

# Docker project folder safity for git
RUN git config --global --add safe.directory /var/www/html

# Composer
ENV COMPOSER_HOME=/var/www/.composer
ENV COMPOSER_CACHE_DIR=/var/www/.composer/cache
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV PATH="$PATH:/var/www/.composer/vendor/bin"
RUN mkdir -p $COMPOSER_CACHE_DIR && chmod -R 777 $COMPOSER_CACHE_DIR

# Project access
RUN mkdir -p ${PROJECT_PATH} && \
    chown -R ${USER_DOCKER_ID}:${GROUP_DOCKER_ID} ${PROJECT_PATH} && \
    chmod -R 755 ${PROJECT_PATH}

WORKDIR ${PROJECT_PATH}

USER "${USER_DOCKER_ID}:${GROUP_DOCKER_ID}"
