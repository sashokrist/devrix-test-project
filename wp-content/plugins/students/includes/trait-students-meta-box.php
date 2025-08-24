<?php
/**
 * Students Meta Box Trait
 *
 * Common meta box handling patterns for the Students plugin
 *
 * @package Students
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Students Meta Box Trait
 *
 * @since 1.0.0
 */
trait Students_Meta_Box {

    /**
     * Save meta fields
     *
     * @param int   $post_id Post ID
     * @param array $fields Meta fields to save
     * @return void
     */
    protected function save_meta_fields( $post_id, $fields ) {
        // Verify nonce
        if ( ! isset( $_POST['students_meta_nonce'] ) || ! wp_verify_nonce( $_POST['students_meta_nonce'], 'students_save_meta' ) ) {
            return;
        }

        // Check autosave
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // Check permissions
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Save each field
        foreach ( $fields as $field_name ) {
            if ( isset( $_POST[ $field_name ] ) ) {
                $value = $this->sanitize_meta_field( $field_name, $_POST[ $field_name ] );
                $config = Students_Config::get_meta_field_config( $field_name );
                $meta_key = isset( $config['meta_key'] ) ? $config['meta_key'] : '_' . $field_name;
                update_post_meta( $post_id, $meta_key, $value );
            } else {
                $config = Students_Config::get_meta_field_config( $field_name );
                $meta_key = isset( $config['meta_key'] ) ? $config['meta_key'] : '_' . $field_name;
                delete_post_meta( $post_id, $meta_key );
            }
        }
    }

    /**
     * Get meta fields
     *
     * @param int $post_id Post ID
     * @return array Meta fields
     */
    protected function get_meta_fields( $post_id ) {
        $fields = array();
        $meta_field_names = Students_Config::get_meta_field_names();

        foreach ( $meta_field_names as $field_name ) {
            $config = Students_Config::get_meta_field_config( $field_name );
            $meta_key = isset( $config['meta_key'] ) ? $config['meta_key'] : '_' . $field_name;
            $fields[ $field_name ] = get_post_meta( $post_id, $meta_key, true );
        }

        return $fields;
    }

    /**
     * Sanitize meta field
     *
     * @param string $field_name Field name
     * @param mixed  $value Field value
     * @return mixed Sanitized value
     */
    protected function sanitize_meta_field( $field_name, $value ) {
        $config = Students_Config::get_meta_field_config( $field_name );
        
        if ( ! $config ) {
            return sanitize_text_field( $value );
        }

        switch ( $config['type'] ) {
            case 'email':
                return sanitize_email( $value );
            case 'textarea':
                return sanitize_textarea_field( $value );
            case 'date':
                return sanitize_text_field( $value );
            case 'tel':
                return sanitize_text_field( $value );
            default:
                return sanitize_text_field( $value );
        }
    }

    /**
     * Render meta field
     *
     * @param string $field_name Field name
     * @param mixed  $value Field value
     * @param array  $config Field configuration
     * @return void
     */
    protected function render_meta_field( $field_name, $value, $config ) {
        $field_id = 'students_' . $field_name;
        $field_name_attr = $field_name;
        $required = isset( $config['required'] ) && $config['required'] ? 'required' : '';

        echo '<p>';
        echo '<label for="' . esc_attr( $field_id ) . '">' . esc_html( $config['label'] ) . '</label>';

        switch ( $config['type'] ) {
            case 'textarea':
                echo '<textarea id="' . esc_attr( $field_id ) . '" name="' . esc_attr( $field_name_attr ) . '" ' . $required . ' rows="3" cols="50">' . esc_textarea( $value ) . '</textarea>';
                break;
            case 'email':
                echo '<input type="email" id="' . esc_attr( $field_id ) . '" name="' . esc_attr( $field_name_attr ) . '" value="' . esc_attr( $value ) . '" ' . $required . ' />';
                break;
            case 'date':
                echo '<input type="date" id="' . esc_attr( $field_id ) . '" name="' . esc_attr( $field_name_attr ) . '" value="' . esc_attr( $value ) . '" ' . $required . ' />';
                break;
            case 'tel':
                echo '<input type="tel" id="' . esc_attr( $field_id ) . '" name="' . esc_attr( $field_name_attr ) . '" value="' . esc_attr( $value ) . '" ' . $required . ' />';
                break;
            case 'select':
                echo '<select id="' . esc_attr( $field_id ) . '" name="' . esc_attr( $field_name_attr ) . '" ' . $required . '>';
                if ( isset( $config['options'] ) && is_array( $config['options'] ) ) {
                    foreach ( $config['options'] as $option_value => $option_label ) {
                        $selected = selected( $value, $option_value, false );
                        echo '<option value="' . esc_attr( $option_value ) . '" ' . $selected . '>' . esc_html( $option_label ) . '</option>';
                    }
                }
                echo '</select>';
                break;
            default:
                echo '<input type="text" id="' . esc_attr( $field_id ) . '" name="' . esc_attr( $field_name_attr ) . '" value="' . esc_attr( $value ) . '" ' . $required . ' />';
                break;
        }

        echo '</p>';
    }

    /**
     * Render meta box content
     *
     * @param WP_Post $post Post object
     * @return void
     */
    protected function render_meta_box_content( $post ) {
        // Add nonce field
        wp_nonce_field( 'students_save_meta', 'students_meta_nonce' );

        // Get current meta values
        $meta_fields = $this->get_meta_fields( $post->ID );

        // Render each field
        foreach ( Students_Config::META_FIELDS as $field_name => $config ) {
            $value = isset( $meta_fields[ $field_name ] ) ? $meta_fields[ $field_name ] : '';
            $this->render_meta_field( $field_name, $value, $config );
        }
    }
}
