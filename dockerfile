# Use the official PHP 8.2 image with Apache
FROM php:8.2-apache

# Set the working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Install PHP extensions
RUN docker-php-ext-install \
    pdo_mysql \
    zip \
    mbstring \
    xml

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy the Symfony project files
COPY . .

# Install Composer dependencies (optimized for production)
RUN composer install --no-dev --optimize-autoloader

# Set permissions for Symfony cache and logs
RUN chown -R www-data:www-data var/

# Clear Symfony cache (optional, but recommended)
RUN php bin/console cache:clear

# Enable Apache rewrite module
RUN a2enmod rewrite

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]