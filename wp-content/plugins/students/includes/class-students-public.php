<?php
/**
 * The public-facing functionality of the plugin
 *
 * @package Students
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Students Public Class
 *
 * @since 1.0.0
 */
class Students_Public {

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
        add_shortcode( 'students_list', array( $this, 'students_list_shortcode' ) );
        add_shortcode( 'student_profile', array( $this, 'student_profile_shortcode' ) );
        add_shortcode( 'student_meta_fields', array( $this, 'student_meta_fields_shortcode' ) );
        add_filter( 'single_template', array( $this, 'load_single_student_template' ) );
        add_filter( 'archive_template', array( $this, 'load_archive_student_template' ) );
        add_filter( 'taxonomy_template', array( $this, 'load_taxonomy_templates' ) );
        
        // Filter main query to only show active students on archive page
        add_action( 'pre_get_posts', array( $this, 'filter_student_archive_query' ) );
    }

    /**
     * Enqueue scripts
     */
    public function enqueue_scripts() {
        if ( is_post_type_archive( 'student' ) || is_singular( 'student' ) ) {
            wp_enqueue_script(
                'students-public',
                STUDENTS_PLUGIN_URL . 'assets/js/public.js',
                array( 'jquery' ),
                STUDENTS_VERSION,
                true
            );

            wp_localize_script( 'students-public', 'students_ajax', array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'students_nonce' ),
            ) );
        }
    }

    /**
     * Enqueue styles
     */
    public function enqueue_styles() {
        if ( is_post_type_archive( 'student' ) || is_singular( 'student' ) ) {
            wp_enqueue_style(
                'students-public',
                STUDENTS_PLUGIN_URL . 'assets/css/public.css',
                array(),
                STUDENTS_VERSION
            );
        }
    }

    /**
     * Students list shortcode
     *
     * @param array $atts Shortcode attributes
     * @return string
     */
    public function students_list_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'posts_per_page' => Students_Plugin::get_option( 'students_per_page', 10 ),
            'course' => '',
            'grade_level' => '',
            'orderby' => 'title',
            'order' => 'ASC',
        ), $atts );

        $args = array(
            'post_type' => 'student',
            'posts_per_page' => intval( $atts['posts_per_page'] ),
            'orderby' => $atts['orderby'],
            'order' => $atts['order'],
            'post_status' => 'publish',
        );

        // Add taxonomy filters
        if ( ! empty( $atts['course'] ) ) {
            $args['tax_query'][] = array(
                'taxonomy' => 'course',
                'field' => 'slug',
                'terms' => explode( ',', $atts['course'] ),
            );
        }

        if ( ! empty( $atts['grade_level'] ) ) {
            $args['tax_query'][] = array(
                'taxonomy' => 'grade_level',
                'field' => 'slug',
                'terms' => explode( ',', $atts['grade_level'] ),
            );
        }

        $students = new WP_Query( $args );

        ob_start();
        ?>
        <div class="students-list">
            <?php if ( $students->have_posts() ) : ?>
                <div class="students-grid">
                    <?php while ( $students->have_posts() ) : $students->the_post(); ?>
                        <div class="student-card">
                            <?php if ( has_post_thumbnail() ) : ?>
                                <div class="student-photo">
                                    <?php the_post_thumbnail( 'medium' ); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="student-info">
                                <h3 class="student-name">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                
                                <?php
                                // Get student meta data safely using the sanitizer
                                $student_meta = Students_Sanitizer::get_student_meta_safely( get_the_ID() );
                                
                                if ( ! empty( $student_meta['_student_id'] ) ) :
                                ?>
                                    <p class="student-id"><?php echo esc_html( __( 'ID:', 'students' ) . ' ' . $student_meta['_student_id'] ); ?></p>
                                <?php endif; ?>

                                <?php if ( ! empty( $student_meta['_student_class_grade'] ) ) : ?>
                                    <p class="student-class-grade"><?php echo esc_html( __( 'Class/Grade:', 'students' ) . ' ' . $student_meta['_student_class_grade'] ); ?></p>
                                <?php endif; ?>

                                <p class="student-status">
                                    <strong><?php esc_html_e( 'Status:', 'students' ); ?></strong>
                                    <?php if ( '1' === $student_meta['_student_is_active'] ) : ?>
                                        <span style="color: green; font-weight: bold;"><?php esc_html_e( 'Active', 'students' ); ?></span>
                                    <?php else : ?>
                                        <span style="color: red; font-weight: bold;"><?php esc_html_e( 'Inactive', 'students' ); ?></span>
                                    <?php endif; ?>
                                </p>

                                <?php
                                $courses = get_the_terms( get_the_ID(), 'course' );
                                if ( $courses && ! is_wp_error( $courses ) ) :
                                ?>
                                    <p class="student-courses">
                                        <?php echo esc_html( __( 'Courses:', 'students' ) . ' ' . implode( ', ', wp_list_pluck( $courses, 'name' ) ) ); ?>
                                    </p>
                                <?php endif; ?>

                                <?php
                                $grade_levels = get_the_terms( get_the_ID(), 'grade_level' );
                                if ( $grade_levels && ! is_wp_error( $grade_levels ) ) :
                                ?>
                                    <p class="student-grade">
                                        <?php echo esc_html( __( 'Grade:', 'students' ) . ' ' . implode( ', ', wp_list_pluck( $grade_levels, 'name' ) ) ); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                
                <?php
                // Pagination
                $big = 999999999;
                echo paginate_links( array(
                    'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                    'format' => '?paged=%#%',
                    'current' => max( 1, get_query_var( 'paged' ) ),
                    'total' => $students->max_num_pages,
                ) );
                ?>
            <?php else : ?>
                <p><?php _e( 'No students found.', 'students' ); ?></p>
            <?php endif; ?>
        </div>
        <?php
        wp_reset_postdata();
        
        return ob_get_clean();
    }

    /**
     * Student profile shortcode
     *
     * @param array $atts Shortcode attributes
     * @return string
     */
    public function student_profile_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'id' => get_the_ID(),
        ), $atts );

        $post = get_post( $atts['id'] );
        
        if ( ! $post || 'student' !== $post->post_type ) {
            return '<p>' . __( 'Student not found.', 'students' ) . '</p>';
        }

        ob_start();
        ?>
        <div class="student-profile">
            <div class="student-header">
                <?php if ( has_post_thumbnail( $post->ID ) ) : ?>
                    <div class="student-photo">
                        <?php echo get_the_post_thumbnail( $post->ID, 'large' ); ?>
                    </div>
                <?php endif; ?>
                
                <div class="student-basic-info">
                    <h2><?php echo esc_html( $post->post_title ); ?></h2>
                    
                    <?php
                    $student_id = get_post_meta( $post->ID, '_student_id', true );
                    if ( $student_id ) :
                    ?>
                        <p class="student-id"><?php echo esc_html( __( 'Student ID:', 'students' ) . ' ' . $student_id ); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="student-details">
                <?php
                // Get all student meta data safely using the sanitizer
                $student_meta = Students_Sanitizer::get_student_meta_safely( $post->ID );
                ?>

                <?php if ( ! empty( $student_meta['_student_id'] ) && Students_Sanitizer::should_display_field( 'student_id' ) ) : ?>
                    <div class="student-field">
                        <strong><?php esc_html_e( 'Student ID:', 'students' ); ?></strong>
                        <?php echo $student_meta['_student_id']; ?>
                    </div>
                <?php endif; ?>

                <?php if ( ! empty( $student_meta['_student_email'] ) && Students_Sanitizer::should_display_field( 'email' ) ) : ?>
                    <div class="student-field">
                        <strong><?php esc_html_e( 'Email:', 'students' ); ?></strong>
                        <a href="mailto:<?php echo esc_attr( $student_meta['_student_email'] ); ?>"><?php echo $student_meta['_student_email']; ?></a>
                    </div>
                <?php endif; ?>

                <?php if ( ! empty( $student_meta['_student_phone'] ) && Students_Sanitizer::should_display_field( 'phone' ) ) : ?>
                    <div class="student-field">
                        <strong><?php esc_html_e( 'Phone:', 'students' ); ?></strong>
                        <a href="tel:<?php echo esc_attr( $student_meta['_student_phone'] ); ?>"><?php echo $student_meta['_student_phone']; ?></a>
                    </div>
                <?php endif; ?>

                <?php if ( ! empty( $student_meta['_student_dob'] ) && Students_Sanitizer::should_display_field( 'dob' ) ) : ?>
                    <div class="student-field">
                        <strong><?php esc_html_e( 'Date of Birth:', 'students' ); ?></strong>
                        <?php 
                        $dob_timestamp = strtotime( $student_meta['_student_dob'] );
                        if ( $dob_timestamp ) {
                            echo esc_html( date_i18n( get_option( 'date_format' ), $dob_timestamp ) );
                        } else {
                            echo $student_meta['_student_dob'];
                        }
                        ?>
                    </div>
                <?php endif; ?>

                <?php if ( ! empty( $student_meta['_student_address'] ) && Students_Sanitizer::should_display_field( 'address' ) ) : ?>
                    <div class="student-field">
                        <strong><?php esc_html_e( 'Address:', 'students' ); ?></strong>
                        <?php echo $student_meta['_student_address']; ?>
                    </div>
                <?php endif; ?>

                <?php 
                $show_location = ( Students_Sanitizer::should_display_field( 'country' ) || Students_Sanitizer::should_display_field( 'city' ) );
                $has_location_data = ( ! empty( $student_meta['_student_country'] ) || ! empty( $student_meta['_student_city'] ) );
                
                if ( $has_location_data && $show_location ) : 
                ?>
                    <div class="student-field">
                        <strong><?php esc_html_e( 'Location:', 'students' ); ?></strong>
                        <?php 
                        $location = array();
                        if ( ! empty( $student_meta['_student_city'] ) && Students_Sanitizer::should_display_field( 'city' ) ) {
                            $location[] = $student_meta['_student_city'];
                        }
                        if ( ! empty( $student_meta['_student_country'] ) && Students_Sanitizer::should_display_field( 'country' ) ) {
                            $location[] = $student_meta['_student_country'];
                        }
                        echo implode( ', ', $location );
                        ?>
                    </div>
                <?php endif; ?>

                <?php if ( ! empty( $student_meta['_student_class_grade'] ) && Students_Sanitizer::should_display_field( 'class_grade' ) ) : ?>
                    <div class="student-field">
                        <strong><?php esc_html_e( 'Class/Grade:', 'students' ); ?></strong>
                        <?php echo $student_meta['_student_class_grade']; ?>
                    </div>
                <?php endif; ?>

                <?php if ( Students_Sanitizer::should_display_field( 'status' ) ) : ?>
                    <div class="student-field">
                        <strong><?php esc_html_e( 'Status:', 'students' ); ?></strong>
                        <?php if ( '1' === $student_meta['_student_is_active'] ) : ?>
                            <span style="color: green; font-weight: bold;"><?php esc_html_e( 'Active', 'students' ); ?></span>
                        <?php else : ?>
                            <span style="color: red; font-weight: bold;"><?php esc_html_e( 'Inactive', 'students' ); ?></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="student-content">
                <?php echo apply_filters( 'the_content', $post->post_content ); ?>
            </div>
        </div>
        <?php
        
        return ob_get_clean();
    }

    /**
     * Load single student template
     *
     * @param string $template Template path
     * @return string
     */
    public function load_single_student_template( $template ) {
        if ( is_singular( 'student' ) ) {
            // First check if theme has a custom template
            $theme_template = locate_template( array( 'single-student.php' ) );
            if ( $theme_template ) {
                return $theme_template;
            }
            
            // Then check for plugin template
            $plugin_template = STUDENTS_PLUGIN_DIR . 'templates/single-student.php';
            if ( file_exists( $plugin_template ) ) {
                return $plugin_template;
            }
        }
        return $template;
    }

    /**
     * Load archive student template
     *
     * @param string $template Template path
     * @return string
     */
    public function load_archive_student_template( $template ) {
        if ( is_post_type_archive( 'student' ) ) {
            // First check if theme has a custom template
            $theme_template = locate_template( array( 'archive-student.php' ) );
            if ( $theme_template ) {
                return $theme_template;
            }
            
            // Then check for plugin template
            $plugin_template = STUDENTS_PLUGIN_DIR . 'templates/archive-student.php';
            if ( file_exists( $plugin_template ) ) {
                return $plugin_template;
            }
        }
        return $template;
    }

    /**
     * Load taxonomy templates
     *
     * @param string $template Template path
     * @return string
     */
    public function load_taxonomy_templates( $template ) {
        if ( is_tax( 'course' ) ) {
            $theme_template = locate_template( array( 'taxonomy-course.php' ) );
            if ( $theme_template ) {
                return $theme_template;
            }
            
            $plugin_template = STUDENTS_PLUGIN_DIR . 'templates/taxonomy-course.php';
            if ( file_exists( $plugin_template ) ) {
                return $plugin_template;
            }
        }
        
        if ( is_tax( 'grade_level' ) ) {
            $theme_template = locate_template( array( 'taxonomy-grade_level.php' ) );
            if ( $theme_template ) {
                return $theme_template;
            }
            
            $plugin_template = STUDENTS_PLUGIN_DIR . 'templates/taxonomy-grade_level.php';
            if ( file_exists( $plugin_template ) ) {
                return $plugin_template;
            }
        }
        
        return $template;
    }

    /**
     * Filter main query to only show active students on archive page
     *
     * @param WP_Query $query The main query object.
     */
    public function filter_student_archive_query( $query ) {
        if ( ! is_admin() && $query->is_main_query() && is_post_type_archive( 'student' ) ) {
            $meta_query = $query->get( 'meta_query' );
            if ( ! is_array( $meta_query ) ) {
                $meta_query = array();
            }
            $meta_query[] = array(
                'key' => '_student_is_active',
                'value' => '1',
                'compare' => '=',
            );
            $query->set( 'meta_query', $meta_query );
        }
    }

    /**
     * Student meta fields shortcode
     *
     * @return string
     */
    public function student_meta_fields_shortcode() {
        // Get student meta data safely using the sanitizer
        $student_meta = Students_Sanitizer::get_student_meta_safely( get_the_ID() );
        
        ob_start();
        ?>
        <div class="student-meta">
            <?php if ( ! empty( $student_meta['_student_class_grade'] ) && Students_Sanitizer::should_display_field( 'class_grade' ) ) : ?>
                <p><strong><?php esc_html_e( 'Class/Grade:', 'students' ); ?></strong> <?php echo $student_meta['_student_class_grade']; ?></p>
            <?php endif; ?>
            
            <?php if ( Students_Sanitizer::should_display_field( 'status' ) ) : ?>
                <p><strong><?php esc_html_e( 'Status:', 'students' ); ?></strong> 
                    <?php if ( '1' === $student_meta['_student_is_active'] ) : ?>
                        <span style="color: green; font-weight: bold;"><?php esc_html_e( 'Active', 'students' ); ?></span>
                    <?php else : ?>
                        <span style="color: red; font-weight: bold;"><?php esc_html_e( 'Inactive', 'students' ); ?></span>
                    <?php endif; ?>
                </p>
            <?php endif; ?>
        </div>
        <?php
        
        return ob_get_clean();
    }
}
