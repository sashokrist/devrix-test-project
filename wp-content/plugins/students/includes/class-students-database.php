<?php
/**
 * Students Database Class
 *
 * Centralized database operations for the Students plugin
 *
 * @package Students
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Students Database Class
 *
 * @since 1.0.0
 */
class Students_Database {

    /**
     * Get students with optional filters
     *
     * @param array $args Query arguments
     * @return WP_Query
     */
    public static function get_students( $args = array() ) {
        $defaults = array(
            'post_type' => Students_Config::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => Students_Config::get_option( 'students_per_page', 12 ),
            'orderby' => 'title',
            'order' => 'ASC',
        );

        $args = wp_parse_args( $args, $defaults );

        return new WP_Query( $args );
    }

    /**
     * Get student by slug
     *
     * @param string $slug Student slug
     * @return WP_Post|null
     */
    public static function get_student_by_slug( $slug ) {
        $args = array(
            'post_type' => Students_Config::POST_TYPE,
            'post_status' => 'publish',
            'name' => $slug,
            'posts_per_page' => 1,
        );

        $query = new WP_Query( $args );

        return $query->have_posts() ? $query->posts[0] : null;
    }

    /**
     * Get student by ID
     *
     * @param int $student_id Student ID
     * @return WP_Post|null
     */
    public static function get_student_by_id( $student_id ) {
        $post = get_post( $student_id );

        if ( $post && $post->post_type === Students_Config::POST_TYPE ) {
            return $post;
        }

        return null;
    }

    /**
     * Get students by course
     *
     * @param string $course_slug Course slug
     * @param array  $args Additional query arguments
     * @return WP_Query
     */
    public static function get_students_by_course( $course_slug, $args = array() ) {
        $defaults = array(
            'post_type' => Students_Config::POST_TYPE,
            'post_status' => 'publish',
            'tax_query' => array(
                array(
                    'taxonomy' => Students_Config::TAXONOMY_COURSE,
                    'field' => 'slug',
                    'terms' => $course_slug,
                ),
            ),
        );

        $args = wp_parse_args( $args, $defaults );

        return new WP_Query( $args );
    }

    /**
     * Get students by grade level
     *
     * @param string $grade_slug Grade level slug
     * @param array  $args Additional query arguments
     * @return WP_Query
     */
    public static function get_students_by_grade_level( $grade_slug, $args = array() ) {
        $defaults = array(
            'post_type' => Students_Config::POST_TYPE,
            'post_status' => 'publish',
            'tax_query' => array(
                array(
                    'taxonomy' => Students_Config::TAXONOMY_GRADE_LEVEL,
                    'field' => 'slug',
                    'terms' => $grade_slug,
                ),
            ),
        );

        $args = wp_parse_args( $args, $defaults );

        return new WP_Query( $args );
    }

    /**
     * Search students
     *
     * @param string $search_term Search term
     * @param array  $args Additional query arguments
     * @return WP_Query
     */
    public static function search_students( $search_term, $args = array() ) {
        $defaults = array(
            'post_type' => Students_Config::POST_TYPE,
            'post_status' => 'publish',
            's' => $search_term,
        );

        $args = wp_parse_args( $args, $defaults );

        return new WP_Query( $args );
    }

    /**
     * Get student meta fields
     *
     * @param int $student_id Student ID
     * @return array Meta fields
     */
    public static function get_student_meta( $student_id ) {
        $meta_fields = array();
        $meta_field_names = Students_Config::get_meta_field_names();

        foreach ( $meta_field_names as $field_name ) {
            $config = Students_Config::get_meta_field_config( $field_name );
            $meta_key = isset( $config['meta_key'] ) ? $config['meta_key'] : '_' . $field_name;
            $meta_fields[ $field_name ] = get_post_meta( $student_id, $meta_key, true );
        }

        return $meta_fields;
    }

    /**
     * Update student meta fields
     *
     * @param int   $student_id Student ID
     * @param array $meta_data Meta data to update
     * @return bool
     */
    public static function update_student_meta( $student_id, $meta_data ) {
        $meta_field_names = Students_Config::get_meta_field_names();
        $updated = true;

        foreach ( $meta_field_names as $field_name ) {
            if ( isset( $meta_data[ $field_name ] ) ) {
                $config = Students_Config::get_meta_field_config( $field_name );
                $meta_key = isset( $config['meta_key'] ) ? $config['meta_key'] : '_' . $field_name;
                $value = self::sanitize_meta_field( $field_name, $meta_data[ $field_name ] );
                
                $result = update_post_meta( $student_id, $meta_key, $value );
                if ( false === $result ) {
                    $updated = false;
                }
            }
        }

        return $updated;
    }

    /**
     * Delete student meta field
     *
     * @param int    $student_id Student ID
     * @param string $meta_key Meta key to delete
     * @return bool
     */
    public static function delete_student_meta( $student_id, $meta_key ) {
        return delete_post_meta( $student_id, $meta_key );
    }

    /**
     * Sanitize meta field
     *
     * @param string $field_name Field name
     * @param mixed  $value Field value
     * @return mixed Sanitized value
     */
    private static function sanitize_meta_field( $field_name, $value ) {
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
     * Get courses
     *
     * @param array $args Taxonomy arguments
     * @return array Courses
     */
    public static function get_courses( $args = array() ) {
        $defaults = array(
            'taxonomy' => Students_Config::TAXONOMY_COURSE,
            'hide_empty' => false,
        );

        $args = wp_parse_args( $args, $defaults );

        return get_terms( $args );
    }

    /**
     * Get grade levels
     *
     * @param array $args Taxonomy arguments
     * @return array Grade levels
     */
    public static function get_grade_levels( $args = array() ) {
        $defaults = array(
            'taxonomy' => Students_Config::TAXONOMY_GRADE_LEVEL,
            'hide_empty' => false,
        );

        $args = wp_parse_args( $args, $defaults );

        return get_terms( $args );
    }
}
