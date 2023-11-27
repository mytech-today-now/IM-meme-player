<?php
// playlist-cpt.php

function register_playlist_cpt() {
    $args = array(
        'public' => true,
        'label'  => 'Playlists',
        'supports' => array('title', 'editor', 'custom-fields'),
    );
    register_post_type('playlist', $args);
}

add_action('init', 'register_playlist_cpt');
