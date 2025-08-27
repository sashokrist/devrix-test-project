<?php
/**
 * Final block test
 */

// Load WordPress
require_once('../../../wp-load.php');

echo "<h2>Students Block Final Test</h2>";

// Check if block is registered
$block_registry = WP_Block_Type_Registry::get_instance();
$block_name = 'students-block/students-display';

if ($block_registry->is_registered($block_name)) {
    echo "✅ Block '$block_name' is registered<br>";
    $block_type = $block_registry->get_registered($block_name);
    echo "Block title: " . $block_type->title . "<br>";
    echo "Block category: " . $block_type->category . "<br>";
    echo "Block supports: " . print_r($block_type->supports, true) . "<br>";
    echo "Block attributes: " . print_r($block_type->attributes, true) . "<br>";
} else {
    echo "❌ Block '$block_name' is NOT registered<br>";
}

// Check if students plugin is active
$active_plugins = get_option('active_plugins');
$students_plugin = 'students/students.php';
echo "<h3>Students Plugin:</h3>";
echo "Active: " . (in_array($students_plugin, $active_plugins) ? 'YES' : 'NO') . "<br>";

// Check if post type exists
if (post_type_exists('student')) {
    echo "✅ 'student' post type exists<br>";
} else {
    echo "❌ 'student' post type does not exist<br>";
}

echo "<h3>Test Complete!</h3>";
echo "If all checks show ✅, the block should be working in the editor.<br>";
echo "Try refreshing the Gutenberg editor page and search for 'Students Display' in the block inserter.<br>";

// Delete this file after use
unlink(__FILE__);
?>
