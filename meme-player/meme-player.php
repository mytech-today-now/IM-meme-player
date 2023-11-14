<?php
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

add_action('wp_enqueue_scripts', 'meme_player_enqueue_scripts');

// Include other PHP files
include_once plugin_dir_path(__FILE__) . 'config.php';
include_once plugin_dir_path(__FILE__) . 'filelist.php';

// Shortcode to display meme player
function meme_player_shortcode() {
    ob_start();
    include(plugin_dir_path(__FILE__) . 'index.html');
    return ob_get_clean();
}

add_shortcode('meme_player', 'meme_player_shortcode');
