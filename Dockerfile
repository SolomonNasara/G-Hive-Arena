# Use official PHP image as base
FROM php:8.0-apache

# Install any necessary PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy your PHP files to the Apache server's root directory
COPY . /var/www/html/

# Enable mod_rewrite for URL rewriting if needed
RUN a2enmod rewrite

# Expose port 80 (the default HTTP port)
EXPOSE 80

# Run Apache in the foreground
CMD ["apache2-foreground"]
