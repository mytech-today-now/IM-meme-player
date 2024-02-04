#!/bin/bash

# Enhanced WordPress Initialization Script
# Author: mytech@protonmail.com
# Description: Initializes WordPress installation, sets up database, configures settings,
# and installs and activates essential plugins including Query Monitor and Debug Log Manager.

# Version: 0.0.7.2
# Version: 0.0.7.3 - https://chat.openai.com/share/ee97747c-9f64-49b3-8920-b17f1b82f9f1
# Version: 0.0.7.4 - https://chat.openai.com/share/aab675bd-ca3f-4692-9b33-062ae614301e 
#                  - Enhanced logging, corrected plugin list to include "Debug Log Manager," and ensured proper script execution with verbose comments for clarity.


# This script is used to initialize a WordPress installation.
# It installs WordPress, sets up the database, and configures settings.

# Set the WordPress installation directory
cd /var/www/html

echo "----- Script Start -----"

# Function to log messages with timestamps for better tracking.
log_message() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') - $1"
}

# Simulate logging to the web browser console by preparing messages.
# Note: This is for demonstration purposes and requires browser-side handling.
prepare_console_log() {
    echo "To log in browser console: console.log('$1')"
}

# Check if the MySQL database is ready before proceeding.
# Uses wp-cli to check database connectivity.
wait_for_db() {
    log_message "Checking database availability..."
    until wp db check --allow-root > /dev/null 2>&1; do
        log_message "Database not ready. Waiting..."
        sleep 5
    done
    log_message "Database is ready."
}

# Initialize WordPress if not already installed.
# Uses wp-cli to install WordPress with provided details.
initialize_wordpress() {
    if ! wp core is-installed --allow-root; then
        log_message "WordPress not installed. Installing..."
        if wp core install --url="http://example.com" --title="Example Site" \
           --admin_user="admin" --admin_password="admin_password" \
           --admin_email="admin@example.com" --allow-root; then
            log_message "WordPress installation successful."
        else
            log_message "WordPress installation failed."
        fi
    else
        log_message "WordPress is already installed."
    fi
}

# Install and activate specified plugins.
# Adjusts to include Query Monitor and Debug Log Manager, ensuring both are installed and activated.
install_activate_plugins() {
    # Define the list of plugins to install and activate.
    # Make sure to replace placeholders with actual plugin slugs.
    declare -a plugins=("query-monitor" "debug-log-manager") # Update this list as needed.

    for plugin in "${plugins[@]}"; do
        log_message "Installing and activating plugin: $plugin..."
        if wp plugin install $plugin --activate --allow-root; then
            log_message "$plugin activated successfully."
            prepare_console_log "$plugin activated successfully."
        else
            log_message "Failed to activate $plugin."
        fi
    done

    # Additional step for custom plugin activation if necessary.
    # Example: IM Meme Player plugin activation.
    local PLUGIN_PATH="/var/www/html/wp-content/plugins/im-meme-player"
    if [ -d "$PLUGIN_PATH" ]; then
        log_message "Activating IM Meme Player plugin..."
        if wp plugin activate im-meme-player --allow-root; then
            log_message "IM Meme Player plugin activated successfully."
            prepare_console_log "IM Meme Player plugin activated successfully."
        else
            log_message "Failed to activate IM Meme Player plugin."
        fi
    else
        log_message "IM Meme Player plugin directory not found: $PLUGIN_PATH"
    fi
}

# Main script execution starts here.

log_message "Starting WordPress initialization script..."
prepare_console_log "Starting WordPress initialization..."

# Check for database readiness before proceeding.
wait_for_db

# Proceed with WordPress installation.
initialize_wordpress

# Install and activate required plugins.
install_activate_plugins

log_message "Initialization script completed successfully."
echo "----- Script End -----"
