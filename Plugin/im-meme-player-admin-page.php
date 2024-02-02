<?php
// Version: 0.0.7.1

// Ensure this file is being included within the WordPress framework
if (!defined('ABSPATH')) {
    ConsoleLogger::error('im-meme-player-admin-page.php - ABSPATH constant not defined');
    exit;
}

// Include the ConsoleLogger class
use MyTechToday\IMMemePlayer\ConsoleLogger;


// Assume this is part of admin-page.php or the respective admin interface file for your plugin

// Include WordPress function for security checks
if (!function_exists('add_action')) {
    ConsoleLogger::log('add_action function does not exist');
    echo "You cannot access this file directly";
    exit;
}

// Handle form submission
function Meme_handle_form_submission() {
    // Check user capabilities and nonce for security
    if (current_user_can('manage_options') && check_admin_referer('Meme_media_action', 'Meme_media_nonce')) {

        // Check if the form is submitted and the media field is set
        if (isset($_POST['Meme_media_url'])) {
            // Retrieve the media URL from the POST data and sanitize it
            $media_url = sanitize_text_field($_POST['Meme_media_url']);

            // Validate the URL (if using URL)
            if (!empty($media_url) && filter_var($media_url, FILTER_VALIDATE_URL)) {
                // Save the valid URL to the plugin's settings or post meta
                update_option('Meme_selected_media', $media_url);

                // Provide admin success notice
                add_action('admin_notices', 'Meme_admin_success_notice');
            } else {
                // Handle invalid URL error
                add_action('admin_notices', 'Meme_admin_error_notice');
            }
        }
    }
}

// Add the admin_post action hook
ConsoleLogger::log('admin_post_Meme_save_media hook for Meme_handle_form_submission initiated');
add_action('admin_post_Meme_save_media', 'Meme_handle_form_submission'); // Hook to handle the form submission
ConsoleLogger::log('admin_post_Meme_save_media hook for Meme_handle_form_submission added');

// Success notice function
function Meme_admin_success_notice() {
    ?>
    <div class="notice notice-success is-dismissible">
        <p><?php _e('Media updated successfully!', 'Meme-domain'); ?></p>
    </div>
    <?php
}

// Error notice function
function Meme_admin_error_notice() {
    ?>
    <div class="notice notice-error is-dismissible">
        <p><?php _e('Failed to update media. Please ensure the URL is correct.', 'Meme-domain'); ?></p>
    </div>
    <?php
}

// Render the admin page form
function Meme_media_admin_page() {
    ?>
    <div class="wrap">
        <h1><?php _e('Meme Media Settings', 'Meme-domain'); ?></h1>
        <form action="<?php echo admin_url('admin-post.php'); ?>" method="post">
            <!-- Security field -->
            <?php wp_nonce_field('Meme_media_action', 'Meme_media_nonce'); ?>
            <!-- Hidden input for handling form submission -->
            <input type="hidden" name="action" value="Meme_save_media">

            <table class="form-table">
                <tr>
                    <th scope="row"><label for="Meme_media_url"><?php _e('Media URL', 'Meme-domain'); ?></label></th>
                    <td>
                        <input type="text" name="Meme_media_url" id="Meme_media_url" class="regular-text">
                        <button id="Meme-open-media-library" class="button"><?php _e('Add Media', 'Meme-domain'); ?></button>
                    </td>
                </tr>
            </table>

            <?php submit_button(__('Save Changes', 'Meme-domain')); ?>
        </form>
    </div>
    <?php
}

function meme_player_admin_settings_page() {
    // Check user permissions
    if (!current_user_can('manage_options')) {
        ConsoleLogger::log('You do not have sufficient permissions to access this page.');
        wp_die(__('You do not have sufficient permissions to access this page.', 'meme-domain'));
    }

    // Check if the form has been submitted
    if (isset($_POST['meme_player_uninstall_option'])) {
        // Update the uninstall option
        $uninstall_option = intval($_POST['meme_player_uninstall_option']);
        ConsoleLogger::log('Uninstall option: ' . $uninstall_option);
        update_option('meme_player_uninstall_option', $uninstall_option);
    }

    // Retrieve the current setting value
    $current_option = get_option('meme_player_uninstall_option', 1); // Default to option 1
    ?>
    <div class="wrap">
        <h1>Meme Player Uninstall Options</h1>
        <form method="post" action="">
            <label for="uninstall_option">Select Uninstall Option:</label>
            <select name="meme_player_uninstall_option" id="uninstall_option">
                <option value="1" <?php echo $current_option == 1 ? 'selected' : ''; ?>>Delete All Data</option>
                <option value="2" <?php echo $current_option == 2 ? 'selected' : ''; ?>>Keep Data</option>
                <option value="3" <?php echo $current_option == 3 ? 'selected' : ''; ?>>Reset Changes</option>
            </select>
            <?php submit_button('Save Changes'); ?>
        </form>
    </div>
    <?php
}

