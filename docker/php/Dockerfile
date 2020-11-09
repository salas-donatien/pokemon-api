FROM composer

FROM php:7.4.10-fpm

COPY --from=composer /usr/bin/composer /usr/local/bin/composer

LABEL Description="Artsper" \
	  maintainer="Artsper <jsalas.luis@gmail.com>" \
      Licence="MIT"

RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install --no-install-recommends -y \
    apt-utils \
    vim \
    wget \
    zip \
    unzip \
    gosu \
    python \
    openssl \
    python-dev \
    libicu-dev \
    libssh-dev \
    libzip-dev \
    zlib1g-dev \
    librabbitmq-dev \
    supervisor \
    chromium && \
	apt-get purge -y --auto-remove && \
	rm -rf /var/lib/apt/lists/* && \
	apt-get clean

RUN pecl install apcu amqp && \
    docker-php-ext-install pdo pdo_mysql zip intl && \
    docker-php-ext-enable opcache amqp

RUN echo 'alias sf="php bin/console"' >> ~/.bashrc

RUN addgroup app-users && \
    adduser --disabled-password --gecos "" --force-badname --ingroup app-users app-user

COPY entrypoint.sh /usr/local/bin/entrypoint

RUN set -o errexit -o nounset -o xtrace; \
    chmod a+x /usr/local/bin/entrypoint

ENTRYPOINT ["entrypoint"]

WORKDIR /srv
