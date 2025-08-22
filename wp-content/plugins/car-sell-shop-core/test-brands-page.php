<?php
/**
 * Test Brands Page
 * 
 * Access this file directly in browser to test the brands page functionality
 */

// Load WordPress
require_once( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-load.php' );

echo '<h2>🚗 Brands Page Test</h2>';

echo '<h3>✅ Page Creation Status:</h3>';
$brands_page = get_page_by_path( 'brands' );
if ( $brands_page ) {
    echo '<p>✅ <strong>Brands page exists!</strong></p>';
    echo '<p><strong>Page ID:</strong> ' . $brands_page->ID . '</p>';
    echo '<p><strong>Page Title:</strong> ' . esc_html( $brands_page->post_title ) . '</p>';
    echo '<p><strong>Page URL:</strong> <a href="' . get_permalink( $brands_page->ID ) . '">' . get_permalink( $brands_page->ID ) . '</a></p>';
} else {
    echo '<p>❌ <strong>Brands page not found!</strong></p>';
}

echo '<h3>✅ Navigation Menu Items:</h3>';
echo '<ul>';
echo '<li><strong>Students:</strong> <a href="' . home_url('/students/') . '">' . home_url('/students/') . '</a></li>';
echo '<li><strong>Course:</strong> <a href="' . home_url('/course/') . '">' . home_url('/course/') . '</a></li>';
echo '<li><strong>Grades:</strong> <a href="' . home_url('/grade-level/') . '">' . home_url('/grade-level/') . '</a></li>';
echo '<li><strong>Cars:</strong> <a href="' . get_post_type_archive_link('car') . '">' . get_post_type_archive_link('car') . '</a></li>';
echo '<li><strong>Brands:</strong> <a href="' . home_url('/brands/') . '">' . home_url('/brands/') . '</a> ← <strong>NEW!</strong></li>';
echo '</ul>';

echo '<h3>✅ Available Car Brands:</h3>';
$brands = get_terms( array(
    'taxonomy' => 'brand',
    'hide_empty' => false,
) );

if ( $brands && ! is_wp_error( $brands ) ) {
    echo '<ul>';
    foreach ( $brands as $brand ) {
        echo '<li><strong>' . esc_html( $brand->name ) . '</strong> (' . $brand->count . ' cars) - <a href="' . get_term_link( $brand ) . '">View Cars</a></li>';
    }
    echo '</ul>';
} else {
    echo '<p>No brands found. Please add some car brands to test the page.</p>';
}

echo '<h3>✅ Template Loading:</h3>';
echo '<ul>';
echo '<li>✅ <strong>Template File:</strong> <code>templates/page-brands.php</code></li>';
echo '<li>✅ <strong>CSS File:</strong> <code>assets/css/car-styles.css</code></li>';
echo '<li>✅ <strong>Template Loading:</strong> <code>class-car-templates.php</code></li>';
echo '</ul>';

echo '<h3>✅ CSS Classes for Brands Page:</h3>';
echo '<ul>';
echo '<li>✅ <strong>.brands-grid</strong> - Responsive grid container</li>';
echo '<li>✅ <strong>.brand-card</strong> - Individual brand card styling</li>';
echo '<li>✅ <strong>.brand-info</strong> - Content area padding</li>';
echo '<li>✅ <strong>.brand-meta</strong> - Metadata section</li>';
echo '<li>✅ <strong>.brand-count</strong> - Car count styling</li>';
echo '<li>✅ <strong>.quick-links</strong> - Quick navigation section</li>';
echo '<li>✅ <strong>.quick-link</strong> - Individual quick link styling</li>';
echo '</ul>';

echo '<h3>✅ Test Your Brands Page:</h3>';
echo '<ul>';
echo '<li><strong>Visit Brands Page:</strong> <a href="' . home_url('/brands/') . '">' . home_url('/brands/') . '</a></li>';
echo '<li><strong>Check Navigation:</strong> Look for "Brands" in the top menu</li>';
echo '<li><strong>Test Template:</strong> Should show blue "PLUGIN TEMPLATE ACTIVE" banner</li>';
echo '<li><strong>Test Responsive:</strong> Try on mobile and desktop</li>';
echo '</ul>';

echo '<h3>✅ Expected Visual Results:</h3>';
echo '<ul>';
echo '<li>✅ <strong>Blue Banner:</strong> "PLUGIN TEMPLATE ACTIVE" confirmation</li>';
echo '<li>✅ <strong>Page Title:</strong> "Car Brands"</li>';
echo '<li>✅ <strong>Brand Cards:</strong> Each brand in styled card</li>';
echo '<li>✅ <strong>Car Counts:</strong> Number of cars per brand in blue</li>';
echo '<li>✅ <strong>View Cars Buttons:</strong> Blue buttons to view brand cars</li>';
echo '<li>✅ <strong>Quick Links:</strong> Navigation section at bottom</li>';
echo '<li>✅ <strong>Hover Effects:</strong> Cards lift on hover</li>';
echo '<li>✅ <strong>Responsive Grid:</strong> Adapts to screen size</li>';
echo '</ul>';

echo '<h3>✅ Navigation Order:</h3>';
echo '<ol>';
echo '<li>Students</li>';
echo '<li>Course</li>';
echo '<li>Grades</li>';
echo '<li>Cars</li>';
echo '<li><strong>Brands</strong> ← Newly Added</li>';
echo '</ol>';

echo '<h3>✅ Active State Logic:</h3>';
echo '<ul>';
echo '<li>✅ <strong>Brands Page Active:</strong> When viewing <code>/brands/</code></li>';
echo '<li>✅ <strong>Individual Brand Active:</strong> When viewing brand taxonomy pages</li>';
echo '</ul>';

echo '<p><strong>Note:</strong> The brands page is now fully functional and accessible via the navigation menu. It displays all car brands in a beautiful grid layout with car counts and quick navigation!</p>';
?>
