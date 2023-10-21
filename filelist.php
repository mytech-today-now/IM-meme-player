<?php

// CORS headers
header("Access-Control-Allow-Origin: https://your-allowed-origin.com"); // Replace with your actual allowed origin
header("Access-Control-Allow-Methods: GET"); // Only allowing GET method
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header('Content-Type: application/json');

// Exit early if it's an OPTIONS request (pre-flight)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit;
}

// Configuration (this can be moved to a separate configuration file in the future)
$config = [
    "dirPath" => "./presenta/memes/"
];

// Check if the directory exists
if (!is_dir($config["dirPath"])) {
    echo json_encode(["error" => "The specified directory does not exist."]);
    exit;
}

// Try to get the file names from the directory
$files = @scandir($config["dirPath"]);

// Check if the scandir function succeeded
if ($files === false) {
    echo json_encode(["error" => "Unable to read the contents of the directory."]);
    exit;
}

// Filter out '.' and '..' and any other unwanted files or directories
$filteredFiles = array_filter($files, function($file) {
    return $file !== '..' && $file !== '.' && !is_dir($file);
});

// Return the JSON response
echo json_encode(["data" => $filteredFiles]);

?>
