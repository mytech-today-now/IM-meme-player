<?php

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

// Configuration
// NOTE: Update this path to the correct file system path on your server
$config = [
    "dirPath" => '/path/to/your/memes/directory/on/server/',
];

try {
    // Check if the directory exists and is readable
    if (!is_dir($config["dirPath"]) || !is_readable($config["dirPath"])) {
        throw new Exception("The specified directory does not exist or is not readable.");
    }

    // Use glob pattern to get only the required files (assuming only images and videos are required)
    $files = glob($config["dirPath"] . '*.{jpg,jpeg,jfif,png,gif,mp4,webm,ogg}', GLOB_BRACE);

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
