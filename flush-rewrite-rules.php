<?php
/**
 * Flush Rewrite Rules Script
 * 
 * This script flushes WordPress rewrite rules to fix student URLs
 */

// Load WordPress
require_once 'wp-load.php';

echo "Flushing rewrite rules...\n";

// Flush rewrite rules
flush_rewrite_rules();

echo "Rewrite rules flushed successfully!\n";

// Test if student post type is registered
$post_types = get_post_types(array(), 'names');
echo "Registered post types: " . implode(', ', $post_types) . "\n";

// Check if 'student' post type exists
if (post_type_exists('student')) {
    echo "✅ Student post type is registered\n";
    
    // Get rewrite rules
    $rewrite_rules = get_option('rewrite_rules');
    $student_rules = array();
    
    foreach ($rewrite_rules as $rule => $rewrite) {
        if (strpos($rule, 'students') !== false) {
            $student_rules[$rule] = $rewrite;
        }
    }
    
    echo "Student rewrite rules found: " . count($student_rules) . "\n";
    foreach ($student_rules as $rule => $rewrite) {
        echo "Rule: $rule -> $rewrite\n";
    }
} else {
    echo "❌ Student post type is NOT registered\n";
}

echo "Done!\n";
?>
