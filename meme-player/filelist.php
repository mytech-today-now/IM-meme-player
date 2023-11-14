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

// Directory URL
$dirPath = "./presenta/memes/";

// Check if the directory exists
if (!is_dir($dirPath)) {
    echo json_encode(["error" => "Directory not found."]);
    exit;
}

// Try to get the file names from the directory
$files = @scandir($dirPath);

// Check if the scandir function succeeded
if ($files === false) {
    echo json_encode(["error" => "Failed to read directory contents."]);
    exit;
}

// Filter out '.' and '..'
$files = array_diff($files, array('..', '.'));

// Return the JSON response
echo json_encode($files);

?>
