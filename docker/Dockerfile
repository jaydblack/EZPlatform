ARG PHP_VERSION=7.1

FROM php:${PHP_VERSION}-apache

RUN apt-get update && \
        apt-get install -y \
        libfreetype6-dev \
        libxrender1 \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng-dev \
        apt-utils \
        sendmail \
        libfontconfig \
    && docker-php-ext-install -j$(nproc) iconv mcrypt zip \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd

RUN docker-php-ext-configure mysqli
RUN docker-php-ext-install mysqli
RUN docker-php-ext-enable mysqli

RUN docker-php-ext-configure pdo
RUN docker-php-ext-install pdo
RUN docker-php-ext-enable pdo

RUN docker-php-ext-configure pdo_mysql
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-enable pdo_mysql

RUN docker-php-ext-configure mbstring
RUN docker-php-ext-install mbstring
RUN docker-php-ext-enable mbstring

RUN docker-php-ext-configure exif
RUN docker-php-ext-install exif
RUN docker-php-ext-enable exif

RUN apt-get update && apt-get install -y libxslt-dev
RUN docker-php-ext-configure xsl
RUN docker-php-ext-install xsl
RUN docker-php-ext-enable xsl

RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl
RUN docker-php-ext-enable intl

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Configure Apache
RUN mkdir /var/www/app
RUN rm -rf /etc/apache2/sites-available/* && rm -rf /etc/apache2/sites-enabled/*
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
ADD app.conf /etc/apache2/sites-available/app.conf

RUN pecl install xdebug-2.5.5 && docker-php-ext-enable xdebug
COPY php/php.ini /usr/local/etc/php/php.ini

RUN usermod -u 1000 www-data

RUN a2ensite app
RUN a2enmod headers
RUN a2enmod rewrite
RUN service apache2 restart

WORKDIR /var/www/app

EXPOSE 80

ENV DEBIAN_FRONTEND teletype

CMD apachectl -D FOREGROUND

ADD import-cron /etc/cron.d/
