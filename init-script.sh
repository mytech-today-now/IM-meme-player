#!/bin/bash

# Version: 0.0.7.2
# Version: 0.0.7.3 - https://chat.openai.com/share/ee97747c-9f64-49b3-8920-b17f1b82f9f1


# This script is used to initialize a WordPress installation.
# It installs WordPress, sets up the database, and configures settings.

# Set the WordPress installation directory
 cd /var/www/html

# Enhanced WordPress Initialization Script with Debugging and Logging

echo "----- Script Start -----"

# Function to log messages with timestamps
log_message() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') - $1"
}

# Simulate logging to the web browser console by preparing messages
prepare_console_log() {
    echo "To log in browser console: console.log('$1')"
}

# Check database availability
wait_for_db() {
    log_message "Checking database availability..."
    until wp db check --allow-root > /dev/null 2>&1; do
        log_message "Database not ready. Waiting..."
        sleep 5
    done
    log_message "Database is ready."
}

# Initialize WordPress
initialize_wordpress() {
    if ! wp core is-installed --allow-root; then
        log_message "WordPress not installed. Installing..."
        wp core install --url="http://example.com" --title="Example Site" --admin_user="admin" --admin_password="admin_password" --admin_email="admin@example.com" --allow-root && log_message "WordPress installation successful." || log_message "WordPress installation failed."
    else
        log_message "WordPress is already installed."
    fi
}

# Install and activate specified plugins
install_activate_plugins() {
    declare -a plugins=("query-monitor" "debug-bar")

    for plugin in "${plugins[@]}"; do
        log_message "Installing and activating plugin: $plugin..."
        wp plugin install $plugin --activate --allow-root && log_message "$plugin activated successfully." || log_message "Failed to activate $plugin."
        prepare_console_log "$plugin activated successfully."
    done

    # IM Meme Player plugin activation
    PLUGIN_PATH="/var/www/html/wp-content/plugins/im-meme-player"
    if [ -d "$PLUGIN_PATH" ]; then
        log_message "Activating IM Meme Player plugin..."
        wp plugin activate im-meme-player --allow-root && log_message "IM Meme Player plugin activated successfully." || log_message "Failed to activate IM Meme Player plugin."
        prepare_console_log "IM Meme Player plugin activated successfully."
    else
        log_message "IM Meme Player plugin directory not found: $PLUGIN_PATH"
    fi
}

# Start of the script execution
log_message "Starting WordPress initialization script..."
prepare_console_log "Starting WordPress initialization..."

# Database readiness check
wait_for_db

# WordPress installation
initialize_wordpress

# Plugin installation and activation
install_activate_plugins

log_message "Initialization script completed."
echo "----- Script End -----"
