php
Copy code
<?php
// meme-player.php

/** 
 * Plugin Name: Meme Player v.0.0.1
 * Author: myTech.Today
 * Description: Multimedia meme player for WordPress sites
 * Version: 0.0.1
 */

// Prevent direct file access
if (!defined('ABSPATH')) {
    exit;
}

// Enqueue scripts and styles
function meme_player_enqueue_scripts() {
    wp_enqueue_style('playlist-style', plugin_dir_url(__FILE__) . 'style.css');
    wp_enqueue_script('playlist-script', plugin_dir_url(__FILE__) . 'script.js', array('jquery'), null, true);

}
function register_playlist_post_type() {
    $args = array(
        'public' => true,
        'label'  => 'Playlists',
        'supports' => array('title', 'author', 'custom-fields')
    );
    register_post_type('playlist', $args);
}

add_action('wp_enqueue_scripts', 'meme_player_enqueue_scripts');
add_action('init', 'register_playlist_post_type');

// Include other PHP files
include_once plugin_dir_path(__FILE__) . 'config.php';
include_once plugin_dir_path(__FILE__) . 'filelist.php';
include_once plugin_dir_path(__FILE__) . 'shortcode.php'; // Include the new shortcode file
include_once(plugin_dir_path(__FILE__) . 'playlist-cpt.php');
include_once(plugin_dir_path(__FILE__) . 'playlist-manager.php');
include_once(plugin_dir_path(__FILE__) . 'playlist-display.php');
include_once(plugin_dir_path(__FILE__) . 'playlist-helpers.php');

// Shortcode to display meme player with a specific playlist
function meme_player_shortcode($atts) {
    // Extract the playlist attribute from the shortcode
    $atts = shortcode_atts(array(
        'playlist' => 'default_playlist' // Default playlist if none provided
    ), $atts, 'meme_player');

    $playlist = $atts['playlist'];

    // Start output buffering
    ob_start();

    // Include the playlist-specific content
    // You might need to adjust this part based on how your playlists are structured
    include(plugin_dir_path(__FILE__) . "playlists/{$playlist}.html");

    // Return the buffered content
    return ob_get_clean();
}

add_shortcode('meme_player', 'meme_player_shortcode');

// Add Folder2Post menu
function folder2post_menu() {
    add_menu_page('Folder2Post Configuration', 'Folder2Post', 'manage_options', 'folder2post', 'folder2post_page', 'dashicons-admin-generic');
}
add_action('admin_menu', 'folder2post_menu');

// Folder2Post page content
function folder2post_page() {
    ?>
    <div class="wrap">
        <h1>Folder2Post Configuration</h1>
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <input type="hidden" name="action" value="folder2post_submit">
            <input type="hidden" name="folder2post_nonce_field" value="<?php echo wp_create_nonce('folder2post_action'); ?>">
            <?php wp_nonce_field('folder2post_nonce', 'folder2post_nonce_field'); ?>
    
            <!-- Your existing form fields, e.g., folder_path -->
            <input type="text" name="folder_path">
    
            <!-- Submit button -->
                <input type="submit" value="Submit">
        </form>
    </div>
    <?php
}

// Handle the form submission
function handle_folder2post_form_submission() {
    if (!isset($_POST['folder2post_nonce_field']) || !wp_verify_nonce($_POST['folder2post_nonce_field'], 'folder2post_nonce')) {
        wp_die('Security check failed');
    }

    if (current_user_can('manage_options')) {
        $folderPath = sanitize_text_field($_POST['folder_path']);
    
        // Check if the folder exists
        if (!file_exists($folderPath) || !is_dir($folderPath)) {
            wp_die('The specified folder does not exist.');
        }
    
        // Read the contents of the folder
        $files = scandir($folderPath);
        if ($files === false) {
            wp_die('Failed to read the folder contents.');
        }
    
        // Filter out '.' and '..'
        $files = array_diff($files, array('.', '..'));
    
        // Convert the file list to JSON
        $json_data = json_encode($files);
        if ($json_data === false) {
            wp_die('Failed to encode the folder contents to JSON.');
        }
    
        // Create a new custom post type entry
        $post_data = array(
            'post_title'    => wp_strip_all_tags('Playlist - ' . basename($folderPath)),
            'post_content'  => $json_data,
            'post_status'   => 'publish',
            'post_author'   => get_current_user_id(),
            'post_type'     => 'playlist',
            'meta_input'    => array(
                'playlist_json' => json_encode($files) // $files is the JSON data
            )
        );
    
        // Insert the post into the database
        $post_id = wp_insert_post($post_data);
    
        if ($post_id == 0) {
            wp_die('There was an error creating the playlist post.');
        }
    
        // Redirect back to the settings page with a success message
        $redirect_url = add_query_arg(['page' => 'folder2post', 'status' => 'success'], admin_url('admin.php'));
        wp_safe_redirect($redirect_url);
        exit;
    } else {
        wp_die('You do not have sufficient permissions to access this page.');
    }
}
add_action('admin_post_folder2post_submit', 'handle_folder2post_form_submission');

add_shortcode('meme_player', 'meme_player_shortcode');
