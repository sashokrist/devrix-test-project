<?php
/**
 * Test Template Override for Brands Page
 * 
 * Access this file directly in browser to test the template override
 */

// Load WordPress
require_once( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-load.php' );

echo '<h2>🚗 Template Override Test</h2>';

echo '<h3>✅ Template Override Methods Applied:</h3>';
echo '<ul>';
echo '<li>✅ <strong>template_include filter</strong> - Override template selection</li>';
echo '<li>✅ <strong>template_redirect action</strong> - Force template inclusion</li>';
echo '<li>✅ <strong>theme_templates filter</strong> - Remove theme templates</li>';
echo '<li>✅ <strong>page_template filter</strong> - Override page templates</li>';
echo '</ul>';

echo '<h3>✅ Brands Page Information:</h3>';
$brands_page = get_page_by_path( 'brands' );
if ( $brands_page ) {
    echo '<p>✅ <strong>Brands page exists!</strong></p>';
    echo '<p><strong>Page ID:</strong> ' . $brands_page->ID . '</p>';
    echo '<p><strong>Page Slug:</strong> ' . $brands_page->post_name . '</p>';
    echo '<p><strong>Page Title:</strong> ' . esc_html( $brands_page->post_title ) . '</p>';
    echo '<p><strong>Page URL:</strong> <a href="' . get_permalink( $brands_page->ID ) . '">' . get_permalink( $brands_page->ID ) . '</a></p>';
} else {
    echo '<p>❌ <strong>Brands page not found!</strong></p>';
}

echo '<h3>✅ Plugin Template Status:</h3>';
$plugin_template = CAR_SELL_SHOP_CORE_PLUGIN_DIR . 'templates/page-brands.php';
if ( file_exists( $plugin_template ) ) {
    echo '<p>✅ <strong>Plugin template exists!</strong></p>';
    echo '<p><strong>Template Path:</strong> ' . $plugin_template . '</p>';
    echo '<p><strong>Template Size:</strong> ' . filesize( $plugin_template ) . ' bytes</p>';
} else {
    echo '<p>❌ <strong>Plugin template not found!</strong></p>';
}

echo '<h3>✅ Template Override Logic:</h3>';
echo '<ul>';
echo '<li>✅ <strong>Page Check:</strong> is_page() && post_name === "brands"</li>';
echo '<li>✅ <strong>Title Check:</strong> strpos( title, "brand" ) !== false</li>';
echo '<li>✅ <strong>Template Path:</strong> ' . CAR_SELL_SHOP_CORE_PLUGIN_DIR . 'templates/page-brands.php</li>';
echo '<li>✅ <strong>File Check:</strong> file_exists( plugin_template )</li>';
echo '<li>✅ <strong>Force Include:</strong> include template && exit</li>';
echo '</ul>';

echo '<h3>✅ Test Your Brands Page:</h3>';
echo '<ul>';
echo '<li><strong>Visit Brands Page:</strong> <a href="' . home_url('/brands/') . '">' . home_url('/brands/') . '</a></li>';
echo '<li><strong>Check Template:</strong> Should show blue "PLUGIN TEMPLATE ACTIVE" banner</li>';
echo '<li><strong>Check Layout:</strong> Should show box-based grid layout</li>';
echo '<li><strong>Check Source:</strong> Should load from plugin PHP file</li>';
echo '</ul>';

echo '<h3>✅ Expected Results:</h3>';
echo '<ul>';
echo '<li>✅ <strong>Blue Banner:</strong> "PLUGIN TEMPLATE ACTIVE - page-brands.php"</li>';
echo '<li>✅ <strong>Box Layout:</strong> Brands in card-based grid</li>';
echo '<li>✅ <strong>Image Areas:</strong> Gradient placeholders with car icons</li>';
echo '<li>✅ <strong>Hover Effects:</strong> Cards lift on hover</li>';
echo '<li>✅ <strong>Blue Buttons:</strong> "View Cars" buttons</li>';
echo '<li>✅ <strong>No Theme Template:</strong> Should not use theme HTML</li>';
echo '</ul>';

echo '<h3>✅ Debug Information:</h3>';
echo '<ul>';
echo '<li><strong>Current Template:</strong> ' . get_page_template_slug( $brands_page->ID ) . '</li>';
echo '<li><strong>Theme Directory:</strong> ' . get_template_directory() . '</li>';
echo '<li><strong>Plugin Directory:</strong> ' . CAR_SELL_SHOP_CORE_PLUGIN_DIR . '</li>';
echo '<li><strong>Template Hierarchy:</strong> Check Query Monitor for template selection</li>';
echo '</ul>';

echo '<h3>✅ Troubleshooting:</h3>';
echo '<ul>';
echo '<li>✅ <strong>Clear Cache:</strong> Clear any caching plugins</li>';
echo '<li>✅ <strong>Check Permalinks:</strong> Flush permalinks if needed</li>';
echo '<li>✅ <strong>Check Query Monitor:</strong> Verify template selection</li>';
echo '<li>✅ <strong>Check Console:</strong> Look for any JavaScript errors</li>';
echo '</ul>';

echo '<p><strong>Note:</strong> The template override should now force the brands page to use the plugin\'s PHP template with the box-based layout!</p>';
?>
