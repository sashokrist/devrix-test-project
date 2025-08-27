<?php
/**
 * Flush rewrite rules and clear cache after revert
 */

// Load WordPress
require_once( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-load.php' );

// Check if user is logged in and has admin privileges
if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( 'You do not have sufficient permissions to access this page.' );
}

echo '<h1>Flushing Rewrite Rules and Cache</h1>';

// Flush rewrite rules
echo '<p>Flushing rewrite rules...</p>';
flush_rewrite_rules();
echo '<p>✅ Rewrite rules flushed successfully.</p>';

// Clear object cache if available
if ( function_exists( 'wp_cache_flush' ) ) {
    echo '<p>Clearing object cache...</p>';
    wp_cache_flush();
    echo '<p>✅ Object cache cleared.</p>';
}

// Clear transients
echo '<p>Clearing transients...</p>';
delete_expired_transients();
echo '<p>✅ Expired transients cleared.</p>';

// Clear any cached options
echo '<p>Clearing cached options...</p>';
wp_cache_delete( 'alloptions', 'options' );
echo '<p>✅ Cached options cleared.</p>';

echo '<h2>✅ All done!</h2>';
echo '<p><a href="' . admin_url() . '">Go to WordPress Admin</a></p>';
echo '<p><a href="' . home_url() . '">Go to Homepage</a></p>';

// Auto-delete this file
unlink(__FILE__);
?>
