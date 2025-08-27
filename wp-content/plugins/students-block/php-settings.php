<?php
/**
 * Set PHP Upload Settings for WordPress
 * This script sets PHP settings for the current session
 */

// Load WordPress
require_once( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-load.php' );

// Check if user is logged in and has admin privileges
if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( 'You do not have sufficient permissions to access this page.' );
}

echo '<h1>Setting PHP Upload Limits</h1>';

// Set PHP settings for this session
$settings_updated = array();

// Try to set upload limits
if (ini_set('upload_max_filesize', '64M') !== false) {
    $settings_updated[] = 'upload_max_filesize = 64M';
}

if (ini_set('post_max_size', '64M') !== false) {
    $settings_updated[] = 'post_max_size = 64M';
}

if (ini_set('memory_limit', '256M') !== false) {
    $settings_updated[] = 'memory_limit = 256M';
}

if (ini_set('max_execution_time', '300') !== false) {
    $settings_updated[] = 'max_execution_time = 300';
}

if (ini_set('max_input_time', '300') !== false) {
    $settings_updated[] = 'max_input_time = 300';
}

echo '<h2>Settings Applied:</h2>';
if (!empty($settings_updated)) {
    echo '<ul>';
    foreach ($settings_updated as $setting) {
        echo '<li>✅ ' . $setting . '</li>';
    }
    echo '</ul>';
} else {
    echo '<p>❌ No settings could be updated via ini_set()</p>';
}

echo '<h2>Current PHP Settings:</h2>';
echo '<table border="1" cellpadding="5" cellspacing="0">';
echo '<tr><th>Setting</th><th>Current Value</th><th>Recommended</th></tr>';

$current_settings = array(
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'post_max_size' => ini_get('post_max_size'),
    'memory_limit' => ini_get('memory_limit'),
    'max_execution_time' => ini_get('max_execution_time'),
    'max_input_time' => ini_get('max_input_time'),
);

foreach ($current_settings as $setting => $value) {
    echo '<tr>';
    echo '<td>' . $setting . '</td>';
    echo '<td>' . $value . '</td>';
    echo '<td>64M/256M/300</td>';
    echo '</tr>';
}

echo '</table>';

echo '<h2>Permanent Solution:</h2>';
echo '<p>For permanent changes, you need to modify the PHP-FPM configuration:</p>';
echo '<ol>';
echo '<li><strong>Create a custom PHP configuration file:</strong></li>';
echo '<pre>sudo tee /etc/php/8.4/fpm/conf.d/99-wordpress-uploads.ini &lt;&lt; \'EOF\'
; WordPress Upload Settings
upload_max_filesize = 64M
post_max_size = 64M
memory_limit = 256M
max_execution_time = 300
max_input_time = 300
EOF</pre>';

echo '<li><strong>Restart PHP-FPM:</strong></li>';
echo '<pre>sudo systemctl restart php8.4-fpm</pre>';

echo '<li><strong>Restart Apache:</strong></li>';
echo '<pre>sudo systemctl restart apache2</pre>';
echo '</ol>';

echo '<h2>Alternative: Add to wp-config.php</h2>';
echo '<p>Add these lines to your wp-config.php file (before "That\'s all, stop editing!"):</p>';
echo '<pre>// PHP Upload Settings
@ini_set(\'upload_max_filesize\', \'64M\');
@ini_set(\'post_max_size\', \'64M\');
@ini_set(\'memory_limit\', \'256M\');
@ini_set(\'max_execution_time\', \'300\');
@ini_set(\'max_input_time\', \'300\');</pre>';

echo '<h2>✅ Done!</h2>';
echo '<p><a href="' . admin_url() . '">Go to WordPress Admin</a></p>';
echo '<p><a href="' . home_url() . '">Go to Homepage</a></p>';

// Auto-delete this file
unlink(__FILE__);
?>
