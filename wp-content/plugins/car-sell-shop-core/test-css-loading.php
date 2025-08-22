<?php
/**
 * Test CSS Loading
 * 
 * Access this file directly in browser to test CSS loading
 */

// Load WordPress
require_once( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-load.php' );

echo '<h2>🎨 Test CSS Loading</h2>';

echo '<h3>✅ Current Page Information:</h3>';
global $post;
if ( $post ) {
    echo '<p><strong>Post ID:</strong> ' . $post->ID . '</p>';
    echo '<p><strong>Post Type:</strong> ' . $post->post_type . '</p>';
    echo '<p><strong>Post Name:</strong> ' . $post->post_name . '</p>';
    echo '<p><strong>Post Title:</strong> ' . esc_html( $post->post_title ) . '</p>';
    echo '<p><strong>Is Page:</strong> ' . (is_page() ? 'YES' : 'NO') . '</p>';
    echo '<p><strong>Is Brands Page:</strong> ' . (($post->post_name === 'brands' || strpos( strtolower( $post->post_title ), 'brand' ) !== false) ? 'YES' : 'NO') . '</p>';
} else {
    echo '<p>❌ No post object found</p>';
}

echo '<h3>✅ CSS Enqueue Logic:</h3>';
echo '<ul>';
echo '<li><strong>Is Car Archive:</strong> ' . (is_post_type_archive( 'car' ) ? 'YES' : 'NO') . '</li>';
echo '<li><strong>Is Car Singular:</strong> ' . (is_singular( 'car' ) ? 'YES' : 'NO') . '</li>';
echo '<li><strong>Is Brand Taxonomy:</strong> ' . (is_tax( 'brand' ) ? 'YES' : 'NO') . '</li>';
echo '<li><strong>Is Brands Page:</strong> ' . (($post && is_page() && ( $post->post_name === 'brands' || strpos( strtolower( $post->post_title ), 'brand' ) !== false )) ? 'YES' : 'NO') . '</li>';
echo '</ul>';

echo '<h3>✅ CSS File Status:</h3>';
$css_file = CAR_SELL_SHOP_CORE_PLUGIN_DIR . 'assets/css/car-styles.css';
echo '<p><strong>CSS File Path:</strong> ' . $css_file . '</p>';
echo '<p><strong>File Exists:</strong> ' . (file_exists( $css_file ) ? 'YES' : 'NO') . '</p>';
if ( file_exists( $css_file ) ) {
    echo '<p><strong>File Size:</strong> ' . filesize( $css_file ) . ' bytes</p>';
    echo '<p><strong>File Permissions:</strong> ' . substr( sprintf( '%o', fileperms( $css_file ) ), -4 ) . '</p>';
}

echo '<h3>✅ CSS URL:</h3>';
$css_url = CAR_SELL_SHOP_CORE_PLUGIN_URL . 'assets/css/car-styles.css';
echo '<p><strong>CSS URL:</strong> <a href="' . $css_url . '" target="_blank">' . $css_url . '</a></p>';

echo '<h3>✅ Test CSS Loading:</h3>';
echo '<p>Visit the brands page and check if the CSS is loaded:</p>';
echo '<ul>';
echo '<li><strong>Visit:</strong> <a href="' . home_url('/brands/') . '">' . home_url('/brands/') . '</a></li>';
echo '<li><strong>Check Browser DevTools:</strong> Network tab should show car-styles.css</li>';
echo '<li><strong>Check Page Source:</strong> Should include link to car-styles.css</li>';
echo '</ul>';

echo '<h3>✅ Manual CSS Test:</h3>';
echo '<p>If CSS is not loading, you can manually test the styles:</p>';
echo '<div style="background: #e7f3ff; border: 2px solid #0073aa; padding: 10px; margin: 10px 0; border-radius: 5px; text-align: center;">';
echo '<strong>✅ CSS Test</strong> - If you see this styled box, CSS is working';
echo '</div>';

echo '<h3>✅ Expected Box Layout:</h3>';
echo '<p>After CSS loads, you should see:</p>';
echo '<ul>';
echo '<li>✅ <strong>Grid Layout:</strong> Brands in a responsive grid</li>';
echo '<li>✅ <strong>Card Design:</strong> Each brand in a white card with shadow</li>';
echo '<li>✅ <strong>Gradient Images:</strong> Purple to blue gradient placeholders</li>';
echo '<li>✅ <strong>Hover Effects:</strong> Cards lift on hover</li>';
echo '<li>✅ <strong>Consistent Styling:</strong> Matches Cars page exactly</li>';
echo '</ul>';

echo '<p><strong>Note:</strong> The CSS should now load on the brands page. If it\'s still not working, check browser cache or try a hard refresh (Ctrl+F5).</p>';
?>
