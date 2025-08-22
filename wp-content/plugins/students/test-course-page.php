<?php
/**
 * Test script to verify course page template override
 * 
 * Access this file directly in browser to test
 */

// Load WordPress
require_once( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-load.php' );

echo '<h2>Course Page Template Test Results</h2>';

// Check if we're on a course page
if ( is_page() && ( strpos( get_the_title(), 'Course' ) !== false || strpos( get_the_title(), 'course' ) !== false ) ) {
    echo '<h3>Course Page Test</h3>';
    echo '<p>Current page title: ' . get_the_title() . '</p>';
    echo '<p>Current template: ' . get_page_template() . '</p>';
    echo '<p>If you see "✅ PLUGIN TEMPLATE ACTIVE" on the page, the plugin template is working!</p>';
    echo '<p>Expected: taxonomy-course.php from plugin folder</p>';
} elseif ( is_tax( 'course' ) ) {
    echo '<h3>Course Taxonomy Page Test</h3>';
    echo '<p>Current template: ' . get_page_template() . '</p>';
    echo '<p>If you see "✅ PLUGIN TEMPLATE ACTIVE" on the page, the plugin template is working!</p>';
    echo '<p>Expected: taxonomy-course.php from plugin folder</p>';
} else {
    echo '<h3>Course Page Template Override Test</h3>';
    echo '<p>This test should be run on a course page.</p>';
    echo '<p>Please visit:</p>';
    echo '<ul>';
    
    // Get course-related pages
    $course_pages = get_pages( array(
        'meta_key' => '_wp_page_template',
        'meta_value' => 'page-course-archive.html',
    ) );
    
    if ( ! empty( $course_pages ) ) {
        echo '<li><strong>Course Pages:</strong></li>';
        foreach ( $course_pages as $page ) {
            echo '<li><a href="' . get_permalink( $page->ID ) . '">' . esc_html( $page->post_title ) . '</a></li>';
        }
    }
    
    // Get course terms
    $courses = get_terms( array(
        'taxonomy' => 'course',
        'hide_empty' => false,
    ) );
    
    if ( ! empty( $courses ) && ! is_wp_error( $courses ) ) {
        echo '<li><strong>Course Taxonomy Pages:</strong></li>';
        foreach ( $courses as $course ) {
            echo '<li><a href="' . get_term_link( $course ) . '">' . esc_html( $course->name ) . '</a></li>';
        }
    }
    
    echo '</ul>';
}

// Show current template hierarchy
echo '<h3>Template Loading Logic:</h3>';
echo '<p>The plugin now forces the use of plugin templates by:</p>';
echo '<ol>';
echo '<li>Disabling theme page templates (renamed to .disabled)</li>';
echo '<li>Adding page template override for course/grade pages</li>';
echo '<li>Directly returning plugin template path</li>';
echo '<li>Bypassing WordPress template hierarchy</li>';
echo '</ol>';

echo '<h3>Modified Methods:</h3>';
echo '<ul>';
echo '<li><code>load_page_templates()</code> - New method for page template override</li>';
echo '<li><code>load_taxonomy_templates()</code> - Forces plugin taxonomy templates</li>';
echo '<li><code>filter_student_archive_query()</code> - Applies to taxonomy pages too</li>';
echo '</ul>';

echo '<h3>Template Files:</h3>';
echo '<ul>';
echo '<li><strong>Course Template:</strong> ' . STUDENTS_PLUGIN_DIR . 'templates/taxonomy-course.php</li>';
echo '<li><strong>Grade Level Template:</strong> ' . STUDENTS_PLUGIN_DIR . 'templates/taxonomy-grade_level.php</li>';
echo '</ul>';

echo '<h3>Expected Behavior:</h3>';
echo '<ul>';
echo '<li>✅ Blue banner showing "PLUGIN TEMPLATE ACTIVE"</li>';
echo '<li>✅ 4 students per page with pagination</li>';
echo '<li>✅ Only active students shown</li>';
echo '<li>✅ Visibility settings respected</li>';
echo '<li>✅ XSS protection applied</li>';
echo '<li>✅ No more theme page templates used</li>';
echo '</ul>';

// Test current page
if ( is_page() || is_tax() ) {
    echo '<h3>Current Page Test:</h3>';
    global $wp_query;
    echo '<ul>';
    echo '<li><strong>Page type:</strong> ' . ( is_page() ? 'Page' : 'Taxonomy' ) . '</li>';
    echo '<li><strong>Page title:</strong> ' . get_the_title() . '</li>';
    echo '<li><strong>Template:</strong> ' . get_page_template() . '</li>';
    echo '</ul>';
}
?>
