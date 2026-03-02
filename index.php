<?php
// index.php - The Front Controller

// Initialize highly secure session settings
require_once 'config/session.php';

// 1. Capture and sanitize the URL (Passed silently by .htaccess)
$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : 'home';
$url = filter_var($url, FILTER_SANITIZE_URL);

// 2. Split the URL into an array
// Example: 'product/view/12' becomes ['product', 'view', '12']
$url_parts = explode('/', $url);

// 3. Define the Controller
// 'product' becomes 'ProductController'
$controllerName = ucfirst($url_parts[0]) . 'Controller';
$controllerFile = 'controllers/' . $controllerName . '.php';

// 4. Define the Method (Action)
// If no method is passed, default to 'index'
$methodName = isset($url_parts[1]) ? $url_parts[1] : 'index';

// 5. Extract Parameters
// Grab everything after the controller and method (e.g., the '12')
$params = array_slice($url_parts, 2);

// 6. Execute the Routing Logic
if (file_exists($controllerFile)) {
    // Load the correct controller file
    require_once $controllerFile;
    
    // Create a new instance of the controller
    $controller = new $controllerName();

    // Check if the requested method exists inside this controller
    if (method_exists($controller, $methodName)) {
        // Call the method and pass any parameters to it
        call_user_func_array([$controller, $methodName], $params);
    } else {
        // The controller exists, but the method doesn't
        http_response_code(404);
        echo "<h1>404 Error</h1><p>Page not found.</p>";
    }
} else {
    // The controller file doesn't exist
    http_response_code(404);
    echo "<h1>404 Error</h1><p>Page not found.</p>";
}