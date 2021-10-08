# Docker file for Toohga (https://github.com/jarne/Toohga)

# Start from Apache HTTP with PHP
FROM php:8.0-apache

# Run package list updates and install needed services
RUN apt-get update
RUN apt-get install -y ssh git wget unzip

# Install MySQLi & Redis extension
RUN docker-php-ext-install mysqli
RUN pecl install redis-5.3.3 && docker-php-ext-enable redis

# Add GitHub host keys
RUN ssh-keyscan github.com >> /root/.ssh/known_hosts

# Go into the webserver folder
WORKDIR /var/www

# Clone the repository and move the files to the right place
RUN git clone git@github.com:jarne/Toohga.git

# Go into the application folder
WORKDIR /var/www/Toohga

# Install dependecies with composer
RUN wget https://getcomposer.org/composer.phar
RUN php composer.phar install --no-dev --no-interaction --optimize-autoloader
RUN rm composer.phar

# Add the predefined Apache2 config
COPY 000-default.conf /etc/apache2/sites-available/

# Enable Apache2 mods
RUN a2enmod rewrite
