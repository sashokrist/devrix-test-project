<?php
/**
 * AJAX Handler for Students Plugin
 *
 * @package Students
 * @version 1.0.0
 */

// Prevent direct access to this file
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * AJAX Handler Class
 */
class Students_Ajax_Handler {

    /**
     * Constructor
     */
    public function __construct() {
        $this->init_hooks();
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Admin AJAX actions (logged in users)
        add_action( 'wp_ajax_students_save_student', array( $this, 'save_student' ) );
        add_action( 'wp_ajax_students_delete_student', array( $this, 'delete_student' ) );
        add_action( 'wp_ajax_students_get_student', array( $this, 'get_student' ) );
        add_action( 'wp_ajax_students_search_students', array( $this, 'search_students' ) );
        add_action( 'wp_ajax_students_auto_save', array( $this, 'auto_save_student' ) );
        add_action( 'wp_ajax_students_validate_field', array( $this, 'validate_field' ) );
        
        // Public AJAX actions (non-logged in users)
        add_action( 'wp_ajax_nopriv_students_search_students', array( $this, 'search_students' ) );
        add_action( 'wp_ajax_nopriv_students_get_student', array( $this, 'get_student' ) );
    }

    /**
     * Save student data
     */
    public function save_student() {
        // Verify nonce
        if ( ! wp_verify_nonce( $_POST['nonce'], 'students_ajax_nonce' ) ) {
            wp_send_json_error( 'Invalid nonce' );
        }

        // Check permissions
        if ( ! current_user_can( 'edit_posts' ) ) {
            wp_send_json_error( 'Insufficient permissions' );
        }

        // Sanitize input data
        $student_data = array(
            'post_title'   => sanitize_text_field( $_POST['title'] ?? '' ),
            'post_content' => wp_kses_post( $_POST['content'] ?? '' ),
            'post_status'  => sanitize_text_field( $_POST['status'] ?? 'draft' ),
            'post_type'    => 'student'
        );

        // Validate required fields
        if ( empty( $student_data['post_title'] ) ) {
            wp_send_json_error( 'Student name is required' );
        }

        // Handle post ID for updates
        $post_id = intval( $_POST['post_id'] ?? 0 );
        if ( $post_id > 0 ) {
            $student_data['ID'] = $post_id;
            $result = wp_update_post( $student_data );
        } else {
            $result = wp_insert_post( $student_data );
        }

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( $result->get_error_message() );
        }

        // Save meta data
        $this->save_student_meta( $result, $_POST );

