# Version: 0.0.7.3

# Use the official WordPress image as a parent image
FROM wordpressdevelop/phpunit

# Install necessary PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Set the working directory to the WordPress directory
WORKDIR /var/www/html

# Copy everything except what's in .dockerignore
COPY ../Plugin/. /var/www/html/wp-content/plugins/im-meme-player/

# Expose port 80
EXPOSE 8080

# Copy your initialization script
COPY init-script.sh /usr/local/bin/

# Set permissions and execute the script on container start
RUN chmod +x /usr/local/bin/init-install.sh
ENTRYPOINT ["init-install.sh"]

# Start Apache server in the foreground
CMD ["apache2-foreground"]
