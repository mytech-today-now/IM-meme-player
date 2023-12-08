<?php
// playlist-display.php

function display_playlist($playlist_id) {
    $playlist = get_post($playlist_id);

    if (!$playlist) {
        echo "Playlist not found.";
        return;
    }

    // Assuming playlist items are stored in a custom field (e.g., 'playlist_items')
    // Each item could be a post ID, a URL, or any identifier for the media/content
    $playlist_items = get_post_meta($playlist_id, 'playlist_items', true);

    if (empty($playlist_items)) {
        echo "No items in this playlist.";
        return;
    }

    echo '<div class="playlist">';
    echo '<h3>' . esc_html($playlist->post_title) . '</h3>';
    echo '<ul class="playlist-items">';

    foreach ($playlist_items as $item_id) {
        // Fetch each item details. This could be another post, a media file, etc.
        // For simplicity, assuming it's another post
        $item = get_post($item_id);
        if ($item) {
            echo '<li class="playlist-item">';
            echo '<a href="' . esc_url(get_permalink($item_id)) . '">' . esc_html($item->post_title) . '</a>';
            echo '</li>';
        }
    }

    echo '</ul>';
    echo '</div>';
}
?>

