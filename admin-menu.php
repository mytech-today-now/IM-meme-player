<?php
// Ensure this file is being included within the WordPress framework
if (!defined('ABSPATH')) {
    exit;
}

// Function to add a menu for the playlist manager in the admin dashboard
function meme_add_playlist_manager_menu() {
    add_menu_page(
        __('Playlist Manager', 'meme-domain'),   // Page title
        __('Playlists', 'meme-domain'),         // Menu title
        'manage_options',                       // Capability required to see this option
        'meme-playlist-manager',                // Unique menu slug
        'meme_playlist_manager_page_content',   // Function to output the content for this page
        'dashicons-playlist-audio',             // Icon for the menu
        6                                      // Position in the menu (6 is just below Posts)
    );
}

// Output the content for the playlist manager page
function meme_playlist_manager_page_content() {
    // Verify user permissions
    if (!current_user_can('manage_options')) {
        return;
    }

    // Handle any form submissions here
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        meme_handle_playlist_form_submission();
    }

    // Displaying existing playlists with options for editing and reordering
    echo '<div class="wrap">';
    echo '<h1>' . __('Playlist Manager', 'meme-domain') . '</h1>';

    $playlists = get_posts(array('post_type' => 'playlist', 'numberposts' => -1));
    foreach ($playlists as $playlist) {
        echo '<div class="playlist" id="playlist-' . esc_attr($playlist->ID) . '">';
        echo '<h3>' . esc_html($playlist->post_title) . '</h3>';

        // Retrieve and display playlist items here
        // You will need to fetch and display the actual playlist items here
        // and ensure they have proper data attributes for handling reordering.

        echo '</div>'; // .playlist
    }

    // Add New Playlist Form
    echo '<h2>' . __('Add New Playlist', 'meme-domain') . '</h2>';
    echo '<form method="post" action="">';
    wp_nonce_field('meme_save_playlist_action', 'meme_save_playlist_nonce');
    echo '<input type="text" name="playlist_title" placeholder="' . __('Playlist Title', 'meme-domain') . '" required>';
    echo '<input type="submit" class="button button-primary" value="' . __('Add New Playlist', 'meme-domain') . '">';
    echo '</form>';

    echo '</div>'; // .wrap
}

// Handle the form submission for playlists
function meme_handle_playlist_form_submission() {
    $nonce_value = isset($_POST['meme_save_playlist_nonce']) ? $_POST['meme_save_playlist_nonce'] : '';
    if (!wp_verify_nonce($nonce_value, 'meme_save_playlist_action')) {
        return;
    }

    if (isset($_POST['playlist_title'])) {
        $new_title = sanitize_text_field($_POST['playlist_title']);
        
        $post_id = wp_insert_post(array(
            'post_title'    => $new_title,
            'post_status'   => 'publish',
            'post_type'     => 'playlist'
        ));

        if ($post_id) {
            // Provide feedback or redirection on success
            // Consider using admin notices or redirection to the edit page
        } else {
            // Provide feedback on failure
            // Consider logging the error or showing an admin notice
        }
    }
}

// Hook into admin_menu to add the menu page
add_action('admin_menu', 'meme_add_playlist_manager_menu');
