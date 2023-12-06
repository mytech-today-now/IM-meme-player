<?php
// playlist-helpers.php

// Assuming a database connection is already established

/**
 * Retrieves playlist items from the database with optional filtering.
 *
 * @param mysqli $mysqli The database connection object.
 * @param int $playlist_id The ID of the playlist.
 * @param string|null $tag Optional tag for filtering.
 * @param string|null $category Optional category for filtering.
 * @return array The playlist items.
 */
function get_playlist_items($mysqli, $playlist_id, $tag = null, $category = null) {
    $items = [];
    $query = "SELECT * FROM playlist_items WHERE playlist_id = ?";
    $params = [$playlist_id];
    $types = "i";

    if ($tag !== null) {
        $query .= " AND tag = ?";
        $params[] = $tag;
        $types .= "s";
    }

    if ($category !== null) {
        $query .= " AND category = ?";
        $params[] = $category;
        $types .= "s";
    }

    if ($stmt = $mysqli->prepare($query)) {
        $stmt->bind_param($types, ...$params);
        if ($stmt->execute()) {
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
        } else {
            error_log("Error executing query: " . $stmt->error);
        }
        $stmt->close();
    } else {
        error_log("Error preparing statement: " . $mysqli->error);
    }

    return $items;
}

/**
 * Adds an item to the playlist with tagging and categorization.
 *
 * @param mysqli $mysqli The database connection object.
 * @param int $playlist_id The ID of the playlist.
 * @param string $item_name The name of the item to add.
 * @param string|null $tag Optional tag for the item.
 * @param string|null $category Optional category for the item.
 */
function add_playlist_item($mysqli, $playlist_id, $item_name, $tag = null, $category = null) {
    $query = "INSERT INTO playlist_items (playlist_id, name, tag, category) VALUES (?, ?, ?, ?)";
    
    if ($stmt = $mysqli->prepare($query)) {
        $stmt->bind_param("isss", $playlist_id, $item_name, $tag, $category);
        if (!$stmt->execute()) {
            error_log("Error executing query: " . $stmt->error);
        }
        $stmt->close();
    } else {
        error_log("Error preparing statement: " . $mysqli->error);
    }
}

// Example usage
$mysqli = new mysqli("host", "user", "password", "database"); // Replace with actual database connection details
$playlist_id = 1; // Example playlist ID
$items = get_playlist_items($mysqli, $playlist_id, 'funny', 'video');
// Display or process $items as needed
