<?php
// playlist-helpers.php

// =====================
// Playlist CRUD methods
// =====================
// get_playlist_items() retrieves playlist items from the database with optional filtering.
// add_playlist_item() adds an item to the playlist with tagging and categorization.
// delete_playlist_item() deletes a playlist item from the database.
// reorder_playlist_items() updates the item_order column for each item in the playlist.
// =====================
// Example usage
// =====================
// $playlist_id = 1; // Example playlist ID
// $items = get_playlist_items($playlist_id, 'funny', 'video');
// Display or process $items as needed
// =====================
// ConsoleLogger class
// =====================
// Mimics JavaScript's console.log and console.error functionality in PHP.
// =====================
// Example usage
// =====================
// ConsoleLogger::log('This is an informational message.');
// ConsoleLogger::error('This is an error message.');


// Prevent direct file access
if (!defined('ABSPATH')) {
    exit; 
}

global $wpdb; // Use the global WordPress database object

/**
 * Retrieves playlist items from the database with optional filtering.
 *
 * @param int $playlist_id The ID of the playlist.
 * @param string|null $tag Optional tag for filtering.
 * @param string|null $category Optional category for filtering.
 * @return array The playlist items.
 */
function get_playlist_items($playlist_id, $tag = null, $category = null) {
    global $wpdb;
    $items = [];
    $query = "SELECT * FROM {$wpdb->prefix}playlist_items WHERE playlist_id = %d";
    $params = [$playlist_id];

    if ($tag !== null) {
        $query .= " AND tag = %s";
        $params[] = $tag;
    }

    if ($category !== null) {
        $query .= " AND category = %s";
        $params[] = $category;
    }

    $prepared_query = $wpdb->prepare($query, $params);
    $results = $wpdb->get_results($prepared_query, ARRAY_A);

    if ($results === null) {
        // Log error if query fails
        ConsoleLogger::error("Error executing query: " . $wpdb->last_error);
        return [];
    } else {
        ConsoleLogger::error("Success: Retrieved " . count($results) . " rows.");
    }

    return $results;
}

/**
 * Adds an item to the playlist with tagging and categorization.
 *
 * @param int $playlist_id The ID of the playlist.
 * @param string $item_name The name of the item to add.
 * @param string|null $tag Optional tag for the item.
 * @param string|null $category Optional category for the item.
 */
function add_playlist_item($playlist_id, $item_name, $tag = null, $category = null) {
    global $wpdb;
    $query = "INSERT INTO {$wpdb->prefix}playlist_items (playlist_id, name, tag, category) VALUES (%d, %s, %s, %s)";
    
    $prepared_query = $wpdb->prepare($query, $playlist_id, $item_name, $tag, $category);
    $result = $wpdb->query($prepared_query);

    if ($result === false) {
        // Log error if query fails
        ConsoleLogger::error("Error executing query: " . $wpdb->last_error);
    } else {
        ConsoleLogger::error("Success: Inserted $result row.");
    }
}

// delete_playlist_item() deletes a playlist item from the database.
function delete_playlist_item($playlist_id, $item_id) {
    global $wpdb;
    $query = "DELETE FROM {$wpdb->prefix}playlist_items WHERE playlist_id = %d AND id = %d";
    $prepared_query = $wpdb->prepare($query, $playlist_id, $item_id);
    $result = $wpdb->query($prepared_query);

    if ($result === false) {
        ConsoleLogger::error("Error executing query: " . $wpdb->last_error);
    } else {
        ConsoleLogger::error("Success: Deleted $result row.");
    }
}

// reorder_playlist_items() updates the item_order column for each item in the playlist.
function reorder_playlist_items($playlist_id, $ordered_ids) {
    global $wpdb;
    foreach ($ordered_ids as $order => $item_id) {
        $query = "UPDATE {$wpdb->prefix}playlist_items SET item_order = %d WHERE id = %d AND playlist_id = %d";
        $prepared_query = $wpdb->prepare($query, $order, $item_id, $playlist_id);
        $wpdb->query($prepared_query);
    }
}

/**
 * Class ConsoleLogger
 * Mimics JavaScript's console.log and console.error functionality in PHP.
 */
class ConsoleLogger {

    /**
     * Logs a message as an informational log.
     * 
     * @param mixed $message The message to log.
     */
    public static function log($message) {
        if (is_array($message) || is_object($message)) {
            $message = print_r($message, true);
        }
        ConsoleLogger::error("Info: " . $message);
    }

    /**
     * Logs a message as an error.
     * 
     * @param mixed $message The message to log.
     */
    public static function error($message) {
        if (is_array($message) || is_object($message)) {
            $message = print_r($message, true);
        }
        ConsoleLogger::error("Error: " . $message);
    }
}

?>
