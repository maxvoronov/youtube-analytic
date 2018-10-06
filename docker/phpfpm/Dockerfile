FROM php:7.2-fpm
LABEL maintainer="Max Voronov"

# Install libs
RUN apt-get update \
    && apt-get install -y --no-install-recommends --allow-unauthenticated \
        g++ \
        curl \
        git \
        zip \
        unzip \
        pkg-config \
        libmcrypt-dev \
        libicu-dev \
        libxslt1-dev \
        libxml2-dev \
        libssl-dev \
        libcurl4-openssl-dev \
        zlib1g-dev

# Install PHP extensions
RUN docker-php-ext-install bcmath dom intl mbstring opcache xsl zip \
    && pecl install mongodb-1.5.3 \
    && docker-php-ext-enable mongodb

# Install Composer
ENV COMPOSER_ALLOW_SUPERUSER 0
ENV COMPOSER_VERSION 1.7.2
ENV COMPOSER_HOME /tmp
RUN curl --silent --fail --location --retry 3 --output /tmp/installer.php --url https://raw.githubusercontent.com/composer/getcomposer.org/b107d959a5924af895807021fcef4ffec5a76aa9/web/installer \
    && php -r " \
        \$signature = '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061'; \
        \$hash = hash('SHA384', file_get_contents('/tmp/installer.php')); \
        if (!hash_equals(\$signature, \$hash)) { \
            unlink('/tmp/installer.php'); \
            echo 'Integrity check failed, installer is either corrupt or worse.' . PHP_EOL; \
            exit(1); \
        }" \
    && php /tmp/installer.php --no-ansi --install-dir=/usr/bin --filename=composer --version=${COMPOSER_VERSION} \
    && composer --ansi --version --no-interaction

# Cleanup to keep the images size small
RUN apt-get purge -y \
        libicu-dev \
        libssl-dev \
        libcurl4-openssl-dev \
        libmcrypt-dev \
        libxslt1-dev \
        libxml2-dev \
        zlib1g-dev \
    && rm -r /var/lib/apt/lists/* \
    && rm -rf /tmp/* /tmp/.htaccess

# Add php.ini and composer configs
ADD configs/php.ini /usr/local/etc/php/

USER www-data
WORKDIR /var/www