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

    // Error handling for non-existent posts
    if (!$playlist_post) {
        return array(); // Return an empty array if the post doesn't exist
    }

    // Assuming the playlist items are stored as JSON in post_content
    $items = json_decode($playlist_post->post_content, true);

    // Error handling for malformed JSON
    if (json_last_error() !== JSON_ERROR_NONE) {
        return array(); // Return an empty array if JSON is malformed
    }

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
            echo '<video controls><source src="' . esc_url($item) . '" type="' . get_video_mime_type($item) . '">Your browser does not support the video tag.</video>';
        }
    }
    echo '</div>';

    // Return the buffered content
    return ob_get_clean();
}

// Modify the existing shortcode function to handle tags, categories, and search
function meme_player_shortcode($atts) {
    $atts = shortcode_atts([
        'playlist' => 'default_playlist',
        'tag' => '',
        'category' => '',
        'search' => ''
    ], $atts, 'meme_player');

    $media_items = get_media_items($atts['tag'], $atts['category'], $atts['search']);

    ob_start();
    // Display the media items
    foreach ($media_items as $item) {
        echo "<div class='media-item'>";
        echo "<h3>" . esc_html($item->title) . "</h3>";
        echo "<p>" . esc_html($item->description) . "</p>";
        // Add more details as needed
        echo "</div>";
    }

    return ob_get_clean();
}

add_shortcode('meme_player', 'meme_player_shortcode');

// Helper functions to determine media type
function is_image($file) {
    $image_extensions = ['jpg', 'jpeg', 'png', 'gif'];
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
