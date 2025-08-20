<?php
/**
 * Plugin Name: Students
 * Plugin URI: https://example.com/students
 * Description: A WordPress plugin for managing students and their information.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: students
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * Network: false
 *
 * @package Students
 * @version 1.0.0
 * @author Your Name
 * @license GPL v2 or later
 */

// Prevent direct access to this file
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Define plugin constants
define( 'STUDENTS_VERSION', '1.0.0' );
define( 'STUDENTS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'STUDENTS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'STUDENTS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Main Students Plugin Class
 *
 * @since 1.0.0
 */
class Students_Plugin {

    /**
     * Plugin instance
     *
     * @var Students_Plugin
     */
    private static $instance = null;

    /**
     * Get plugin instance
     *
     * @return Students_Plugin
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->init_hooks();
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action( 'init', array( $this, 'init' ) );
        add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
        register_uninstall_hook( __FILE__, array( 'Students_Plugin', 'uninstall' ) );
    }

    /**
     * Initialize the plugin
     */
    public function init() {
        // Load required files
        $this->load_dependencies();
        
        // Initialize components
        $this->init_components();
        
        // Add admin hooks
        if ( is_admin() ) {
            $this->init_admin();
        }
    }

    /**
     * Load plugin dependencies
     */
    private function load_dependencies() {
        // Load core files
        require_once STUDENTS_PLUGIN_DIR . 'includes/class-students-loader.php';
        require_once STUDENTS_PLUGIN_DIR . 'includes/class-students-post-type.php';
        require_once STUDENTS_PLUGIN_DIR . 'includes/class-students-admin.php';
        require_once STUDENTS_PLUGIN_DIR . 'includes/class-students-public.php';
    }

    /**
     * Initialize plugin components
     */
    private function init_components() {
        // Initialize post type
        new Students_Post_Type();
        
        // Initialize public functionality
        new Students_Public();
    }

    /**
     * Initialize admin functionality
     */
    private function init_admin() {
        new Students_Admin();
    }

    /**
     * Load plugin textdomain
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'students',
            false,
            dirname( STUDENTS_PLUGIN_BASENAME ) . '/languages'
        );
    }

    /**
     * Plugin activation
     */
    public function activate() {
        // Create database tables if needed
        $this->create_tables();
        
        // Set default options
        $this->set_default_options();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Plugin uninstall
     */
    public static function uninstall() {
        // Remove plugin data if needed
        // delete_option( 'students_options' );
    }

    /**
     * Create database tables
     */
    private function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        // Example table creation (uncomment if needed)
        /*
        $table_name = $wpdb->prefix . 'students';
        
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            email varchar(100) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
        */
    }

    /**
     * Set default options
     */
    private function set_default_options() {
        $default_options = array(
            'students_per_page' => 10,
            'enable_search' => true,
            'show_email' => false,
        );

        add_option( 'students_options', $default_options );
    }

    /**
     * Get plugin option
     *
     * @param string $key Option key
     * @param mixed  $default Default value
     * @return mixed
     */
    public static function get_option( $key, $default = null ) {
        $options = get_option( 'students_options', array() );
        return isset( $options[ $key ] ) ? $options[ $key ] : $default;
    }

    /**
     * Update plugin option
     *
     * @param string $key Option key
     * @param mixed  $value Option value
     */
    public static function update_option( $key, $value ) {
        $options = get_option( 'students_options', array() );
        $options[ $key ] = $value;
        update_option( 'students_options', $options );
    }
}

// Initialize the plugin
function students_plugin() {
    return Students_Plugin::get_instance();
}

// Start the plugin
students_plugin();
