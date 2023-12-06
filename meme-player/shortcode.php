<?php
// shortcode.php

// Prevent direct file access
if (!defined('ABSPATH')) {
    exit;
}

// Function to retrieve playlist items
function get_playlist_items($playlist_id) {
    // Retrieve the playlist post
    $playlist_post = get_post($playlist_id);

    // Assuming the playlist items are stored as JSON in post_content
    $items = json_decode($playlist_post->post_content, true);

    return is_array($items) ? $items : array();
}

// Shortcode to display meme player with a specific playlist
function meme_player_shortcode($atts) {
    // Extract the playlist attribute from the shortcode
    $atts = shortcode_atts(array(
        'playlist' => 'default_playlist' // Default playlist if none provided
    ), $atts, 'meme_player');

    $playlist = $atts['playlist'];

    // Start output buffering
    ob_start();

    // Retrieve playlist items
    $playlist_items = get_playlist_items($playlist);

    // Render the playlist items
    echo '<div class="meme-player-container">';
    foreach ($playlist_items as $item) {
        // Assuming $item contains the URL of the media
        if (is_image($item)) {
            echo '<img src="' . esc_url($item) . '" alt="Meme Image">';
        } elseif (is_video($item)) {
            echo '<video controls><source src="' . esc_url($item) . '" type="video/mp4">Your browser does not support the video tag.</video>';
        }
    }
    echo '</div>';

    // Return the buffered content
    return ob_get_clean();
}

add_shortcode('meme_player', 'meme_player_shortcode');

// Helper functions to determine media type
function is_image($file) {
    $image_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    return in_array($ext, $image_extensions);
}

function is_video($file) {
    $video_extensions = ['mp4', 'webm', 'ogg'];
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    return in_array($ext, $video_extensions);
}
?>
