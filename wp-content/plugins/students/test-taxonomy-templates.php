<?php
/**
 * Test script to verify taxonomy template override
 * 
 * Access this file directly in browser to test
 */

// Load WordPress
require_once( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-load.php' );

echo '<h2>Taxonomy Template Test Results</h2>';

// Check if we're on a taxonomy page
if ( is_tax( 'course' ) ) {
    echo '<h3>Course Taxonomy Page Test</h3>';
    echo '<p>Current template: ' . get_page_template() . '</p>';
    echo '<p>If you see "✅ PLUGIN TEMPLATE ACTIVE" on the page, the plugin template is working!</p>';
    echo '<p>Expected: taxonomy-course.php from plugin folder</p>';
} elseif ( is_tax( 'grade_level' ) ) {
    echo '<h3>Grade Level Taxonomy Page Test</h3>';
    echo '<p>Current template: ' . get_page_template() . '</p>';
    echo '<p>If you see "✅ PLUGIN TEMPLATE ACTIVE" on the page, the plugin template is working!</p>';
    echo '<p>Expected: taxonomy-grade_level.php from plugin folder</p>';
} else {
    echo '<h3>Taxonomy Template Override Test</h3>';
    echo '<p>This test should be run on a taxonomy page.</p>';
    echo '<p>Please visit:</p>';
    echo '<ul>';
    
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
}

// Show current template hierarchy
echo '<h3>Template Loading Logic:</h3>';
echo '<p>The plugin now forces the use of plugin templates by:</p>';
echo '<ol>';
echo '<li>Removing theme template checks for taxonomies</li>';
echo '<li>Directly returning plugin template path</li>';
echo '<li>Bypassing WordPress template hierarchy for taxonomy pages</li>';
echo '</ol>';

echo '<h3>Modified Methods:</h3>';
echo '<ul>';
echo '<li><code>load_taxonomy_templates()</code> - Now forces plugin taxonomy templates</li>';
echo '<li><code>filter_student_archive_query()</code> - Now applies to taxonomy pages too</li>';
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
echo '</ul>';

// Test taxonomy query
if ( is_tax() ) {
    echo '<h3>Current Query Test:</h3>';
    global $wp_query;
    echo '<ul>';
    echo '<li><strong>Posts found:</strong> ' . $wp_query->found_posts . '</li>';
    echo '<li><strong>Posts per page:</strong> ' . $wp_query->query_vars['posts_per_page'] . '</li>';
    echo '<li><strong>Max pages:</strong> ' . $wp_query->max_num_pages . '</li>';
    echo '<li><strong>Current page:</strong> ' . $wp_query->query_vars['paged'] . '</li>';
    echo '</ul>';
}
?>
