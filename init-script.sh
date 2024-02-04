#!/bin/bash

# Version: 0.0.7.2

# This script is used to initialize a WordPress installation.
# It installs WordPress, sets up the database, and configures settings.

# Set the WordPress installation directory
 cd /var/www/html

# This script sets up WordPress, ensures the IM Meme Player plugin and additional specified plugins are installed and activated.

# Function to wait for the database to be ready
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
        wp core install --url="http://example.com" --title="Example Site" --admin_user="admin" --admin_password="admin_password" --admin_email="admin@example.com" --allow-root
        echo "WordPress installed successfully."
    else
        echo "WordPress is already installed."
    fi
}

# Install and activate plugins
install_activate_plugins() {
    # Array of plugins to install and activate
    declare -a plugins=("query-monitor" "debug-log-manager" "im-meme-player")

    for plugin in "${plugins[@]}"; do
        if [ "$plugin" == "im-meme-player" ]; then
            PLUGIN_PATH="/var/www/html/wp-content/plugins/im-meme-player"
            if [ -d "$PLUGIN_PATH" ]; then
                echo "Installing and activating $plugin..."
                wp plugin install $PLUGIN_PATH --activate --allow-root
            else
                echo "$plugin directory not found: $PLUGIN_PATH"
            fi
        else
            echo "Installing and activating $plugin from WordPress plugin repository..."
            wp plugin install $plugin --activate --allow-root
        fi
    done

    echo "All specified plugins have been installed and activated successfully."
}

# Start the script
echo "Starting WordPress initialization script..."

# Ensure the WordPress environment is ready
wait_for_db
initialize_wordpress

# Install and activate the specified plugins
install_activate_plugins

# Keep the container running after initialization
exec "$@"
