FROM php:8.1-bullseye

## Install required dependencies
RUN apt-get update && apt-get install -y --no-install-recommends bash make gpg gnupg dirmngr git unzip \
    && curl -L -O https://phar.io/releases/phive.phar && curl -L -O https://phar.io/releases/phive.phar.asc \
    && gpg --keyserver hkps://keys.openpgp.org --recv-keys 0x9D8A98B29B2D5D79 && gpg --verify phive.phar.asc phive.phar \
    && chmod +x phive.phar && mv phive.phar /usr/local/bin/phive \
    && pecl install pcov && docker-php-ext-enable pcov \
    # Cleanup
    && apt-get autoremove -y \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

VOLUME /app