        wp_send_json_success( array(
            'message' => 'Student saved successfully',
            'post_id' => $result,
            'redirect_url' => admin_url( 'edit.php?post_type=student' )
        ) );
    }

    /**
     * Auto-save student data
     */
    public function auto_save_student() {
        // Verify nonce
        if ( ! wp_verify_nonce( $_POST['nonce'], 'students_ajax_nonce' ) ) {
            wp_send_json_error( 'Invalid nonce' );
        }

        // Check permissions
        if ( ! current_user_can( 'edit_posts' ) ) {
            wp_send_json_error( 'Insufficient permissions' );
        }

        // Sanitize input data
        $student_data = array(
            'post_title'   => sanitize_text_field( $_POST['title'] ?? '' ),
            'post_content' => wp_kses_post( $_POST['content'] ?? '' ),
            'post_status'  => 'auto-draft', // Auto-save as draft
            'post_type'    => 'student'
        );

        // Handle post ID for updates
        $post_id = intval( $_POST['post_id'] ?? 0 );
        if ( $post_id > 0 ) {
            $student_data['ID'] = $post_id;
            $result = wp_update_post( $student_data );
        } else {
            $result = wp_insert_post( $student_data );
        }

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( $result->get_error_message() );
        }

        // Save meta data
        $this->save_student_meta( $result, $_POST );

        wp_send_json_success( array(
            'message' => 'Auto-saved successfully',
            'post_id' => $result,
            'timestamp' => current_time( 'mysql' )
        ) );
    }

    /**
     * Delete student
     */
    public function delete_student() {
        // Verify nonce
        if ( ! wp_verify_nonce( $_POST['nonce'], 'students_ajax_nonce' ) ) {
            wp_send_json_error( 'Invalid nonce' );
        }

        // Check permissions
        if ( ! current_user_can( 'delete_posts' ) ) {
            wp_send_json_error( 'Insufficient permissions' );
        }

        $post_id = intval( $_POST['post_id'] ?? 0 );
        if ( $post_id <= 0 ) {
            wp_send_json_error( 'Invalid student ID' );
        }

        $result = wp_delete_post( $post_id, true );
        if ( ! $result ) {
            wp_send_json_error( 'Failed to delete student' );
        }

        wp_send_json_success( array(
            'message' => 'Student deleted successfully',
            'redirect_url' => admin_url( 'edit.php?post_type=student' )
        ) );
    }

    /**
     * Get student data
     */
    public function get_student() {
        // Verify nonce
        if ( ! wp_verify_nonce( $_POST['nonce'], 'students_ajax_nonce' ) ) {
            wp_send_json_error( 'Invalid nonce' );
        }

        $post_id = intval( $_POST['post_id'] ?? 0 );
        if ( $post_id <= 0 ) {
            wp_send_json_error( 'Invalid student ID' );
        }

        $student = get_post( $post_id );
        if ( ! $student || $student->post_type !== 'student' ) {
            wp_send_json_error( 'Student not found' );
        }

        // Get meta data
        $meta_data = array(
            'student_id' => get_post_meta( $post_id, '_student_id', true ),
            'student_email' => get_post_meta( $post_id, '_student_email', true ),
            'student_phone' => get_post_meta( $post_id, '_student_phone', true ),
            'student_address' => get_post_meta( $post_id, '_student_address', true ),
            'student_birth_date' => get_post_meta( $post_id, '_student_birth_date', true ),
            'student_gender' => get_post_meta( $post_id, '_student_gender', true ),
        );

        // Get taxonomies
        $courses = wp_get_post_terms( $post_id, 'course', array( 'fields' => 'names' ) );
        $grade_levels = wp_get_post_terms( $post_id, 'grade_level', array( 'fields' => 'names' ) );

        wp_send_json_success( array(
            'student' => $student,
            'meta_data' => $meta_data,
            'courses' => $courses,
            'grade_levels' => $grade_levels
        ) );
    }

    /**
     * Search students
     */
    public function search_students() {
        // Verify nonce
        if ( ! wp_verify_nonce( $_POST['nonce'], 'students_ajax_nonce' ) ) {
            wp_send_json_error( 'Invalid nonce' );
        }

        $search_term = sanitize_text_field( $_POST['search'] ?? '' );
        $course = sanitize_text_field( $_POST['course'] ?? '' );
        $grade_level = sanitize_text_field( $_POST['grade_level'] ?? '' );

        $args = array(
            'post_type' => 'student',
            'post_status' => 'publish',
            'posts_per_page' => 10,
            's' => $search_term
        );

        // Add taxonomy filters
        $tax_query = array();
        if ( ! empty( $course ) ) {
            $tax_query[] = array(
                'taxonomy' => 'course',
                'field' => 'slug',
                'terms' => $course
            );
        }
        if ( ! empty( $grade_level ) ) {
            $tax_query[] = array(
                'taxonomy' => 'grade_level',
                'field' => 'slug',
                'terms' => $grade_level
            );
        }
        if ( ! empty( $tax_query ) ) {
            $args['tax_query'] = array(
                'relation' => 'AND',
                $tax_query
            );
        }

        $query = new WP_Query( $args );
        $students = array();

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $post_id = get_the_ID();
                
                $students[] = array(
                    'id' => $post_id,
                    'title' => get_the_title(),
                    'content' => get_the_excerpt(),
                    'url' => get_permalink(),
                    'student_id' => get_post_meta( $post_id, '_student_id', true ),
                    'student_email' => get_post_meta( $post_id, '_student_email', true ),
                    'courses' => wp_get_post_terms( $post_id, 'course', array( 'fields' => 'names' ) ),
                    'grade_levels' => wp_get_post_terms( $post_id, 'grade_level', array( 'fields' => 'names' ) )
                );
            }
        }
        wp_reset_postdata();

        wp_send_json_success( array(
            'students' => $students,
            'total' => $query->found_posts
        ) );
    }

    /**
     * Validate field
     */
    public function validate_field() {
        // Verify nonce
        if ( ! wp_verify_nonce( $_POST['nonce'], 'students_ajax_nonce' ) ) {
            wp_send_json_error( 'Invalid nonce' );
        }

        $field_name = sanitize_text_field( $_POST['field_name'] ?? '' );
        $field_value = sanitize_text_field( $_POST['field_value'] ?? '' );
        $post_id = intval( $_POST['post_id'] ?? 0 );

        $validation_result = $this->validate_student_field( $field_name, $field_value, $post_id );

        if ( $validation_result['valid'] ) {
            wp_send_json_success( array(
                'message' => 'Field is valid',
                'field_name' => $field_name
            ) );
        } else {
            wp_send_json_error( array(
                'message' => $validation_result['message'],
                'field_name' => $field_name
            ) );
        }
    }

    /**
     * Save student meta data
     */
    private function save_student_meta( $post_id, $data ) {
        $meta_fields = array(
            '_student_id' => 'student_id',
            '_student_email' => 'student_email',
            '_student_phone' => 'student_phone',
            '_student_address' => 'student_address',
            '_student_birth_date' => 'student_birth_date',
            '_student_gender' => 'student_gender'
        );

        foreach ( $meta_fields as $meta_key => $field_name ) {
            if ( isset( $data[ $field_name ] ) ) {
                $value = sanitize_text_field( $data[ $field_name ] );
                update_post_meta( $post_id, $meta_key, $value );
            }
        }

        // Save taxonomies
        if ( isset( $data['courses'] ) ) {
            wp_set_object_terms( $post_id, $data['courses'], 'course' );
        }
        if ( isset( $data['grade_levels'] ) ) {
            wp_set_object_terms( $post_id, $data['grade_levels'], 'grade_level' );
        }
    }

    /**
     * Validate student field
     */
    private function validate_student_field( $field_name, $field_value, $post_id = 0 ) {
        switch ( $field_name ) {
            case 'title':
                if ( empty( $field_value ) ) {
                    return array( 'valid' => false, 'message' => 'Student name is required' );
                }
                if ( strlen( $field_value ) < 2 ) {
                    return array( 'valid' => false, 'message' => 'Student name must be at least 2 characters' );
                }
                break;

            case 'student_email':
                if ( ! empty( $field_value ) && ! is_email( $field_value ) ) {
                    return array( 'valid' => false, 'message' => 'Please enter a valid email address' );
                }
                // Check for duplicate email
                if ( ! empty( $field_value ) ) {
                    $existing_posts = get_posts( array(
                        'post_type' => 'student',
                        'meta_query' => array(
                            array(
                                'key' => '_student_email',
                                'value' => $field_value,
                                'compare' => '='
                            )
                        ),
                        'post__not_in' => array( $post_id ),
                        'posts_per_page' => 1
                    ) );
                    if ( ! empty( $existing_posts ) ) {
                        return array( 'valid' => false, 'message' => 'This email address is already in use' );
                    }
                }
                break;

            case 'student_id':
                if ( ! empty( $field_value ) ) {
                    // Check for duplicate student ID
                    $existing_posts = get_posts( array(
                        'post_type' => 'student',
                        'meta_query' => array(
                            array(
                                'key' => '_student_id',
                                'value' => $field_value,
                                'compare' => '='
                            )
                        ),
                        'post__not_in' => array( $post_id ),
                        'posts_per_page' => 1
                    ) );
                    if ( ! empty( $existing_posts ) ) {
                        return array( 'valid' => false, 'message' => 'This student ID is already in use' );
                    }
                }
                break;
        }

        return array( 'valid' => true, 'message' => '' );
    }
}

// Initialize the AJAX handler
new Students_Ajax_Handler();
