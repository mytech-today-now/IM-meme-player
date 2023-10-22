<?php

// CORS headers
header("Access-Control-Allow-Origin: https://insidiousmeme.com");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header('Content-Type: application/json');

// Configuration
$config = [
    "dirPath" => 'https://insidiousmeme.com/presenta/memes/',
];

try {
    // Check if the directory exists and is readable
    if (!is_dir($config["dirPath"]) || !is_readable($config["dirPath"])) {
        throw new Exception("The specified directory does not exist or is not readable.");
    }

    // Use glob pattern to get only the required files (assuming only images and videos are required)
    $files = glob($config["dirPath"] . '*.{jpg,jpeg,png,gif,mp4,webm,ogg}', GLOB_BRACE);

    // Return the JSON response
    echo json_encode(["data" => $files]);
} catch (Exception $e) {
    // Structured error response without HTML
    echo json_encode([
        "status" => "error",
        "message" => "An error occurred while processing the request: " . $e->getMessage()
    ]);
}

?>
