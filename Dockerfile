FROM serversideup/php:8.4-fpm-nginx-alpine
USER root
RUN install-php-extensions gd
USER www-data