<?php
/**
 * Simple page to get WordPress nonce for API testing
 * Access this page while logged in as administrator
 */

// Load WordPress
require_once dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-load.php';

// Check if user is logged in
if ( ! is_user_logged_in() ) {
    echo "<h2>❌ Authentication Required</h2>";
    echo "<p>Please <a href='/wp-admin/'>log in</a> as an administrator first.</p>";
    exit;
}

// Check if user has admin privileges
if ( ! current_user_can( 'manage_options' ) ) {
    echo "<h2>❌ Administrator Privileges Required</h2>";
    echo "<p>You need administrator privileges to access the API.</p>";
    exit;
}

// Get current user info
$current_user = wp_get_current_user();

// Create nonce
$nonce = wp_create_nonce( 'wp_rest' );

?>
<!DOCTYPE html>
<html>
<head>
    <title>Get WordPress Nonce for API Testing</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .container { max-width: 800px; margin: 0 auto; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .code { background: #f8f9fa; border: 1px solid #e9ecef; padding: 15px; border-radius: 5px; font-family: monospace; margin: 10px 0; }
        .copy-btn { background: #007cba; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer; }
        .copy-btn:hover { background: #005a87; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 WordPress Nonce for API Testing</h1>
        
        <div class="success">
            <h3>✅ Authentication Successful</h3>
            <p><strong>User:</strong> <?php echo esc_html( $current_user->user_login ); ?></p>
            <p><strong>Role:</strong> <?php echo esc_html( implode( ', ', $current_user->roles ) ); ?></p>
        </div>

        <div class="info">
            <h3>📋 Your Nonce</h3>
            <p>Use this nonce in your API requests:</p>
            <div class="code" id="nonce-display">
                <?php echo esc_html( $nonce ); ?>
                <button class="copy-btn" onclick="copyNonce()">Copy</button>
            </div>
        </div>

        <div class="info">
            <h3>🧪 Test Commands</h3>
            <p>Here are some test commands you can use with your nonce:</p>
            
            <h4>Create Student:</h4>
            <div class="code">
curl -X POST "http://localhost/devrix-test-project/?rest_route=/students/v1/students" \
  -H "Content-Type: application/json" \
  -H "X-WP-Nonce: <?php echo esc_html( $nonce ); ?>" \
  -d '{
    "title": "Test Student",
    "student_id": "TEST_<?php echo time(); ?>",
    "student_email": "test@example.com",
    "student_is_active": "active"
  }'
            </div>

            <h4>Update Student (replace 123 with actual ID):</h4>
            <div class="code">
curl -X PUT "http://localhost/devrix-test-project/?rest_route=/students/v1/students/123" \
  -H "Content-Type: application/json" \
  -H "X-WP-Nonce: <?php echo esc_html( $nonce ); ?>" \
  -d '{
    "title": "Updated Student",
    "student_is_active": "inactive"
  }'
            </div>

            <h4>Delete Student (replace 123 with actual ID):</h4>
            <div class="code">
curl -X DELETE "http://localhost/devrix-test-project/?rest_route=/students/v1/students/123" \
  -H "X-WP-Nonce: <?php echo esc_html( $nonce ); ?>"
            </div>
        </div>

        <div class="info">
            <h3>⚠️ Important Notes</h3>
            <ul>
                <li><strong>Nonce Expiry:</strong> Nonces expire after 24 hours</li>
                <li><strong>Security:</strong> Keep your nonce private and don't share it</li>
                <li><strong>Testing:</strong> Use the test script for easier testing: <a href="test-authenticated-api.php">test-authenticated-api.php</a></li>
            </ul>
        </div>
    </div>

    <script>
        function copyNonce() {
            const nonceText = '<?php echo esc_js( $nonce ); ?>';
            navigator.clipboard.writeText(nonceText).then(function() {
                alert('Nonce copied to clipboard!');
            }).catch(function(err) {
                console.error('Could not copy text: ', err);
            });
        }
    </script>
</body>
</html>
