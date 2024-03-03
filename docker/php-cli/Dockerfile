# Smaller than other installs
FROM php:8.1-cli-alpine

# Bash
RUN apk add --no-cache bash

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

VOLUME /opt/project
WORKDIR /opt/project