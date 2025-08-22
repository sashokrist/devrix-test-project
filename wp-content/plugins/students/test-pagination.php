<?php
/**
 * Test script to verify pagination settings
 * 
 * Access this file directly in browser to test
 */

// Load WordPress
require_once( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-load.php' );

echo '<h2>Pagination Test Results</h2>';

// Check current settings
$options = get_option( 'students_options', array() );
$students_per_page = isset( $options['students_per_page'] ) ? $options['students_per_page'] : 4;

echo '<h3>Current Settings:</h3>';
echo '<ul>';
echo '<li><strong>Students per page setting:</strong> ' . $students_per_page . '</li>';
echo '<li><strong>Default fallback:</strong> 4</li>';
echo '</ul>';

// Count total active students
$args = array(
    'post_type' => 'student',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'meta_query' => array(
        array(
            'key' => '_student_is_active',
            'value' => '1',
            'compare' => '=',
        ),
    ),
);

$query = new WP_Query( $args );
$total_students = $query->wp_count_posts;

echo '<h3>Student Counts:</h3>';
echo '<ul>';
echo '<li><strong>Total active students:</strong> ' . $query->found_posts . '</li>';
echo '<li><strong>Students per page:</strong> ' . $students_per_page . '</li>';
echo '<li><strong>Total pages needed:</strong> ' . ceil( $query->found_posts / $students_per_page ) . '</li>';
echo '</ul>';

// Test the main query
$main_args = array(
    'post_type' => 'student',
    'post_status' => 'publish',
    'posts_per_page' => $students_per_page,
    'paged' => 1,
    'meta_query' => array(
        array(
            'key' => '_student_is_active',
            'value' => '1',
            'compare' => '=',
        ),
    ),
);

$main_query = new WP_Query( $main_args );

echo '<h3>First Page Results:</h3>';
echo '<ul>';
echo '<li><strong>Posts found:</strong> ' . $main_query->found_posts . '</li>';
echo '<li><strong>Posts per page:</strong> ' . $main_query->query_vars['posts_per_page'] . '</li>';
echo '<li><strong>Current page:</strong> ' . $main_query->query_vars['paged'] . '</li>';
echo '<li><strong>Max pages:</strong> ' . $main_query->max_num_pages . '</li>';
echo '</ul>';

echo '<h3>Student List (First Page):</h3>';
if ( $main_query->have_posts() ) {
    echo '<ol>';
    while ( $main_query->have_posts() ) {
        $main_query->the_post();
        echo '<li>' . get_the_title() . ' (ID: ' . get_post_meta( get_the_ID(), '_student_id', true ) . ')</li>';
    }
    echo '</ol>';
} else {
    echo '<p>No students found.</p>';
}

wp_reset_postdata();

echo '<h3>Test Links:</h3>';
echo '<ul>';
echo '<li><a href="' . home_url( '/students/' ) . '">Visit Students Archive Page</a></li>';
echo '<li><a href="' . home_url( '/students/page/2/' ) . '">Visit Page 2</a></li>';
echo '</ul>';

echo '<h3>Expected Behavior:</h3>';
echo '<ul>';
echo '<li>✅ Archive page should show exactly ' . $students_per_page . ' students</li>';
echo '<li>✅ Pagination should appear if more than ' . $students_per_page . ' students exist</li>';
echo '<li>✅ Only active students should be shown</li>';
echo '<li>✅ Plugin template should be used (blue banner visible)</li>';
echo '</ul>';
?>
