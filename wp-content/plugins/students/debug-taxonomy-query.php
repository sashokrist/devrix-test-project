<?php
/**
 * Debug script to check taxonomy query issues
 * 
 * Access this file directly in browser to test
 */

// Load WordPress
require_once( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-load.php' );

echo '<h2>Taxonomy Query Debug Results</h2>';

// Check current page type
echo '<h3>Current Page Analysis:</h3>';
echo '<ul>';
echo '<li><strong>Is Course Taxonomy:</strong> ' . ( is_tax( 'course' ) ? 'Yes' : 'No' ) . '</li>';
echo '<li><strong>Is Grade Level Taxonomy:</strong> ' . ( is_tax( 'grade_level' ) ? 'Yes' : 'No' ) . '</li>';
echo '<li><strong>Is Student Archive:</strong> ' . ( is_post_type_archive( 'student' ) ? 'Yes' : 'No' ) . '</li>';
echo '<li><strong>Is Page:</strong> ' . ( is_page() ? 'Yes' : 'No' ) . '</li>';
echo '</ul>';

// Get all students
echo '<h3>All Students Analysis:</h3>';
$all_students = get_posts( array(
    'post_type' => 'student',
    'post_status' => 'publish',
    'posts_per_page' => -1,
) );

echo '<p><strong>Total Students:</strong> ' . count( $all_students ) . '</p>';

// Check active students
$active_students = get_posts( array(
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
) );

echo '<p><strong>Active Students:</strong> ' . count( $active_students ) . '</p>';

// Check course assignments
echo '<h3>Course Assignments:</h3>';
$courses = get_terms( array(
    'taxonomy' => 'course',
    'hide_empty' => false,
) );

if ( ! empty( $courses ) && ! is_wp_error( $courses ) ) {
    echo '<ul>';
    foreach ( $courses as $course ) {
        $course_students = get_posts( array(
            'post_type' => 'student',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'course',
                    'field' => 'term_id',
                    'terms' => $course->term_id,
                ),
            ),
            'meta_query' => array(
                array(
                    'key' => '_student_is_active',
                    'value' => '1',
                    'compare' => '=',
                ),
            ),
        ) );
        
        echo '<li><strong>' . esc_html( $course->name ) . '</strong>: ' . count( $course_students ) . ' active students</li>';
        
        if ( count( $course_students ) > 0 ) {
            echo '<ul>';
            foreach ( $course_students as $student ) {
                echo '<li>' . esc_html( $student->post_title ) . ' (ID: ' . get_post_meta( $student->ID, '_student_id', true ) . ')</li>';
            }
            echo '</ul>';
        }
    }
    echo '</ul>';
}

// Check grade level assignments
echo '<h3>Grade Level Assignments:</h3>';
$grade_levels = get_terms( array(
    'taxonomy' => 'grade_level',
    'hide_empty' => false,
) );

if ( ! empty( $grade_levels ) && ! is_wp_error( $grade_levels ) ) {
    echo '<ul>';
    foreach ( $grade_levels as $grade ) {
        $grade_students = get_posts( array(
            'post_type' => 'student',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'grade_level',
                    'field' => 'term_id',
                    'terms' => $grade->term_id,
                ),
            ),
            'meta_query' => array(
                array(
                    'key' => '_student_is_active',
                    'value' => '1',
                    'compare' => '=',
                ),
            ),
        ) );
        
        echo '<li><strong>' . esc_html( $grade->name ) . '</strong>: ' . count( $grade_students ) . ' active students</li>';
        
        if ( count( $grade_students ) > 0 ) {
            echo '<ul>';
            foreach ( $grade_students as $student ) {
                echo '<li>' . esc_html( $student->post_title ) . ' (ID: ' . get_post_meta( $student->ID, '_student_id', true ) . ')</li>';
            }
            echo '</ul>';
        }
    }
    echo '</ul>';
}

// Test the main query
echo '<h3>Main Query Test:</h3>';
global $wp_query;

if ( is_tax( 'course' ) || is_tax( 'grade_level' ) ) {
    echo '<ul>';
    echo '<li><strong>Posts Found:</strong> ' . $wp_query->found_posts . '</li>';
    echo '<li><strong>Posts Per Page:</strong> ' . $wp_query->query_vars['posts_per_page'] . '</li>';
    echo '<li><strong>Max Pages:</strong> ' . $wp_query->max_num_pages . '</li>';
    echo '<li><strong>Current Page:</strong> ' . $wp_query->query_vars['paged'] . '</li>';
    echo '<li><strong>Query Vars:</strong> <pre>' . print_r( $wp_query->query_vars, true ) . '</pre></li>';
    echo '</ul>';
}

echo '<h3>Test Links:</h3>';
echo '<ul>';

if ( ! empty( $courses ) && ! is_wp_error( $courses ) ) {
    echo '<li><strong>Course Pages:</strong></li>';
    foreach ( $courses as $course ) {
        echo '<li><a href="' . get_term_link( $course ) . '">' . esc_html( $course->name ) . '</a></li>';
    }
}

if ( ! empty( $grade_levels ) && ! is_wp_error( $grade_levels ) ) {
    echo '<li><strong>Grade Level Pages:</strong></li>';
    foreach ( $grade_levels as $grade ) {
        echo '<li><a href="' . get_term_link( $grade ) . '">' . esc_html( $grade->name ) . '</a></li>';
    }
}

echo '</ul>';

echo '<h3>Recommendations:</h3>';
echo '<ul>';
echo '<li>✅ Check if students are assigned to courses and grade levels</li>';
echo '<li>✅ Verify that students have <code>_student_is_active = "1"</code></li>';
echo '<li>✅ Ensure taxonomy terms exist and are properly linked</li>';
echo '<li>✅ Test with a course/grade that has active students</li>';
echo '</ul>';
?>
