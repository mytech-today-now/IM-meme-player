<?php

// CORS headers
header("Access-Control-Allow-Origin: https://insidiousmeme.com");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header('Content-Type: application/json');

// Exit early if it's an OPTIONS request (pre-flight)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit;
}

try {
    // Configuration
    $config = [
        "dirPath" => "./",
    ];

    // Check if the directory exists
    if (!is_dir($config["dirPath"])) {
        throw new Exception("The specified directory does not exist.");
    }

    // Try to get the file names from the directory
    $files = scandir($config["dirPath"]);

    // Filter out '.' and '..' and any other unwanted files or directories
    $filteredFiles = array_filter($files, function($file) {
        return $file !== '..' && $file !== '.' && !is_dir($file);
    });

    // Return the JSON response
    echo json_encode(["data" => $filteredFiles]);
} catch (Exception $e) {
    error_log("Error in filelist.php: " . $e->getMessage() . " on line " . $e->getLine());
    echo json_encode([
        "error" => "<div style='white-space: pre-line; word-wrap: break-word; overflow-wrap: break-word;'>An error occurred while processing the request.</div>"
    ]);
}

?>
