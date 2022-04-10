FROM php:8.1-alpine

RUN docker-php-ext-enable opcache \
  && docker-php-ext-install sockets \
  && docker-php-ext-install pcntl

EXPOSE 8080

ENTRYPOINT ["php", "server.php"]
