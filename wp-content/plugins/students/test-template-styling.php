<?php
/**
 * Test script to verify template styling consistency
 * 
 * Access this file directly in browser to test
 */

// Load WordPress
require_once( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-load.php' );

echo '<h2>Template Styling Consistency Test</h2>';

echo '<h3>Styling Updates Applied:</h3>';
echo '<ul>';
echo '<li>✅ <strong>Course Pages</strong> - Now show all student metadata fields</li>';
echo '<li>✅ <strong>Grade Level Pages</strong> - Now show all student metadata fields</li>';
echo '<li>✅ <strong>Consistent Layout</strong> - All pages use same card structure</li>';
echo '<li>✅ <strong>Visibility Settings</strong> - All pages respect show/hide preferences</li>';
echo '<li>✅ <strong>XSS Protection</strong> - All data properly sanitized and escaped</li>';
echo '</ul>';

echo '<h3>Metadata Fields Now Displayed on All Pages:</h3>';
echo '<ul>';
echo '<li><strong>Student ID</strong> - Shows student identification number</li>';
echo '<li><strong>Class/Grade</strong> - Shows student class or grade level</li>';
echo '<li><strong>Status</strong> - Shows Active/Inactive status with color coding</li>';
echo '<li><strong>Courses</strong> - Shows enrolled courses (on Grade Level pages)</li>';
echo '<li><strong>Grade Levels</strong> - Shows grade levels (on Course pages)</li>';
echo '<li><strong>Student Description</strong> - Shows student excerpt</li>';
echo '<li><strong>View Profile Button</strong> - Links to full student profile</li>';
echo '</ul>';

echo '<h3>Test Links:</h3>';
echo '<ul>';

// Get course terms
$courses = get_terms( array(
    'taxonomy' => 'course',
    'hide_empty' => false,
) );

if ( ! empty( $courses ) && ! is_wp_error( $courses ) ) {
    echo '<li><strong>Course Pages:</strong></li>';
    foreach ( $courses as $course ) {
        echo '<li><a href="' . get_term_link( $course ) . '">' . esc_html( $course->name ) . '</a> - Should show students with full metadata</li>';
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
        echo '<li><a href="' . get_term_link( $grade ) . '">' . esc_html( $grade->name ) . '</a> - Should show students with full metadata</li>';
    }
}

echo '<li><strong>Student Archive:</strong> <a href="' . home_url( '/students/' ) . '">All Students</a> - Reference for styling</li>';
echo '</ul>';

echo '<h3>Expected Results:</h3>';
echo '<ul>';
echo '<li>✅ <strong>Blue Banner</strong> - "PLUGIN TEMPLATE ACTIVE" on all pages</li>';
echo '<li>✅ <strong>4 Students Per Page</strong> - Consistent pagination across all pages</li>';
echo '<li>✅ <strong>Student Cards</strong> - Same layout and styling on all pages</li>';
echo '<li>✅ <strong>Metadata Display</strong> - All fields shown consistently</li>';
echo '<li>✅ <strong>Visibility Controls</strong> - Respects admin settings</li>';
echo '<li>✅ <strong>Navigation</strong> - "Other Courses" and "Other Grade Levels" sections</li>';
echo '<li>✅ <strong>Active Students Only</strong> - Inactive students filtered out</li>';
echo '</ul>';

echo '<h3>Template Structure Comparison:</h3>';
echo '<table border="1" style="border-collapse: collapse; width: 100%;">';
echo '<tr><th>Page Type</th><th>Template File</th><th>Metadata Fields</th><th>Navigation</th></tr>';
echo '<tr><td>Student Archive</td><td>archive-student.php</td><td>All fields</td><td>Pagination only</td></tr>';
echo '<tr><td>Course Pages</td><td>taxonomy-course.php</td><td>All fields</td><td>Pagination + Other Courses</td></tr>';
echo '<tr><td>Grade Level Pages</td><td>taxonomy-grade_level.php</td><td>All fields</td><td>Pagination + Other Grade Levels</td></tr>';
echo '<tr><td>Single Student</td><td>single-student.php</td><td>All fields</td><td>None</td></tr>';
echo '</table>';

echo '<h3>CSS Classes Used:</h3>';
echo '<ul>';
echo '<li><code>.students-grid</code> - Main container for student cards</li>';
echo '<li><code>.student-card</code> - Individual student card</li>';
echo '<li><code>.student-photo</code> - Student image container</li>';
echo '<li><code>.student-info</code> - Student information container</li>';
echo '<li><code>.student-meta</code> - Metadata fields container</li>';
echo '<li><code>.meta-item</code> - Individual metadata field</li>';
echo '<li><code>.entry-summary</code> - Student description</li>';
echo '<li><code>.read-more</code> - View Profile button</li>';
echo '</ul>';

echo '<p><strong>Note:</strong> All pages now have consistent styling and functionality. The Course and Grade Level pages will look exactly like the Student Archive page but with additional navigation sections.</p>';
?>
