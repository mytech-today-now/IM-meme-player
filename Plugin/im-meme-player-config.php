<?php
// Version: 0.0.7.1

// Ensure this file is being included within the WordPress framework
if (!defined('ABSPATH')) {
    ConsoleLogger::error('im-meme-player-admin-page.php - ABSPATH constant not defined');
    exit;
}

function add_cors_http_header(){
    header("Access-Control-Allow-Origin: *");
    ConsoleLogger::log('Access-Control-Allow-Origin header definiton');
}

// Include the ConsoleLogger class
ConsoleLogger::log('im-meme-player-config.php included');
add_action('init','add_cors_http_header');
ConsoleLogger::log('add_cors_http_header hook added');

function handle_cors() {
    // Fetch user-defined allowed origins or default to the server\'s root domain
    $allowed_origins = get_option('meme_player_allowed_origins', [get_site_url()]);

    // Check the Origin header of the incoming request
    $request_origin = $_SERVER['HTTP_ORIGIN'] ?? '';

    // Only proceed with CORS handling if an Origin header is present
    if (!empty($request_origin)) {
        if (in_array($request_origin, $allowed_origins, true)) {
            header("Access-Control-Allow-Origin: $request_origin");
            header("Access-Control-Allow-Methods: GET");
            header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
        } else {
            // If the origin is not allowed, log the violation and exit early
            ConsoleLogger::error("CORS policy violation: Origin $request_origin is not allowed.");
            error_log("CORS policy violation: Origin $request_origin is not allowed."); // Use error_log
            exit('CORS policy violation.');
        }
    } else {
        // If no Origin header is present, you might want to log this occurrence or handle it appropriately
        ConsoleLogger::error("No HTTP_ORIGIN header present in the request.");
        error_log("No HTTP_ORIGIN header present in the request."); // Use error_log
    }

    // Exit early if it's an OPTIONS request (pre-flight)
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        // Log the pre-flight request and exit
        ConsoleLogger::log('CORS pre-flight request detected');
    exit;
}

try {
    // Configuration values
    $config = [
        "MEDIA_DIRECTORY" => plugin_dir_path(__FILE__) . 'memes/'
    ];

    // Return the JSON response
    echo json_encode($config);
} catch (Exception $e) {
    ConsoleLogger::error("Error in config.php: " . $e->getMessage() . " on line " . $e->getLine());
    // Structured error response
    echo json_encode([
        "status" => "error",
        "message" => "An error occurred while processing the request."
    ]);
};
}
