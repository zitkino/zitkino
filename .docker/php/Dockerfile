FROM php:8.3-apache

# System packages
RUN apt-get clean && apt-get update && apt-cache search php-mysql && apt-get install --fix-missing -y \
	curl libcurl4-openssl-dev \
	default-mysql-client \
	g++ \
	git \
	gnupg \
	libfreetype6-dev \
	libicu-dev \
	libjpeg62-turbo-dev \
	libmagickwand-dev \
	libonig-dev \
	libpng-dev \
	libssl-dev \
	libwebp-dev \
	libxml2-dev \
	linux-libc-dev \
	locales \
	sudo \
	vim \
	wget \
	zip libzip-dev

# Apache
RUN a2enmod rewrite ssl headers

# PHP extensions
RUN docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg --with-webp
RUN docker-php-ext-install \
	curl \
	exif \
	gettext \
	gd \
	intl \
	mbstring \
	mysqli \
	pdo \
	pdo_mysql \
	soap \
	xml \
	xmlwriter \
	zip

# Imagick
RUN pecl install imagick && docker-php-ext-enable imagick

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

# Node.js and npm
RUN echo "Package: *\nPin: origin deb.nodesource.com\nPin-Priority: 600" > "/etc/apt/preferences.d/nodesource" && sudo apt-cache policy nodejs
RUN rm -rf /var/lib/apt/lists/ && \
	curl -sL https://deb.nodesource.com/setup_16.x | sudo -E bash -&& \
	apt-get clean && apt-get update && apt-get install -y nodejs && \
	npm install npm@8.* -g && \
	npm install gulp-cli -g
