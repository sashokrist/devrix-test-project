<?php
/**
 * Plugin Name: Students Block
 * Plugin URI: https://example.com/students-block
 * Description: A Gutenberg block for displaying students with filtering options.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: students-block
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * Network: false
 *
 * @package Students_Block
 * @version 1.0.0
 * @author Your Name
 * @license GPL v2 or later
 */

// Prevent direct access to this file
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Define plugin constants
define( 'STUDENTS_BLOCK_VERSION', '1.0.0' );
define( 'STUDENTS_BLOCK_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'STUDENTS_BLOCK_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'STUDENTS_BLOCK_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Main Students Block Plugin Class
 *
 * @since 1.0.0
 */
class Students_Block_Plugin {

    /**
     * Plugin instance
     *
     * @var Students_Block_Plugin
     */
    private static $instance = null;

    /**
     * Get plugin instance
     *
     * @return Students_Block_Plugin
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
        
        // Removed content filter as it was causing serialization issues
        // WordPress handles block content serialization automatically
    }

    /**
     * Initialize the plugin
     */
    public function init() {
        // Check if Gutenberg is available
        if ( ! function_exists( 'register_block_type' ) ) {
            return;
        }

        // Register the block
        $this->register_block();
        
        // REST API registration temporarily disabled to prevent errors
        // add_action( 'rest_api_init', array( $this, 'register_rest_api' ) );
        
        // Enqueue block assets
        add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
        
        // Test files removed - no longer needed
        
        // REST API test file temporarily disabled
        // require_once STUDENTS_BLOCK_PLUGIN_DIR . 'rest-api-test.php';
    }

    /**
     * Register the Gutenberg block
     */
    private function register_block() {
        register_block_type( 'students-block/students-display', array(
            'editor_script' => 'students-block-editor',
            'editor_style'  => 'students-block-editor-style',
            'style'         => 'students-block-style',
            'render_callback' => array( $this, 'render_block' ),
        ) );
    }

    /**
     * Enqueue block editor assets
     */
    public function enqueue_block_editor_assets() {
        wp_enqueue_script(
            'students-block-editor',
            STUDENTS_BLOCK_PLUGIN_URL . 'build/index.js',
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wp-components', 'wp-data' ),
            STUDENTS_BLOCK_VERSION
        );

        wp_enqueue_style(
            'students-block-editor-style',
            STUDENTS_BLOCK_PLUGIN_URL . 'build/index.css',
            array( 'wp-edit-blocks' ),
            STUDENTS_BLOCK_VERSION
        );

        // Localize script with students data for the editor
        wp_localize_script( 'students-block-editor', 'studentsBlockData', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'students_block_nonce' ),
            'students' => $this->get_students_for_editor(),
        ) );
    }

    /**
     * REST API support temporarily disabled to prevent errors
     */
    public function register_rest_api() {
        // Temporarily disabled
    }

    /**
     * REST API callback temporarily disabled
     */
    public function get_students_rest( $request ) {
        // Temporarily disabled
        return rest_ensure_response( array() );
    }

    /**
     * Ensure block is properly registered
     */
    public function ensure_block_registration() {
        // Check if block is already registered
        $block_type = WP_Block_Type_Registry::get_instance()->get_registered( 'students-block/students-display' );
        
        if ( ! $block_type ) {
            // Re-register the block if it's not found
            $this->register_block();
        }
        
        // Debug block registration temporarily disabled
        // $this->debug_block_registration();
    }

    /**
     * Ensure block content is properly serialized
     */
    public function ensure_block_content( $content ) {
        // Check if content contains our block
        if ( strpos( $content, '<!-- wp:students-block/students-display' ) !== false ) {
            // Don't modify the content - let WordPress handle it naturally
            // The content filter was causing issues by removing the div element
            return $content;
        }
        
        return $content;
    }

    /**
     * Debug function to check block registration
     */
    public function debug_block_registration() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $block_type = WP_Block_Type_Registry::get_instance()->get_registered( 'students-block/students-display' );
        
        if ( $block_type ) {
            error_log( 'Students Block is properly registered' );
        } else {
            error_log( 'Students Block is NOT registered' );
        }
    }

    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        wp_enqueue_style(
            'students-block-style',
            STUDENTS_BLOCK_PLUGIN_URL . 'build/style-index.css',
            array(),
            STUDENTS_BLOCK_VERSION
        );
    }

    /**
     * Get students data for the editor
     */
    private function get_students_for_editor() {
        $args = array(
            'post_type' => 'student',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
        );

        $students = get_posts( $args );
        $students_data = array();

        foreach ( $students as $student ) {
            $students_data[] = array(
                'id' => $student->ID,
                'title' => $student->post_title,
                'status' => get_post_meta( $student->ID, '_student_is_active', true ),
            );
        }

        return $students_data;
    }

    /**
     * Render the block
     *
     * @param array $attributes Block attributes
     * @return string HTML output
     */
    public function render_block( $attributes ) {
        // Parse and validate attributes
        $number_of_students = isset( $attributes['numberOfStudents'] ) ? intval( $attributes['numberOfStudents'] ) : 4;
        $status = isset( $attributes['status'] ) ? sanitize_text_field( $attributes['status'] ) : 'active';
        $show_specific_student = isset( $attributes['showSpecificStudent'] ) ? (bool) $attributes['showSpecificStudent'] : false;
        $specific_student_id = isset( $attributes['specificStudentId'] ) ? intval( $attributes['specificStudentId'] ) : 0;
        $order_by = isset( $attributes['orderBy'] ) ? sanitize_text_field( $attributes['orderBy'] ) : 'title';
        $order = isset( $attributes['order'] ) ? sanitize_text_field( $attributes['order'] ) : 'ASC';
        $className = isset( $attributes['className'] ) ? sanitize_text_field( $attributes['className'] ) : '';

        // Validate inputs
        if ( $number_of_students < 1 ) {
            $number_of_students = 4;
        }
        if ( $number_of_students > 20 ) {
            $number_of_students = 20;
        }

        if ( ! in_array( $status, array( 'active', 'inactive', 'all' ) ) ) {
            $status = 'active';
        }

        if ( ! in_array( $order, array( 'ASC', 'DESC' ) ) ) {
            $order = 'ASC';
        }

        // Build query arguments
        $args = array(
            'post_type' => 'student',
            'post_status' => 'publish',
            'posts_per_page' => $number_of_students,
            'orderby' => $order_by,
            'order' => $order,
            'meta_query' => array(),
        );

        // If showing specific student
        if ( $show_specific_student && $specific_student_id > 0 ) {
            $args['p'] = $specific_student_id;
            $args['posts_per_page'] = 1;
        } else {
            // Add status filter for multiple students
            if ( 'active' === $status ) {
                $args['meta_query'][] = array(
                    'key' => '_student_is_active',
                    'value' => '1',
                    'compare' => '='
                );
            } elseif ( 'inactive' === $status ) {
                $args['meta_query'][] = array(
                    'key' => '_student_is_active',
                    'value' => '0',
                    'compare' => '='
                );
            }
            // 'all' status doesn't add any meta query
        }

        // If no meta query, remove the array
        if ( empty( $args['meta_query'] ) ) {
            unset( $args['meta_query'] );
        }

        // Run the query
        $students_query = new WP_Query( $args );

        ob_start();
        ?>
        <div class="wp-block-students-block-students-display <?php echo esc_attr( $className ); ?>">
            <?php if ( $students_query->have_posts() ) : ?>
                <div class="students-grid">
                    <?php while ( $students_query->have_posts() ) : $students_query->the_post(); ?>
                        <?php
                        $student_id = get_the_ID();
                        $student_name = get_the_title();
                        $student_picture = get_the_post_thumbnail_url( $student_id, 'medium' );
                        $student_class_grade = get_post_meta( $student_id, '_student_class_grade', true );
                        $student_is_active = get_post_meta( $student_id, '_student_is_active', true );
                        $student_email = get_post_meta( $student_id, '_student_email', true );
                        $student_phone = get_post_meta( $student_id, '_student_phone', true );
                        
                                    // Get ACF fields
            $student_age = '';
            $student_school = '';
            $student_how_many = '';
            $student_test = '';
            if ( function_exists( 'get_field' ) ) {
                $student_age = get_field( 'age', $student_id );
                $student_school = get_field( 'school', $student_id );
                $student_how_many = get_field( 'how many', $student_id );
                $student_test = get_field( 'test', $student_id );
            }
                        
                        // Default image if no featured image
                        if ( ! $student_picture ) {
                            $student_picture = 'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200"><rect width="200" height="200" fill="#f8f9fa"/><circle cx="100" cy="80" r="30" fill="#dee2e6"/><path d="M50 180 Q100 140 150 180" stroke="#dee2e6" stroke-width="3" fill="none"/><text x="100" y="195" text-anchor="middle" font-family="Arial" font-size="12" fill="#6c757d">No Image</text></svg>' );
                        }
                        
                        // Student status class
                        $status_class = ( '1' === $student_is_active ) ? 'active' : 'inactive';
                        ?>
                        
                        <div class="student-card <?php echo esc_attr( $status_class ); ?>">
                            <div class="student-image">
                                <a href="<?php echo esc_url( get_permalink() ); ?>">
                                    <img src="<?php echo esc_url( $student_picture ); ?>" 
                                         alt="<?php echo esc_attr( $student_name ); ?>" 
                                         loading="lazy" />
                                </a>
                            </div>
                            
                            <div class="student-info">
                                <h3 class="student-name">
                                    <a href="<?php echo esc_url( get_permalink() ); ?>">
                                        <?php echo esc_html( $student_name ); ?>
                                    </a>
                                </h3>
                                
                                <?php if ( ! empty( $student_class_grade ) ) : ?>
                                    <div class="student-class-grade">
                                        <span class="label"><?php esc_html_e( 'Class/Grade:', 'students-block' ); ?></span>
                                        <span class="value"><?php echo esc_html( $student_class_grade ); ?></span>
                                    </div>
                                <?php endif; ?>

                                <?php if ( ! empty( $student_email ) ) : ?>
                                    <div class="student-email">
                                        <span class="label"><?php esc_html_e( 'Email:', 'students-block' ); ?></span>
                                        <span class="value"><?php echo esc_html( $student_email ); ?></span>
                                    </div>
                                <?php endif; ?>

                                <?php if ( ! empty( $student_phone ) ) : ?>
                                    <div class="student-phone">
                                        <span class="label"><?php esc_html_e( 'Phone:', 'students-block' ); ?></span>
                                        <span class="value"><?php echo esc_html( $student_phone ); ?></span>
                                    </div>
                                <?php endif; ?>

                                <?php if ( ! empty( $student_age ) ) : ?>
                                    <div class="student-age">
                                        <span class="label"><?php esc_html_e( 'Age:', 'students-block' ); ?></span>
                                        <span class="value"><?php echo esc_html( $student_age ); ?></span>
                                    </div>
                                <?php endif; ?>

                                            <?php if ( ! empty( $student_school ) ) : ?>
                <div class="student-school">
                    <span class="label"><?php esc_html_e( 'School:', 'students-block' ); ?></span>
                    <span class="value"><?php echo esc_html( $student_school ); ?></span>
                </div>
            <?php endif; ?>
            
            <?php if ( ! empty( $student_how_many ) ) : ?>
                <div class="student-how-many">
                    <span class="label"><?php esc_html_e( 'How Many:', 'students-block' ); ?></span>
                    <span class="value"><?php echo esc_html( $student_how_many ); ?></span>
                </div>
            <?php endif; ?>
            
            <?php if ( ! empty( $student_test ) ) : ?>
                <div class="student-test">
                    <span class="label"><?php esc_html_e( 'Test:', 'students-block' ); ?></span>
                    <span class="value"><?php echo esc_html( $student_test ); ?></span>
                </div>
            <?php endif; ?>
                                
                                <div class="student-status">
                                    <span class="status-indicator <?php echo esc_attr( $status_class ); ?>">
                                        <?php echo ( '1' === $student_is_active ) ? esc_html__( 'Active', 'students-block' ) : esc_html__( 'Inactive', 'students-block' ); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                    <?php endwhile; ?>
                </div>
                
                <?php if ( ! $show_specific_student && $students_query->found_posts > $number_of_students ) : ?>
                    <div class="students-footer">
                        <p class="students-count">
                            <?php 
                            printf(
                                esc_html__( 'Showing %1$d of %2$d students', 'students-block' ),
                                min( $number_of_students, $students_query->found_posts ),
                                $students_query->found_posts
                            );
                            ?>
                        </p>
                        <?php if ( get_post_type_archive_link( 'student' ) ) : ?>
                            <a href="<?php echo esc_url( get_post_type_archive_link( 'student' ) ); ?>" class="view-all-students">
                                <?php esc_html_e( 'View All Students', 'students-block' ); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
            <?php else : ?>
                <div class="no-students">
                    <p><?php 
                    if ( $show_specific_student && $specific_student_id > 0 ) {
                        esc_html_e( 'The selected student was not found or is not published.', 'students-block' );
                    } else {
                        esc_html_e( 'No students found matching the current filters.', 'students-block' );
                    }
                    ?></p>
                </div>
            <?php endif; ?>
        </div>
        <?php
        wp_reset_postdata();
        
        return ob_get_clean();
    }

    /**
     * Load plugin textdomain
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'students-block',
            false,
            dirname( STUDENTS_BLOCK_PLUGIN_BASENAME ) . '/languages'
        );
    }

    /**
     * Plugin activation
     */
    public function activate() {
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Clear any cached block registrations
        if ( function_exists( 'wp_cache_flush' ) ) {
            wp_cache_flush();
        }
    }

    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }
}

// Initialize the plugin
function students_block_plugin() {
    return Students_Block_Plugin::get_instance();
}

// Start the plugin
students_block_plugin();
