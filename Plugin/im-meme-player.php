<?php
/*
Plugin Name: IM Meme Player
Description: A multimedia meme player plugin for WordPress.
Version: 0.0.7
Author: mytechtoday@protonmail.com
GitHub: @mytech-today-now/IM-meme-player/
*/

// Prevent direct file access
defined('ABSPATH') || exit;

// =====================
// ConsoleLogger class
// =====================
// Mimics JavaScript's console.log and console.error functionality in PHP.
// =====================
// Example usage
// =====================
// ConsoleLogger::log('This is an informational message.');
// ConsoleLogger::error('This is an error message.');
class ConsoleLogger {

    /**
     * Logs a message as an informational log.
     * 
     * @param mixed $message The message to log.
     */
    public static function log($message) {
        if (is_array($message) || is_object($message)) {
            $message = print_r($message, true);
        }
        // Log the message as an informational log
        error_log("Info: " . $message);
    }

    /**
     * Logs a message as an error.
     * 
     * @param mixed $message The message to log.
     */
    public static function error($message) {
        if (is_array($message) || is_object($message)) {
            $message = print_r($message, true);
        }
        // Log the message as an error
        error_log("Error: " . $message);
    }
}

// Define the function to run upon plugin activation
function meme_set_default_allowed_origins() {
    // Fetch current allowed origins
    $allowed_origins = get_option('meme_player_allowed_origins');

    // Set default allowed origins if not set
    if (empty($allowed_origins)) {
        update_option('meme_player_allowed_origins', [get_site_url()]);
    }
}

// Register the above function to run on plugin activation
register_activation_hook(__FILE__, 'meme_set_default_allowed_origins');

// Including other PHP files
include_once plugin_dir_path(__FILE__) . 'im-meme-player-admin-menu.php';
include_once plugin_dir_path(__FILE__) . 'im-meme-player-admin-page.php';
include_once plugin_dir_path(__FILE__) . 'im-meme-player-config.php';
include_once plugin_dir_path(__FILE__) . 'filelist.php';
include_once plugin_dir_path(__FILE__) . 'im-meme-player-functions.php';
include_once plugin_dir_path(__FILE__) . 'im-meme-player-playlist-cpt.php';
include_once plugin_dir_path(__FILE__) . 'im-meme-player-playlist-display.php';
include_once plugin_dir_path(__FILE__) . 'im-meme-player-playlist-helpers.php';
include_once plugin_dir_path(__FILE__) . 'im-meme-player-shortcode.php';
include_once plugin_dir_path(__FILE__) . 'im-meme-player-uninstall.php';

// Handle CORS via function
handle_cors();

/**
 * Retrieves a playlist from the database.
 * 
 * @param int $playlist_id The ID of the playlist.
 * @return object|null The playlist object or null if not found.
 */
function get_playlist($playlist_id) {
    global $wpdb;
    $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}playlists WHERE id = %d", $playlist_id);

    try {
        $result = $wpdb->get_row($query);
        if (!$result) {
            throw new Exception('Playlist not found.');
        }
        return $result;
    } catch (Exception $e) {
        ConsoleLogger::error('Error in get_playlist: ' . $e->getMessage());
        return null;
    }
}

/**
 * Sanitizes and echoes a string.
 * 
 * @param string $string The string to sanitize and echo.
 */
function safe_echo($string) {
    echo esc_html($string);
}

// CSRF Token Generation
function generate_csrf_token() {
    return wp_create_nonce('meme_player_action');
}

// CSRF Token Verification
function verify_csrf_token($token) {
    return wp_verify_nonce($token, 'meme_player_action');
}

// Secure File Upload (File Upload Vulnerabilities)
function handle_file_upload($file) {
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    $file_return = wp_handle_upload($file, ['test_form' => false]);
    if (isset($file_return['error']) || isset($file_return['upload_error_handler'])) {
        return false; // Handle the error
    }
    return $file_return['file'];
}

// Secure File Path (Directory Traversal)
function get_secure_path($file) {
    return basename($file);
}

// Session Security (Session Hijacking and Fixation)
add_action('wp_login', function() {
    session_regenerate_id(true);
});

// Access Control (IDOR)
function can_edit_playlist($playlist_id, $user_id) {
    // Implement logic to check if $user_id can edit $playlist_id
    return true; // Placeholder, replace with actual logic
}

