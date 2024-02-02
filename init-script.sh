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
 export WP_PLUGIN_VERSION="1.0.0"

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

# Set the plugin domain path
 export WP_PLUGIN_DOMAIN_PATH="/languages"

# Set the plugin network
 export WP_PLUGIN_NETWORK="false"

# Set the plugin requires at least
 export WP_PLUGIN_REQUIRES_AT_LEAST="5.2"

# Set the plugin tested up to
 export WP_PLUGIN_TESTED_UP_TO="5.8"

# Set the plugin requires PHP
 export WP_PLUGIN_REQUIRES_PHP="7.0"


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
