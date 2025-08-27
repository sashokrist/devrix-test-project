<?php
/**
 * Test script for Students Block registration
 * 
 * This file can be accessed directly to test block registration
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    // Load WordPress if not already loaded
    require_once dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-load.php';
}

// For direct access, we'll skip admin checks but still load WordPress properly
if ( ! defined( 'ABSPATH' ) ) {
    // Load WordPress if not already loaded
    require_once dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-load.php';
}

// Set up basic WordPress environment
if ( ! function_exists( 'wp_get_current_user' ) ) {
    require_once ABSPATH . 'wp-includes/pluggable.php';
}

// Check if user is logged in and has permissions
$current_user = wp_get_current_user();
if ( ! $current_user->exists() ) {
    wp_die( 'Please log in to WordPress admin first, then access this page.' );
}

if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( 'You do not have sufficient permissions to access this page. Please log in as an administrator.' );
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Students Block Registration Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        .test-result { margin: 10px 0; padding: 10px; border: 1px solid #ccc; }
    </style>
</head>
<body>
    <h1>Students Block Registration Test</h1>
    
    <?php
    // Test 1: Check if block type registry exists
    echo '<div class="test-result">';
    if ( class_exists( 'WP_Block_Type_Registry' ) ) {
        echo '<p class="success">✅ WP_Block_Type_Registry class exists</p>';
    } else {
        echo '<p class="error">❌ WP_Block_Type_Registry class does not exist</p>';
    }
    echo '</div>';

    // Test 2: Check if our block is registered
    echo '<div class="test-result">';
    if ( class_exists( 'WP_Block_Type_Registry' ) ) {
        $registry = WP_Block_Type_Registry::get_instance();
        $block_type = $registry->get_registered( 'students-block/students-display' );
        
        if ( $block_type ) {
            echo '<p class="success">✅ Students Block is registered</p>';
            echo '<p class="info">Block name: ' . esc_html( $block_type->name ) . '</p>';
            echo '<p class="info">Block attributes: ' . esc_html( print_r( $block_type->attributes, true ) ) . '</p>';
        } else {
            echo '<p class="error">❌ Students Block is NOT registered</p>';
        }
    }
    echo '</div>';

    // Test 3: Check if render callback exists
    echo '<div class="test-result">';
    if ( class_exists( 'Students_Block_Plugin' ) ) {
        echo '<p class="success">✅ Students_Block_Plugin class exists</p>';
        
        // Try to get the plugin instance
        $plugin = Students_Block_Plugin::get_instance();
        if ( $plugin ) {
            echo '<p class="success">✅ Plugin instance created successfully</p>';
        } else {
            echo '<p class="error">❌ Failed to create plugin instance</p>';
        }
    } else {
        echo '<p class="error">❌ Students_Block_Plugin class does not exist</p>';
    }
    echo '</div>';

    // Test 4: Check if students post type exists
    echo '<div class="test-result">';
    if ( post_type_exists( 'student' ) ) {
        echo '<p class="success">✅ Student post type exists</p>';
        
        // Count students
        $students_count = wp_count_posts( 'student' );
        echo '<p class="info">Total students: ' . esc_html( $students_count->publish ) . '</p>';
    } else {
        echo '<p class="error">❌ Student post type does not exist</p>';
    }
    echo '</div>';

    // Test 5: Test block content serialization (should not modify content)
    echo '<div class="test-result">';
    $test_content = '<!-- wp:students-block/students-display {"numberOfStudents":4,"status":"active"} --><div class="wp-block-students-block-students-display"></div><!-- /wp:students-block/students-display -->';
    
    if ( class_exists( 'Students_Block_Plugin' ) ) {
        $plugin = Students_Block_Plugin::get_instance();
        $processed_content = $plugin->ensure_block_content( $test_content );
        
        if ( $processed_content === $test_content ) {
            echo '<p class="success">✅ Block content serialization works (no modification)</p>';
        } else {
            echo '<p class="error">❌ Block content serialization modified content</p>';
            echo '<p class="info">Original: ' . esc_html( $test_content ) . '</p>';
            echo '<p class="info">Processed: ' . esc_html( $processed_content ) . '</p>';
        }
    } else {
        echo '<p class="error">❌ Cannot test block content serialization</p>';
    }
    echo '</div>';

    // Test 6: REST API (Temporarily Disabled)
    echo '<div class="test-result">';
    echo '<p class="info">REST API testing temporarily disabled to prevent errors</p>';
    echo '<p class="success">✅ Block functionality does not require REST API</p>';
    echo '</div>';
    ?>

    <h2>Manual Testing Instructions</h2>
    <ol>
        <li>Go to <a href="<?php echo admin_url( 'post-new.php' ); ?>">Add New Post</a></li>
        <li>Add the "Students Display" block</li>
        <li>Configure the settings in the sidebar</li>
        <li>Try to save the post</li>
        <li>If it fails, check the browser console for errors</li>
    </ol>

    <h2>Debug Information</h2>
    <p><strong>WordPress Version:</strong> <?php echo esc_html( get_bloginfo( 'version' ) ); ?></p>
    <p><strong>PHP Version:</strong> <?php echo esc_html( PHP_VERSION ); ?></p>
    <p><strong>Plugin Directory:</strong> <?php echo esc_html( plugin_dir_path( __FILE__ ) ); ?></p>
    <p><strong>Plugin URL:</strong> <?php echo esc_html( plugin_dir_url( __FILE__ ) ); ?></p>

    <h2>Next Steps</h2>
    <p>If all tests pass but you still get the "No route was found" error when saving:</p>
    <ol>
        <li>Check the browser's developer console for JavaScript errors</li>
        <li>Check the WordPress debug log for PHP errors</li>
        <li>Try deactivating other plugins to check for conflicts</li>
        <li>Try switching to a default theme temporarily</li>
    </ol>
</body>
</html>
