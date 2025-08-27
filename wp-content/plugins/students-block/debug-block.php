<?php
/**
 * Debug script to check block registration
 */

// Load WordPress
require_once('../../../wp-load.php');

echo "<h2>Students Block Debug</h2>";

// Check if plugin is active
$active_plugins = get_option('active_plugins');
$plugin_file = 'students-block/students-block.php';

echo "<h3>Plugin Status:</h3>";
echo "Plugin active: " . (in_array($plugin_file, $active_plugins) ? 'YES' : 'NO') . "<br>";

// Check if block is registered
$block_registry = WP_Block_Type_Registry::get_instance();
$block_name = 'students-block/students-display';

echo "<h3>Block Registration:</h3>";
if ($block_registry->is_registered($block_name)) {
    echo "✅ Block '$block_name' is registered<br>";
    $block_type = $block_registry->get_registered($block_name);
    echo "Block title: " . $block_type->title . "<br>";
    echo "Block category: " . $block_type->category . "<br>";
    echo "Block attributes: " . print_r($block_type->attributes, true) . "<br>";
} else {
    echo "❌ Block '$block_name' is NOT registered<br>";
}

// Check all registered blocks
echo "<h3>All Registered Blocks:</h3>";
$all_blocks = $block_registry->get_all_registered();
$students_blocks = array_filter($all_blocks, function($block) {
    return strpos($block->name, 'students') !== false;
});

if (empty($students_blocks)) {
    echo "No blocks with 'students' in the name found<br>";
} else {
    foreach ($students_blocks as $block) {
        echo "- " . $block->name . " (Title: " . $block->title . ")<br>";
    }
}

// Check for JavaScript errors
echo "<h3>Build Files:</h3>";
$build_dir = plugin_dir_path(__FILE__) . 'build/';
echo "Build directory: " . $build_dir . "<br>";
echo "index.js exists: " . (file_exists($build_dir . 'index.js') ? 'YES' : 'NO') . "<br>";
echo "index.css exists: " . (file_exists($build_dir . 'index.css') ? 'YES' : 'NO') . "<br>";

// Check file sizes
if (file_exists($build_dir . 'index.js')) {
    echo "index.js size: " . filesize($build_dir . 'index.js') . " bytes<br>";
}

// Delete this file after use
unlink(__FILE__);
?>
