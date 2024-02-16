# Version: 0.0.7.4 - add WP-CLI feature
# Version: 0.0.7.5 - https://chat.openai.com/share/16fe15f6-de52-461b-86ed-9dcb48503664
# Dockerfile for setting up a WordPress environment with WP-CLI and necessary PHP extensions
# This Dockerfile is intended to be used with the official WordPress image as the base
# It includes the installation of WP-CLI for command-line WordPress management
# The Dockerfile also installs the necessary PHP extensions for WordPress to function correctly
# The Dockerfile is designed to copy a custom WordPress plugin from the Plugin directory to the appropriate location in the container
# It also exposes port 8080 for the web server and includes an initialization script to configure the WordPress environment and start Apache
# The script is responsible for setting up WordPress configurations and ensuring the environment is secure and ready
# The Dockerfile is intended to be used with the 'docker build' command to create a Docker image for a WordPress environment
# The image can be run as a container with the 'docker run' command to host a WordPress site
# Note: It's important to maintain the Dockerfile and monitor for updates to base images and dependencies
# Regular updates are necessary to keep the WordPress environment secure and performant

# Starting with the official WordPress image for a stable and secure base
FROM wordpressdevelop/phpunit

# Setting the working directory to the WordPress root directory
WORKDIR /var/www/html/

# Installing WP-CLI for command-line WordPress management
# Ensures that the downloaded WP-CLI Phar is executable and moves it to a global location for easy access
RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x wp-cli.phar \
    && mv wp-cli.phar /usr/local/bin/wp \
    && echo "WP-CLI successfully installed."

# Copying the custom WordPress plugin from the Plugin directory to the appropriate location in the container
# Excludes files specified in .dockerignore to minimize the image size and secure the build environment
chmod -R 777 ./Plugin
COPY /g/Insidious_Meme/2023-09-29_MemePlayer_v_2/IM-meme-player/IM-meme-player/IM-meme-player/IM-meme-player/Plugin/ /var/www/html/wp-content/plugins/im-meme-player/

# Installing the necessary PHP extensions for WordPress to function correctly
# mysqli for MySQL database support, pdo and pdo_mysql for PHP Data Objects to abstract database access
RUN docker-php-ext-install mysqli pdo pdo_mysql \
    && echo "PHP extensions for mysqli, pdo, and pdo_mysql installed successfully."

# Exposing port 8080 for the web server
# Ensure this matches your Apache configuration and intended external port mappings
EXPOSE 8080

# Copying an initialization script to configure the WordPress environment and start Apache
# Ensures that the script is executable and will run as the container starts
# Copying the initialization script to the container's /usr/local/bin directory for easy access
COPY /g/Insidious_Meme/2023-09-29_MemePlayer_v_2/IM-meme-player/IM-meme-player/IM-meme-player/IM-meme-player/init-script.sh /usr/local/bin/init-script.sh  
RUN chmod +x /usr/local/bin/init-script.sh \
    && echo "Initialization script copied and set as executable."

# Defining the entry point to initialize the WordPress environment
# The script is responsible for setting up WordPress configurations and ensuring the environment is secure and ready
ENTRYPOINT ["/usr/local/bin/init-script.sh"]

# Starting the Apache server in the foreground
# The init-script will handle the execution of 'apache2-foreground' to start the server
# Ensuring that Apache runs in the foreground keeps the Docker container alive
CMD ["/usr/local/bin/init-script.sh", "apache2-foreground"]

# Note: It's important to maintain the Dockerfile and monitor for updates to base images and dependencies
# Regular updates are necessary to keep the WordPress environment secure and performant
