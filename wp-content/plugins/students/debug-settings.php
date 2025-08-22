<?php
/**
 * Debug Settings - Students Plugin
 * 
 * This file helps debug the metadata visibility settings.
 * 
 * WARNING: This file is for debugging purposes only and should be removed in production.
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

?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug Settings - Students Plugin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .debug-section { margin: 20px 0; padding: 15px; border: 1px solid #ccc; }
        .setting-item { margin: 10px 0; padding: 10px; background: #f9f9f9; }
        .true { color: green; font-weight: bold; }
        .false { color: red; font-weight: bold; }
        .missing { color: orange; font-weight: bold; }
        h1 { color: #333; }
        h2 { color: #666; }
    </style>
</head>
<body>
    <h1>Debug Settings - Students Plugin</h1>
    
    <div class="debug-section">
        <h2>Current Settings</h2>
        <pre><?php print_r( $options ); ?></pre>
    </div>

    <div class="debug-section">
        <h2>Metadata Visibility Settings</h2>
        
        <?php
        $metadata_fields = array(
            'show_student_id' => 'Student ID',
            'show_email' => 'Email',
            'show_phone' => 'Phone',
            'show_dob' => 'Date of Birth',
            'show_address' => 'Address',
            'show_country' => 'Country',
            'show_city' => 'City',
            'show_class_grade' => 'Class/Grade',
            'show_status' => 'Status',
            'show_courses' => 'Courses',
            'show_grade_levels' => 'Grade Levels'
        );

        foreach ( $metadata_fields as $setting_key => $field_name ) :
            $value = isset( $options[ $setting_key ] ) ? $options[ $setting_key ] : 'MISSING';
            $class = '';
            
            if ( $value === true ) {
                $class = 'true';
                $display_value = 'TRUE (Will Show)';
            } elseif ( $value === false ) {
                $class = 'false';
                $display_value = 'FALSE (Will Hide)';
            } else {
                $class = 'missing';
                $display_value = 'MISSING (Will Show - Default)';
            }
        ?>
            <div class="setting-item">
                <strong><?php echo esc_html( $field_name ); ?>:</strong>
                <span class="<?php echo $class; ?>"><?php echo $display_value; ?></span>
                <br>
                <small>Setting Key: <?php echo esc_html( $setting_key ); ?></small>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="debug-section">
        <h2>Test should_display_field() Method</h2>
        
        <?php
        foreach ( $metadata_fields as $setting_key => $field_name ) :
            $field_name_short = str_replace( 'show_', '', $setting_key );
            $should_display = Students_Sanitizer::should_display_field( $field_name_short );
            $class = $should_display ? 'true' : 'false';
        ?>
            <div class="setting-item">
                <strong><?php echo esc_html( $field_name ); ?>:</strong>
                <span class="<?php echo $class; ?>">
                    <?php echo $should_display ? 'WILL DISPLAY' : 'WILL HIDE'; ?>
                </span>
                <br>
                <small>Method: should_display_field('<?php echo esc_html( $field_name_short ); ?>')</small>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="debug-section">
        <h2>Actions</h2>
        <p><a href="<?php echo admin_url( 'options-general.php?page=students-settings' ); ?>">Go to Settings Page</a></p>
        <p><a href="<?php echo admin_url(); ?>">← Back to WordPress Admin</a></p>
    </div>

    <script>
        // Add some JavaScript to help with debugging
        console.log('Students Plugin Debug Info:');
        console.log('Settings:', <?php echo json_encode( $options ); ?>);
    </script>
</body>
</html>
