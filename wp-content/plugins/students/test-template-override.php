<?php
/**
 * Test script to verify plugin template override
 * 
 * Access this file directly in browser to test
 */

// Load WordPress
require_once( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-load.php' );

// Check if we're on a student page
if ( is_singular( 'student' ) ) {
    echo '<h2>Single Student Page Test</h2>';
    echo '<p>Current template: ' . get_page_template() . '</p>';
    echo '<p>If you see "✅ PLUGIN TEMPLATE ACTIVE" on the page, the plugin template is working!</p>';
} elseif ( is_post_type_archive( 'student' ) ) {
    echo '<h2>Student Archive Page Test</h2>';
    echo '<p>Current template: ' . get_page_template() . '</p>';
    echo '<p>If you see "✅ PLUGIN TEMPLATE ACTIVE" on the page, the plugin template is working!</p>';
} else {
    echo '<h2>Template Override Test</h2>';
    echo '<p>This test should be run on a student page.</p>';
    echo '<p>Please visit:</p>';
    echo '<ul>';
    echo '<li><a href="' . home_url( '/students/' ) . '">Student Archive Page</a></li>';
    echo '<li>Or any individual student page</li>';
    echo '</ul>';
}

// Show current template hierarchy
echo '<h3>Template Loading Logic:</h3>';
echo '<p>The plugin now forces the use of plugin templates by:</p>';
echo '<ol>';
echo '<li>Removing theme template checks</li>';
echo '<li>Directly returning plugin template path</li>';
echo '<li>Bypassing WordPress template hierarchy for student pages</li>';
echo '</ol>';

echo '<h3>Modified Methods:</h3>';
echo '<ul>';
echo '<li><code>load_single_student_template()</code> - Now forces plugin single-student.php</li>';
echo '<li><code>load_archive_student_template()</code> - Now forces plugin archive-student.php</li>';
echo '<li><code>load_taxonomy_templates()</code> - Now forces plugin taxonomy templates</li>';
echo '</ul>';
?>
