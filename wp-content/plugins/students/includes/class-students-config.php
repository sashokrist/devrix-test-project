<?php
/**
 * Students Plugin Configuration
 *
 * Centralized configuration management for the Students plugin
 *
 * @package Students
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Students Configuration Class
 *
 * @since 1.0.0
 */
class Students_Config {

    /**
     * Plugin version
     */
    const VERSION = '1.0.0';

    /**
     * Plugin text domain
     */
    const TEXT_DOMAIN = 'students';

    /**
     * Post type name
     */
    const POST_TYPE = 'student';

    /**
     * Taxonomy names
     */
    const TAXONOMY_COURSE = 'course';
    const TAXONOMY_GRADE_LEVEL = 'grade_level';

    /**
     * Rewrite rules configuration
     */
    const REWRITE_RULES = array(
        'students/([^/]+)/?' => 'index.php?post_type=student&name=$matches[1]',
        'students/?' => 'index.php?post_type=student',
        'students/page/([0-9]+)/?' => 'index.php?post_type=student&paged=$matches[1]',
        'course/?' => 'index.php?taxonomy=course',
        'course/([^/]+)/?' => 'index.php?taxonomy=course&term=$matches[1]',
        'course/page/([0-9]+)/?' => 'index.php?taxonomy=course&paged=$matches[1]',
        'grade-level/?' => 'index.php?taxonomy=grade_level',
        'grade-level/([^/]+)/?' => 'index.php?taxonomy=grade_level&term=$matches[1]',
        'grade-level/page/([0-9]+)/?' => 'index.php?taxonomy=grade_level&paged=$matches[1]',
        'taxonomy-archive/?' => 'index.php?taxonomy_archive=1',
        'page/([0-9]+)/?' => 'index.php?paged=$matches[1]',
    );

    /**
     * Meta fields configuration
     */
    const META_FIELDS = array(
        'student_id' => array(
            'type' => 'text',
            'label' => 'Student ID',
            'required' => true,
            'meta_key' => '_student_id',
        ),
        'student_email' => array(
            'type' => 'email',
            'label' => 'Email',
            'required' => false,
            'meta_key' => '_student_email',
        ),
        'student_phone' => array(
            'type' => 'tel',
            'label' => 'Phone',
            'required' => false,
            'meta_key' => '_student_phone',
        ),
        'student_dob' => array(
            'type' => 'date',
            'label' => 'Date of Birth',
            'required' => false,
            'meta_key' => '_student_dob',
        ),
        'student_address' => array(
            'type' => 'textarea',
            'label' => 'Address',
            'required' => false,
            'meta_key' => '_student_address',
        ),
        'student_country' => array(
            'type' => 'text',
            'label' => 'Country',
            'required' => false,
            'meta_key' => '_student_country',
        ),
        'student_city' => array(
            'type' => 'text',
            'label' => 'City',
            'required' => false,
            'meta_key' => '_student_city',
        ),
        'student_class_grade' => array(
            'type' => 'text',
            'label' => 'Class / Grade',
            'required' => false,
            'meta_key' => '_student_class_grade',
        ),
        'student_is_active' => array(
            'type' => 'select',
            'label' => 'Status',
            'required' => false,
            'meta_key' => '_student_is_active',
            'options' => array(
                '1' => 'Active',
                '0' => 'Inactive'
            ),
        ),
    );

    /**
     * Default settings
     */
    const DEFAULT_SETTINGS = array(
        'students_per_page' => 12,
        'enable_search' => true,
        'show_email_publicly' => false,
        'enable_ajax' => true,
        'auto_save' => true,
    );

    /**
     * Get plugin option
     *
     * @param string $key Option key
     * @param mixed  $default Default value
     * @return mixed
     */
    public static function get_option( $key, $default = null ) {
        $options = get_option( 'students_options', self::DEFAULT_SETTINGS );
        return isset( $options[ $key ] ) ? $options[ $key ] : $default;
    }

    /**
     * Update plugin option
     *
     * @param string $key Option key
     * @param mixed  $value Option value
     * @return bool
     */
    public static function update_option( $key, $value ) {
        $options = get_option( 'students_options', self::DEFAULT_SETTINGS );
        $options[ $key ] = $value;
        return update_option( 'students_options', $options );
    }

    /**
     * Get meta field configuration
     *
     * @param string $field_name Field name
     * @return array|null
     */
    public static function get_meta_field_config( $field_name ) {
        return isset( self::META_FIELDS[ $field_name ] ) ? self::META_FIELDS[ $field_name ] : null;
    }

    /**
     * Get all meta field names
     *
     * @return array
     */
    public static function get_meta_field_names() {
        return array_keys( self::META_FIELDS );
    }

    /**
     * Get required meta fields
     *
     * @return array
     */
    public static function get_required_meta_fields() {
        $required = array();
        foreach ( self::META_FIELDS as $field_name => $config ) {
            if ( isset( $config['required'] ) && $config['required'] ) {
                $required[] = $field_name;
            }
        }
        return $required;
    }

    /**
     * Get rewrite rules
     *
     * @return array
     */
    public static function get_rewrite_rules() {
        return self::REWRITE_RULES;
    }

    /**
     * Get template paths
     *
     * @return array
     */
    public static function get_template_paths() {
        return array(
            'theme' => get_template_directory() . '/students/',
            'plugin' => STUDENTS_PLUGIN_DIR . 'templates/',
        );
    }
}
