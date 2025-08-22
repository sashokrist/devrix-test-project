<?php
/**
 * Test Visibility Settings - Students Plugin
 * 
 * This file tests if the visibility settings are working correctly.
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

// Get current settings
$options = get_option( 'students_options', array() );

// Test the should_display_field method
$test_fields = array(
    'student_id',
    'email', 
    'phone',
    'dob',
    'address',
    'country',
    'city',
    'class_grade',
    'status',
    'courses',
    'grade_levels'
);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Visibility Settings - Students Plugin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ccc; }
        .field-test { margin: 10px 0; padding: 10px; background: #f9f9f9; }
        .true { color: green; font-weight: bold; }
        .false { color: red; font-weight: bold; }
        .missing { color: orange; font-weight: bold; }
        h1 { color: #333; }
        h2 { color: #666; }
        .debug-info { background: #e6f3ff; padding: 10px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Test Visibility Settings - Students Plugin</h1>
    
    <div class="test-section">
        <h2>Raw Settings Data</h2>
        <div class="debug-info">
            <pre><?php print_r( $options ); ?></pre>
        </div>
    </div>

    <div class="test-section">
        <h2>Individual Field Tests</h2>
        
        <?php foreach ( $test_fields as $field ) : ?>
            <?php
            $setting_key = 'show_' . $field;
            $raw_value = isset( $options[ $setting_key ] ) ? $options[ $setting_key ] : 'MISSING';
            $should_display = Students_Sanitizer::should_display_field( $field );
            
            $raw_class = '';
            if ( $raw_value === true ) {
                $raw_class = 'true';
                $raw_display = 'TRUE';
            } elseif ( $raw_value === false ) {
                $raw_class = 'false';
                $raw_display = 'FALSE';
            } else {
                $raw_class = 'missing';
                $raw_display = 'MISSING';
            }
            
            $display_class = $should_display ? 'true' : 'false';
            $display_text = $should_display ? 'WILL SHOW' : 'WILL HIDE';
            ?>
            
            <div class="field-test">
                <strong><?php echo esc_html( ucfirst( str_replace( '_', ' ', $field ) ) ); ?>:</strong><br>
                <small>Setting Key: <?php echo esc_html( $setting_key ); ?></small><br>
                <small>Raw Value: <span class="<?php echo $raw_class; ?>"><?php echo $raw_display; ?></span></small><br>
                <small>Method Result: <span class="<?php echo $display_class; ?>"><?php echo $display_text; ?></span></small><br>
                <small>Method Call: should_display_field('<?php echo esc_html( $field ); ?>')</small>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="test-section">
        <h2>Manual Test</h2>
        <p>Let's manually test the should_display_field method:</p>
        
        <?php
        // Manual test
        echo '<div class="debug-info">';
        echo '<strong>Manual Test Results:</strong><br>';
        echo 'show_student_id exists: ' . ( isset( $options['show_student_id'] ) ? 'YES' : 'NO' ) . '<br>';
        echo 'show_student_id value: ' . ( isset( $options['show_student_id'] ) ? var_export( $options['show_student_id'], true ) : 'NOT SET' ) . '<br>';
        echo 'show_student_id === true: ' . ( isset( $options['show_student_id'] ) && $options['show_student_id'] === true ? 'YES' : 'NO' ) . '<br>';
        echo 'should_display_field("student_id"): ' . ( Students_Sanitizer::should_display_field( 'student_id' ) ? 'TRUE' : 'FALSE' ) . '<br>';
        echo '</div>';
        ?>
    </div>

    <div class="test-section">
        <h2>Actions</h2>
        <p><a href="<?php echo admin_url( 'options-general.php?page=students-settings' ); ?>">Go to Settings Page</a></p>
        <p><a href="<?php echo admin_url(); ?>">← Back to WordPress Admin</a></p>
    </div>

    <script>
        console.log('Students Plugin Test Info:');
        console.log('Settings:', <?php echo json_encode( $options ); ?>);
    </script>
</body>
</html>
