# Use the official WordPress image as a parent image
FROM wordpress:latest

# Install necessary PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy the plugin files into the WordPress plugins directory
COPY ./im-meme-player /var/www/html/wp-content/plugins/im-meme-player

# Set the working directory to the WordPress directory
WORKDIR /var/www/html

# Expose port 80
EXPOSE 80

# Start Apache server in the foreground
CMD ["apache2-foreground"]
