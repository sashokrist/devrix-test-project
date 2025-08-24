<?php
/**
 * Handle students directory requests - both archive and individual profiles
 */

// Get the current path
$request_uri = $_SERVER['REQUEST_URI'];
$path_parts = explode('/', trim($request_uri, '/'));

// Find the position of 'students' in the path
$students_index = array_search('students', $path_parts);

if ($students_index !== false && isset($path_parts[$students_index + 1])) {
    // Individual student profile - there's a student slug after 'students'
    $student_slug = $path_parts[$students_index + 1];
    
    // Load WordPress to check if student exists
    require_once('../wp-config.php');
    require_once('../wp-load.php');
    
    // Check if student exists
    $student = get_page_by_path($student_slug, OBJECT, 'student');
    
    if ($student && $student->post_status === 'publish') {
        // Student exists, redirect to WordPress query format
        $redirect_url = '../?post_type=student&p=' . $student->ID;
        header('Location: ' . $redirect_url, true, 301);
        exit;
    } else {
        // Student doesn't exist, redirect to 404 or students archive
        header('Location: ../?post_type=student', true, 301);
        exit;
    }
} else {
    // Students archive - no specific student slug
    header('Location: ../?post_type=student', true, 301);
    exit;
}
?>
