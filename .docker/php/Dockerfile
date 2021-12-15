FROM php:7.4-fpm

ARG GROUP_ID
ARG USER_ID
ARG USER_NAME

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libzip-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    rsync

ARG XDEBUG_ENABLE=false

RUN if [ ${XDEBUG_ENABLE} = true ]; then \
    apt-get update && apt-get install -y \
        && pecl install xdebug-2.7.1 \
        && docker-php-ext-enable xdebug \
;fi

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions
RUN docker-php-ext-install pdo_mysql zip exif pcntl

# imagick
RUN apt-get update && apt-get install -y libmagickwand-dev --no-install-recommends && rm -rf /var/lib/apt/lists/*
RUN printf "\n" | pecl install imagick
RUN docker-php-ext-enable imagick

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add user for laravel application
RUN groupadd -g $GROUP_ID $USER_NAME
RUN useradd -u $USER_ID -ms /bin/bash -g $USER_NAME $USER_NAME
# Change current user to $USER_NAME
USER $USER_NAME

WORKDIR /var/www

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]