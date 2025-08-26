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
    }

    /**
     * Register REST API routes
     */
    public function register_routes() {
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

        // Get the student post
        $student = get_post( $student_id );

        if ( ! $student || $student->post_type !== Students_Config::POST_TYPE ) {
            return new WP_Error(
                'student_not_found',
                __( 'Student not found', 'students' ),
                array( 'status' => 404 )
            );
        }

        if ( $student->post_status !== 'publish' ) {
            return new WP_Error(
                'student_not_accessible',
                __( 'Student is not accessible', 'students' ),
                array( 'status' => 403 )
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
