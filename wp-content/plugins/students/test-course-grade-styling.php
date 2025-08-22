<?php
/**
 * Test Course and Grade Pages Styling
 * 
 * Access this file directly in browser to test styling
 */

// Load WordPress
require_once( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-load.php' );

echo '<h2>🎓 Test Course and Grade Pages Styling</h2>';

echo '<h3>✅ Current Page Information:</h3>';
global $post;
if ( $post ) {
    echo '<p><strong>Post ID:</strong> ' . $post->ID . '</p>';
    echo '<p><strong>Post Type:</strong> ' . $post->post_type . '</p>';
    echo '<p><strong>Post Name:</strong> ' . $post->post_name . '</p>';
    echo '<p><strong>Post Title:</strong> ' . esc_html( $post->post_title ) . '</p>';
    echo '<p><strong>Is Page:</strong> ' . (is_page() ? 'YES' : 'NO') . '</p>';
    echo '<p><strong>Is Course Taxonomy:</strong> ' . (is_tax( 'course' ) ? 'YES' : 'NO') . '</p>';
    echo '<p><strong>Is Grade Level Taxonomy:</strong> ' . (is_tax( 'grade_level' ) ? 'YES' : 'NO') . '</p>';
} else {
    echo '<p>❌ No post object found</p>';
}

echo '<h3>✅ CSS Enqueue Logic:</h3>';
echo '<ul>';
echo '<li><strong>Is Student Archive:</strong> ' . (is_post_type_archive( 'student' ) ? 'YES' : 'NO') . '</li>';
echo '<li><strong>Is Student Singular:</strong> ' . (is_singular( 'student' ) ? 'YES' : 'NO') . '</li>';
echo '<li><strong>Is Course Taxonomy:</strong> ' . (is_tax( 'course' ) ? 'YES' : 'NO') . '</li>';
echo '<li><strong>Is Grade Level Taxonomy:</strong> ' . (is_tax( 'grade_level' ) ? 'YES' : 'NO') . '</li>';
echo '</ul>';

echo '<h3>✅ CSS File Status:</h3>';
$css_file = STUDENTS_PLUGIN_DIR . 'assets/css/public.css';
echo '<p><strong>CSS File Path:</strong> ' . $css_file . '</p>';
echo '<p><strong>File Exists:</strong> ' . (file_exists( $css_file ) ? 'YES' : 'NO') . '</p>';
if ( file_exists( $css_file ) ) {
    echo '<p><strong>File Size:</strong> ' . filesize( $css_file ) . ' bytes</p>';
    echo '<p><strong>File Permissions:</strong> ' . substr( sprintf( '%o', fileperms( $css_file ) ), -4 ) . '</p>';
}

echo '<h3>✅ CSS URL:</h3>';
$css_url = STUDENTS_PLUGIN_URL . 'assets/css/public.css';
echo '<p><strong>CSS URL:</strong> <a href="' . $css_url . '" target="_blank">' . $css_url . '</a></p>';

echo '<h3>✅ Test Course and Grade Pages:</h3>';
echo '<p>Visit the course and grade pages to see the box layout:</p>';

// Get some course terms
$courses = get_terms( array(
    'taxonomy' => 'course',
    'hide_empty' => true,
    'number' => 3,
) );

if ( $courses && ! is_wp_error( $courses ) ) {
    echo '<ul>';
    foreach ( $courses as $course ) {
        echo '<li><strong>Course:</strong> <a href="' . get_term_link( $course ) . '">' . esc_html( $course->name ) . '</a></li>';
    }
    echo '</ul>';
}

// Get some grade level terms
$grade_levels = get_terms( array(
    'taxonomy' => 'grade_level',
    'hide_empty' => true,
    'number' => 3,
) );

if ( $grade_levels && ! is_wp_error( $grade_levels ) ) {
    echo '<ul>';
    foreach ( $grade_levels as $grade ) {
        echo '<li><strong>Grade Level:</strong> <a href="' . get_term_link( $grade ) . '">' . esc_html( $grade->name ) . '</a></li>';
    }
    echo '</ul>';
}

echo '<h3>✅ Expected Box Layout Features:</h3>';
echo '<p>After CSS loads, you should see:</p>';
echo '<ul>';
echo '<li>✅ <strong>Grid Layout:</strong> Students in a responsive grid</li>';
echo '<li>✅ <strong>Card Design:</strong> Each student in a white card with shadow</li>';
echo '<li>✅ <strong>Student Photos:</strong> Featured images in card headers</li>';
echo '<li>✅ <strong>Hover Effects:</strong> Cards lift on hover</li>';
echo '<li>✅ <strong>Student Info:</strong> ID, grade, status, courses in card body</li>';
echo '<li>✅ <strong>View Profile Button:</strong> Blue button in card footer</li>';
echo '<li>✅ <strong>Consistent Styling:</strong> Matches Students archive page</li>';
echo '</ul>';

echo '<h3>✅ Manual CSS Test:</h3>';
echo '<p>If CSS is not loading, you can manually test the styles:</p>';
echo '<div style="background: #e7f3ff; border: 2px solid #0073aa; padding: 10px; margin: 10px 0; border-radius: 5px; text-align: center;">';
echo '<strong>✅ CSS Test</strong> - If you see this styled box, CSS is working';
echo '</div>';

echo '<h3>✅ Box Layout Test:</h3>';
echo '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 2rem; margin: 2rem 0;">';
echo '<div style="border: 1px solid #ddd; border-radius: 8px; overflow: hidden; background: #fff; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">';
echo '<div style="width: 100%; height: 200px; overflow: hidden; background: #f5f5f5; display: flex; align-items: center; justify-content: center;">';
echo '<span style="font-size: 3rem;">👨‍🎓</span>';
echo '</div>';
echo '<div style="padding: 1.5rem;">';
echo '<h3 style="margin: 0 0 1rem 0; font-size: 1.25rem; font-weight: 600;">Sample Student</h3>';
echo '<div style="margin-bottom: 1rem;">';
echo '<div style="margin: 0.5rem 0; font-size: 0.9rem; color: #666;"><strong>ID:</strong> STU001</div>';
echo '<div style="margin: 0.5rem 0; font-size: 0.9rem; color: #666;"><strong>Grade:</strong> 10th Grade</div>';
echo '<div style="margin: 0.5rem 0; font-size: 0.9rem; color: #666;"><strong>Status:</strong> <span style="color: green; font-weight: bold;">Active</span></div>';
echo '</div>';
echo '<div style="text-align: center;">';
echo '<a href="#" style="display: inline-block; padding: 0.5rem 1rem; background: #0073aa; color: #fff; text-decoration: none; border-radius: 4px;">View Profile</a>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '</div>';

echo '<p><strong>Note:</strong> The Course and Grade pages should now display students in a box layout. If it\'s still not working, check browser cache or try a hard refresh (Ctrl+F5).</p>';
?>
