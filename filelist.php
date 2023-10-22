<?php

// CORS headers
header("Access-Control-Allow-Origin: https://insidiousmeme.com");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header('Content-Type: application/json');

error_log("filelist.php: Executing CORS headers (Line 5-8)");

// Exit early if it's an OPTIONS request (pre-flight)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    error_log("filelist.php: Detected OPTIONS request method (Line 11)");
    exit;
}

try {
    error_log("filelist.php: Entering try block (Line 15)");

    // Configuration
    $config = [
        "dirPath" => 'https://insidiousmeme.com/presenta/memes/',
    ];
    error_log("filelist.php: Configured directory path (Line 20)");

    // Check if the directory exists
    if (!is_dir($config["dirPath"])) {
        error_log("filelist.php: Directory does not exist (Line 24)");
        throw new Exception("The specified directory does not exist.");
    }

    // Try to get the file names from the directory
    $files = scandir($config["dirPath"]);
    error_log("filelist.php: Fetched file names from directory (Line 30)");

    // Filter out '.' and '..' and any other unwanted files or directories
    $filteredFiles = array_filter($files, function($file) {
        error_log("filelist.php: Filtering files (Line 34)");
        return $file !== '..' && $file !== '.' && !is_dir($file);
    });

    // Return the JSON response
    echo json_encode(["data" => $filteredFiles]);
    error_log("filelist.php: Returned JSON response (Line 40)");
} catch (Exception $e) {
    error_log("filelist.php: Error encountered - " . $e->getMessage() . " on line " . $e->getLine());
    echo json_encode([
        "error" => "<div style='white-space: pre-line; word-wrap: break-word; overflow-wrap: break-word;'>An error occurred while processing the request.</div>"
    ]);
}

?>
