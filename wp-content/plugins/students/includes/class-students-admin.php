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

    use Students_Meta_Box;

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
        
        // Meta boxes are now handled by the post type class using traits
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        // Add top-level Students menu
        add_menu_page(
            __( 'Students', 'students' ),
            __( 'Students', 'students' ),
            'manage_options',
            'students',
            array( $this, 'main_page' ),
            'dashicons-groups',
            20
        );

        // Add submenu pages
        add_submenu_page(
            'students',
            __( 'All Students', 'students' ),
            __( 'All Students', 'students' ),
            'manage_options',
            'edit.php?post_type=student'
        );

        add_submenu_page(
            'students',
            __( 'Add New Student', 'students' ),
            __( 'Add New Student', 'students' ),
            'manage_options',
            'post-new.php?post_type=student'
        );

        add_submenu_page(
            'students',
            __( 'Courses', 'students' ),
            __( 'Courses', 'students' ),
            'manage_options',
            'edit-tags.php?taxonomy=course&post_type=student'
        );

        add_submenu_page(
            'students',
            __( 'Grade Levels', 'students' ),
            __( 'Grade Levels', 'students' ),
            'manage_options',
            'edit-tags.php?taxonomy=grade_level&post_type=student'
        );

        add_submenu_page(
            'students',
            __( 'Students Settings', 'students' ),
            __( 'Settings', 'students' ),
            'manage_options',
            'students-settings',
            array( $this, 'settings_page' )
        );

        // Remove the old submenu from the main Students post type menu
        remove_submenu_page( 'edit.php?post_type=student', 'students-settings' );
    }

    /**
     * Main Students page
     */
    public function main_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Students Management', 'students' ); ?></h1>
            
            <div class="students-dashboard">
                <div class="students-overview">
                    <h2><?php esc_html_e( 'Overview', 'students' ); ?></h2>
                    
                    <?php
                    $total_students = wp_count_posts( 'student' );
                    $active_students = get_posts( array(
                        'post_type' => 'student',
                        'post_status' => 'publish',
                        'meta_query' => array(
                            array(
                                'key' => '_student_is_active',
                                'value' => '1',
                                'compare' => '='
                            )
                        ),
                        'numberposts' => -1
                    ) );
                    $active_count = count( $active_students );
                    ?>
                    
                    <div class="stats-grid">
                        <div class="stat-card">
                            <h3><?php echo esc_html( $total_students->publish ); ?></h3>
                            <p><?php esc_html_e( 'Total Students', 'students' ); ?></p>
                        </div>
                        <div class="stat-card">
                            <h3><?php echo esc_html( $active_count ); ?></h3>
                            <p><?php esc_html_e( 'Active Students', 'students' ); ?></p>
                        </div>
                        <div class="stat-card">
                            <h3><?php echo esc_html( $total_students->draft ); ?></h3>
                            <p><?php esc_html_e( 'Draft Students', 'students' ); ?></p>
                        </div>
                    </div>
                </div>

                <div class="quick-actions">
                    <h2><?php esc_html_e( 'Quick Actions', 'students' ); ?></h2>
                    <div class="action-buttons">
                        <a href="<?php echo admin_url( 'post-new.php?post_type=student' ); ?>" class="button button-primary">
                            <?php esc_html_e( 'Add New Student', 'students' ); ?>
                        </a>
                        <a href="<?php echo admin_url( 'edit.php?post_type=student' ); ?>" class="button">
                            <?php esc_html_e( 'View All Students', 'students' ); ?>
                        </a>
                        <a href="<?php echo admin_url( 'admin.php?page=students-settings' ); ?>" class="button">
                            <?php esc_html_e( 'Settings', 'students' ); ?>
                        </a>
                    </div>
                </div>

                <div class="recent-students">
                    <h2><?php esc_html_e( 'Recent Students', 'students' ); ?></h2>
                    <?php
                    $recent_students = get_posts( array(
                        'post_type' => 'student',
                        'post_status' => 'publish',
                        'numberposts' => 5,
                        'orderby' => 'date',
                        'order' => 'DESC'
                    ) );

                    if ( $recent_students ) :
                    ?>
                        <table class="wp-list-table widefat fixed striped">
                            <thead>
                                <tr>
                                    <th><?php esc_html_e( 'Name', 'students' ); ?></th>
                                    <th><?php esc_html_e( 'Status', 'students' ); ?></th>
                                    <th><?php esc_html_e( 'Date Added', 'students' ); ?></th>
                                    <th><?php esc_html_e( 'Actions', 'students' ); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ( $recent_students as $student ) : 
                                    $is_active = get_post_meta( $student->ID, '_student_is_active', true );
                                ?>
                                    <tr>
                                        <td>
                                            <strong><a href="<?php echo get_edit_post_link( $student->ID ); ?>"><?php echo esc_html( $student->post_title ); ?></a></strong>
                                        </td>
                                        <td>
                                            <?php if ( '1' === $is_active ) : ?>
                                                <span style="color: green; font-weight: bold;"><?php esc_html_e( 'Active', 'students' ); ?></span>
                                            <?php else : ?>
                                                <span style="color: red; font-weight: bold;"><?php esc_html_e( 'Inactive', 'students' ); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo esc_html( get_the_date( '', $student->ID ) ); ?></td>
                                        <td>
                                            <a href="<?php echo get_edit_post_link( $student->ID ); ?>" class="button button-small"><?php esc_html_e( 'Edit', 'students' ); ?></a>
                                            <a href="<?php echo get_permalink( $student->ID ); ?>" class="button button-small" target="_blank"><?php esc_html_e( 'View', 'students' ); ?></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else : ?>
                        <p><?php esc_html_e( 'No students found.', 'students' ); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <style>
            .students-dashboard {
                margin-top: 20px;
            }
            .students-overview, .quick-actions, .recent-students {
                margin-bottom: 30px;
            }
            .stats-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 20px;
                margin-top: 15px;
            }
            .stat-card {
                background: #fff;
                border: 1px solid #ddd;
                padding: 20px;
                text-align: center;
                border-radius: 5px;
            }
            .stat-card h3 {
                font-size: 2em;
                margin: 0 0 10px 0;
                color: #0073aa;
            }
            .stat-card p {
                margin: 0;
                color: #666;
            }
            .action-buttons {
                margin-top: 15px;
            }
            .action-buttons .button {
                margin-right: 10px;
                margin-bottom: 10px;
            }
        </style>
        <?php
    }

    /**
     * Initialize settings
     */
    public function init_settings() {
        // Register settings for both pages
        register_setting( 'students_options', 'students_options', array( $this, 'sanitize_settings' ) );

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

        // Add metadata visibility section
        add_settings_section(
            'students_metadata_visibility',
            __( 'Metadata Visibility Settings', 'students' ),
            array( $this, 'metadata_visibility_section' ),
            'students-settings'
        );

        // Add metadata visibility fields programmatically
        $this->add_metadata_visibility_fields();
    }

    /**
     * Sanitize settings before saving
     *
     * @param array $input The input array
     * @return array Sanitized input
     */
    public function sanitize_settings( $input ) {
        $sanitized_input = array();
        
        // Handle general settings
        if ( isset( $input['students_per_page'] ) ) {
            $sanitized_input['students_per_page'] = absint( $input['students_per_page'] );
        }
        
        if ( isset( $input['enable_search'] ) ) {
            $sanitized_input['enable_search'] = true;
        }
        
        if ( isset( $input['show_email'] ) ) {
            $sanitized_input['show_email'] = true;
        }
        
        // Handle metadata visibility settings
        $metadata_fields = array(
            'show_student_id', 'show_email', 'show_phone', 'show_dob', 
            'show_address', 'show_country', 'show_city', 'show_class_grade', 'show_status',
            'show_courses', 'show_grade_levels'
        );
        
        foreach ( $metadata_fields as $field ) {
            if ( isset( $input[ $field ] ) ) {
                $sanitized_input[ $field ] = true;
            } else {
                $sanitized_input[ $field ] = false;
            }
        }
        
        return $sanitized_input;
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
        $value = isset( $options['students_per_page'] ) ? $options['students_per_page'] : 4;
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

                wp_enqueue_script(
                    'students-ajax-handler',
                    STUDENTS_PLUGIN_URL . 'assets/js/ajax-handler.js',
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

                wp_enqueue_style(
                    'students-ajax-handler',
                    STUDENTS_PLUGIN_URL . 'assets/css/ajax-handler.css',
                    array(),
                    STUDENTS_VERSION
                );

                // Localize AJAX data
                wp_localize_script( 'students-ajax-handler', 'students_ajax', array(
                    'ajax_url' => admin_url( 'admin-ajax.php' ),
                    'nonce' => wp_create_nonce( 'students_ajax_nonce' ),
                    'strings' => array(
                        'saving' => __( 'Saving...', 'students' ),
                        'saved' => __( 'Saved successfully!', 'students' ),
                        'error' => __( 'An error occurred.', 'students' ),
                        'confirm_delete' => __( 'Are you sure you want to delete this student?', 'students' )
                    )
                ) );
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
                $new_columns['location'] = __( 'Location', 'students' );
                $new_columns['class_grade'] = __( 'Class/Grade', 'students' );
                $new_columns['status'] = __( 'Status', 'students' );
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
                
            case 'location':
                $country = get_post_meta( $post_id, '_student_country', true );
                $city = get_post_meta( $post_id, '_student_city', true );
                $location = array();
                if ( ! empty( $city ) ) {
                    $location[] = $city;
                }
                if ( ! empty( $country ) ) {
                    $location[] = $country;
                }
                echo esc_html( implode( ', ', $location ) );
                break;
                
            case 'class_grade':
                $class_grade = get_post_meta( $post_id, '_student_class_grade', true );
                echo esc_html( $class_grade );
                break;
                
            case 'status':
                $is_active = get_post_meta( $post_id, '_student_is_active', true );
                if ( '1' === $is_active ) {
                    echo '<span style="color: green; font-weight: bold;">' . __( 'Active', 'students' ) . '</span>';
                } else {
                    echo '<span style="color: red; font-weight: bold;">' . __( 'Inactive', 'students' ) . '</span>';
                }
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
        $columns['class_grade'] = 'class_grade';
        $columns['status'] = 'status';
        return $columns;
    }

    /**
     * Add meta boxes for student post type
     * 
     * @deprecated Meta boxes are now handled by the post type class using traits
     */
    public function add_student_meta_boxes() {
        // Meta boxes are now handled by the post type class using traits
        // This method is kept for backward compatibility but does nothing
    }

    /**
     * Render personal information meta box
     *
     * @param WP_Post $post
     */
    public function render_personal_info_meta_box( $post ) {
        // Add nonce for security
        wp_nonce_field( 'student_meta_box_nonce', 'student_meta_box_nonce' );

        // Get current values and sanitize them for display using the sanitizer class
        $country = Students_Sanitizer::validate_for_display( get_post_meta( $post->ID, '_student_country', true ), 'country' );
        $city = Students_Sanitizer::validate_for_display( get_post_meta( $post->ID, '_student_city', true ), 'city' );
        $class_grade = Students_Sanitizer::validate_for_display( get_post_meta( $post->ID, '_student_class_grade', true ), 'class_grade' );
        $is_active = Students_Sanitizer::validate_for_display( get_post_meta( $post->ID, '_student_is_active', true ), 'status' );
        ?>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="student_id"><?php esc_html_e( 'Student ID', 'students' ); ?></label>
                </th>
                <td>
                    <input type="text" id="student_id" name="student_id" value="<?php echo esc_attr( Students_Sanitizer::validate_for_display( get_post_meta( $post->ID, '_student_id', true ), 'student_id' ) ); ?>" class="regular-text" maxlength="50" />
                    <p class="description"><?php esc_html_e( 'Enter the student ID (letters, numbers, and hyphens only)', 'students' ); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="student_email"><?php esc_html_e( 'Email', 'students' ); ?></label>
                </th>
                <td>
                    <input type="email" id="student_email" name="student_email" value="<?php echo esc_attr( Students_Sanitizer::validate_for_display( get_post_meta( $post->ID, '_student_email', true ), 'email' ) ); ?>" class="regular-text" />
                    <p class="description"><?php esc_html_e( 'Enter the student email address', 'students' ); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="student_phone"><?php esc_html_e( 'Phone', 'students' ); ?></label>
                </th>
                <td>
                    <input type="tel" id="student_phone" name="student_phone" value="<?php echo esc_attr( Students_Sanitizer::validate_for_display( get_post_meta( $post->ID, '_student_phone', true ), 'phone' ) ); ?>" class="regular-text" maxlength="20" />
                    <p class="description"><?php esc_html_e( 'Enter the student phone number (digits, spaces, hyphens, parentheses, and plus signs only)', 'students' ); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="student_dob"><?php esc_html_e( 'Date of Birth', 'students' ); ?></label>
                </th>
                <td>
                    <input type="date" id="student_dob" name="student_dob" value="<?php echo esc_attr( Students_Sanitizer::validate_for_display( get_post_meta( $post->ID, '_student_dob', true ), 'date' ) ); ?>" class="regular-text" />
                    <p class="description"><?php esc_html_e( 'Enter the student date of birth', 'students' ); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="student_address"><?php esc_html_e( 'Address', 'students' ); ?></label>
                </th>
                <td>
                    <textarea id="student_address" name="student_address" rows="3" class="large-text" maxlength="200"><?php echo esc_textarea( Students_Sanitizer::validate_for_display( get_post_meta( $post->ID, '_student_address', true ), 'address' ) ); ?></textarea>
                    <p class="description"><?php esc_html_e( 'Enter the student address (letters, numbers, spaces, and common punctuation only)', 'students' ); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="student_country"><?php esc_html_e( 'Country', 'students' ); ?></label>
                </th>
                <td>
                    <input type="text" id="student_country" name="student_country" value="<?php echo esc_attr( $country ); ?>" class="regular-text" maxlength="100" />
                    <p class="description"><?php esc_html_e( 'Enter the country name (letters, spaces, and common punctuation only)', 'students' ); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="student_city"><?php esc_html_e( 'City', 'students' ); ?></label>
                </th>
                <td>
                    <input type="text" id="student_city" name="student_city" value="<?php echo esc_attr( $city ); ?>" class="regular-text" maxlength="100" />
                    <p class="description"><?php esc_html_e( 'Enter the city name (letters, spaces, and common punctuation only)', 'students' ); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="student_class_grade"><?php esc_html_e( 'Class / Grade', 'students' ); ?></label>
                </th>
                <td>
                    <input type="text" id="student_class_grade" name="student_class_grade" value="<?php echo esc_attr( $class_grade ); ?>" class="regular-text" maxlength="50" />
                    <p class="description"><?php esc_html_e( 'e.g., Grade 10, Class A, etc. (letters, numbers, spaces, and common punctuation only)', 'students' ); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="student_is_active"><?php esc_html_e( 'Status', 'students' ); ?></label>
                </th>
                <td>
                    <select id="student_is_active" name="student_is_active">
                        <option value="1" <?php selected( $is_active, '1' ); ?>><?php esc_html_e( 'Active', 'students' ); ?></option>
                        <option value="0" <?php selected( $is_active, '0' ); ?>><?php esc_html_e( 'Inactive', 'students' ); ?></option>
                    </select>
                    <p class="description"><?php esc_html_e( 'Whether the student is currently active or not', 'students' ); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Save student meta box data
     *
     * @param int $post_id
     * @deprecated Meta box saving is now handled by the post type class using traits
     */
    public function save_student_meta_boxes( $post_id ) {
        // Meta box saving is now handled by the post type class using traits
        // This method is kept for backward compatibility but does nothing
    }

    /**
     * Add metadata visibility fields programmatically
     */
    private function add_metadata_visibility_fields() {
        $metadata_fields = array(
            'show_student_id' => array(
                'label' => __( 'Student ID', 'students' ),
                'description' => __( 'Show student ID on single student pages', 'students' )
            ),
            'show_email' => array(
                'label' => __( 'Email', 'students' ),
                'description' => __( 'Show email address on single student pages', 'students' )
            ),
            'show_phone' => array(
                'label' => __( 'Phone', 'students' ),
                'description' => __( 'Show phone number on single student pages', 'students' )
            ),
            'show_dob' => array(
                'label' => __( 'Date of Birth', 'students' ),
                'description' => __( 'Show date of birth on single student pages', 'students' )
            ),
            'show_address' => array(
                'label' => __( 'Address', 'students' ),
                'description' => __( 'Show address on single student pages', 'students' )
            ),
            'show_country' => array(
                'label' => __( 'Country', 'students' ),
                'description' => __( 'Show country on single student pages', 'students' )
            ),
            'show_city' => array(
                'label' => __( 'City', 'students' ),
                'description' => __( 'Show city on single student pages', 'students' )
            ),
            'show_class_grade' => array(
                'label' => __( 'Class/Grade', 'students' ),
                'description' => __( 'Show class/grade on single student pages', 'students' )
            ),
            'show_status' => array(
                'label' => __( 'Status', 'students' ),
                'description' => __( 'Show student status on single student pages', 'students' )
            ),
            'show_courses' => array(
                'label' => __( 'Courses', 'students' ),
                'description' => __( 'Show courses on single student pages', 'students' )
            ),
            'show_grade_levels' => array(
                'label' => __( 'Grade Levels', 'students' ),
                'description' => __( 'Show grade levels on single student pages', 'students' )
            ),
        );

        foreach ( $metadata_fields as $field_key => $field_data ) {
            add_settings_field(
                $field_key,
                $field_data['label'],
                array( $this, 'metadata_visibility_field' ),
                'students-settings',
                'students_metadata_visibility',
                array(
                    'field_key' => $field_key,
                    'description' => $field_data['description']
                )
            );
        }
    }

    /**
     * Metadata visibility section callback
     */
    public function metadata_visibility_section() {
        echo '<p>' . __( 'Control which metadata fields are displayed on single student pages. Uncheck fields you want to hide.', 'students' ) . '</p>';
    }

    /**
     * Metadata visibility field callback
     *
     * @param array $args Field arguments
     */
    public function metadata_visibility_field( $args ) {
        $options = get_option( 'students_options', array() );
        $field_key = $args['field_key'];
        $description = $args['description'];
        
        // Check if the setting exists and is true
        $value = isset( $options[ $field_key ] ) && $options[ $field_key ] === true;
        ?>
        <input type="checkbox" 
               name="students_options[<?php echo esc_attr( $field_key ); ?>]" 
               value="1" 
               <?php checked( $value, true ); ?> />
        <span class="description"><?php echo esc_html( $description ); ?></span>
        <?php
    }

    /**
     * Reset settings to defaults
     */
    public function reset_settings_to_defaults() {
        $default_options = array(
            'students_per_page' => 4,
            'enable_search' => true,
            'show_email' => true,
            'show_student_id' => true,
            'show_phone' => true,
            'show_dob' => true,
            'show_address' => true,
            'show_country' => true,
            'show_city' => true,
            'show_class_grade' => true,
            'show_status' => true,
        );
        
        update_option( 'students_options', $default_options );
    }
}
