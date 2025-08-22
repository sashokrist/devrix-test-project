<?php
/**
 * Debug Template Override
 * 
 * Access this file directly in browser to debug template override
 */

// Load WordPress
require_once( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-load.php' );

echo '<h2>🚗 Debug Template Override</h2>';

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

echo '<h3>✅ Template Override Logic:</h3>';
echo '<ul>';
echo '<li><strong>Post Name Check:</strong> ' . ($post && $post->post_name === 'brands' ? 'PASS' : 'FAIL') . '</li>';
echo '<li><strong>Title Check:</strong> ' . ($post && strpos( strtolower( $post->post_title ), 'brand' ) !== false ? 'PASS' : 'FAIL') . '</li>';
echo '<li><strong>Is Page Check:</strong> ' . (is_page() ? 'PASS' : 'FAIL') . '</li>';
echo '</ul>';

echo '<h3>✅ Plugin Template Status:</h3>';
$plugin_template = CAR_SELL_SHOP_CORE_PLUGIN_DIR . 'templates/page-brands.php';
echo '<p><strong>Template Path:</strong> ' . $plugin_template . '</p>';
echo '<p><strong>File Exists:</strong> ' . (file_exists( $plugin_template ) ? 'YES' : 'NO') . '</p>';
if ( file_exists( $plugin_template ) ) {
    echo '<p><strong>File Size:</strong> ' . filesize( $plugin_template ) . ' bytes</p>';
    echo '<p><strong>File Permissions:</strong> ' . substr( sprintf( '%o', fileperms( $plugin_template ) ), -4 ) . '</p>';
}

echo '<h3>✅ Template Filters Applied:</h3>';
echo '<ul>';
echo '<li>✅ <strong>template_include (priority 999):</strong> force_brands_template()</li>';
echo '<li>✅ <strong>init action:</strong> force_brands_template_early()</li>';
echo '<li>✅ <strong>theme_templates:</strong> remove_brands_theme_template()</li>';
echo '</ul>';

echo '<h3>✅ Test Template Override:</h3>';
echo '<ul>';
echo '<li><strong>Visit Brands Page:</strong> <a href="' . home_url('/brands/') . '">' . home_url('/brands/') . '</a></li>';
echo '<li><strong>Check Debug Panel:</strong> Template File should show plugin path</li>';
echo '<li><strong>Check Blue Banner:</strong> Should show "PLUGIN TEMPLATE ACTIVE"</li>';
echo '<li><strong>Check Layout:</strong> Should show box-based grid</li>';
echo '</ul>';

echo '<h3>✅ Expected Debug Panel Results:</h3>';
echo '<ul>';
echo '<li>✅ <strong>Template File:</strong> Should show plugin template path</li>';
echo '<li>✅ <strong>Body Classes:</strong> Should include plugin-specific classes</li>';
echo '<li>✅ <strong>Theme:</strong> Should still show car-sell-shop</li>';
echo '<li>✅ <strong>Template Parts:</strong> Should show header.php and footer.php</li>';
echo '</ul>';

echo '<h3>✅ Troubleshooting Steps:</h3>';
echo '<ol>';
echo '<li>Clear any caching plugins</li>';
echo '<li>Flush permalinks (Settings > Permalinks > Save)</li>';
echo '<li>Check error logs for any PHP errors</li>';
echo '<li>Verify plugin is active</li>';
echo '<li>Check file permissions on plugin template</li>';
echo '</ol>';

echo '<h3>✅ Manual Template Test:</h3>';
echo '<p>If the override is not working, you can manually test the template:</p>';
echo '<p><a href="' . home_url('/brands/') . '?template=plugin" target="_blank">Test with template parameter</a></p>';

echo '<p><strong>Note:</strong> The template override should now force the brands page to use the plugin\'s PHP template. Check the debug panel to confirm the template file is being recognized.</p>';
?>
