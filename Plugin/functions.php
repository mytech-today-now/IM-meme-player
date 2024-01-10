<?php
// Version: 0.0.7

// Ensure this file is being included within the WordPress framework
if (!defined('ABSPATH')) {
    exit;
}

// Function to handle the AJAX request for updating playlist order
function meme_handle_update_playlist_order() {
    // Verify nonce and user capabilities
    check_ajax_referer('meme_update_playlist_order_action', 'nonce');
    
    if (!current_user_can('edit_others_playlists')) {
        wp_send_json_error('You do not have permission to edit playlists.');
        return;
    }

    // Get the new order and playlist ID from the AJAX request
    $orderedIds = isset($_POST['order']) ? $_POST['order'] : array();
    $playlist_id = isset($_POST['playlist_id']) ? intval($_POST['playlist_id']) : 0;

    // Update each item's order
    $success = true; // Assume success unless an error occurs
    foreach ($orderedIds as $order => $item_id) {
        // Update the order in the database, assuming a structure for storing order
        $updated = update_post_meta($item_id, 'meme_playlist_order', $order);
        if (!$updated) {
            $success = false; // If any update fails, mark success as false
        }
    }

    if ($success) {
        wp_send_json_success('Playlist order updated successfully.');
    } else {
        wp_send_json_error('Failed to update playlist order.');
    }
}

// Function to handle the form submission for creating new playlists
function meme_handle_playlist_form_submission() {
    // Ensure this function is triggered from the expected form and nonce
    check_admin_referer('meme_save_playlist_action', 'meme_save_playlist_nonce');

    if (!current_user_can('publish_playlists')) {
        wp_die('You do not have permission to create playlists.');
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
            // You might use admin notices or set a transient to show confirmation
        } else {
            // Provide feedback on failure
            // Similarly, you might use admin notices or logging for errors
        }
    }
}

// AJAX action hook for updating playlist order
add_action('wp_ajax_meme_update_playlist_order', 'meme_handle_update_playlist_order');

// Admin POST action hook for creating new playlists
add_action('admin_post_meme_save_playlist', 'meme_handle_playlist_form_submission');
