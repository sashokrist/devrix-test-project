<?php
/**
 * Test XSS Protection
 * 
 * This file demonstrates how the Students plugin protects against XSS attacks
 * by properly sanitizing and escaping user input.
 * 
 * WARNING: This file is for testing purposes only and should be removed in production.
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Only allow access to administrators
if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( 'Access denied.' );
}

// Test malicious input
$malicious_inputs = array(
    'script_attack' => '<script>alert("XSS ATTACK");</script>',
    'img_attack' => '<img src="x" onerror="alert(\'XSS\')">',
    'javascript_attack' => 'javascript:alert("XSS")',
    'html_entities' => '&lt;script&gt;alert("XSS")&lt;/script&gt;',
    'mixed_content' => 'Hello <script>alert("XSS")</script> World',
    'email_attack' => 'test@example.com<script>alert("XSS")</script>',
    'phone_attack' => '123-456-7890<script>alert("XSS")</script>',
    'address_attack' => '123 Main St<script>alert("XSS")</script>',
);

// Load the sanitizer class
require_once plugin_dir_path( __FILE__ ) . 'includes/class-students-sanitizer.php';

?>
<!DOCTYPE html>
<html>
<head>
    <title>XSS Protection Test - Students Plugin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-case { margin: 20px 0; padding: 15px; border: 1px solid #ccc; }
        .original { background: #ffe6e6; padding: 10px; margin: 10px 0; }
        .sanitized { background: #e6ffe6; padding: 10px; margin: 10px 0; }
        .escaped { background: #e6e6ff; padding: 10px; margin: 10px 0; }
        h1 { color: #333; }
        h2 { color: #666; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; margin: 20px 0; }
    </style>
</head>
<body>
    <h1>XSS Protection Test - Students Plugin</h1>
    
    <div class="warning">
        <strong>Warning:</strong> This page demonstrates XSS protection. The malicious scripts below should NOT execute.
        If you see any alert dialogs, there's a security issue that needs to be fixed.
    </div>

    <h2>Testing XSS Protection Methods</h2>

    <?php foreach ( $malicious_inputs as $test_name => $malicious_input ) : ?>
        <div class="test-case">
            <h3>Test: <?php echo esc_html( $test_name ); ?></h3>
            
            <div class="original">
                <strong>Original Malicious Input:</strong><br>
                <code><?php echo esc_html( $malicious_input ); ?></code>
            </div>

            <div class="sanitized">
                <strong>After Sanitization:</strong><br>
                <code><?php echo esc_html( Students_Sanitizer::validate_for_display( $malicious_input, 'text' ) ); ?></code>
            </div>

            <div class="escaped">
                <strong>After Escaping for Display:</strong><br>
                <code><?php echo Students_Sanitizer::display_meta_safely( $malicious_input, 'text' ); ?></code>
            </div>

            <div class="escaped">
                <strong>Rendered Output (should be safe):</strong><br>
                <?php echo Students_Sanitizer::display_meta_safely( $malicious_input, 'text' ); ?>
            </div>
        </div>
    <?php endforeach; ?>

    <h2>Field-Specific Sanitization Tests</h2>

    <?php
    $field_tests = array(
        'email' => array(
            'input' => 'test@example.com<script>alert("XSS")</script>',
            'type' => 'email'
        ),
        'phone' => array(
            'input' => '123-456-7890<script>alert("XSS")</script>',
            'type' => 'phone'
        ),
        'address' => array(
            'input' => '123 Main St<script>alert("XSS")</script>',
            'type' => 'address'
        ),
        'country' => array(
            'input' => 'United States<script>alert("XSS")</script>',
            'type' => 'country'
        ),
        'city' => array(
            'input' => 'New York<script>alert("XSS")</script>',
            'type' => 'city'
        ),
        'class_grade' => array(
            'input' => 'Grade 10<script>alert("XSS")</script>',
            'type' => 'class_grade'
        ),
    );
    ?>

    <?php foreach ( $field_tests as $field_name => $test ) : ?>
        <div class="test-case">
            <h3>Field: <?php echo esc_html( $field_name ); ?></h3>
            
            <div class="original">
                <strong>Original Input:</strong><br>
                <code><?php echo esc_html( $test['input'] ); ?></code>
            </div>

            <div class="sanitized">
                <strong>After Field-Specific Sanitization:</strong><br>
                <code><?php echo esc_html( Students_Sanitizer::validate_for_display( $test['input'], $test['type'] ) ); ?></code>
            </div>

            <div class="escaped">
                <strong>Safe Display Output:</strong><br>
                <?php echo Students_Sanitizer::display_meta_safely( $test['input'], $test['type'] ); ?>
            </div>
        </div>
    <?php endforeach; ?>

    <h2>How to Test in WordPress Admin</h2>
    <ol>
        <li>Go to WordPress Admin → Students → Add New Student</li>
        <li>Enter the malicious script in any of the meta fields: <code>&lt;script&gt;alert('XSS ATTACK');&lt;/script&gt;</code></li>
        <li>Save the student</li>
        <li>View the student on the front end - the script should be safely escaped and not execute</li>
        <li>Check the student profile page - all metadata should be displayed safely</li>
    </ol>

    <h2>Security Features Implemented</h2>
    <ul>
        <li><strong>Input Sanitization:</strong> All user input is sanitized using field-specific methods</li>
        <li><strong>Output Escaping:</strong> All output is escaped using WordPress escaping functions</li>
        <li><strong>XSS Prevention:</strong> Script tags and other malicious content are stripped or escaped</li>
        <li><strong>Context-Aware:</strong> Different sanitization rules for different field types</li>
        <li><strong>Archive Filtering:</strong> Only active students are shown on archive pages</li>
    </ul>

    <p><a href="<?php echo admin_url(); ?>">← Back to WordPress Admin</a></p>
</body>
</html>
