FROM php:7.3-apache

# System packages
RUN apt-get clean && apt-get update && apt-cache search php-mysql && apt-get install --fix-missing -y \
    gnupg \
    sudo \
    vim \
    imagemagick \
    git \
    memcached \
    libmemcached-tools \
    libmemcached-dev \
    libfreetype6-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    curl libcurl4-openssl-dev \
    default-mysql-client \
    zip \
    wget \
    linux-libc-dev \
    libssl-dev \
    libzip-dev \
    libicu-dev \
    g++ \
	nodejs

# Memcached
RUN curl -L -o /tmp/memcached.tar.gz "https://api.github.com/repos/php-memcached-dev/php-memcached/tarball" \
    && mkdir -p /usr/src/php/ext/memcached \
    && tar -C /usr/src/php/ext/memcached -zxvf /tmp/memcached.tar.gz --strip 1 \
    && docker-php-ext-configure memcached \
    && docker-php-ext-install memcached \
    && rm /tmp/memcached.tar.gz

# PHP extensions
RUN a2enmod rewrite ssl headers

RUN docker-php-ext-configure gd --with-jpeg-dir=/usr/include/
RUN docker-php-ext-configure intl
RUN docker-php-ext-configure zip --with-libzip

RUN docker-php-ext-install \
	curl \
    gd \
    mbstring \
    zip \
    pdo \
    pdo_mysql \
    mysqli \
    intl

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

# Node.js and npm
RUN rm -rf /var/lib/apt/lists/ && \
    curl -sL https://deb.nodesource.com/setup_11.x | sudo -E bash -&& \
    apt-get clean && apt-get update && apt-get install -y nodejs && \
    npm install npm@latest -g
