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
        
        // Add meta boxes
        add_action( 'add_meta_boxes', array( $this, 'add_student_meta_boxes' ) );
        add_action( 'save_post', array( $this, 'save_student_meta_boxes' ) );
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

        // Add settings page under main Settings menu
        add_options_page(
            __( 'Students Settings', 'students' ),
            __( 'Students', 'students' ),
            'manage_options',
            'students-settings',
            array( $this, 'settings_page' )
        );
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
     */
    public function add_student_meta_boxes() {
        add_meta_box(
            'student_personal_info',
            __( 'Additional Information', 'students' ),
            array( $this, 'render_personal_info_meta_box' ),
            'student',
            'normal',
            'high'
        );
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
     */
    public function save_student_meta_boxes( $post_id ) {
        // Check if nonce is valid
        if ( ! isset( $_POST['student_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['student_meta_box_nonce'], 'student_meta_box_nonce' ) ) {
            return;
        }

        // Check if user has permissions to save data
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Check if not an autosave
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // Check if our custom fields are set and sanitize them properly using the sanitizer class
        if ( isset( $_POST['student_id'] ) ) {
            $student_id = Students_Sanitizer::sanitize_student_id( $_POST['student_id'] );
            update_post_meta( $post_id, '_student_id', $student_id );
        }

        if ( isset( $_POST['student_email'] ) ) {
            $email = Students_Sanitizer::sanitize_email( $_POST['student_email'] );
            update_post_meta( $post_id, '_student_email', $email );
        }

        if ( isset( $_POST['student_phone'] ) ) {
            $phone = Students_Sanitizer::sanitize_phone( $_POST['student_phone'] );
            update_post_meta( $post_id, '_student_phone', $phone );
        }

        if ( isset( $_POST['student_dob'] ) ) {
            $dob = Students_Sanitizer::sanitize_date( $_POST['student_dob'] );
            update_post_meta( $post_id, '_student_dob', $dob );
        }

        if ( isset( $_POST['student_address'] ) ) {
            $address = Students_Sanitizer::sanitize_address( $_POST['student_address'] );
            update_post_meta( $post_id, '_student_address', $address );
        }

        if ( isset( $_POST['student_country'] ) ) {
            $country = Students_Sanitizer::sanitize_country( $_POST['student_country'] );
            update_post_meta( $post_id, '_student_country', $country );
        }

        if ( isset( $_POST['student_city'] ) ) {
            $city = Students_Sanitizer::sanitize_city( $_POST['student_city'] );
            update_post_meta( $post_id, '_student_city', $city );
        }

        if ( isset( $_POST['student_class_grade'] ) ) {
            $class_grade = Students_Sanitizer::sanitize_class_grade( $_POST['student_class_grade'] );
            update_post_meta( $post_id, '_student_class_grade', $class_grade );
        }

        if ( isset( $_POST['student_is_active'] ) ) {
            $is_active = Students_Sanitizer::sanitize_status( $_POST['student_is_active'] );
            update_post_meta( $post_id, '_student_is_active', $is_active );
        }
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
            'students_per_page' => 10,
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
