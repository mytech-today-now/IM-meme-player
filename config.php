<?php

// Centralized CORS headers
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
        "MEDIA_DIRECTORY" => "./"
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

?>
