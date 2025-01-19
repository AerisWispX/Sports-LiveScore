<?php
// config.php

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'u875728402_fk');
define('DB_USER', 'u875728402_fk');
define('DB_PASS', '@Agodx01');

// API configuration
define('API_TOKEN', '66DxtVLqA8D5rcVuQKxqSO64v0v0eX8no0Yz9LtizfmbSaQz0zqWvwgY75Xo');
define('API_BASE_URL', 'https://api.sportmonks.com/v3/football/');

// Time zone
date_default_timezone_set('UTC');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1);

// Database connection function
function db_connect() {
    static $conn;
    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            die('Database connection failed: ' . $conn->connect_error);
        }
    }
    return $conn;
}

// API request function
if (!function_exists('api_request')) {
    function api_request($endpoint, $params = []) {
        $api_url = API_BASE_URL . $endpoint;
        
        // Add API token to params
        $params['api_token'] = API_TOKEN;
        
        // Build query string
        $query_string = http_build_query($params);
        
        // Append query string to URL
        $api_url .= '?' . $query_string;

        $ch = curl_init($api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
        
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }
        
        curl_close($ch);
        
        $data = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON decode error: ' . json_last_error_msg());
        }
        
        return $data;
    }
}
?>
