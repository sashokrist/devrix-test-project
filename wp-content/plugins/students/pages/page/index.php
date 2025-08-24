<?php
/**
 * Handle pagination URLs and redirect them properly
 */

// Get the URL path
$request_uri = $_SERVER['REQUEST_URI'];
$parsed_url = parse_url($request_uri);
$path = $parsed_url['path'];

// Extract page number from URL like /page/2/
if (preg_match('/\/page\/(\d+)\/$/', $path, $matches)) {
    $page_number = $matches[1];
    
    // Get query parameters
    $query_params = array();
    if (isset($parsed_url['query'])) {
        parse_str($parsed_url['query'], $query_params);
    }
    
    // Check if this is a post type archive pagination
    if (isset($query_params['post_type'])) {
        $post_type = $query_params['post_type'];
        
        if ($post_type === 'student') {
            // Redirect to students archive with pagination
            $redirect_url = '../?post_type=student&paged=' . $page_number;
            header('Location: ' . $redirect_url, true, 301);
            exit;
        } elseif ($post_type === 'car') {
            // Redirect to cars archive with pagination
            $redirect_url = '../?post_type=car&paged=' . $page_number;
            header('Location: ' . $redirect_url, true, 301);
            exit;
        }
    }
    
    // Default pagination redirect (for regular posts)
    $redirect_url = '../?paged=' . $page_number;
    
    // Add any other query parameters
    if (!empty($query_params)) {
        $redirect_url .= '&' . http_build_query($query_params);
    }
    
    header('Location: ' . $redirect_url, true, 301);
    exit;
}

// If no pagination pattern found, redirect to homepage
header('Location: ../', true, 301);
exit;
?>
