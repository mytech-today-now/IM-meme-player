<?php
// shortcode.php

// Prevent direct file access
if (!defined('ABSPATH')) {
    exit;
}

// Shortcode to display meme player
function meme_player_shortcode() {
    ob_start();
    include(plugin_dir_path(__FILE__) . 'index.html');
    return ob_get_clean();
}

add_shortcode('meme_player', 'meme_player_shortcode');
?>
