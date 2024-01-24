###################
# BUILD Composer
###################
FROM composer:2.0 as php

WORKDIR /app

COPY ./application/database/ database/
COPY ./application/composer.json composer.json
COPY ./application/composer.lock composer.lock

COPY ./application .

RUN composer install \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --ignore-platform-reqs \
    --optimize-autoloader \
    --prefer-dist

###################
# BUILD Vue
###################
FROM node:18-alpine As vue

WORKDIR /app

COPY --chown=node:node application/package.json ./
COPY --chown=node:node application/yarn.lock ./
COPY --chown=node:node --from=php /app/vendor /app/vendor

COPY --chown=node:node ./application/ .

RUN yarn install --frozen-lockfile

RUN npx vite build


###################
# FINAL BUILD WITH LARAVEL
###################
FROM php:8.2-fpm-alpine3.18

COPY --from=vue /app/public/build /var/www/public//build
COPY --from=vue /app/vendor /var/www/vendor

RUN echo "http://dl-cdn.alpinelinux.org/alpine/edge/main" > /etc/apk/repositories \
    && echo "http://dl-cdn.alpinelinux.org/alpine/edge/community" >> /etc/apk/repositories \
    && echo "http://dl-cdn.alpinelinux.org/alpine/edge/testing" >> /etc/apk/repositories \
    && echo "http://dl-cdn.alpinelinux.org/alpine/v3.17/main" >> /etc/apk/repositories \
    && apk upgrade -U -a \
    && apk add -Uuv \
    autoconf \
    bash \
    fcgi \
    font-noto-emoji \
    freetype \
    freetype-dev \
    gifsicle \
    gcc \
    g++ \
    git \
    harfbuzz \
    libstdc++ \
    libtool \
    libzip-dev \
    make \
    netcat-openbsd \
    postgresql-dev \
    pcre-dev \
    nginx \
    nss \
    optipng \
    pngquant \
    postgresql-client \
    supervisor \
    ttf-freefont \
    wqy-zenhei \
    && rm -rf /var/cache/apk/*

COPY --from=composer:2.3 /usr/bin/composer /usr/bin/composer

RUN docker-php-ext-install -j$(nproc) pdo pdo_pgsql pdo_mysql mysqli pgsql opcache pcntl bcmath posix zip exif intl

RUN printf "\n" | pecl install redis && docker-php-ext-enable redis

RUN apk del autoconf g++ libtool make pcre-dev

COPY application  /var/www

COPY system  /

# Install PHP extensions
RUN chmod a+x /opt/install-extensions.sh /opt/entrypoint.sh \
    && chmod a+x /opt/php-fpm-healthcheck \
    && mkdir -p /run/nginx \
    && /opt/install-extensions.sh

WORKDIR /var/www

STOPSIGNAL SIGQUIT

CMD ["/bin/bash", "/opt/entrypoint.sh"]
