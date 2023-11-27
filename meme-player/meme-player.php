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
    wp_enqueue_script('meme-player-script', plugins_url('script.js', __FILE__), array('jquery'), '0.0.1', true);
    wp_enqueue_style('meme-player-style', plugins_url('style.css', __FILE__), array(), '0.0.1');
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

// Shortcode to display meme player
function meme_player_shortcode() {
    ob_start();
    include(plugin_dir_path(__FILE__) . 'index.html');
    return ob_get_clean();
}

add_shortcode('meme_player', 'meme_player_shortcode');

// Register custom post type
function register_playlist_post_type() {
    // Custom post type registration code...
}
add_action('init', 'register_playlist_post_type');

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
        <form method="post" action="<?php echo esc_html(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="folder2post_submit">
            <?php wp_nonce_field('folder2post_nonce', 'folder2post_nonce_field'); ?>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Folder Path:</th>
                    <td><input type="text" name="folder_path" value="" class="regular-text" /></td>
                </tr>
            </table>

            <p class="submit">
                <input type="submit" class="button-primary" value="Save Changes" />
            </p>
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
