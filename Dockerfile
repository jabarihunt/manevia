#############################################################################################
# docker build . -t gcr.io/manevia/manevia-app:1.0.0 -t gcr.io/manevia/manevia-app:latest
# PORT=8080 && docker run -p 8080:${PORT} -e PORT=${PORT} gcr.io/manevia/manevia-app
# docker push gcr.io/manevia/manevia-app
#############################################################################################
# docker stop $(docker ps -a -q)
# docker rm $(docker ps -a -q)
#############################################################################################

# Use the official PHP 8 image.
# https://hub.docker.com/_/php
FROM php:8.0-apache

# Install and setup crons
# Install PHP extensions & Apache Modules
# Remove old Apache configuration
RUN docker-php-ext-install mysqli && \
	docker-php-ext-install gettext && \
    # pecl install redis && \
    # docker-php-ext-enable redis && \
	service apache2 stop && \
    a2enmod rewrite && \
    a2enmod expires && \
    a2enmod headers && \
    a2enmod ssl && \
    rm /etc/apache2/sites-enabled/000-default.conf && \
    rm /etc/apache2/sites-available/000-default.conf

# Copy local code to the container image
# Copy Apache config to container image
COPY app/. /var/www/html/
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# Configure new Apache configuration
# Use production php.ini file
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf && \
	ln -s /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-enabled/000-default.conf && \
	mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"