FROM php:8.2-fpm

ARG PROJECT_PATH
ARG USER_DOCKER_ID
ARG GROUP_DOCKER_ID

# Установка необходимых пакетов
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
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    zip \
    unzip \
    cron \
    nano \
    graphviz \
    jq

# Node.js + npm
RUN curl -sL https://deb.nodesource.com/setup_18.x | bash && \
    apt-get install -y nodejs

# Очистка после установки
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Настройка и установка PHP-расширений
RUN docker-php-ext-configure imap --with-imap --with-kerberos --with-imap-ssl && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install \
        pdo_mysql \
        intl \
        mbstring \
        zip \
        gd \
        soap \
        exif \
        bcmath \
        imap \
        pcntl \
        posix

# Установка и активация xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Кастомный php.ini
ADD .docker/php/php.ini /usr/local/etc/php/php.ini

# Настройка прав для .npm
RUN mkdir -p /.npm && \
    chmod -R 755 /.npm && \
    chown -R ${USER_DOCKER_ID}:${GROUP_DOCKER_ID} /.npm

# Установка правильного HOME для psysh
ENV HOME=/var/www

# Создание конфигурационной папки для psysh
RUN mkdir -p /var/www/.config/psysh && \
    chmod -R 775 /var/www/.config && \
    chown -R ${USER_DOCKER_ID}:${GROUP_DOCKER_ID} /var/www/.config

# Установка Composer
ENV COMPOSER_HOME=/var/www/.composer
ENV COMPOSER_CACHE_DIR=/var/www/.composer/cache
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV PATH="$PATH:/var/www/.composer/vendor/bin"
RUN mkdir -p $COMPOSER_CACHE_DIR && chmod -R 777 $COMPOSER_CACHE_DIR

# Права проекта
RUN mkdir -p ${PROJECT_PATH} && \
    chown -R ${USER_DOCKER_ID}:${GROUP_DOCKER_ID} ${PROJECT_PATH} && \
    chmod -R 755 ${PROJECT_PATH}

WORKDIR ${PROJECT_PATH}

USER "${USER_DOCKER_ID}:${GROUP_DOCKER_ID}"
