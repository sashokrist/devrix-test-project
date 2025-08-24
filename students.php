<?php
/**
 * Students Handler
 * 
 * This file handles student page requests directly
 */

// Load WordPress
require_once('wp-load.php');

// Get the request path
$request_uri = $_SERVER['REQUEST_URI'];
$path_parts = explode('/', trim($request_uri, '/'));

// Find the position of 'students' in the path
$students_index = array_search('students', $path_parts);

if ($students_index !== false && isset($path_parts[$students_index + 1])) {
    // Individual student page
    $student_slug = $path_parts[$students_index + 1];
    
    // Get the student
    $student = get_page_by_path($student_slug, OBJECT, 'student');
    
    if ($student && $student->post_status === 'publish') {
        // Set up the query
        global $wp_query;
        $wp_query->set('post_type', 'student');
        $wp_query->set('p', $student->ID);
        $wp_query->set('name', $student_slug);
        
        // Load the template
        $template = STUDENTS_PLUGIN_DIR . 'templates/single-student.php';
        if (file_exists($template)) {
            include $template;
            exit;
        }
    }
}

// If we get here, show a 404
http_response_code(404);
echo "Student not found.";
exit;
?>
