<?php

// CORS headers to allow any origin to access (for simplicity, but in a real-world scenario, you'd want to be more restrictive)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header('Content-Type: application/json');

// Exit early if it's an OPTIONS request (pre-flight)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit;
}

// Configuration values
$config = [
    "MEDIA_DIRECTORY" => "https://insidiousmeme.com/presenta/memes/"
];

// Return the JSON response
echo json_encode($config);

?>
