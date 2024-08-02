FROM php:8.2-fpm

RUN apt update \
    && apt install -y zlib1g-dev g++ git libicu-dev zip libzip-dev zip libpq-dev \
    && docker-php-ext-install intl opcache pdo pdo_pgsql \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip

WORKDIR /var/www

COPY . .

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN curl -sS https://get.symfony.com/cli/installer | bash && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

# Verify Composer installation
RUN pwd && ls -l

# Verify installation paths
RUN ls -la /root/.symfony/bin/ || echo "Symfony directory not found"
RUN ls -la /root/.symfony/bin/symfony || echo "Symfony binary not found"

RUN composer install

CMD ["php-fpm"]
