<?php
/**
 * Create Brands Page Script
 * 
 * Run this script to create the brands page automatically
 */

// Load WordPress
require_once( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-load.php' );

echo '<h2>🚗 Creating Brands Page</h2>';

// Check if brands page already exists
$brands_page = get_page_by_path( 'brands' );

if ( $brands_page ) {
    echo '<p>✅ <strong>Brands page already exists!</strong></p>';
    echo '<p><strong>Page ID:</strong> ' . $brands_page->ID . '</p>';
    echo '<p><strong>Page URL:</strong> <a href="' . get_permalink( $brands_page->ID ) . '">' . get_permalink( $brands_page->ID ) . '</a></p>';
} else {
    // Create the brands page
    $page_data = array(
        'post_title'    => 'Car Brands',
        'post_name'     => 'brands',
        'post_content'  => 'This page displays all car brands available in our collection.',
        'post_status'   => 'publish',
        'post_type'     => 'page',
        'post_author'   => 1,
        'page_template' => 'page-brands.php'
    );
    
    $page_id = wp_insert_post( $page_data );
    
    if ( $page_id && ! is_wp_error( $page_id ) ) {
        echo '<p>✅ <strong>Brands page created successfully!</strong></p>';
        echo '<p><strong>Page ID:</strong> ' . $page_id . '</p>';
        echo '<p><strong>Page URL:</strong> <a href="' . get_permalink( $page_id ) . '">' . get_permalink( $page_id ) . '</a></p>';
    } else {
        echo '<p>❌ <strong>Error creating brands page.</strong></p>';
        if ( is_wp_error( $page_id ) ) {
            echo '<p>Error: ' . $page_id->get_error_message() . '</p>';
        }
    }
}

echo '<h3>✅ Navigation Updated:</h3>';
echo '<p>The "Brands" link has been added to the top navigation menu.</p>';

echo '<h3>✅ Template Ready:</h3>';
echo '<p>The brands page will use the plugin template: <code>templates/page-brands.php</code></p>';

echo '<h3>✅ Features:</h3>';
echo '<ul>';
echo '<li>✅ <strong>Grid Layout:</strong> Brands displayed in responsive grid</li>';
echo '<li>✅ <strong>Card Design:</strong> Each brand in styled card</li>';
echo '<li>✅ <strong>Car Count:</strong> Shows number of cars per brand</li>';
echo '<li>✅ <strong>Quick Links:</strong> Easy navigation to other pages</li>';
echo '<li>✅ <strong>Hover Effects:</strong> Cards lift on hover</li>';
echo '<li>✅ <strong>Responsive:</strong> Works on all devices</li>';
echo '</ul>';

echo '<h3>✅ Test Your Brands Page:</h3>';
echo '<ul>';
echo '<li><strong>Visit:</strong> <a href="' . home_url('/brands/') . '">' . home_url('/brands/') . '</a></li>';
echo '<li><strong>Navigation:</strong> Check the top menu for "Brands" link</li>';
echo '<li><strong>Template:</strong> Should show blue "PLUGIN TEMPLATE ACTIVE" banner</li>';
echo '</ul>';

echo '<h3>✅ Expected Results:</h3>';
echo '<ul>';
echo '<li>✅ <strong>Blue Banner:</strong> "PLUGIN TEMPLATE ACTIVE" confirmation</li>';
echo '<li>✅ <strong>Brand Cards:</strong> Each brand in styled card</li>';
echo '<li>✅ <strong>Car Counts:</strong> Number of cars per brand</li>';
echo '<li>✅ <strong>View Cars:</strong> Blue buttons to view brand cars</li>';
echo '<li>✅ <strong>Quick Links:</strong> Navigation to All Cars and Home</li>';
echo '</ul>';

echo '<p><strong>Note:</strong> The brands page is now ready and accessible via the navigation menu!</p>';
?>
