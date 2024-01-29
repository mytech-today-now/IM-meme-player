#!/bin/bash

# Version: 0.0.7.1

# Function to wait for the database to be ready
wait_for_db() {
    RETRIES=10
    until wp db check --allow-root > /dev/null 2>&1 || [ $RETRIES -eq 0 ]; do
        echo "Waiting for the database server to be available..."
        sleep 5
        RETRIES=$((RETRIES-=1))
    done
}

# Start the script
echo "Starting WordPress initialization script..."

# Wait for the database to be ready
wait_for_db

# Check if WordPress is already installed
if ! wp core is-installed --allow-root; then
    # Install WordPress
    wp core install --url="example.com" --title="Example Site" --admin_user="admin" --admin_password="admin_password" --admin_email="mytechtoday@protonmail.com" --allow-root

    # Configure settings
    wp option update blogdescription "Testing the IM Meme Player" --allow-root
    wp rewrite structure '/%year%/%monthnum%/%day%/%postname%/' --allow-root
    wp option update timezone_string "America/New_York" --allow-root

    # Install and activate a theme
    wp theme install twentytwentytwentyfour --activate --allow-root

    # Install and activate plugins
    wp plugin install akismet --activate --allow-root
    wp plugin install hello-dolly --activate --allow-root
    wp plugin install error-log-monitor --activate --allow-root
    wp plugin install /var/www/html/wp-content/plugins/im-meme-player --activate --allow-root

    echo "WordPress has been successfully initialized."
else
    echo "WordPress is already installed."
fi

# Keep the container running after initialization
exec "$@"
