<?php
/*
Plugin Name: IM Meme Player
Description: A multimedia meme player plugin for WordPress.
Version: 0.0.7.1
Author: mytechtoday@protonmail.com
GitHub: @mytech-today-now/IM-meme-player/
*/

namespace MyTechToday\IMMemePlayer;

// Prevent direct file access
defined('ABSPATH') || exit;

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
ConsoleLogger::log('Default allowed origins set.');

// Including other PHP files
include_once plugin_dir_path(__FILE__) . 'im-meme-player-admin-menu.php';
ConsoleLogger::log('im-meme-player-admin-menu.php file included.');
include_once plugin_dir_path(__FILE__) . 'im-meme-player-admin-page.php';
ConsoleLogger::log('im-meme-player-admin-page.php file included.');
include_once plugin_dir_path(__FILE__) . 'im-meme-player-config.php';
ConsoleLogger::log('im-meme-player-config.php file included.');
include_once plugin_dir_path(__FILE__) . 'filelist.php';
ConsoleLogger::log('filelist.php file included.');
include_once plugin_dir_path(__FILE__) . 'im-meme-player-functions.php';
ConsoleLogger::log('im-meme-player-functions.php file included.');
include_once plugin_dir_path(__FILE__) . 'im-meme-player-playlist-cpt.php';
ConsoleLogger::log('im-meme-player-playlist-cpt.php file included.');
include_once plugin_dir_path(__FILE__) . 'im-meme-player-playlist-display.php';
ConsoleLogger::log('im-meme-player-playlist-display.php file included.');
include_once plugin_dir_path(__FILE__) . 'im-meme-player-playlist-helpers.php';
ConsoleLogger::log('im-meme-player-playlist-helpers.php file included.');
include_once plugin_dir_path(__FILE__) . 'im-meme-player-shortcode.php';
ConsoleLogger::log('im-meme-player-shortcode.php file included.');
include_once plugin_dir_path(__FILE__) . 'im-meme-player-uninstall.php';
ConsoleLogger::log('im-meme-player-uninstall.php file included.');

// Handle CORS via function
ConsoleLogger::log('Handling CORS...');
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
            // Log the error and throw an exception
            ConsoleLogger::error('Playlist not found.');
            throw new Exception('Playlist not found.');
        }
        // Return the playlist object
        ConsoleLogger::log('Playlist retrieved successfully.');
        return $result;
    } catch (Exception $e) {
        // Log the error and return null
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
    // Sanitize and echo the string
    ConsoleLogger::log('Sanitizing and echoing string.');
    echo esc_html($string);
}

// CSRF Token Generation
function generate_csrf_token() {
    // Generate a CSRF token
    ConsoleLogger::log('Generating CSRF token.');
    return wp_create_nonce('meme_player_action');
}

// CSRF Token Verification
function verify_csrf_token($token) {
    // Verify the CSRF token
    ConsoleLogger::log('Verifying CSRF token.');
    return wp_verify_nonce($token, 'meme_player_action');
}

// Secure Database Queries (SQL Injection)
// Secure File Upload (File Upload Vulnerabilities)
function handle_file_upload($file) {
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    $file_return = wp_handle_upload($file, ['test_form' => false]);
    if (isset($file_return['error']) || isset($file_return['upload_error_handler'])) {
        ConsoleLogger::error('Error in file upload: ' . $file_return['error']);
        return false; // Handle the error
    }
    ConsoleLogger::log('File uploaded successfully.');
    return $file_return['file'];
}

// Secure File Path (Directory Traversal)
function get_secure_path($file) {
    // Get the basename of the file
    ConsoleLogger::log('Getting the basename of the file.');
    return basename($file);
}

// Session Security (Session Hijacking and Fixation)
add_action('wp_login', function() {
    // Regenerate the session ID on login
    ConsoleLogger::log('Regenerating session ID on login.');
    session_regenerate_id(true);
});

// Access Control (IDOR)
function can_edit_playlist($playlist_id, $user_id) {
    // Implement logic to check if $user_id can edit $playlist_id
    ConsoleLogger::log('Checking if user can edit playlist.');
    return true; // Placeholder, replace with actual logic
}

