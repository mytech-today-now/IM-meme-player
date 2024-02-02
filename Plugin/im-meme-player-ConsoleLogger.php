<?php

namespace MyTechToday\IMMemePlayer;

// Ensure this file is being included within the WordPress framework
if (!defined('ABSPATH')) {
    ConsoleLogger::error('im-meme-player-admin-page.php - ABSPATH constant not defined');
    exit;
}

// =====================
// ConsoleLogger class
// =====================
// Mimics JavaScript's console.log and console.error functionality in PHP.
// =====================
// Example usage
// =====================
// ConsoleLogger::log('This is an informational message.');
// ConsoleLogger::error('This is an error message.');
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
        // Log the message as an informational log
        error_log("Info: " . $message);
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
        // Log the message as an error
        error_log("Error: " . $message);
    }
}