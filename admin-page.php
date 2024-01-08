<?php
// Version: 0.0.3

// Assume this is part of admin-page.php or the respective admin interface file for your plugin

// Include WordPress function for security checks
if (!function_exists('add_action')) {
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
add_action('admin_post_Meme_save_media', 'Meme_handle_form_submission'); // Hook to handle the form submission

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

// Enqueue necessary scripts and styles
function Meme_enqueue_media_uploader() {
    wp_enqueue_media();
    wp_enqueue_script('Meme-media-script', plugin_dir_url(__FILE__) . 'admin-script.js', array('jquery'), false, true);
}
add_action('admin_enqueue_scripts', 'Meme_enqueue_media_uploader');

// Render the admin page
function Meme_add_admin_menu() {
    add_menu_page(__('Meme Media', 'Meme-domain'), __('Meme Media', 'Meme-domain'), 'manage_options', 'Meme-media', 'Meme_media_admin_page');
}
add_action('admin_menu', 'Meme_add_admin_menu');

