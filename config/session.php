<?php
// config/session.php

ini_set('session.use_only_cookies', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.gc_maxlifetime', 1800);

session_set_cookie_params([
    'lifetime' => 0,                        // 0 = Expires when the browser is closed
    'domain' => 'localhost',                
    'path' => '/',                          // Available across the entire domain   
    'secure' => true,                       // Set to true if using HTTPS
    'httponly' => true,                     // Prevent JavaScript access to session cookies
    'samesite' => 'Lax'                     // Adjust as needed (None, Lax, Strict)
]);

// Start the session globally for the entire application
session_start();

// 1. MANAGE IDLE TIMEOUT (Logout after 30 mins of inactivity)
$timeout_duration = 1800;

if (isset($_SESSION['last_activity'])) {
    if (time() - $_SESSION['last_activity'] >= $timeout_duration) {
        // User was inactive too long. Destroy session and start fresh.
        session_unset();     
        session_destroy();   
        session_start();     
    }
}

// Update the last activity timestamp on every page load
$_SESSION['last_activity'] = time();

// 2. MANAGE SESSION REGENERATION (Prevent Fixation Attacks)
$regeneration_interval = 30 * 60; // 30 minutes

if (!isset($_SESSION['last_regeneration'])) {
    regenerateSession();
}
else {
    if (time() - $_SESSION['last_regeneration'] >= $regeneration_interval) {
        regenerateSession();
    }
}

function regenerateSession() {
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}

// 3. GENERATE CSRF TOKEN
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}