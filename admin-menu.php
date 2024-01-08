<?php
// Version: 0.0.3

// Ensure this file is being included within the WordPress framework
if (!defined('ABSPATH')) {
    exit;
}

// Function to add a submenu for the playlist manager in the admin dashboard under Tools
function meme_add_playlist_manager_menu() {
    add_submenu_page(
        'tools.php', // Parent slug for Tools
        __('Playlist Manager', 'meme-domain'),   // Page title
        __('Playlists', 'meme-domain'),         // Menu title
        'manage_options',                       // Capability required to see this option
        'meme-playlist-manager',                // Unique menu slug
        'meme_playlist_manager_page_content'    // Function to output the content for this page
    );
}

// Output the content for the playlist manager page
function meme_playlist_manager_page_content() {
    // Verify user permissions
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'meme-domain'));
    }

    // Determine the page number
    $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;

    // Define query arguments
    $args = array(
        'post_type' => 'playlist',  // Assuming 'playlist' is the custom post type
        'posts_per_page' => get_option('posts_per_page'), // Adhering to WordPress' "posts per page" setting
        'paged' => $paged           // Current page number
    );

    // Fetch playlists with WP_Query
    $playlists = new WP_Query($args);

    echo '<div class="wrap">';
    echo '<h1>' . esc_html(get_admin_page_title()) . '</h1>';

    // Loop through the playlists and display them
    if ($playlists->have_posts()) {
        echo '<ul class="playlists">'; // Start of the playlist list

        while ($playlists->have_posts()) {
            $playlists->the_post();
            // Display each playlist
            echo '<li>' . esc_html(get_the_title()) . '</li>'; // Simplified display
        }

        echo '</ul>'; // End of the playlist list

        // Inline Pagination
        $page_links = paginate_links(array(
            'base' => add_query_arg('paged', '%#%'),
            'format' => '?paged=%#%',
            'prev_text' => __('&laquo; Previous', 'meme-domain'),
            'next_text' => __('Next &raquo;', 'meme-domain'),
            'total' => $playlists->max_num_pages,
            'current' => $paged
        ));

        if ($page_links) {
            echo '<div class="tablenav"><div class="tablenav-pages">' . $page_links . '</div></div>';
        }

    } else {
        echo '<p>' . __('No playlists found.', 'meme-domain') . '</p>';
    }

    // Reset post data to avoid conflicts
    wp_reset_postdata();

    echo '</div>'; // Close .wrap
}

// Hook into admin_menu to add the menu page
add_action('admin_menu', 'meme_add_playlist_manager_menu');