// Secure XML Processing (XXE)
function process_xml($xml_string) {
    // In PHP 8.0 and later, external entity loading is disabled by default
    // Therefore, there's no need to call libxml_disable_entity_loader()

    // Error handling for XML loading
    libxml_use_internal_errors(true);

    ConsoleLogger::log('Parsing XML...');

    $xml = simplexml_load_string($xml_string);
    if ($xml === false) {
        // Handle errors in XML parsing
        foreach (libxml_get_errors() as $error) {
            ConsoleLogger::error('XML Error: ' . $error->message);
            // Handle or log the error
        }
        libxml_clear_errors();
        ConsoleLogger::error('Failed to parse XML.');
        return null; // or handle the error as appropriate
    }
    ConsoleLogger::log('XML parsed successfully.');
    return $xml;
}

/*
Start of Actual Code
*/

/**
 * Enqueues scripts and styles for the plugin.
 */
function meme_player_enqueue_scripts() {
    // Enqueue the plugin's styles
    wp_enqueue_style('playlist-style', plugin_dir_url(__FILE__) . 'style.css');
    ConsoleLogger::log('Plugin styles enqueued.');

    // Enqueue the plugin's scripts
    wp_enqueue_script('playlist-script', plugin_dir_url(__FILE__) . 'script.js', array('jquery'), null, true);
    ConsoleLogger::log('Plugin scripts enqueued.');
}

/**
 * Enqueues the media uploader scripts.
 */
function mytech_enqueue_media_uploader() {
    // Enqueue the media uploader scripts
    wp_enqueue_media();
    ConsoleLogger::log('Media uploader scripts enqueued.');
}

// Add the settings page to the MemePlayer menu
function meme_player_admin_menu() {
    // Add the main menu page
    add_menu_page('MemePlayer', 'MemePlayer', 'manage_options', 'meme-player-settings', 'meme_player_settings_page', 'dashicons-admin-generic');
    ConsoleLogger::log('MemePlayer admin menu added.');

    // Add the submenu page
    add_submenu_page('meme-player-settings', 'General Settings', 'General Settings', 'manage_options', 'meme-player-general-settings', 'meme_player_general_settings_page');
    ConsoleLogger::log('MemePlayer general settings submenu added.');
}

function meme_player_general_settings_page() {
    // Check if the user has submitted the settings
    if (isset($_POST['meme_player_settings_nonce']) && wp_verify_nonce($_POST['meme_player_settings_nonce'], 'meme_player_settings_action')) {
        // Validate and sanitize the setting
        $image_display_time = isset($_POST['image_display_time']) ? intval($_POST['image_display_time']) : 0;
        if ($image_display_time > 0) {
            // Update the setting
            ConsoleLogger::log('Image display time updated to: ' . $image_display_time);
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

// Enqueue scripts and styles
add_action('wp_enqueue_scripts', 'meme_player_enqueue_scripts');
ConsoleLogger::log('MemePlayer scripts and styles enqueued.');

// Register the playlist custom post type
add_action('init', 'register_playlist_post_type');
ConsoleLogger::log('Playlist custom post type registered.');

// Enqueue media uploader scripts
add_action('admin_enqueue_scripts', 'mytech_enqueue_media_uploader');
ConsoleLogger::log('Media uploader scripts enqueued.');

// Add Folder2Post menu
function folder2post_menu() {
    add_menu_page('Folder2Post Configuration', 'Folder2Post', 'manage_options', 'folder2post', 'folder2post_page', 'dashicons-admin-generic');
}

// Add Folder2Post menu
add_action('admin_menu', 'folder2post_menu');
ConsoleLogger::log('Folder2Post menu added.');

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
            ConsoleLogger::error('The specified folder does not exist.');
            wp_die('The specified folder does not exist.');
        }
    }

    public function readFolder() {
        $files = scandir($this->folderPath);
        if ($files === false) {
            ConsoleLogger::error('Failed to read the folder contents.');
            wp_die('Failed to read the folder contents.');
        }
        return $files;
    }
}


/**
 * Handles the form submission for Folder2Post.
 */
