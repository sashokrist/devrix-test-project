<?php
/**
 * Fix PHP Upload Settings for WordPress
 */

// Load WordPress
require_once( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-load.php' );

// Check if user is logged in and has admin privileges
if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( 'You do not have sufficient permissions to access this page.' );
}

echo '<h1>PHP Upload Settings Check</h1>';

// Current settings
echo '<h2>Current PHP Settings:</h2>';
echo '<table border="1" cellpadding="5" cellspacing="0">';
echo '<tr><th>Setting</th><th>Current Value</th><th>Recommended</th><th>Status</th></tr>';

$settings = array(
    'upload_max_filesize' => array('current' => ini_get('upload_max_filesize'), 'recommended' => '64M'),
    'post_max_size' => array('current' => ini_get('post_max_size'), 'recommended' => '64M'),
    'memory_limit' => array('current' => ini_get('memory_limit'), 'recommended' => '256M'),
    'max_execution_time' => array('current' => ini_get('max_execution_time'), 'recommended' => '300'),
    'max_input_time' => array('current' => ini_get('max_input_time'), 'recommended' => '300'),
);

foreach ($settings as $setting => $values) {
    $status = '✅ OK';
    if ($setting === 'upload_max_filesize' || $setting === 'post_max_size') {
        $current_mb = intval($values['current']);
        $recommended_mb = intval($values['recommended']);
        if ($current_mb < $recommended_mb) {
            $status = '❌ Too Low';
        }
    }
    
    echo '<tr>';
    echo '<td>' . $setting . '</td>';
    echo '<td>' . $values['current'] . '</td>';
    echo '<td>' . $values['recommended'] . '</td>';
    echo '<td>' . $status . '</td>';
    echo '</tr>';
}

echo '</table>';

echo '<h2>How to Fix:</h2>';
echo '<p>To increase upload limits, you need to:</p>';
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

echo '<h2>Alternative: Add to .htaccess</h2>';
echo '<p>If you cannot modify PHP configuration, add this to your .htaccess file:</p>';
echo '<pre>php_value upload_max_filesize 64M
php_value post_max_size 64M
php_value memory_limit 256M
php_value max_execution_time 300
php_value max_input_time 300</pre>';

echo '<h2>Current .htaccess Content:</h2>';
$htaccess_file = ABSPATH . '.htaccess';
if (file_exists($htaccess_file)) {
    echo '<pre>' . htmlspecialchars(file_get_contents($htaccess_file)) . '</pre>';
} else {
    echo '<p>No .htaccess file found.</p>';
}

echo '<h2>✅ Done!</h2>';
echo '<p><a href="' . admin_url() . '">Go to WordPress Admin</a></p>';
echo '<p><a href="' . home_url() . '">Go to Homepage</a></p>';

// Auto-delete this file
unlink(__FILE__);
?>
