<?php
// playlist-helpers.php

// Assuming a database connection is already established

/**
 * Retrieves playlist items from the database.
 *
 * @param int $playlist_id The ID of the playlist.
 * @return array The playlist items.
 */
function get_playlist_items($playlist_id) {
    $items = [];
    $query = "SELECT * FROM playlist_items WHERE playlist_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $playlist_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }

    $stmt->close();
    return $items;
}

/**
 * Displays playlist items.
 *
 * @param array $items The playlist items.
 */
function display_playlist_items($items) {
    echo "<ul>";
    foreach ($items as $item) {
        echo "<li>" . htmlspecialchars($item['name']) . "</li>"; // Assuming 'name' is a column in your playlist_items table
    }
    echo "</ul>";
}

/**
 * Adds an item to the playlist.
 *
 * @param int $playlist_id The ID of the playlist.
 * @param string $item_name The name of the item to add.
 */
function add_playlist_item($playlist_id, $item_name) {
    $query = "INSERT INTO playlist_items (playlist_id, name) VALUES (?, ?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("is", $playlist_id, $item_name);
    $stmt->execute();
    $stmt->close();
}

/**
 * Removes an item from the playlist.
 *
 * @param int $playlist_id The ID of the playlist.
 * @param int $item_id The ID of the item to remove.
 */
function remove_playlist_item($playlist_id, $item_id) {
    $query = "DELETE FROM playlist_items WHERE playlist_id = ? AND id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("ii", $playlist_id, $item_id);
    $stmt->execute();
    $stmt->close();
}

// Example usage
$playlist_id = 1; // Example playlist ID
$items = get_playlist_items($playlist_id);
display_playlist_items($items);

