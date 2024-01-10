# Version: 0.0.6

# Use the official WordPress image as a parent image
FROM wordpressdevelop/phpunit

# Install necessary PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy the plugin files into the WordPress plugins directory
COPY / /var/www/html/wp-content/plugins/im-meme-player

# Set the working directory to the WordPress directory
WORKDIR /var/www/html

# Expose port 80
EXPOSE 80

# Copy your custom plugin
COPY / /var/www/html/wp-content/plugins/im-meme-player

# Copy your initialization script
COPY init-install.sh /usr/local/bin/

# Set permissions and execute the script on container start
RUN chmod +x /usr/local/bin/init-install.sh
ENTRYPOINT ["init-install.sh"]

# Start Apache server in the foreground
CMD ["apache2-foreground"]
