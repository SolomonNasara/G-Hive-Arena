# Use an official PHP image with Apache support
FROM php:8.0-apache

# Enable mod_rewrite for Apache
RUN a2enmod rewrite

# Set the working directory inside the container
WORKDIR /var/www/html

# Copy the project files into the container's /var/www/html directory
COPY . /var/www/html/

# Expose port 80 (default port for Apache)
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]

RUN chmod 666 joiners.txt
