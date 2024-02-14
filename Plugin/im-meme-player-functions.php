<?php
// Version: 0.0.7.1

// im-meme-player-functions.php

// Include the ConsoleLogger class
use MyTechToday\IMMemePlayer\ConsoleLogger;

// Ensure this file is being included within the WordPress framework
if (!defined('ABSPATH')) {
    ConsoleLogger::error('im-meme-player-functions.php - ABSPATH constant not defined');
    exit;
}

// Function to handle the AJAX request for updating playlist order
function meme_handle_update_playlist_order() {
    // Verify nonce and user capabilities
    check_ajax_referer('meme_update_playlist_order_action', 'nonce');
    ConsoleLogger::log("meme_handle_update_playlist_order() - Verify nonce and user capabilities."); // Log for debugging
    
    if (!current_user_can('edit_others_playlists')) {
        ConsoleLogger::error("You do not have permission to edit playlists."); // Log for debugging
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
        ConsoleLogger::log("Playlist order updated successfully."); // Log for debugging
        wp_send_json_success('Playlist order updated successfully.');
    } else {
        ConsoleLogger::error("Failed to update playlist order."); // Log for debugging
        wp_send_json_error('Failed to update playlist order.');
    }
}

// Function to handle the form submission for creating new playlists
function meme_handle_playlist_form_submission() {
    // Ensure this function is triggered from the expected form and nonce
    check_admin_referer('meme_save_playlist_action', 'meme_save_playlist_nonce');
    ConsoleLogger::log("meme_handle_playlist_form_submission() - Security check with nonce - complete"); // Log for debugging

    if (!current_user_can('publish_playlists')) {
        ConsoleLogger::error("You do not have permission to create playlists."); // Log error for debugging
        wp_die('You do not have permission to create playlists.');
    }

    if (isset($_POST['playlist_title'])) {
        $new_title = sanitize_text_field($_POST['playlist_title']);
        ConsoleLogger::log("meme_handle_playlist_form_submission() - Sanitize input data - complete"); // Log for debugging
        
        $post_id = wp_insert_post(array(
            'post_title'    => $new_title,
            'post_status'   => 'publish',
            'post_type'     => 'playlist'
        ));

        if ($post_id) {
            // Provide feedback or redirection on success
            // You might use admin notices or set a transient to show confirmation on the next page load
            ConsoleLogger::log("meme_handle_playlist_form_submission() - success"); // Log for debugging
            wp_redirect(admin_url('tools.php?page=meme-playlist-manager'));
        } else {
            // Provide feedback on failure
            // Similarly, you might use admin notices or logging for errors
            ConsoleLogger::error("Failed to create playlist."); // Log error for debugging
            wp_die('Failed to create playlist.');
        }
    }
}

function im_meme_player_rename_playlist() {
    // Security check with nonce
    check_ajax_referer('im_meme_player_rename_playlist_nonce', 'security');
    ConsoleLogger::error("im_meme_player_rename_playlist() - Security check with nonce - complete"); // Log for debugging

    // Sanitize input data
    $playlist_id = isset($_POST['playlist_id']) ? intval($_POST['playlist_id']) : 0;
    $new_title = isset($_POST['new_title']) ? sanitize_text_field($_POST['new_title']) : '';
    ConsoleLogger::log("im_meme_player_rename_playlist() - Sanitize input data - complete"); // Log for debugging

    // Check user capabilities
    if (!current_user_can('edit_post', $playlist_id)) {
        ConsoleLogger::error("Insufficient permissions to rename this playlist."); // Log error for debugging
        wp_send_json_error('Insufficient permissions to rename this playlist.');
        return;
    }

    // Update post title
    $post_data = array(
        'ID' => $playlist_id,
        'post_title' => $new_title,
    );

    $result = wp_update_post($post_data, true);

    if (is_wp_error($result)) {
        ConsoleLogger::error($result->get_error_message()); // Log error for debugging
        wp_send_json_error($result->get_error_message());
    } else {
        ConsoleLogger::log("Playlist renamed successfully."); // Log for debugging
        wp_send_json_success('Playlist renamed successfully.');
    }
}

function im_meme_player_delete_playlist() {
    // Security check with nonce
    ConsoleLogger::log("im_meme_player_delete_playlist() - Security check with nonce - complete"); // Log for debugging
    check_ajax_referer('im_meme_player_delete_playlist_nonce', 'security');

    // Sanitize input data
    $playlist_id = isset($_POST['playlist_id']) ? intval($_POST['playlist_id']) : 0;

    // Check user capabilities
    if (!current_user_can('delete_post', $playlist_id)) {
        ConsoleLogger::log("Insufficient permissions to delete this playlist."); // Log for debugging
        wp_send_json_error('Insufficient permissions to delete this playlist.');
        return;
    }

    $result = wp_delete_post($playlist_id, true); // True to bypass trash and permanently delete

    if (!$result) {
        ConsoleLogger::error("Failed to delete playlist."); // Log error for debugging
        wp_send_json_error('Failed to delete playlist.');
    } else {
        ConsoleLogger::log("Playlist deleted successfully."); // Log for debugging
        wp_send_json_success('Playlist deleted successfully.');
    }
}

// AJAX action hook for deleting playlists
ConsoleLogger::log("AJAX action hooking for deleting playlists."); // Log for debugging
add_action('wp_ajax_im_meme_player_delete_playlist', 'im_meme_player_delete_playlist');
ConsoleLogger::log("AJAX action hooked for deleting playlists."); // Log for debugging

// AJAX action hook for renaming playlists
ConsoleLogger::log("AJAX action hooking for renaming playlists."); // Log for debugging
add_action('wp_ajax_im_meme_player_rename_playlist', 'im_meme_player_rename_playlist');
ConsoleLogger::log("AJAX action hooked for renaming playlists."); // Log for debugging

// AJAX action hook for updating playlist order
ConsoleLogger::log("AJAX action hooking for updating playlist order."); // Log for debugging
add_action('wp_ajax_meme_update_playlist_order', 'meme_handle_update_playlist_order');
ConsoleLogger::log("AJAX action hooked for updating playlist order."); // Log for debugging

// Admin POST action hook for creating new playlists
add_action('admin_post_meme_save_playlist', 'meme_handle_playlist_form_submission');
ConsoleLogger::log("Admin POST action hook for creating new playlists."); // Log for debugging