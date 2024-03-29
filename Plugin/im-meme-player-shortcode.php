<?php
// Version: 0.0.7.1

// im-meme-player-shortcode.php

// Include the ConsoleLogger class
use MyTechToday\IMMemePlayer\ConsoleLogger;

// Ensure this file is being included within the WordPress framework
if (!defined('ABSPATH')) {
    ConsoleLogger::error('im-meme-player-playlist-helpers.php - ABSPATH constant not defined');
    exit;
}

/**
 * Shortcode to display meme player with a specific playlist.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML content to display.
 */
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
            echo '<video controls><source src="' . esc_url($item) . '" type="' . get_video_mime_type($item) . '">Your browser does not support the video tag.</video>';
        }
    }
    echo '</div>';

    // Return the buffered content
    return ob_get_clean();
}

add_shortcode('meme_player', 'meme_player_shortcode');

// Helper functions to determine media type
function is_image($file) {
    $image_extensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp', 'ico', 'tif', 'tiff', 'jfif'];
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    return in_array(strtolower($ext), $image_extensions);
}

function is_video($file) {
    $video_extensions = ['mp4', 'webm', 'ogg'];
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    return in_array(strtolower($ext), $video_extensions);
}

// Function to get video MIME type based on file extension
function get_video_mime_type($file) {
    $mime_types = [
        'mp4' => 'video/mp4',
        'webm' => 'video/webm',
        'ogg' => 'video/ogg'
    ];
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    return isset($mime_types[$ext]) ? $mime_types[$ext] : 'video/mp4'; // Default to mp4 if extension not found
}
?>
