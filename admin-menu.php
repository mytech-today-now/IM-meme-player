<?php
// Ensure this file is being included within the WordPress framework
if (!defined('ABSPATH')) {
    exit;
}

// Function to add a menu for our playlist manager in the admin dashboard
function meme_add_playlist_manager_menu() {
    add_menu_page(
        __('Playlist Manager', 'meme-domain'),   // Page title
        __('Playlists', 'meme-domain'),         // Menu title
        'manage_options',                         // Capability required to see this option
        'meme-playlist-manager',                // Unique menu slug
        'meme_playlist_manager_page_content',   // Function to output the content for this page
        'dashicons-playlist-audio',               // Icon for the menu
        6                                        // Position in the menu (6 is just below Posts)
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

    ?>
    <div class="wrap">
        <h1><?php _e('Playlist Manager', 'meme-domain'); ?></h1>
        <form method="post" action="<?php echo esc_html(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="meme_save_playlist">
            <?php
            // Security field
            wp_nonce_field('meme_save_playlist_action', 'meme_save_playlist_nonce');
            ?>
            <input type="text" name="playlist_title" placeholder="<?php _e('Playlist Title', 'meme-domain'); ?>" required>
            <input type="submit" class="button button-primary" value="<?php _e('Add New Playlist', 'meme-domain'); ?>">
        </form>
        <!-- You can further add list or edit forms here -->
    </div>
    <?php
}

// Handle the form submission
function meme_handle_playlist_form_submission() {
    // Check nonce for security
    $nonce_value = isset($_POST['meme_save_playlist_nonce']) ? $_POST['meme_save_playlist_nonce'] : '';
    if (!wp_verify_nonce($nonce_value, 'meme_save_playlist_action')) {
        return;
    }

    // Process form: here we just handle adding a new playlist for simplicity
    if (isset($_POST['playlist_title'])) {
        $new_title = sanitize_text_field($_POST['playlist_title']);
        
        // Insert the new playlist as a post of type 'playlist'
        $post_id = wp_insert_post(array(
            'post_title'    => $new_title,
            'post_status'   => 'publish',
            'post_type'     => 'playlist'
        ));

        // Handle error or success feedback
        if ($post_id) {
            // Admin notice or redirect on success
        } else {
            // Admin notice or handling on failure
        }
    }
}

// Hook into admin_menu to add the menu page
add_action('admin_menu', 'meme_add_playlist_manager_menu');
