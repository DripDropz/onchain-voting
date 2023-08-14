export PHPREDIS_VERSION="${PHPREDIS_VERSION-5.3.7}"
mkdir -p /usr/src/php/ext/redis \
    && curl -L https://github.com/phpredis/phpredis/archive/$PHPREDIS_VERSION.tar.gz | tar xvz -C /usr/src/php/ext/redis --strip 1 \
    && echo 'redis' >> /usr/src/php-available-exts

# Install mysqli, opcache, phpredis, and pcntl
# NPROC=$(grep -c ^processor /proc/cpuinfo 2>/dev/null || 1)
# docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ &&\
# docker-php-ext-install "-j${NPROC}" pdo pdo_pgsql mysqli pgsql opcache redis pcntl gd bcmath posix zip exif intl