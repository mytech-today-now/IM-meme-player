<?php
// Version: 0.0.7.1

// playlist-display.php

// Ensure this file is being included within the WordPress framework
if (!defined('ABSPATH')) {
    ConsoleLogger::error('playlist-display.php - ABSPATH constant not defined');
    exit;
}

// Include the ConsoleLogger class
use MyTechToday\IMMemePlayer\ConsoleLogger;

/**
 * Displays a playlist based on the provided playlist ID.
 * 
 * @param int $playlist_id The ID of the playlist to display.
 */
function display_playlist($playlist_id) {
    // Retrieve the playlist post
    $playlist = get_post($playlist_id);

    // Check if the playlist exists
    if (!$playlist) {
        echo "Playlist not found.";
        ConsoleLogger::error("Playlist with ID $playlist_id not found."); // Log error for debugging
        return;
    }

    // Retrieve playlist items, assuming they are stored in a custom field 'playlist_items'
    $playlist_items = get_post_meta($playlist_id, 'playlist_items', true);

    // Check if the playlist has items
    if (empty($playlist_items)) {
        echo "No items in this playlist.";
        ConsoleLogger::error("No items in playlist $playlist->post_title ($playlist_id)"); // Log error for debugging
        return;
    }

    // Display the playlist
    echo '<div class="playlist">';
    echo '<h3>' . esc_html($playlist->post_title) . '</h3>';
    echo '<ul class="playlist-items">';

    // Iterate over each item in the playlist
    foreach ($playlist_items as $item_id) {
        // Fetch details for each item, assuming it's another post
        $item = get_post($item_id);
        if ($item) {
            echo '<li class="playlist-item"><div class="playlist-item-title">';
            
            echo '<a href="' . esc_url(get_permalink($item_id)) . '">' . esc_html($item->post_title) . '</a></div>';

            echo '<div class="playlist-item-description">' . esc_html($item->post_content) . '</div><div class="playlist-item-media">';

            // Display the image or video preview (thumbnail)
            $media_url = get_the_post_thumbnail_url($item_id, 'medium');
            if ($media_url) {
                echo '<img src="' . esc_url($media_url) . '" alt="' . esc_attr($item->post_title) . '" class="playlist-item-thumbnail"></div>';
            }

            
            echo '</li>';
        } else {
            ConsoleLogger::error("Playlist item with ID $item_id not found."); // Log error for missing items
        }
    }

    echo '</ul>';
    echo '</div>';
}
?>