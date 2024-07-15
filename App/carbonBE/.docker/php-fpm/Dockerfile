FROM surnet/alpine-wkhtmltopdf:3.16.2-0.12.6-full as wkhtmltopdf
FROM php:8.2.1-fpm-alpine

WORKDIR /app

RUN ln -s /usr/local/bin/php /usr/bin/php8.2
RUN docker-php-ext-install pdo_mysql

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN chmod u+s /usr/bin/composer

RUN echo 'linux' > /etc/docker_host && \
    chmod +r /etc/docker_host

RUN apk update
RUN apk add --update --no-cache autoconf bash figlet pkgconf libssl1.1

RUN apk add autoconf bash figlet pkgconf openssl-dev freetype-dev libpng-dev libjpeg-turbo-dev libmcrypt-dev git openssh-client curl wget libtool zlib-dev icu-dev g++ linux-headers libzip-dev

RUN apk add build-base
#RUN apk add --no-cache gmp gmp-dev && \
#    docker-php-ext-install gmp

# Install X-debug
#RUN pecl install xdebug && docker-php-ext-enable xdebug


RUN apk add --no-cache \
        libstdc++ \
        libx11 \
        libxrender \
        libxext \
        ca-certificates \
        fontconfig \
        freetype \
        ttf-droid \
        ttf-freefont \
        ttf-liberation \
        ;
# wkhtmltopdf copy bins from ext image
COPY --from=wkhtmltopdf /bin/wkhtmltopdf /bin/libwkhtmltox.so /bin/

