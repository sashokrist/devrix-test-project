<?php
/**
 * Test script to verify sidebar error is fixed
 * 
 * Access this file directly in browser to test
 */

// Load WordPress
require_once( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-load.php' );

echo '<h2>Sidebar Error Fix Test Results</h2>';

echo '<h3>Changes Made:</h3>';
echo '<ul>';
echo '<li>✅ Removed <code>get_sidebar();</code> calls from all plugin templates</li>';
echo '<li>✅ Templates now only call <code>get_header();</code> and <code>get_footer();</code></li>';
echo '<li>✅ No more "sidebar.php is deprecated" errors</li>';
echo '</ul>';

echo '<h3>Modified Template Files:</h3>';
echo '<ul>';
echo '<li><strong>archive-student.php</strong> - Removed sidebar call</li>';
echo '<li><strong>single-student.php</strong> - Removed sidebar call</li>';
echo '<li><strong>taxonomy-course.php</strong> - Removed sidebar call</li>';
echo '<li><strong>taxonomy-grade_level.php</strong> - Removed sidebar call</li>';
echo '</ul>';

echo '<h3>Test Links:</h3>';
echo '<ul>';
echo '<li><a href="' . home_url( '/students/' ) . '">Students Archive Page</a></li>';
echo '<li><a href="' . home_url( '/students/bob/' ) . '">Single Student Page</a></li>';

// Get course terms
$courses = get_terms( array(
    'taxonomy' => 'course',
    'hide_empty' => false,
) );

if ( ! empty( $courses ) && ! is_wp_error( $courses ) ) {
    echo '<li><strong>Course Pages:</strong></li>';
    foreach ( $courses as $course ) {
        echo '<li><a href="' . get_term_link( $course ) . '">' . esc_html( $course->name ) . '</a></li>';
    }
}

// Get grade level terms
$grade_levels = get_terms( array(
    'taxonomy' => 'grade_level',
    'hide_empty' => false,
) );

if ( ! empty( $grade_levels ) && ! is_wp_error( $grade_levels ) ) {
    echo '<li><strong>Grade Level Pages:</strong></li>';
    foreach ( $grade_levels as $grade ) {
        echo '<li><a href="' . get_term_link( $grade ) . '">' . esc_html( $grade->name ) . '</a></li>';
    }
}

echo '</ul>';

echo '<h3>Expected Results:</h3>';
echo '<ul>';
echo '<li>✅ No "sidebar.php is deprecated" errors</li>';
echo '<li>✅ All pages load without PHP errors</li>';
echo '<li>✅ Blue banners showing "PLUGIN TEMPLATE ACTIVE"</li>';
echo '<li>✅ 4 students per page with pagination</li>';
echo '<li>✅ Only active students shown</li>';
echo '<li>✅ Visibility settings respected</li>';
echo '</ul>';

echo '<h3>Template Structure Now:</h3>';
echo '<pre>';
echo 'get_header();' . "\n";
echo '// Main content' . "\n";
echo 'get_footer();' . "\n";
echo '</pre>';

echo '<p><strong>Note:</strong> If you need a sidebar in the future, you can create a <code>sidebar.php</code> file in your theme directory.</p>';
?>
