<?php
// Version: 0.0.6

/**
 * IM Meme Player - File List
 * 
 * This script lists files from a specific directory (memes) and returns them in JSON format.
 * It includes error handling to manage directory access and file reading issues.
 */

// Prevent direct file access
if (!defined('ABSPATH')) {
    exit;
}

// Set CORS headers for cross-origin requests
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header('Content-Type: application/json');

// Exit early if it's an OPTIONS request (pre-flight)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit;
}

// Directory URL
$dirPath = plugin_dir_path(__FILE__) . 'memes/';

// Check if the directory exists
if (!is_dir($dirPath)) {
    ConsoleLogger::error("Directory not found: " . $dirPath);
    echo json_encode(["error" => "Directory not found."]);
    exit;
}

// Try to get the file names from the directory
try {
    $files = scandir($dirPath);
    if ($files === false) {
        throw new Exception("Failed to read directory contents.");
    }

    // Filter out '.' and '..'
    $files = array_diff($files, array('..', '.'));

    // Return the JSON response
    echo json_encode($files);
} catch (Exception $e) {
    ConsoleLogger::error("Error in filelist.php: " . $e->getMessage());
    echo json_encode(["error" => $e->getMessage()]);
    exit;
}

?>