<?php
/**
 * Redirect for student profile: gchgcnhc
 */

// Load WordPress
require_once('../../wp-config.php');
require_once('../../wp-load.php');

// Get the student by slug
$student = get_page_by_path('gchgcnhc', OBJECT, 'student');

if ($student) {
    // Redirect to the student single page
    $redirect_url = home_url('/?post_type=student&p=' . $student->ID);
    header('Location: ' . $redirect_url, true, 301);
    exit;
} else {
    // Student not found, redirect to students archive
    header('Location: ' . home_url('/?post_type=student'), true, 301);
    exit;
}
?>