<?php
/**
 * Handle pagination for page 4
 */

// Get query parameters
$query_params = array();
if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
    parse_str($_SERVER['QUERY_STRING'], $query_params);
}

// Check if this is a post type archive pagination
if (isset($query_params['post_type'])) {
    $post_type = $query_params['post_type'];
    
    if ($post_type === 'student') {
        // Redirect to students archive with pagination
        $redirect_url = '../../?post_type=student&paged=4';
        header('Location: ' . $redirect_url, true, 301);
        exit;
    } elseif ($post_type === 'car') {
        // Redirect to cars archive with pagination
        $redirect_url = '../../?post_type=car&paged=4';
        header('Location: ' . $redirect_url, true, 301);
        exit;
    }
}

// Default pagination redirect
$redirect_url = '../../?paged=4';

// Add any other query parameters
if (!empty($query_params)) {
    $redirect_url .= '&' . http_build_query($query_params);
}

header('Location: ' . $redirect_url, true, 301);
exit;
?>
