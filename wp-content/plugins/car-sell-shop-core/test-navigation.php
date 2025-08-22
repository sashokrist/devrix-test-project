<?php
/**
 * Test Cars Navigation
 * 
 * Access this file directly in browser to test the Cars navigation
 */

// Load WordPress
require_once( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-load.php' );

echo '<h2>🚗 Cars Navigation Test</h2>';

echo '<h3>✅ Navigation Menu Items:</h3>';
echo '<ul>';
echo '<li><strong>Students:</strong> <a href="' . home_url('/students/') . '">' . home_url('/students/') . '</a></li>';
echo '<li><strong>Course:</strong> <a href="' . home_url('/course/') . '">' . home_url('/course/') . '</a></li>';
echo '<li><strong>Grades:</strong> <a href="' . home_url('/grade-level/') . '">' . home_url('/grade-level/') . '</a></li>';
echo '<li><strong>Cars:</strong> <a href="' . get_post_type_archive_link('car') . '">' . get_post_type_archive_link('car') . '</a></li>';
echo '</ul>';

echo '<h3>✅ Car Post Type Archive Link:</h3>';
$car_archive_link = get_post_type_archive_link('car');
echo '<p><strong>Archive Link:</strong> <a href="' . $car_archive_link . '">' . $car_archive_link . '</a></p>';

echo '<h3>✅ Active State Logic:</h3>';
echo '<ul>';
echo '<li><strong>Car Archive Active:</strong> ' . (is_post_type_archive('car') ? 'YES' : 'NO') . '</li>';
echo '<li><strong>Single Car Active:</strong> ' . (is_singular('car') ? 'YES' : 'NO') . '</li>';
echo '<li><strong>Brand Taxonomy Active:</strong> ' . (is_tax('brand') ? 'YES' : 'NO') . '</li>';
echo '</ul>';

echo '<h3>✅ Test Navigation States:</h3>';
echo '<ul>';

// Test car archive
echo '<li><strong>Car Archive Page:</strong> <a href="' . get_post_type_archive_link('car') . '">Visit Cars Archive</a></li>';

// Get car posts for single car links
$cars = get_posts( array(
    'post_type' => 'car',
    'post_status' => 'publish',
    'posts_per_page' => 3,
) );

if ( ! empty( $cars ) ) {
    echo '<li><strong>Single Car Pages:</strong></li>';
    foreach ( $cars as $car ) {
        echo '<li><a href="' . get_permalink( $car->ID ) . '">' . esc_html( $car->post_title ) . '</a></li>';
    }
}

// Get brand terms for taxonomy links
$brands = get_terms( array(
    'taxonomy' => 'brand',
    'hide_empty' => false,
) );

if ( ! empty( $brands ) && ! is_wp_error( $brands ) ) {
    echo '<li><strong>Brand Pages:</strong></li>';
    foreach ( $brands as $brand ) {
        echo '<li><a href="' . get_term_link( $brand ) . '">' . esc_html( $brand->name ) . '</a></li>';
    }
}

echo '</ul>';

echo '<h3>✅ Navigation Code Added:</h3>';
echo '<pre><code>';
echo '&lt;li class="nav-item ' . '<?php echo (is_post_type_archive(\'car\') || is_singular(\'car\') || is_tax(\'brand\')) ? \'active\' : \'\'; ?>"&gt;' . "\n";
echo '    &lt;a href="' . '<?php echo esc_url(get_post_type_archive_link(\'car\')); ?>"&gt;Cars&lt;/a&gt;' . "\n";
echo '&lt;/li&gt;';
echo '</code></pre>';

echo '<h3>✅ Expected Results:</h3>';
echo '<ul>';
echo '<li>✅ <strong>Cars Menu Item:</strong> Should appear in top navigation</li>';
echo '<li>✅ <strong>Active State:</strong> Should highlight when on car pages</li>';
echo '<li>✅ <strong>Archive Link:</strong> Should link to /cars/ archive page</li>';
echo '<li>✅ <strong>Plugin Template:</strong> Should show blue "PLUGIN TEMPLATE ACTIVE" banner</li>';
echo '<li>✅ <strong>Responsive:</strong> Should work on mobile and desktop</li>';
echo '</ul>';

echo '<h3>✅ Navigation Order:</h3>';
echo '<ol>';
echo '<li>Students</li>';
echo '<li>Course</li>';
echo '<li>Grades</li>';
echo '<li><strong>Cars</strong> ← Newly Added</li>';
echo '</ol>';

echo '<p><strong>Note:</strong> The Cars navigation item has been added to the top navigation menu. It will be active when viewing any car-related page (archive, single car, or brand pages).</p>';
?>
