<?php
// playlist-cpt.php

/**
 * Registers a custom post type 'playlist'.
 * This function sets up the labels, arguments, and capabilities for the playlist post type.
 * It is hooked into WordPress 'init' action.
 */
function register_playlist_cpt() {
    // Define labels for the Playlist post type
    $labels = array(
        'name'               => 'Playlists',
        'singular_name'      => 'Playlist',
        'menu_name'          => 'Playlists',
        'name_admin_bar'     => 'Playlist',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Playlist',
        'new_item'           => 'New Playlist',
        'edit_item'          => 'Edit Playlist',
        'view_item'          => 'View Playlist',
        'all_items'          => 'All Playlists',
        'search_items'       => 'Search Playlists',
        'parent_item_colon'  => 'Parent Playlists:',
        'not_found'          => 'No playlists found.',
        'not_found_in_trash' => 'No playlists found in Trash.'
    );

    // Define arguments for the Playlist post type
    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'playlist'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title', 'editor', 'custom-fields', 'thumbnail'),
        'taxonomies'         => array('category', 'post_tag'),
        'capabilities'       => array(
            'edit_post'          => 'edit_playlist',
            'read_post'          => 'read_playlist',
            'delete_post'        => 'delete_playlist',
            'edit_posts'         => 'edit_playlists',
            'edit_others_posts'  => 'edit_others_playlists',
            'publish_posts'      => 'publish_playlists',
            'read_private_posts' => 'read_private_playlists'
        ),
        'map_meta_cap'       => true
    );

    // Register the post type
    if (!register_post_type('playlist', $args)) {
        // Error handling if the post type registration fails
        ConsoleLogger::error('Failed to register custom post type "playlist"');
    }
}

// Hook the function into the 'init' action
add_action('init', 'register_playlist_cpt');
