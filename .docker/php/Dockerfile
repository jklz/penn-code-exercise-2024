FROM php:8.2-fpm

# install needed php extensions to allow connecting to mysql database
RUN docker-php-ext-install mysqli pdo  pdo_mysql
# enable installed php extensions to allow connecting to mysql database
RUN docker-php-ext-enable mysqli pdo  pdo_mysql

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer