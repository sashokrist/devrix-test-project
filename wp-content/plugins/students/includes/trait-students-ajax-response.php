<?php
/**
 * Students AJAX Response Trait
 *
 * Common AJAX response patterns for the Students plugin
 *
 * @package Students
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Students AJAX Response Trait
 *
 * @since 1.0.0
 */
trait Students_Ajax_Response {

    /**
     * Send JSON success response
     *
     * @param mixed $data Response data
     * @param string $message Success message
     * @return void
     */
    protected function send_json_success( $data = null, $message = '' ) {
        $response = array(
            'success' => true,
            'message' => $message,
        );

        if ( $data !== null ) {
            $response['data'] = $data;
        }

        wp_send_json( $response );
    }

    /**
     * Send JSON error response
     *
     * @param string $message Error message
     * @param int $code Error code
     * @param mixed $data Additional error data
     * @return void
     */
    protected function send_json_error( $message = '', $code = 0, $data = null ) {
        $response = array(
            'success' => false,
            'message' => $message,
            'code' => $code,
        );

        if ( $data !== null ) {
            $response['data'] = $data;
        }

        wp_send_json( $response );
    }

    /**
     * Verify nonce
     *
     * @param string $nonce Nonce value
     * @param string $action Nonce action
     * @return bool
     */
    protected function verify_nonce( $nonce, $action = 'students_ajax_nonce' ) {
        if ( ! wp_verify_nonce( $nonce, $action ) ) {
            $this->send_json_error( 'Invalid nonce', 403 );
            return false;
        }
        return true;
    }

    /**
     * Check user capabilities
     *
     * @param string $capability Required capability
     * @return bool
     */
    protected function check_capability( $capability = 'edit_posts' ) {
        if ( ! current_user_can( $capability ) ) {
            $this->send_json_error( 'Insufficient permissions', 403 );
            return false;
        }
        return true;
    }

    /**
     * Validate required fields
     *
     * @param array $data Data to validate
     * @param array $required_fields Required field names
     * @return bool
     */
    protected function validate_required_fields( $data, $required_fields ) {
        foreach ( $required_fields as $field ) {
            if ( empty( $data[ $field ] ) ) {
                $this->send_json_error( sprintf( 'Field "%s" is required', $field ), 400 );
                return false;
            }
        }
        return true;
    }

    /**
     * Sanitize student data
     *
     * @param array $data Raw data
     * @return array Sanitized data
     */
    protected function sanitize_student_data( $data ) {
        $sanitized = array();

        // Basic post data
        if ( isset( $data['title'] ) ) {
            $sanitized['post_title'] = sanitize_text_field( $data['title'] );
        }
        if ( isset( $data['content'] ) ) {
            $sanitized['post_content'] = wp_kses_post( $data['content'] );
        }
        if ( isset( $data['status'] ) ) {
            $sanitized['post_status'] = sanitize_text_field( $data['status'] );
        }

        // Meta fields
        $meta_fields = Students_Config::get_meta_field_names();
        foreach ( $meta_fields as $field ) {
            if ( isset( $data[ $field ] ) ) {
                $config = Students_Config::get_meta_field_config( $field );
                if ( $config ) {
                    switch ( $config['type'] ) {
                        case 'email':
                            $sanitized[ $field ] = sanitize_email( $data[ $field ] );
                            break;
                        case 'textarea':
                            $sanitized[ $field ] = sanitize_textarea_field( $data[ $field ] );
                            break;
                        default:
                            $sanitized[ $field ] = sanitize_text_field( $data[ $field ] );
                            break;
                    }
                }
            }
        }

        return $sanitized;
    }

    /**
     * Handle database errors
     *
     * @param WP_Error $error WordPress error object
     * @return void
     */
    protected function handle_database_error( $error ) {
        if ( is_wp_error( $error ) ) {
            $this->send_json_error( $error->get_error_message(), 500 );
        }
    }
}
