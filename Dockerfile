# docker container build file
# for: Toohga
# created in: 2017 by: Jarne

# Start from Apache HTTP with PHP
FROM php:8.2-apache

# Other dependency versions
ENV NODE_VERSION=20
ENV PECL_REDIS_VERSION=6.0.2

# Run package list updates and install needed services
RUN apt-get update
RUN apt-get install -y wget unzip gnupg

# Add Nodesource GPG key for installing Node.js (client)
RUN mkdir -p /etc/apt/keyrings
RUN curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg
RUN echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_${NODE_VERSION}.x nodistro main" | tee /etc/apt/sources.list.d/nodesource.list

# Run package list updates and install Node.js
RUN apt-get update
RUN apt-get install -y nodejs

# Install MySQLi & Redis extension
RUN docker-php-ext-install mysqli
RUN pecl install redis-${PECL_REDIS_VERSION} && docker-php-ext-enable redis

# Use the default production configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Go into the application folder
WORKDIR /var/www/Toohga

# Gain permissions to the app user for the created source folder
RUN chown www-data:www-data /var/www/Toohga

# Add app source code and set permissions to application user
COPY --chown=www-data:www-data ./ ./

# Install PHP dependecies with Composer
RUN wget https://getcomposer.org/composer.phar
RUN php composer.phar install --no-dev --no-interaction --optimize-autoloader
RUN rm composer.phar

# Install client dependecies with NPM and build client assets
RUN npm install --production
RUN npm run assets:build

# Add the predefined Apache2 config
COPY 000-default.conf /etc/apache2/sites-available/

# Enable Apache2 mods
RUN a2enmod rewrite
