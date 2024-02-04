#!/bin/bash

# Version: 0.0.7.2

# This script is used to initialize a WordPress installation.
# It installs WordPress, sets up the database, and configures settings.

# Set the WordPress installation directory
 cd /var/www/html

# This script checks if WordPress is installed and then installs and activates the IM Meme Player plugin.

# Wait for database to be ready
wait_for_db() {
    echo "Waiting for database to be ready..."
    until wp db check --allow-root > /dev/null 2>&1; do
        sleep 5
    done
    echo "Database is ready."
}

# Initialize WordPress if not already installed
initialize_wordpress() {
    if ! wp core is-installed --allow-root; then
        echo "Installing WordPress..."
        wp core install --url="http://example.com" --title="Example Site" --admin_user="admin" --admin_password="admin_password" --admin_email="mytechtoday@protonmail.com" --allow-root
        echo "WordPress installed successfully."
    else
        echo "WordPress is already installed."
    fi
}

# Install and activate the IM Meme Player plugin
install_activate_plugin() {
    PLUGIN_PATH="/var/www/html/wp-content/plugins/im-meme-player"
    if [ -d "$PLUGIN_PATH" ]; then
        echo "Installing and activating IM Meme Player plugin..."
        wp plugin install $PLUGIN_PATH --activate --allow-root
        echo "IM Meme Player plugin activated successfully."
    else
        echo "IM Meme Player plugin directory not found: $PLUGIN_PATH"
    fi
}

# Start the script
echo "Starting WordPress initialization script..."

# Ensure the WordPress environment is ready
wait_for_db
initialize_wordpress

# Install and activate the IM Meme Player plugin
install_activate_plugin

# Keep the container running after initialization
exec "$@"
