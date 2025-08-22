<?php
/**
 * Force Test Settings - Students Plugin
 * 
 * This file forces sets the settings to test if they work.
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Only allow access to administrators
if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( 'Access denied.' );
}

// Load the sanitizer class
require_once plugin_dir_path( __FILE__ ) . 'includes/class-students-sanitizer.php';

// Force set settings to test
$test_settings = array(
    'students_per_page' => 10,
    'enable_search' => true,
    'show_email' => false,
    'show_student_id' => true,
    'show_phone' => false,
    'show_dob' => false,
    'show_address' => false,
    'show_country' => false,
    'show_city' => false,
    'show_class_grade' => false,
    'show_status' => false,
    'show_courses' => false,
    'show_grade_levels' => false,
);

// Update the settings
update_option( 'students_options', $test_settings );

// Get the updated settings
$options = get_option( 'students_options', array() );

?>
<!DOCTYPE html>
<html>
<head>
    <title>Force Test Settings - Students Plugin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ccc; }
        .success { background: #d4edda; color: #155724; padding: 10px; }
        h1 { color: #333; }
    </style>
</head>
<body>
    <h1>Force Test Settings - Students Plugin</h1>
    
    <div class="test-section">
        <div class="success">
            <strong>Settings have been force-set!</strong><br>
            Only "Student ID" should be visible now.
        </div>
    </div>

    <div class="test-section">
        <h2>Current Settings</h2>
        <pre><?php print_r( $options ); ?></pre>
    </div>

    <div class="test-section">
        <h2>Test Results</h2>
        <?php
        $test_fields = array( 'student_id', 'email', 'phone', 'dob', 'address', 'country', 'city', 'class_grade', 'status', 'courses', 'grade_levels' );
        
        foreach ( $test_fields as $field ) :
            $should_display = Students_Sanitizer::should_display_field( $field );
        ?>
            <p><strong><?php echo esc_html( ucfirst( str_replace( '_', ' ', $field ) ) ); ?>:</strong> 
                <?php echo $should_display ? '<span style="color: green;">WILL SHOW</span>' : '<span style="color: red;">WILL HIDE</span>'; ?>
            </p>
        <?php endforeach; ?>
    </div>

    <div class="test-section">
        <h2>Next Steps</h2>
        <ol>
            <li>Go to your Students page</li>
            <li>You should only see Student ID</li>
            <li>All other fields should be hidden</li>
            <li>If this works, the issue was with settings saving</li>
            <li>If this doesn't work, there's a template or caching issue</li>
        </ol>
    </div>

    <div class="test-section">
        <h2>Actions</h2>
        <p><a href="<?php echo home_url( '/students/' ); ?>">Go to Students Page</a></p>
        <p><a href="<?php echo admin_url(); ?>">← Back to WordPress Admin</a></p>
    </div>
</body>
</html>
