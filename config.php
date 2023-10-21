<?php

// CORS headers to allow any origin to access
header("Access-Control-Allow-Origin: *");
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
        "MEDIA_DIRECTORY" => "./presenta/memes/"
    ];

    // Return the JSON response
    echo json_encode($config);
} catch (Exception $e) {
    error_log("Error in config.php: " . $e->getMessage() . " on line " . $e->getLine());
    echo json_encode([
        "error" => "<div style='white-space: pre-line; word-wrap: break-word; overflow-wrap: break-word;'>An error occurred while processing the request.</div>"
    ]);
}

?>
