<?php
// config/session.php

ini_set('session.use_only_cookies', 1);
ini_set('session.use_strict_mode', 1);

session_set_cookie_params([
    'lifetime' => 1800,                     // 30 minutes
    'domain' => 'localhost',                // 
    'path' => '/',                          // Available across the entire domain   
    'secure' => true,                      // Set to true if using HTTPS
    'httponly' => true,                     // Prevent JavaScript access to session cookies
    'samesite' => 'Lax'                     // Adjust as needed (None, Lax, Strict)
]);

// Start the session globally for the entire application
session_start();

// Generate a CSRF token if one doesn't already exist for this session
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Periodically regenerate the session ID to prevent fixation attacks
$interval = 30 * 60; // 30 minutes

if (!isset($_SESSION['last_regeneration'])) {
    regenerateSession();
}
else {
    if (time() - $_SESSION['last_regeneration'] >= $interval) {
        regenerateSession();
    }
}



function regenerateSession() {
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}