// Enqueue necessary scripts and styles
function Meme_enqueue_media_uploader() {
    wp_enqueue_media();
    wp_enqueue_script('Meme-media-script', plugin_dir_url(__FILE__) . 'admin-script.js', array('jquery'), false, true);
}
ConsoleLogger::log('admin_enqueue_scripts hook for Meme_enqueue_media_uploader initiated');
add_action('admin_enqueue_scripts', 'Meme_enqueue_media_uploader');
ConsoleLogger::log('admin_enqueue_scripts hook for Meme_enqueue_media_uploader added');

// Render the admin page
function Meme_add_admin_menu() {
    add_menu_page(__('Meme Media', 'Meme-domain'), __('Meme Media', 'Meme-domain'), 'manage_options', 'Meme-media', 'Meme_media_admin_page');
}
ConsoleLogger::log('admin_menu hook for Meme_add_admin_menu initiated');
add_action('admin_menu', 'Meme_add_admin_menu');
ConsoleLogger::log('admin_menu hook for Meme_add_admin_menu added');

// Add a submenu item to the Settings menu
function meme_player_add_settings_submenu() {
    add_submenu_page('options-general.php', 'Meme Player Settings', 'Meme Player', 'manage_options', 'meme-player-settings', 'meme_player_admin_settings_page');
}
ConsoleLogger::log('admin_menu hook for meme_player_add_settings_submenu initiated');
add_action('admin_menu', 'meme_player_add_settings_submenu');
ConsoleLogger::log('admin_menu hook for meme_player_add_settings_submenu added');

// Enqueue scripts and styles
function mytech_enqueue_playlist_editor_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_media(); // Ensures the WordPress media uploader is loaded
    wp_enqueue_script('mytech-playlist-editor', plugin_dir_url(__FILE__) . 'js/playlist-editor.js', ['jquery'], null, true);

    // Localize script to pass AJAX URL and nonce to JavaScript
    wp_localize_script('mytech-playlist-editor', 'mytechAjax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('mytech_playlist_nonce'),
    ]);
}

// Add the admin_enqueue_scripts hook to the function
ConsoleLogger::log('admin_enqueue_scripts hook for mytech_enqueue_playlist_editor_scripts initiated');
add_action('admin_enqueue_scripts', 'mytech_enqueue_playlist_editor_scripts');
ConsoleLogger::log('admin_enqueue_scripts hook for mytech_enqueue_playlist_editor_scripts added');

// AJAX handler for adding the uploaded file to the playlist and media library
function mytech_handle_playlist_item_upload() {
    check_ajax_referer('mytech_playlist_nonce', 'nonce');

    // Ensure the user has the capability to upload files
    if (!current_user_can('upload_files')) {
        ConsoleLogger::log('Insufficient permissions to upload files');
        wp_send_json_error(['message' => 'Insufficient permissions']);
    }

    $file_id = media_handle_upload('file', 0); // 0 means no parent post
    if (is_wp_error($file_id)) {
        ConsoleLogger::log('Error uploading file: ' . $file_id->get_error_message());
        wp_send_json_error(['message' => $file_id->get_error_message()]);
    }
    ConsoleLogger::log('File uploaded successfully with ID: ' . $file_id);
    wp_send_json_success(['message' => 'File uploaded successfully', 'id' => $file_id]);
}

// Add the wp_ajax_mytech_upload_playlist_item action to the function
ConsoleLogger::log('wp_ajax_mytech_upload_playlist_item hook for mytech_handle_playlist_item_upload initiated');
add_action('wp_ajax_mytech_upload_playlist_item', 'mytech_handle_playlist_item_upload');
ConsoleLogger::log('wp_ajax_mytech_upload_playlist_item hook for mytech_handle_playlist_item_upload added');
