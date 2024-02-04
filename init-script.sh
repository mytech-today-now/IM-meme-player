#!/bin/bash

# Version: 0.0.7.1

# This script is used to initialize a WordPress installation.
# It installs WordPress, sets up the database, and configures settings.

# Set the WordPress installation directory
 cd /var/www/html

# Set the database credentials
 export WP_DB_HOST="localhost"
 export WP_DB_NAME="wordpress"
 export WP_DB_USER="wordpress"
 export WP_DB_PASSWORD="wordpress"

# Set the site URL
 export WP_SITE_URL="http://example.com"

# Set the site title
 export WP_SITE_TITLE="Example Site"

# Set the admin user
 export WP_ADMIN_USER="admin"

# Set the admin password
 export WP_ADMIN_PASSWORD="admin_password"

# Set the admin email
 export WP_ADMIN_EMAIL="mytechtoday@protonmail.com"

# Set the timezone
 export WP_TIMEZONE="America/New_York"

# Set the theme
 export WP_THEME="twentytwentytwentyfour"

# Set the plugins
 export WP_PLUGINS="akismet hello-dolly error-log-monitor"

# Set the plugin directory
 export WP_PLUGIN_DIR="/var/www/html/wp-content/plugins"

# Set the plugin
 export WP_PLUGIN="im-meme-player"

# Set the plugin URL
 export WP_PLUGIN_URL=""

# Set the plugin version
 export WP_PLUGIN_VERSION="0.0.7.2"

# Set the plugin author
 export WP_PLUGIN_AUTHOR="IM Meme Player"

# Set the plugin author URL
 export WP_PLUGIN_AUTHOR_URL="https://www.google.com"

# Set the plugin description
 export WP_PLUGIN_DESCRIPTION="A plugin for playing memes."

# Set the plugin license
 export WP_PLUGIN_LICENSE="GPLv2 or later"

# Set the plugin license URL
 export WP_PLUGIN_LICENSE_URL="GPL-2.0-or-later"

# Set the plugin text domain
 export WP_PLUGIN_TEXT_DOMAIN="im-meme-player"

# Set the plugin network
 export WP_PLUGIN_NETWORK="false"

# Set the plugin requires at least
 export WP_PLUGIN_REQUIRES_AT_LEAST="5.2"

# Set the plugin tested up to
 export WP_PLUGIN_TESTED_UP_TO="5.8"

# Set the plugin requires PHP
 export WP_PLUGIN_REQUIRES_PHP="7.0"

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