function handle_folder2post_form_submission() {

    // Check if the nonce is set and valid
    if (!isset($_POST['folder2post_nonce_field']) || !wp_verify_nonce($_POST['folder2post_nonce_field'], 'folder2post_nonce')) {
        ConsoleLogger::error('Security check failed');
        wp_die('Security check failed');
    }

    // Check if the user has the required permissions
    if (current_user_can('manage_options')) {
        
        // Validate the folder path & read the folder contents
        if (isset($_POST['folder_path'])) {
            try {
                $folderReader = new FolderReader($_POST['folder_path']);
                $files = $folderReader->readFolder();
                // Process the $files as needed
            } catch (Exception $e) {
                ConsoleLogger::error('Error reading the folder: ' . $e->getMessage());
                // Handle exceptions if necessary
            }
        }

        // Convert the file list to JSON
        $json_data = json_encode($files);
        if ($json_data === false) {
            ConsoleLogger::error('Failed to encode the folder contents to JSON.');
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
            ConsoleLogger::error('There was an error creating the playlist post.');
            wp_die('There was an error creating the playlist post.');
        }
    
        // Redirect back to the settings page with a success message
        $redirect_url = add_query_arg(['page' => 'folder2post', 'status' => 'success'], admin_url('admin.php'));
        ConsoleLogger::log('Redirecting to: ' . $redirect_url);
        wp_safe_redirect($redirect_url);
        exit;
    } else {
        ConsoleLogger::error('You do not have sufficient permissions to access this page.');
        wp_die('You do not have sufficient permissions to access this page.');
    }
}

/**
 * Enqueues scripts required for the media uploader within the WordPress admin area.
 *
 * This function is responsible for loading the necessary JavaScript files
 * that facilitate the use of the WordPress media uploader. It also localizes
 * a script to pass PHP data into JavaScript, such as the AJAX URL, which is
 * essential for handling AJAX requests within the admin area.
 */
function im_meme_enqueue_media_uploader_scripts() {
    // Load the WordPress media uploader interface.
    // This function ensures that the media uploader functionality is available
    // on the pages where our script runs, allowing users to select or upload media.
    wp_enqueue_media(); 
    ConsoleLogger::log('WordPress media uploader loaded.');

    // Enqueue a custom JavaScript file that handles the media uploader interactions.
    // This script is responsible for opening the media library window, selecting media,
    // and then performing actions with the selected media items.
    wp_enqueue_script(
        'im-meme-media-uploader', // Handle for the script.
        plugin_dir_url(__FILE__) . 'js/media-uploader.js', // Path to the script.
        array('jquery'), // Dependencies, ensuring jQuery is loaded first.
        null, // Version number, null to prevent caching during development.
        true // Place script in the footer to ensure DOM is fully loaded before execution.
    );
    ConsoleLogger::log('Custom media uploader script enqueued.');

    // Localize the script to pass PHP values to JavaScript.
    // This includes providing a URL for AJAX requests, allowing the JavaScript code
    // to dynamically interact with the server without page reloads.
    wp_localize_script(
        'im-meme-media-uploader', // Handle for the script to localize.
        'imMemeUploaderData', // Object name in JavaScript to hold the localized data.
        array(
            'ajax_url' => admin_url('admin-ajax.php'), // URL to WordPress AJAX handling endpoint.
        )
    );
    ConsoleLogger::log('Script localized with AJAX URL for admin-ajax.php.');
}

// Hook the above function into WordPress to run it at the appropriate time.
// 'admin_enqueue_scripts' action ensures our script is only loaded in the admin area,
// preventing unnecessary loading on the front end of the site.
add_action('admin_enqueue_scripts', 'im_meme_enqueue_media_uploader_scripts');
ConsoleLogger::log('Enqueue media uploader scripts function hooked to admin_enqueue_scripts.');

// Add the AJAX action for the media uploader
add_action('admin_menu', 'meme_player_admin_menu');
ConsoleLogger::log('MemePlayer admin menu added.');

// Add the AJAX action for the media uploader
add_action('admin_post_folder2post_submit', 'handle_folder2post_form_submission');
ConsoleLogger::log('Folder2Post form submission handler hooked to admin_post_folder2post_submit.');

// Add the AJAX action for the media uploader
add_shortcode('meme_player', 'meme_player_shortcode');
ConsoleLogger::log('MemePlayer shortcode added.');