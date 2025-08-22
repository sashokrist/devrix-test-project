<?php
/**
 * Template Test - Students Plugin
 * 
 * This file tests which template is being used for student pages.
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Only allow access to administrators
if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( 'Access denied.' );
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Template Test - Students Plugin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ccc; }
        .warning { background: #fff3cd; color: #856404; padding: 10px; }
        .success { background: #d4edda; color: #155724; padding: 10px; }
        h1 { color: #333; }
    </style>
</head>
<body>
    <h1>Template Test - Students Plugin</h1>
    
    <div class="test-section">
        <h2>Template Paths</h2>
        
        <?php
        // Check for theme templates
        $theme_single = locate_template( array( 'single-student.php' ) );
        $theme_archive = locate_template( array( 'archive-student.php' ) );
        
        // Check for plugin templates
        $plugin_single = STUDENTS_PLUGIN_DIR . 'templates/single-student.php';
        $plugin_archive = STUDENTS_PLUGIN_DIR . 'templates/archive-student.php';
        
        $plugin_single_exists = file_exists( $plugin_single );
        $plugin_archive_exists = file_exists( $plugin_archive );
        ?>
        
        <h3>Single Student Template:</h3>
        <?php if ( $theme_single ) : ?>
            <div class="warning">
                <strong>Theme template found:</strong> <?php echo esc_html( $theme_single ); ?><br>
                <em>This will override the plugin template!</em>
            </div>
        <?php else : ?>
            <div class="success">
                <strong>No theme template found.</strong><br>
                Plugin template will be used: <?php echo esc_html( $plugin_single ); ?>
            </div>
        <?php endif; ?>
        
        <h3>Archive Student Template:</h3>
        <?php if ( $theme_archive ) : ?>
            <div class="warning">
                <strong>Theme template found:</strong> <?php echo esc_html( $theme_archive ); ?><br>
                <em>This will override the plugin template!</em>
            </div>
        <?php else : ?>
            <div class="success">
                <strong>No theme template found.</strong><br>
                Plugin template will be used: <?php echo esc_html( $plugin_archive ); ?>
            </div>
        <?php endif; ?>
        
        <h3>Plugin Template Status:</h3>
        <p>Single template exists: <?php echo $plugin_single_exists ? 'YES' : 'NO'; ?></p>
        <p>Archive template exists: <?php echo $plugin_archive_exists ? 'YES' : 'NO'; ?></p>
    </div>

    <div class="test-section">
        <h2>Current Settings Test</h2>
        <?php
        $options = get_option( 'students_options', array() );
        echo '<p><strong>Settings:</strong></p>';
        echo '<pre>' . print_r( $options, true ) . '</pre>';
        
        // Test visibility
        echo '<p><strong>Visibility Test:</strong></p>';
        $test_fields = array( 'student_id', 'email', 'phone', 'dob', 'address', 'country', 'city', 'class_grade', 'status', 'courses', 'grade_levels' );
        foreach ( $test_fields as $field ) {
            $should_display = Students_Sanitizer::should_display_field( $field );
            echo '<p>' . esc_html( ucfirst( str_replace( '_', ' ', $field ) ) ) . ': ' . 
                 ( $should_display ? '<span style="color: green;">WILL SHOW</span>' : '<span style="color: red;">WILL HIDE</span>' ) . '</p>';
        }
        ?>
    </div>

    <div class="test-section">
        <h2>Actions</h2>
        <p><a href="<?php echo home_url( '/students/' ); ?>">Go to Students Archive Page</a></p>
        <p><a href="<?php echo admin_url( 'edit.php?post_type=student' ); ?>">Go to Students Admin</a></p>
        <p><a href="<?php echo admin_url(); ?>">← Back to WordPress Admin</a></p>
    </div>

    <?php if ( $theme_single || $theme_archive ) : ?>
    <div class="test-section">
        <div class="warning">
            <h3>⚠️ IMPORTANT: Theme Template Override Detected</h3>
            <p>Your theme has custom templates for students that are overriding the plugin templates. 
            This means the visibility settings won't work because the theme templates don't include the visibility checks.</p>
            
            <h4>Solutions:</h4>
            <ol>
                <li><strong>Rename theme templates:</strong> Rename the theme's student templates to disable them</li>
                <li><strong>Update theme templates:</strong> Add visibility checks to your theme templates</li>
                <li><strong>Use a different theme:</strong> Switch to a theme without custom student templates</li>
            </ol>
            
            <h4>Theme templates to check:</h4>
            <ul>
                <?php if ( $theme_single ) : ?>
                    <li><?php echo esc_html( $theme_single ); ?></li>
                <?php endif; ?>
                <?php if ( $theme_archive ) : ?>
                    <li><?php echo esc_html( $theme_archive ); ?></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>
</body>
</html>
