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
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install \
    pdo_mysql \
    zip \
    mbstring \
    xml

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create a non-root user and switch to it
RUN useradd -m symfony-user
RUN chown -R symfony-user:symfony-user /var/www/html
USER symfony-user

# Copy the Symfony project files
COPY . .

# Install Composer dependencies (optimized for production)
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# Switch back to root for Apache setup
USER root

# Set permissions for Symfony cache and logs
RUN chown -R www-data:www-data var/ public/

# Enable Apache rewrite module
RUN a2enmod rewrite

# Set the document root to Symfony's public directory
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# Configure Apache to allow overrides and proper permissions
RUN echo '<Directory /var/www/html/public>' >> /etc/apache2/apache2.conf && \
    echo '    AllowOverride All' >> /etc/apache2/apache2.conf && \
    echo '    Require all granted' >> /etc/apache2/apache2.conf && \
    echo '</Directory>' >> /etc/apache2/apache2.conf

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