// Secure XML Processing (XXE)
function process_xml($xml_string) {
    // In PHP 8.0 and later, external entity loading is disabled by default
    // Therefore, there's no need to call libxml_disable_entity_loader()

    // Error handling for XML loading
    libxml_use_internal_errors(true);

    $xml = simplexml_load_string($xml_string);
    if ($xml === false) {
        // Handle errors in XML parsing
        foreach (libxml_get_errors() as $error) {
            // Handle or log the error
        }
        libxml_clear_errors();
        return null; // or handle the error as appropriate
    }

    return $xml;
}

/*
Start of Actual Code
*/

/**
 * Enqueues scripts and styles for the plugin.
 */
function meme_player_enqueue_scripts() {
    wp_enqueue_style('playlist-style', plugin_dir_url(__FILE__) . 'style.css');
    wp_enqueue_script('playlist-script', plugin_dir_url(__FILE__) . 'script.js', array('jquery'), null, true);
}

/**
 * Enqueues the media uploader scripts.
 */
function mytech_enqueue_media_uploader() {
    wp_enqueue_media();
}

// Add the settings page to the MemePlayer menu
function meme_player_admin_menu() {
    add_menu_page('MemePlayer', 'MemePlayer', 'manage_options', 'meme-player-settings', 'meme_player_settings_page', 'dashicons-admin-generic');
    add_submenu_page('meme-player-settings', 'General Settings', 'General Settings', 'manage_options', 'meme-player-general-settings', 'meme_player_general_settings_page');
}

function meme_player_general_settings_page() {
    // Check if the user has submitted the settings
    if (isset($_POST['meme_player_settings_nonce']) && wp_verify_nonce($_POST['meme_player_settings_nonce'], 'meme_player_settings_action')) {
        // Validate and sanitize the setting
        $image_display_time = isset($_POST['image_display_time']) ? intval($_POST['image_display_time']) : 0;
        if ($image_display_time > 0) {
            update_option('meme_player_image_display_time', $image_display_time);
        } else {
            // Handle invalid input
            echo '<div class="error">Invalid Image Display Time. Please enter a positive number.</div>';
        }
    }

    // Retrieve the current setting value
    $current_value = get_option('meme_player_image_display_time', 5); // Default to 5 if not set

    ?>
    <div class="wrap">
        <h1>MemePlayer General Settings</h1>
        <form method="post" action="">
            <?php wp_nonce_field('meme_player_settings_action', 'meme_player_settings_nonce'); ?>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Image Display Time (seconds)</th>
                    <td>
                        <input type="number" name="image_display_time" value="<?php echo esc_attr($current_value); ?>" min="1" />
                        <p class="description">Set the duration for how long each image should be displayed in the player.</p>
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Function to retrieve media items based on tags, categories, or search terms
function imp_get_media_items($tag = '', $category = '', $search = '') {
    global $wpdb;
    $query = "SELECT * FROM {$wpdb->prefix}media WHERE 1=1";

    if (!empty($tag)) {
        $query .= $wpdb->prepare(" AND tag = %s", $tag);
    }

    if (!empty($category)) {
        $query .= $wpdb->prepare(" AND category = %s", $category);
    }

    if (!empty($search)) {
        $query .= $wpdb->prepare(" AND (title LIKE %s OR description LIKE %s)", "%$search%", "%$search%");
    }

    return $wpdb->get_results($query);
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
add_action('admin_enqueue_scripts', 'mytech_enqueue_media_uploader');

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

// Folder2Post form submission handler
class FolderReader {
    private $folderPath;

    public function __construct($folderPath) {
        $this->folderPath = sanitize_text_field($folderPath);
        $this->validateFolderPath();
    }

    private function validateFolderPath() {
        if (!file_exists($this->folderPath) || !is_dir($this->folderPath)) {
            wp_die('The specified folder does not exist.');
        }
    }

    public function readFolder() {
        $files = scandir($this->folderPath);
        if ($files === false) {
            wp_die('Failed to read the folder contents.');
        }
        return $files;
    }
}


/**
 * Handles the form submission for Folder2Post.
 */
function handle_folder2post_form_submission() {
    if (!isset($_POST['folder2post_nonce_field']) || !wp_verify_nonce($_POST['folder2post_nonce_field'], 'folder2post_nonce')) {
        wp_die('Security check failed');
    }

    if (current_user_can('manage_options')) {
        
        // Validate the folder path & read the folder contents
        if (isset($_POST['folder_path'])) {
            try {
                $folderReader = new FolderReader($_POST['folder_path']);
                $files = $folderReader->readFolder();
                // Process the $files as needed
            } catch (Exception $e) {
                // Handle exceptions if necessary
            }
        }

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

add_action('admin_menu', 'meme_player_admin_menu');
add_action('admin_post_folder2post_submit', 'handle_folder2post_form_submission');

add_shortcode('meme_player', 'meme_player_shortcode');
