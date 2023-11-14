<?php

// Prevent direct file access
if (!defined('ABSPATH')) {
    exit;
}

// Allowed origins
$allowed_origins = ["https://insidiousmeme.com", "https://www.insidiousmeme.com"];

// Check the Origin header of the incoming request
$request_origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($request_origin, $allowed_origins, true)) {
    header("Access-Control-Allow-Origin: $request_origin");
} else {
    // If the origin is not allowed, exit early
    exit('CORS policy violation.');
}

header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header('Content-Type: application/json');

// Exit early if it's an OPTIONS request (pre-flight)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit;
}

try {
    // Configuration values
    $config = [
        "MEDIA_DIRECTORY" => plugin_dir_path(__FILE__) . 'presenta/memes/'
    ];

    // Return the JSON response
    echo json_encode($config);
} catch (Exception $e) {
    error_log("Error in config.php: " . $e->getMessage() . " on line " . $e->getLine());
    // Structured error response
    echo json_encode([
        "status" => "error",
        "message" => "An error occurred while processing the request."
    ]);
}

function meme_player_shortcode() {
    ob_start(); // Start output buffering
    ?>
    <!-- Your HTML content goes here -->
    <div class="container">
        <h1>IM Meme Player</h1>
        <div id="mediaBoxElement">
            <div id="prevButton">&#8678;</div>
            <div id="mediaBox"></div>
            <div id="nextButton">&#8680;</div>
        </div>
    </div>
    <?php
    return ob_get_clean(); // Return the buffered content
}

add_shortcode('meme_player', 'meme_player_shortcode');


?>
