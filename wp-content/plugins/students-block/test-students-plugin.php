<?php
/**
 * Test script to check students plugin
 */

// Load WordPress
require_once('../../../wp-load.php');

echo "<h2>Students Plugin Test</h2>";

// Check if plugin file exists
$plugin_file = 'students/students.php';
$plugin_path = WP_PLUGIN_DIR . '/' . $plugin_file;

echo "<h3>Plugin File Check:</h3>";
echo "Plugin file exists: " . (file_exists($plugin_path) ? 'YES' : 'NO') . "<br>";
echo "Plugin path: " . $plugin_path . "<br>";

// Check if plugin is active
$active_plugins = get_option('active_plugins');
echo "<h3>Plugin Activation Check:</h3>";
echo "Plugin active: " . (in_array($plugin_file, $active_plugins) ? 'YES' : 'NO') . "<br>";

// Try to include the plugin file
echo "<h3>Plugin Loading Test:</h3>";
try {
    include_once($plugin_path);
    echo "✅ Plugin file loaded successfully<br>";
    
    // Check if the main class exists
    if (class_exists('Students_Plugin')) {
        echo "✅ Students_Plugin class found<br>";
    } else {
        echo "❌ Students_Plugin class not found<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error loading plugin: " . $e->getMessage() . "<br>";
}

// Check for any PHP errors
echo "<h3>PHP Error Check:</h3>";
$error_log = ini_get('error_log');
if ($error_log && file_exists($error_log)) {
    $errors = file_get_contents($error_log);
    if (strpos($errors, 'students') !== false) {
        echo "⚠️ Found errors related to students plugin in error log<br>";
    } else {
        echo "✅ No errors found in error log<br>";
    }
} else {
    echo "ℹ️ Error log not available<br>";
}

echo "<h3>Plugin Status Summary:</h3>";
if (file_exists($plugin_path) && in_array($plugin_file, $active_plugins)) {
    echo "✅ Plugin appears to be properly installed and activated<br>";
} else {
    echo "❌ Plugin has issues - check file existence and activation<br>";
}

// Delete this file after use
unlink(__FILE__);
?>
