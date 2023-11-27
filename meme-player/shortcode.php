<?php
// shortcode.php

// Prevent direct file access
if (!defined('ABSPATH')) {
    exit;
}

// Shortcode to display meme player with a specific playlist
function meme_player_shortcode($atts) {
    // Extract the playlist attribute from the shortcode
    $atts = shortcode_atts(array(
        'playlist' => 'default' // Default playlist if none provided
    ), $atts, 'meme_player');

    $playlist = sanitize_file_name($atts['playlist']);

    // Start output buffering
    ob_start();

    // Dynamically include the playlist-specific content
    $playlist_file = plugin_dir_path(__FILE__) . 'playlists/' . $playlist . '.html';

    // Check if the playlist file exists, otherwise include the default playlist
    if (file_exists($playlist_file)) {
        include($playlist_file);
    } else {
        include(plugin_dir_path(__FILE__) . 'playlists/default.html');
    }

    // Return the buffered content
    return ob_get_clean();
}

add_shortcode('meme_player', 'meme_player_shortcode');
?>
