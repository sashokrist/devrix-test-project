<?php
/**
 * Debug ACF Block Registration
 */

// Load WordPress
require_once( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-load.php' );

echo "<h1>ACF Block Debug</h1>";

// Check if ACF is active
echo "<h2>1. ACF Plugin Status</h2>";
if ( function_exists( 'acf' ) ) {
    echo "✅ ACF is active<br>";
    echo "ACF Version: " . ACF_VERSION . "<br>";
} else {
    echo "❌ ACF is NOT active<br>";
}

// Check if ACF Pro functions exist
echo "<h2>2. ACF Pro Functions</h2>";
if ( function_exists( 'acf_register_block_type' ) ) {
    echo "✅ acf_register_block_type function exists<br>";
} else {
    echo "❌ acf_register_block_type function does NOT exist<br>";
}

if ( function_exists( 'acf_get_field_groups' ) ) {
    echo "✅ acf_get_field_groups function exists<br>";
} else {
    echo "❌ acf_get_field_groups function does NOT exist<br>";
}

// Check if our block registration function exists
echo "<h2>3. Our Block Registration</h2>";
if ( function_exists( 'car_sell_shop_register_acf_blocks' ) ) {
    echo "✅ car_sell_shop_register_acf_blocks function exists<br>";
} else {
    echo "❌ car_sell_shop_register_acf_blocks function does NOT exist<br>";
}

// Check registered blocks
echo "<h2>4. Registered Blocks</h2>";
$blocks = WP_Block_Type_Registry::get_instance()->get_all_registered();
echo "Total registered blocks: " . count( $blocks ) . "<br>";

foreach ( $blocks as $block_name => $block ) {
    if ( strpos( $block_name, 'acf/' ) === 0 ) {
        echo "ACF Block: " . $block_name . " - " . $block->title . "<br>";
    }
}

// Check if our specific block is registered
if ( isset( $blocks['acf/students-display-block'] ) ) {
    echo "✅ Our block 'acf/students-display-block' is registered<br>";
} else {
    echo "❌ Our block 'acf/students-display-block' is NOT registered<br>";
}

// Check theme functions
echo "<h2>5. Theme Functions</h2>";
$theme_functions = file_get_contents( get_template_directory() . '/functions.php' );
if ( strpos( $theme_functions, 'car_sell_shop_register_acf_blocks' ) !== false ) {
    echo "✅ Block registration function found in theme functions.php<br>";
} else {
    echo "❌ Block registration function NOT found in theme functions.php<br>";
}

if ( strpos( $theme_functions, 'acf/init' ) !== false ) {
    echo "✅ ACF init hook found in theme functions.php<br>";
} else {
    echo "❌ ACF init hook NOT found in theme functions.php<br>";
}

// Check template file
echo "<h2>6. Template File</h2>";
$template_file = get_template_directory() . '/template-parts/blocks/students-display-block.php';
if ( file_exists( $template_file ) ) {
    echo "✅ Template file exists: " . $template_file . "<br>";
} else {
    echo "❌ Template file does NOT exist: " . $template_file . "<br>";
}

// Try to manually register the block
echo "<h2>7. Manual Block Registration Test</h2>";
if ( function_exists( 'acf_register_block_type' ) ) {
    try {
        acf_register_block_type( array(
            'name'              => 'test-students-display-block',
            'title'             => 'Test Students Display',
            'description'       => 'Test block registration',
            'render_template'   => 'template-parts/blocks/students-display-block.php',
            'category'          => 'widgets',
            'icon'              => 'groups',
        ) );
        echo "✅ Manual block registration successful<br>";
    } catch ( Exception $e ) {
        echo "❌ Manual block registration failed: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ Cannot test manual registration - acf_register_block_type not available<br>";
}

echo "<h2>8. WordPress Version</h2>";
echo "WordPress Version: " . get_bloginfo( 'version' ) . "<br>";

echo "<h2>9. Active Plugins</h2>";
$active_plugins = get_option( 'active_plugins' );
foreach ( $active_plugins as $plugin ) {
    if ( strpos( $plugin, 'acf' ) !== false ) {
        echo "ACF Plugin: " . $plugin . "<br>";
    }
}

echo "<h2>10. Theme Info</h2>";
echo "Active Theme: " . get_template() . "<br>";
echo "Theme Directory: " . get_template_directory() . "<br>";
