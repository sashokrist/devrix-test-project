<?php
/**
 * The admin-specific functionality of the plugin
 *
 * @package Students
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Students Admin Class
 *
 * @since 1.0.0
 */
class Students_Admin {

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'init_settings' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
        add_filter( 'manage_student_posts_columns', array( $this, 'add_custom_columns' ) );
        add_action( 'manage_student_posts_custom_column', array( $this, 'display_custom_columns' ), 10, 2 );
        add_filter( 'manage_edit-student_sortable_columns', array( $this, 'make_columns_sortable' ) );
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=student',
            __( 'Students Settings', 'students' ),
            __( 'Settings', 'students' ),
            'manage_options',
            'students-settings',
            array( $this, 'settings_page' )
        );
    }

    /**
     * Initialize settings
     */
    public function init_settings() {
        register_setting( 'students_options', 'students_options' );

        add_settings_section(
            'students_general',
            __( 'General Settings', 'students' ),
            array( $this, 'general_settings_section' ),
            'students-settings'
        );

        add_settings_field(
            'students_per_page',
            __( 'Students per page', 'students' ),
            array( $this, 'students_per_page_field' ),
            'students-settings',
            'students_general'
        );

        add_settings_field(
            'enable_search',
            __( 'Enable search', 'students' ),
            array( $this, 'enable_search_field' ),
            'students-settings',
            'students_general'
        );

        add_settings_field(
            'show_email',
            __( 'Show email publicly', 'students' ),
            array( $this, 'show_email_field' ),
            'students-settings',
            'students_general'
        );
    }

    /**
     * General settings section callback
     */
    public function general_settings_section() {
        echo '<p>' . __( 'Configure general settings for the Students plugin.', 'students' ) . '</p>';
    }

    /**
     * Students per page field
     */
    public function students_per_page_field() {
        $options = get_option( 'students_options', array() );
        $value = isset( $options['students_per_page'] ) ? $options['students_per_page'] : 10;
        ?>
        <input type="number" name="students_options[students_per_page]" value="<?php echo esc_attr( $value ); ?>" min="1" max="100" />
        <p class="description"><?php _e( 'Number of students to display per page in lists.', 'students' ); ?></p>
        <?php
    }

    /**
     * Enable search field
     */
    public function enable_search_field() {
        $options = get_option( 'students_options', array() );
        $value = isset( $options['enable_search'] ) ? $options['enable_search'] : true;
        ?>
        <input type="checkbox" name="students_options[enable_search]" value="1" <?php checked( $value, 1 ); ?> />
        <span class="description"><?php _e( 'Enable search functionality for students.', 'students' ); ?></span>
        <?php
    }

    /**
     * Show email field
     */
    public function show_email_field() {
        $options = get_option( 'students_options', array() );
        $value = isset( $options['show_email'] ) ? $options['show_email'] : false;
        ?>
        <input type="checkbox" name="students_options[show_email]" value="1" <?php checked( $value, 1 ); ?> />
        <span class="description"><?php _e( 'Show student email addresses on public pages.', 'students' ); ?></span>
        <?php
    }

    /**
     * Settings page
     */
    public function settings_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'students_options' );
                do_settings_sections( 'students-settings' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts( $hook ) {
        if ( 'post.php' === $hook || 'post-new.php' === $hook ) {
            global $post_type;
            if ( 'student' === $post_type ) {
                wp_enqueue_script(
                    'students-admin',
                    STUDENTS_PLUGIN_URL . 'assets/js/admin.js',
                    array( 'jquery' ),
                    STUDENTS_VERSION,
                    true
                );

                wp_enqueue_style(
                    'students-admin',
                    STUDENTS_PLUGIN_URL . 'assets/css/admin.css',
                    array(),
                    STUDENTS_VERSION
                );
            }
        }
    }

    /**
     * Add custom columns to students list
     *
     * @param array $columns
     * @return array
     */
    public function add_custom_columns( $columns ) {
        $new_columns = array();
        
        foreach ( $columns as $key => $value ) {
            $new_columns[ $key ] = $value;
            if ( 'title' === $key ) {
                $new_columns['student_id'] = __( 'Student ID', 'students' );
                $new_columns['email'] = __( 'Email', 'students' );
                $new_columns['course'] = __( 'Course', 'students' );
                $new_columns['grade_level'] = __( 'Grade Level', 'students' );
            }
        }
        
        return $new_columns;
    }

    /**
     * Display custom column content
     *
     * @param string $column
     * @param int    $post_id
     */
    public function display_custom_columns( $column, $post_id ) {
        switch ( $column ) {
            case 'student_id':
                $student_id = get_post_meta( $post_id, '_student_id', true );
                echo esc_html( $student_id );
                break;
                
            case 'email':
                $email = get_post_meta( $post_id, '_student_email', true );
                echo esc_html( $email );
                break;
                
            case 'course':
                $terms = get_the_terms( $post_id, 'course' );
                if ( $terms && ! is_wp_error( $terms ) ) {
                    $course_names = array();
                    foreach ( $terms as $term ) {
                        $course_names[] = $term->name;
                    }
                    echo esc_html( implode( ', ', $course_names ) );
                }
                break;
                
            case 'grade_level':
                $terms = get_the_terms( $post_id, 'grade_level' );
                if ( $terms && ! is_wp_error( $terms ) ) {
                    $grade_names = array();
                    foreach ( $terms as $term ) {
                        $grade_names[] = $term->name;
                    }
                    echo esc_html( implode( ', ', $grade_names ) );
                }
                break;
        }
    }

    /**
     * Make columns sortable
     *
     * @param array $columns
     * @return array
     */
    public function make_columns_sortable( $columns ) {
        $columns['student_id'] = 'student_id';
        $columns['email'] = 'email';
        return $columns;
    }
}
