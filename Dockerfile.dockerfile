# Version: 0.0.7.4 - add WP-CLI feature

# Install WP-CLI
RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x wp-cli.phar \
    && mv wp-cli.phar /usr/local/bin/wp

# Use the official WordPress image as a parent image
FROM wordpressdevelop/phpunit

# Set the working directory to the WordPress directory
WORKDIR /var/www/html

# Copy everything except what's in .dockerignore
COPY /Plugin/. /var/www/html/wp-content/plugins/im-meme-player/

# Install necessary PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Expose port 80
EXPOSE 8080

# Copy your initialization script
COPY init-script.sh /usr/local/bin/init-script.sh

# Set permissions and execute the script on container start
RUN chmod +x /usr/local/bin/init-script.sh
ENTRYPOINT ["/usr/local/bin/init-script.sh"]

# Run the initialization script
CMD ["/usr/local/bin/init-script.sh"]

# Start Apache server in the foreground
CMD ["apache2-foreground"]
