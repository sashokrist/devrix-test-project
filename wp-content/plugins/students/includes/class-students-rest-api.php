<?php
/**
 * Students REST API Class
 *
 * Handles REST API endpoints for the Students plugin
 *
 * @package Students
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Students REST API Class
 *
 * @since 1.0.0
 */
class Students_REST_API {

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
        add_filter( 'nonce_life', array( $this, 'set_custom_nonce_lifetime' ) );
    }

    /**
     * Register REST API routes
     */
    public function register_routes() {
        // Custom nonce endpoint (10-minute lifetime)
        register_rest_route(
            'students/v1',
            '/nonce',
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_custom_nonce' ),
                'permission_callback' => array( $this, 'check_admin_permissions' ),
            )
        );

        // Get all students endpoint
        register_rest_route(
            'students/v1',
            '/students',
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_students' ),
                'permission_callback' => '__return_true',
                'args'                => array(
                    'per_page' => array(
                        'default'           => 10,
                        'sanitize_callback' => 'absint',
                        'validate_callback' => function( $param ) {
                            return is_numeric( $param ) && $param > 0 && $param <= 100;
                        },
                    ),
                    'page'     => array(
                        'default'           => 1,
                        'sanitize_callback' => 'absint',
                        'validate_callback' => function( $param ) {
                            return is_numeric( $param ) && $param > 0;
                        },
                    ),
                    'course'   => array(
                        'default'           => '',
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                    'grade_level' => array(
                        'default'           => '',
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                    'status'   => array(
                        'default'           => '',
                        'sanitize_callback' => 'sanitize_text_field',
                        'validate_callback' => function( $param ) {
                            return in_array( $param, array( '', 'active', 'inactive' ) );
                        },
                    ),
                ),
            )
        );

        // Get student by ID endpoint
        register_rest_route(
            'students/v1',
            '/students/(?P<id>\d+)',
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_student' ),
                'permission_callback' => '__return_true',
                'args'                => array(
                    'id' => array(
                        'validate_callback' => function( $param ) {
                            return is_numeric( $param ) && $param > 0;
                        },
                    ),
                ),
            )
        );

        // Get active students endpoint
        register_rest_route(
            'students/v1',
            '/students/active',
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_active_students' ),
                'permission_callback' => '__return_true',
                'args'                => array(
                    'per_page' => array(
                        'default'           => 10,
                        'sanitize_callback' => 'absint',
                        'validate_callback' => function( $param ) {
                            return is_numeric( $param ) && $param > 0 && $param <= 100;
                        },
                    ),
                    'page'     => array(
                        'default'           => 1,
                        'sanitize_callback' => 'absint',
                        'validate_callback' => function( $param ) {
                            return is_numeric( $param ) && $param > 0;
                        },
                    ),
                    'course'   => array(
                        'default'           => '',
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                    'grade_level' => array(
                        'default'           => '',
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                ),
            )
        );

        // Add new student endpoint (authenticated)
        register_rest_route(
            'students/v1',
            '/students',
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'create_student' ),
                'permission_callback' => array( $this, 'check_admin_permissions' ),
                'args'                => array(
                    'title' => array(
                        'required'          => true,
                        'sanitize_callback' => 'sanitize_text_field',
                        'validate_callback' => function( $param ) {
                            return ! empty( $param ) && strlen( $param ) <= 200;
                        },
                    ),
                    'content' => array(
                        'required'          => false,
                        'sanitize_callback' => 'wp_kses_post',
                        'default'           => '',
                    ),
                    'excerpt' => array(
                        'required'          => false,
                        'sanitize_callback' => 'sanitize_textarea_field',
                        'default'           => '',
                    ),
                    'student_id' => array(
                        'required'          => true,
                        'sanitize_callback' => 'sanitize_text_field',
                        'validate_callback' => function( $param ) {
                            return ! empty( $param ) && strlen( $param ) <= 50;
                        },
                    ),
                    'student_email' => array(
                        'required'          => false,
                        'sanitize_callback' => 'sanitize_email',
                        'validate_callback' => function( $param ) {
                            return empty( $param ) || is_email( $param );
                        },
                    ),
                    'student_phone' => array(
                        'required'          => false,
                        'sanitize_callback' => 'sanitize_text_field',
                        'default'           => '',
                    ),
                    'student_dob' => array(
                        'required'          => false,
                        'sanitize_callback' => 'sanitize_text_field',
                        'validate_callback' => function( $param ) {
                            return empty( $param ) || preg_match( '/^\d{4}-\d{2}-\d{2}$/', $param );
                        },
                    ),
                    'student_address' => array(
                        'required'          => false,
                        'sanitize_callback' => 'sanitize_textarea_field',
                        'default'           => '',
                    ),
                    'student_country' => array(
                        'required'          => false,
                        'sanitize_callback' => 'sanitize_text_field',
                        'default'           => '',
                    ),
                    'student_city' => array(
                        'required'          => false,
                        'sanitize_callback' => 'sanitize_text_field',
                        'default'           => '',
                    ),
                    'student_class_grade' => array(
                        'required'          => false,
                        'sanitize_callback' => 'sanitize_text_field',
                        'default'           => '',
                    ),
                    'student_is_active' => array(
                        'required'          => false,
                        'sanitize_callback' => 'sanitize_text_field',
                        'validate_callback' => function( $param ) {
                            return in_array( $param, array( 'active', 'inactive' ) );
                        },
                        'default'           => 'active',
                    ),
                    'courses' => array(
                        'required'          => false,
                        'type'              => 'array',
                        'items'             => array(
                            'type' => 'string',
                        ),
                        'default'           => array(),
                    ),
                    'grade_levels' => array(
                        'required'          => false,
                        'type'              => 'array',
                        'items'             => array(
                            'type' => 'string',
                        ),
                        'default'           => array(),
                    ),
                ),
            )
        );

        // Update student endpoint (authenticated)
        register_rest_route(
            'students/v1',
            '/students/(?P<id>\d+)',
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array( $this, 'update_student' ),
                'permission_callback' => array( $this, 'check_admin_permissions' ),
                'args'                => array(
                    'id' => array(
                        'validate_callback' => function( $param ) {
                            return is_numeric( $param ) && $param > 0;
                        },
                    ),
                    'title' => array(
                        'required'          => false,
                        'sanitize_callback' => 'sanitize_text_field',
                        'validate_callback' => function( $param ) {
                            return empty( $param ) || strlen( $param ) <= 200;
                        },
                    ),
                    'content' => array(
                        'required'          => false,
                        'sanitize_callback' => 'wp_kses_post',
                    ),
                    'excerpt' => array(
                        'required'          => false,
                        'sanitize_callback' => 'sanitize_textarea_field',
                    ),
                    'student_id' => array(
                        'required'          => false,
                        'sanitize_callback' => 'sanitize_text_field',
                        'validate_callback' => function( $param ) {
                            return empty( $param ) || strlen( $param ) <= 50;
                        },
                    ),
                    'student_email' => array(
                        'required'          => false,
                        'sanitize_callback' => 'sanitize_email',
                        'validate_callback' => function( $param ) {
                            return empty( $param ) || is_email( $param );
                        },
                    ),
                    'student_phone' => array(
                        'required'          => false,
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                    'student_dob' => array(
                        'required'          => false,
                        'sanitize_callback' => 'sanitize_text_field',
                        'validate_callback' => function( $param ) {
                            return empty( $param ) || preg_match( '/^\d{4}-\d{2}-\d{2}$/', $param );
                        },
                    ),
                    'student_address' => array(
                        'required'          => false,
                        'sanitize_callback' => 'sanitize_textarea_field',
                    ),
                    'student_country' => array(
                        'required'          => false,
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                    'student_city' => array(
                        'required'          => false,
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                    'student_class_grade' => array(
                        'required'          => false,
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                    'student_is_active' => array(
                        'required'          => false,
                        'sanitize_callback' => 'sanitize_text_field',
                        'validate_callback' => function( $param ) {
                            return empty( $param ) || in_array( $param, array( 'active', 'inactive' ) );
                        },
                    ),
                    'courses' => array(
                        'required'          => false,
                        'type'              => 'array',
                        'items'             => array(
                            'type' => 'string',
                        ),
                    ),
                    'grade_levels' => array(
                        'required'          => false,
                        'type'              => 'array',
                        'items'             => array(
                            'type' => 'string',
                        ),
                    ),
                ),
            )
        );

        // Delete student endpoint (authenticated)
        register_rest_route(
            'students/v1',
            '/students/(?P<id>\d+)',
            array(
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => array( $this, 'delete_student' ),
                'permission_callback' => array( $this, 'check_admin_permissions' ),
                'args'                => array(
                    'id' => array(
                        'validate_callback' => function( $param ) {
                            return is_numeric( $param ) && $param > 0;
                        },
                    ),
                    'force' => array(
                        'required'          => false,
                        'type'              => 'boolean',
                        'default'           => false,
                    ),
                ),
            )
        );
    }

    /**
     * Check admin permissions for authenticated endpoints
     *
     * @param WP_REST_Request $request The request object
     * @return bool|WP_Error
     */
    public function check_admin_permissions( $request ) {
        // Check if user is logged in
        if ( ! is_user_logged_in() ) {
            return new WP_Error(
                'rest_forbidden',
                __( 'Authentication required', 'students' ),
                array( 'status' => 401 )
            );
        }

        // Check if user has administrator capabilities
        if ( ! current_user_can( 'manage_options' ) ) {
            return new WP_Error(
                'rest_forbidden',
                __( 'Administrator privileges required', 'students' ),
                array( 'status' => 403 )
            );
        }

        return true;
    }

    /**
     * Set custom nonce lifetime to 10 minutes
     */
    public function set_custom_nonce_lifetime() {
        return 600; // 10 minutes in seconds
    }

    /**
     * Get custom nonce with 10-minute lifetime
     *
     * @param WP_REST_Request $request The request object
     * @return WP_REST_Response
     */
    public function get_custom_nonce( $request ) {
        $nonce = wp_create_nonce( 'wp_rest' );
        return new WP_REST_Response(
            array(
                'nonce' => $nonce,
                'expires_in' => 600, // 10 minutes in seconds
                'expires_at' => date( 'Y-m-d H:i:s', time() + 600 ),
                'message' => 'Nonce created with 10-minute lifetime'
            ),
            200
        );
    }

    /**
     * Create a new student
     *
     * @param WP_REST_Request $request The request object
     * @return WP_REST_Response|WP_Error
     */
    public function create_student( $request ) {
        // Get sanitized data
        $title = $request->get_param( 'title' );
        $content = $request->get_param( 'content' );
        $excerpt = $request->get_param( 'excerpt' );
        $student_id = $request->get_param( 'student_id' );
        $student_email = $request->get_param( 'student_email' );
        $student_phone = $request->get_param( 'student_phone' );
        $student_dob = $request->get_param( 'student_dob' );
        $student_address = $request->get_param( 'student_address' );
        $student_country = $request->get_param( 'student_country' );
        $student_city = $request->get_param( 'student_city' );
        $student_class_grade = $request->get_param( 'student_class_grade' );
        $student_is_active = $request->get_param( 'student_is_active' );
        $courses = $request->get_param( 'courses' );
        $grade_levels = $request->get_param( 'grade_levels' );

        // Enhanced validation with detailed error messages
        $validation_errors = array();

        // Required field validation
        if ( empty( $title ) ) {
            $validation_errors[] = 'Student name is required';
        }

        if ( empty( $student_id ) ) {
            $validation_errors[] = 'Student ID is required';
        }

        // Student ID format validation
        if ( ! empty( $student_id ) && ! preg_match( '/^[A-Z0-9_-]+$/', $student_id ) ) {
            $validation_errors[] = 'Student ID must contain only uppercase letters, numbers, hyphens, and underscores';
        }

        // Email validation
        if ( ! empty( $student_email ) && ! is_email( $student_email ) ) {
            $validation_errors[] = 'Invalid email format';
        }

        // Date of birth validation
        if ( ! empty( $student_dob ) ) {
            $dob_date = DateTime::createFromFormat( 'Y-m-d', $student_dob );
            if ( ! $dob_date || $dob_date->format( 'Y-m-d' ) !== $student_dob ) {
                $validation_errors[] = 'Date of birth must be in YYYY-MM-DD format';
            } else {
                $today = new DateTime();
                $age = $today->diff( $dob_date )->y;
                if ( $age < 5 || $age > 100 ) {
                    $validation_errors[] = 'Date of birth must represent an age between 5 and 100 years';
                }
            }
        }

        // Status validation
        if ( ! empty( $student_is_active ) && ! in_array( $student_is_active, array( 'active', 'inactive' ) ) ) {
            $validation_errors[] = 'Status must be either "active" or "inactive"';
        }

        // Phone number validation (basic)
        if ( ! empty( $student_phone ) && ! preg_match( '/^[\d\s\-\+\(\)]+$/', $student_phone ) ) {
            $validation_errors[] = 'Phone number contains invalid characters';
        }

        // Return validation errors if any
        if ( ! empty( $validation_errors ) ) {
            return new WP_Error(
                'validation_failed',
                __( 'Validation failed', 'students' ),
                array(
                    'status' => 400,
                    'errors' => $validation_errors,
                    'message' => 'Please correct the following errors: ' . implode( ', ', $validation_errors )
                )
            );
        }

        // Check if student ID already exists
        $existing_student = get_posts( array(
            'post_type' => Students_Config::POST_TYPE,
            'meta_query' => array(
                array(
                    'key' => '_student_id',
                    'value' => $student_id,
                    'compare' => '=',
                ),
            ),
            'post_status' => array( 'publish', 'draft', 'pending' ),
            'posts_per_page' => 1,
        ) );

        if ( ! empty( $existing_student ) ) {
            return new WP_Error(
                'student_id_exists',
                __( 'Student ID already exists', 'students' ),
                array( 
                    'status' => 409,
                    'existing_student_id' => $existing_student[0]->ID,
                    'message' => sprintf( 'A student with ID "%s" already exists (ID: %d)', $student_id, $existing_student[0]->ID )
                )
            );
        }

        // Create post data
        $post_data = array(
            'post_title'   => $title,
            'post_content' => $content,
            'post_excerpt' => $excerpt,
            'post_status'  => 'publish',
            'post_type'    => Students_Config::POST_TYPE,
        );

        // Insert the post
        $post_id = wp_insert_post( $post_data );

        if ( is_wp_error( $post_id ) ) {
            return new WP_Error(
                'create_failed',
                __( 'Failed to create student', 'students' ),
                array( 'status' => 500 )
            );
        }

        // Save meta fields
        $this->save_student_meta( $post_id, array(
            'student_id' => $student_id,
            'student_email' => $student_email,
            'student_phone' => $student_phone,
            'student_dob' => $student_dob,
            'student_address' => $student_address,
            'student_country' => $student_country,
            'student_city' => $student_city,
            'student_class_grade' => $student_class_grade,
            'student_is_active' => $student_is_active,
        ) );

        // Save taxonomies
        if ( ! empty( $courses ) ) {
            wp_set_object_terms( $post_id, $courses, Students_Config::TAXONOMY_COURSE );
        }
        if ( ! empty( $grade_levels ) ) {
            wp_set_object_terms( $post_id, $grade_levels, Students_Config::TAXONOMY_GRADE_LEVEL );
        }

        // Get the created student
        $student = get_post( $post_id );
        $student_data = $this->format_student_data( $student );

        $response = array(
            'success' => true,
            'message' => __( 'Student created successfully', 'students' ),
            'data'    => $student_data,
        );

        return new WP_REST_Response( $response, 201 );
    }

    /**
     * Update an existing student
     *
     * @param WP_REST_Request $request The request object
     * @return WP_REST_Response|WP_Error
     */
    public function update_student( $request ) {
        $post_id = $request->get_param( 'id' );

        // Enhanced validation for student ID
        if ( ! is_numeric( $post_id ) || $post_id <= 0 ) {
            return new WP_Error(
                'invalid_student_id',
                __( 'Invalid student ID', 'students' ),
                array(
                    'status' => 400,
                    'message' => 'Student ID must be a positive integer',
                    'provided_id' => $post_id,
                    'suggestion' => 'Please provide a valid numeric student ID'
                )
            );
        }

        // Check if student exists
        $student = get_post( $post_id );
        if ( ! $student ) {
            return new WP_Error(
                'student_not_found',
                __( 'Student not found', 'students' ),
                array(
                    'status' => 404,
                    'message' => sprintf( 'No student found with ID %d', $post_id ),
                    'provided_id' => $post_id,
                    'suggestion' => 'Please check the student ID or try listing all students first'
                )
            );
        }

        if ( $student->post_type !== Students_Config::POST_TYPE ) {
            return new WP_Error(
                'invalid_post_type',
                __( 'Invalid post type', 'students' ),
                array(
                    'status' => 400,
                    'message' => sprintf( 'Post ID %d is not a student (type: %s)', $post_id, $student->post_type ),
                    'provided_id' => $post_id,
                    'post_type' => $student->post_type
                )
            );
        }

        // Get sanitized data
        $title = $request->get_param( 'title' );
        $content = $request->get_param( 'content' );
        $excerpt = $request->get_param( 'excerpt' );
        $student_id = $request->get_param( 'student_id' );
        $student_email = $request->get_param( 'student_email' );
        $student_phone = $request->get_param( 'student_phone' );
        $student_dob = $request->get_param( 'student_dob' );
        $student_address = $request->get_param( 'student_address' );
        $student_country = $request->get_param( 'student_country' );
        $student_city = $request->get_param( 'student_city' );
        $student_class_grade = $request->get_param( 'student_class_grade' );
        $student_is_active = $request->get_param( 'student_is_active' );
        $courses = $request->get_param( 'courses' );
        $grade_levels = $request->get_param( 'grade_levels' );

        // Check if student ID already exists (if changed)
        if ( ! empty( $student_id ) ) {
            $current_student_id = get_post_meta( $post_id, '_student_id', true );
            if ( $student_id !== $current_student_id ) {
                $existing_student = get_posts( array(
                    'post_type' => Students_Config::POST_TYPE,
                    'meta_query' => array(
                        array(
                            'key' => '_student_id',
                            'value' => $student_id,
                            'compare' => '=',
                        ),
                    ),
                    'post_status' => array( 'publish', 'draft', 'pending' ),
                    'posts_per_page' => 1,
                    'post__not_in' => array( $post_id ),
                ) );

                if ( ! empty( $existing_student ) ) {
                    return new WP_Error(
                        'student_id_exists',
                        __( 'Student ID already exists', 'students' ),
                        array(
                            'status' => 409,
                            'existing_student_id' => $existing_student[0]->ID,
                            'message' => sprintf( 'A student with ID "%s" already exists (ID: %d)', $student_id, $existing_student[0]->ID ),
                            'current_student_id' => $post_id,
                            'suggestion' => 'Please choose a different student ID or update the existing student instead'
                        )
                    );
                }
            }
        }

        // Update post data
        $post_data = array(
            'ID' => $post_id,
        );

        if ( ! empty( $title ) ) {
            $post_data['post_title'] = $title;
        }
        if ( ! empty( $content ) ) {
            $post_data['post_content'] = $content;
        }
        if ( ! empty( $excerpt ) ) {
            $post_data['post_excerpt'] = $excerpt;
        }

        // Update the post
        $updated_post_id = wp_update_post( $post_data );

        if ( is_wp_error( $updated_post_id ) ) {
            return new WP_Error(
                'update_failed',
                __( 'Failed to update student', 'students' ),
                array( 'status' => 500 )
            );
        }

        // Update meta fields
        $meta_data = array();
        if ( ! empty( $student_id ) ) $meta_data['student_id'] = $student_id;
        if ( ! empty( $student_email ) ) $meta_data['student_email'] = $student_email;
        if ( ! empty( $student_phone ) ) $meta_data['student_phone'] = $student_phone;
        if ( ! empty( $student_dob ) ) $meta_data['student_dob'] = $student_dob;
        if ( ! empty( $student_address ) ) $meta_data['student_address'] = $student_address;
        if ( ! empty( $student_country ) ) $meta_data['student_country'] = $student_country;
        if ( ! empty( $student_city ) ) $meta_data['student_city'] = $student_city;
        if ( ! empty( $student_class_grade ) ) $meta_data['student_class_grade'] = $student_class_grade;
        if ( ! empty( $student_is_active ) ) $meta_data['student_is_active'] = $student_is_active;

        if ( ! empty( $meta_data ) ) {
            $this->save_student_meta( $post_id, $meta_data );
        }

        // Update taxonomies
        if ( ! empty( $courses ) ) {
            wp_set_object_terms( $post_id, $courses, Students_Config::TAXONOMY_COURSE );
        }
        if ( ! empty( $grade_levels ) ) {
            wp_set_object_terms( $post_id, $grade_levels, Students_Config::TAXONOMY_GRADE_LEVEL );
        }

        // Get the updated student
        $updated_student = get_post( $post_id );
        $student_data = $this->format_student_data( $updated_student );

        $response = array(
            'success' => true,
            'message' => __( 'Student updated successfully', 'students' ),
            'data'    => $student_data,
        );

        return new WP_REST_Response( $response, 200 );
    }

    /**
     * Delete a student
     *
     * @param WP_REST_Request $request The request object
     * @return WP_REST_Response|WP_Error
     */
    public function delete_student( $request ) {
        $post_id = $request->get_param( 'id' );
        $force = $request->get_param( 'force' );

        // Enhanced validation for student ID
        if ( ! is_numeric( $post_id ) || $post_id <= 0 ) {
            return new WP_Error(
                'invalid_student_id',
                __( 'Invalid student ID', 'students' ),
                array(
                    'status' => 400,
                    'message' => 'Student ID must be a positive integer',
                    'provided_id' => $post_id,
                    'suggestion' => 'Please provide a valid numeric student ID'
                )
            );
        }

        // Check if student exists
        $student = get_post( $post_id );
        if ( ! $student ) {
            return new WP_Error(
                'student_not_found',
                __( 'Student not found', 'students' ),
                array(
                    'status' => 404,
                    'message' => sprintf( 'No student found with ID %d', $post_id ),
                    'provided_id' => $post_id,
                    'suggestion' => 'Please check the student ID or try listing all students first'
                )
            );
        }

        if ( $student->post_type !== Students_Config::POST_TYPE ) {
            return new WP_Error(
                'invalid_post_type',
                __( 'Invalid post type', 'students' ),
                array(
                    'status' => 400,
                    'message' => sprintf( 'Post ID %d is not a student (type: %s)', $post_id, $student->post_type ),
                    'provided_id' => $post_id,
                    'post_type' => $student->post_type
                )
            );
        }

        // Delete the post
        $deleted = wp_delete_post( $post_id, $force );

        if ( ! $deleted ) {
            return new WP_Error(
                'delete_failed',
                __( 'Failed to delete student', 'students' ),
                array( 'status' => 500 )
            );
        }

        $response = array(
            'success' => true,
            'message' => $force ? __( 'Student permanently deleted', 'students' ) : __( 'Student moved to trash', 'students' ),
            'data'    => array(
                'id' => $post_id,
                'deleted' => true,
            ),
        );

        return new WP_REST_Response( $response, 200 );
    }

    /**
     * Save student meta fields
     *
     * @param int   $post_id The post ID
     * @param array $meta_data The meta data to save
     */
    private function save_student_meta( $post_id, $meta_data ) {
        $meta_fields = Students_Config::get_meta_field_names();

        foreach ( $meta_data as $field_name => $value ) {
            if ( in_array( $field_name, $meta_fields ) ) {
                $field_config = Students_Config::get_meta_field_config( $field_name );
                if ( $field_config ) {
                    $meta_key = $field_config['meta_key'];
                    
                    // Handle special cases
                    if ( $field_name === 'student_is_active' ) {
                        $value = ( $value === 'active' ) ? '1' : '0';
                    }
                    
                    update_post_meta( $post_id, $meta_key, $value );
                }
            }
        }
    }

    /**
     * Get all students
     *
     * @param WP_REST_Request $request The request object
     * @return WP_REST_Response|WP_Error
     */
    public function get_students( $request ) {
        $per_page   = $request->get_param( 'per_page' );
        $page       = $request->get_param( 'page' );
        $course     = $request->get_param( 'course' );
        $grade_level = $request->get_param( 'grade_level' );
        $status     = $request->get_param( 'status' );

        // Enhanced parameter validation
        $validation_errors = array();

        // Validate per_page
        if ( $per_page < 1 || $per_page > 100 ) {
            $validation_errors[] = 'per_page must be between 1 and 100';
        }

        // Validate page
        if ( $page < 1 ) {
            $validation_errors[] = 'page must be a positive integer';
        }

        // Validate status
        if ( ! empty( $status ) && ! in_array( $status, array( 'active', 'inactive' ) ) ) {
            $validation_errors[] = 'status must be either "active" or "inactive"';
        }

        // Return validation errors if any
        if ( ! empty( $validation_errors ) ) {
            return new WP_Error(
                'invalid_parameters',
                __( 'Invalid parameters', 'students' ),
                array(
                    'status' => 400,
                    'errors' => $validation_errors,
                    'message' => 'Please correct the following parameters: ' . implode( ', ', $validation_errors ),
                    'provided_parameters' => array(
                        'per_page' => $per_page,
                        'page' => $page,
                        'status' => $status
                    )
                )
            );
        }

        // Build query args
        $args = array(
            'post_type'      => Students_Config::POST_TYPE,
            'post_status'    => 'publish',
            'posts_per_page' => $per_page,
            'paged'          => $page,
            'orderby'        => 'title',
            'order'          => 'ASC',
        );

        // Add taxonomy filters
        $tax_query = array();
        
        if ( ! empty( $course ) ) {
            $tax_query[] = array(
                'taxonomy' => Students_Config::TAXONOMY_COURSE,
                'field'    => 'slug',
                'terms'    => $course,
            );
        }

        if ( ! empty( $grade_level ) ) {
            $tax_query[] = array(
                'taxonomy' => Students_Config::TAXONOMY_GRADE_LEVEL,
                'field'    => 'slug',
                'terms'    => $grade_level,
            );
        }

        if ( ! empty( $tax_query ) ) {
            if ( count( $tax_query ) > 1 ) {
                $tax_query['relation'] = 'AND';
            }
            $args['tax_query'] = $tax_query;
        }

        // Add meta query for status filter
        if ( ! empty( $status ) ) {
            $meta_value = ( $status === 'active' ) ? '1' : '0';
            $args['meta_query'] = array(
                array(
                    'key'     => '_student_is_active',
                    'value'   => $meta_value,
                    'compare' => '=',
                ),
            );
        }

        // Get students
        $query = new WP_Query( $args );
        $students = array();

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $students[] = $this->format_student_data( get_post() );
            }
        }

        wp_reset_postdata();

        // Prepare response
        $response = array(
            'success' => true,
            'data'    => array(
                'students' => $students,
                'pagination' => array(
                    'current_page' => $page,
                    'per_page'     => $per_page,
                    'total_posts'  => $query->found_posts,
                    'total_pages'  => $query->max_num_pages,
                ),
            ),
        );

        return new WP_REST_Response( $response, 200 );
    }

    /**
     * Get student by ID
     *
     * @param WP_REST_Request $request The request object
     * @return WP_REST_Response|WP_Error
     */
    public function get_student( $request ) {
        $student_id = $request->get_param( 'id' );

        // Enhanced validation for student ID
        if ( ! is_numeric( $student_id ) || $student_id <= 0 ) {
            return new WP_Error(
                'invalid_student_id',
                __( 'Invalid student ID', 'students' ),
                array(
                    'status' => 400,
                    'message' => 'Student ID must be a positive integer',
                    'provided_id' => $student_id,
                    'suggestion' => 'Please provide a valid numeric student ID'
                )
            );
        }

        // Get the student post
        $student = get_post( $student_id );

        if ( ! $student ) {
            return new WP_Error(
                'student_not_found',
                __( 'Student not found', 'students' ),
                array(
                    'status' => 404,
                    'message' => sprintf( 'No student found with ID %d', $student_id ),
                    'provided_id' => $student_id,
                    'suggestion' => 'Please check the student ID or try listing all students first'
                )
            );
        }

        if ( $student->post_type !== Students_Config::POST_TYPE ) {
            return new WP_Error(
                'invalid_post_type',
                __( 'Invalid post type', 'students' ),
                array(
                    'status' => 400,
                    'message' => sprintf( 'Post ID %d is not a student (type: %s)', $student_id, $student->post_type ),
                    'provided_id' => $student_id,
                    'post_type' => $student->post_type
                )
            );
        }

        if ( $student->post_status !== 'publish' ) {
            return new WP_Error(
                'student_not_accessible',
                __( 'Student is not accessible', 'students' ),
                array(
                    'status' => 403,
                    'message' => sprintf( 'Student ID %d exists but is not published (status: %s)', $student_id, $student->post_status ),
                    'provided_id' => $student_id,
                    'post_status' => $student->post_status,
                    'suggestion' => 'Only published students are accessible via the public API'
                )
            );
        }

        // Format student data
        $student_data = $this->format_student_data( $student );

        $response = array(
            'success' => true,
            'data'    => $student_data,
        );

        return new WP_REST_Response( $response, 200 );
    }

    /**
     * Get active students only
     *
     * @param WP_REST_Request $request The request object
     * @return WP_REST_Response|WP_Error
     */
    public function get_active_students( $request ) {
        $per_page   = $request->get_param( 'per_page' );
        $page       = $request->get_param( 'page' );
        $course     = $request->get_param( 'course' );
        $grade_level = $request->get_param( 'grade_level' );

        // Build query args - only active students
        $args = array(
            'post_type'      => Students_Config::POST_TYPE,
            'post_status'    => 'publish',
            'posts_per_page' => $per_page,
            'paged'          => $page,
            'orderby'        => 'title',
            'order'          => 'ASC',
            'meta_query'     => array(
                array(
                    'key'     => '_student_is_active',
                    'value'   => '1',
                    'compare' => '=',
                ),
            ),
        );

        // Add taxonomy filters
        $tax_query = array();
        
        if ( ! empty( $course ) ) {
            $tax_query[] = array(
                'taxonomy' => Students_Config::TAXONOMY_COURSE,
                'field'    => 'slug',
                'terms'    => $course,
            );
        }

        if ( ! empty( $grade_level ) ) {
            $tax_query[] = array(
                'taxonomy' => Students_Config::TAXONOMY_GRADE_LEVEL,
                'field'    => 'slug',
                'terms'    => $grade_level,
            );
        }

        if ( ! empty( $tax_query ) ) {
            if ( count( $tax_query ) > 1 ) {
                $tax_query['relation'] = 'AND';
            }
            $args['tax_query'] = $tax_query;
        }

        // Get students
        $query = new WP_Query( $args );
        $students = array();

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $students[] = $this->format_student_data( get_post() );
            }
        }

        wp_reset_postdata();

        // Prepare response
        $response = array(
            'success' => true,
            'data'    => array(
                'students' => $students,
                'pagination' => array(
                    'current_page' => $page,
                    'per_page'     => $per_page,
                    'total_posts'  => $query->found_posts,
                    'total_pages'  => $query->max_num_pages,
                ),
                'filter' => 'active_only',
            ),
        );

        return new WP_REST_Response( $response, 200 );
    }

    /**
     * Format student data for API response
     *
     * @param WP_Post $post The student post object
     * @return array
     */
    private function format_student_data( $post ) {
        // Get meta fields
        $meta_fields = Students_Config::get_meta_field_names();
        $meta_data = array();

        foreach ( $meta_fields as $field_name ) {
            $field_config = Students_Config::get_meta_field_config( $field_name );
            if ( $field_config ) {
                $meta_key = $field_config['meta_key'];
                $value = get_post_meta( $post->ID, $meta_key, true );
                
                // Handle special cases
                if ( $field_name === 'student_is_active' ) {
                    $value = ( $value === '1' ) ? 'active' : 'inactive';
                }
                
                $meta_data[ $field_name ] = $value;
            }
        }

        // Get taxonomies
        $courses = wp_get_post_terms( $post->ID, Students_Config::TAXONOMY_COURSE, array( 'fields' => 'names' ) );
        $grade_levels = wp_get_post_terms( $post->ID, Students_Config::TAXONOMY_GRADE_LEVEL, array( 'fields' => 'names' ) );

        // Get featured image
        $featured_image = null;
        if ( has_post_thumbnail( $post->ID ) ) {
            $image_id = get_post_thumbnail_id( $post->ID );
            $image_data = wp_get_attachment_image_src( $image_id, 'medium' );
            if ( $image_data ) {
                $featured_image = array(
                    'url'    => $image_data[0],
                    'width'  => $image_data[1],
                    'height' => $image_data[2],
                    'alt'    => get_post_meta( $image_id, '_wp_attachment_image_alt', true ),
                );
            }
        }

        return array(
            'id'              => $post->ID,
            'title'           => $post->post_title,
            'content'         => $post->post_content,
            'excerpt'         => $post->post_excerpt,
            'slug'            => $post->post_name,
            'date'            => $post->post_date,
            'modified'        => $post->post_modified,
            'status'          => $post->post_status,
            'featured_image'  => $featured_image,
            'meta'            => $meta_data,
            'taxonomies'      => array(
                'courses'      => $courses,
                'grade_levels' => $grade_levels,
            ),
            'links'           => array(
                'self'         => rest_url( 'students/v1/students/' . $post->ID ),
                'collection'   => rest_url( 'students/v1/students' ),
                'website'      => get_permalink( $post->ID ),
            ),
        );
    }
}
