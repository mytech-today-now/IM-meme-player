<?php
// Version: 0.0.7.1


// Ensure this file is being included within the WordPress framework
if (!defined('ABSPATH')) {
    exit;
}

// Include the ConsoleLogger class
use MyTechToday\IMMemePlayer\ConsoleLogger;

// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Option 1: Uninstall all code, remove custom post type and database changes
function uninstall_plugin_completely() {
    // Code to delete custom post type entries
    $post_types = ['your_custom_post_type']; // Replace with your custom post type(s)
    foreach ($post_types as $post_type) {
        $posts = get_posts(['post_type' => $post_type, 'numberposts' => -1]);
        foreach ($posts as $post) {
            wp_delete_post($post->ID, true);
        }
    }

    // Code to remove any other database changes made by the plugin
    $options = ['meme_player_option1', 'meme_player_option2']; // Replace with actual option names
    foreach ($options as $option) {
        delete_option($option);
    }
}

// Option 2: Deactivate plugin but keep custom post type and database changes
// No code needed here

// Option 3: Keep plugin installed but reset database changes
function reset_plugin_database_changes() {
    // Code to reset only the database changes made by the plugin
    // Example: Reset a specific option to its default value
    update_option('meme_player_some_option', 'default_value'); // Replace with actual logic
}

// Determine which option to execute based on saved settings or user input
$uninstall_option = get_option('meme_player_uninstall_option', 1);

switch ($uninstall_option) {
    case 1:
        uninstall_plugin_completely();
        break;
    case 2:
        // No action needed
        break;
    case 3:
        reset_plugin_database_changes();
        break;
}

// Delete the option that stores the uninstall preference
delete_option('meme_player_uninstall_option');
