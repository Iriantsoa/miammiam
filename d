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
RUN composer install --no-dev --optimize-autoloader

# Switch back to root for Apache setup
USER root

# Set the right permissions for the Apache server
RUN chown -R www-data:www-data /var/www/html/public
RUN chown -R www-data:www-data /var/www/html/var
RUN chown -R www-data:www-data /var/www/html/vendor
RUN chmod -R 755 /var/www/html/public
RUN chmod -R 755 /var/www/html/var
RUN chmod -R 755 /var/www/html/vendor

# Enable Apache rewrite module
RUN a2enmod rewrite

# Set the document root to Symfony's public directory
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# Configure Apache to allow overrides and proper permissions
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Allow directory indexing for the public directory
RUN echo '<Directory /var/www/html/public>' >> /etc/apache2/apache2.conf && \
    echo '    AllowOverride All' >> /etc/apache2/apache2.conf && \
    echo '    Require all granted' >> /etc/apache2/apache2.conf && \
    echo '    DirectoryIndex index.php' >> /etc/apache2/apache2.conf && \
    echo '</Directory>' >> /etc/apache2/apache2.conf

# Expose port 8080
EXPOSE 8080

# Start Apache
CMD ["apache2-foreground"]