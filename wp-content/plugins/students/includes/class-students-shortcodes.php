<?php
/**
 * Students Shortcodes Class
 * 
 * Handles shortcode functionality for displaying students
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Students_Shortcodes {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_shortcode( 'students_list', array( $this, 'students_list_shortcode' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_shortcode_styles' ) );
        add_action( 'wp_ajax_load_more_students', array( $this, 'ajax_load_more_students' ) );
        add_action( 'wp_ajax_nopriv_load_more_students', array( $this, 'ajax_load_more_students' ) );
    }
    
    /**
     * Students list shortcode
     * 
     * Usage: [students_list count="5"]
     * Usage: [students_list id="123"] - Show specific student
     * Usage: [students_list count="4" max="12"] - Show 4 at a time, max 12 total
     * 
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public function students_list_shortcode( $atts ) {
        // Parse attributes
        $atts = shortcode_atts( array(
            'count' => 4, // Default number of students
            'orderby' => 'date', // Order by date, title, etc.
            'order' => 'DESC', // ASC or DESC
            'status' => 'active', // active, inactive, or all
            'class' => '', // Additional CSS classes
            'id' => '', // Specific student ID
            'max' => 0, // Maximum number of students to show (0 = no limit)
        ), $atts, 'students_list' );
        
        // Sanitize attributes
        $count = absint( $atts['count'] );
        $orderby = sanitize_text_field( $atts['orderby'] );
        $order = strtoupper( sanitize_text_field( $atts['order'] ) );
        $status = sanitize_text_field( $atts['status'] );
        $additional_class = sanitize_text_field( $atts['class'] );
        $student_id = absint( $atts['id'] );
        $max_students = absint( $atts['max'] );
        
        // Validate inputs
        if ( $count < 1 ) {
            $count = 4;
        }
        
        if ( ! in_array( $order, array( 'ASC', 'DESC' ) ) ) {
            $order = 'DESC';
        }
        
        if ( ! in_array( $status, array( 'active', 'inactive', 'all' ) ) ) {
            $status = 'active';
        }
        
        // Build query arguments
        $args = array(
            'post_type' => 'student',
            'post_status' => 'publish',
            'posts_per_page' => $count,
            'orderby' => $orderby,
            'order' => $order,
            'meta_query' => array(),
        );
        
        // If specific student ID is provided, show only that student
        if ( ! empty( $student_id ) ) {
            $args['p'] = $student_id; // WordPress parameter for specific post ID
            $args['posts_per_page'] = 1; // Force to show only one
        }
        
        // If max students is set and greater than count, adjust posts_per_page
        if ( $max_students > 0 && $max_students < $count ) {
            $args['posts_per_page'] = $max_students;
        }
        
        // Add status filter (only if not showing specific student)
        if ( empty( $student_id ) ) {
            if ( 'active' === $status ) {
                $args['meta_query'][] = array(
                    'key' => '_student_is_active',
                    'value' => '1',
                    'compare' => '='
                );
            } elseif ( 'inactive' === $status ) {
                $args['meta_query'][] = array(
                    'key' => '_student_is_active',
                    'value' => '0',
                    'compare' => '='
                );
            }
        }
        
        // If no meta query, remove the array
        if ( empty( $args['meta_query'] ) ) {
            unset( $args['meta_query'] );
        }
        
        // Run the query
        $students_query = new WP_Query( $args );
        
        // Start output buffering
        ob_start();
        
        if ( $students_query->have_posts() ) {
            ?>
            <div class="students-list-shortcode <?php echo esc_attr( $additional_class ); ?>">
                <div class="students-grid" data-total="<?php echo esc_attr( $students_query->found_posts ); ?>" data-shown="<?php echo esc_attr( $students_query->post_count ); ?>" data-count="<?php echo esc_attr( $count ); ?>" data-max="<?php echo esc_attr( $max_students ); ?>" data-orderby="<?php echo esc_attr( $orderby ); ?>" data-order="<?php echo esc_attr( $order ); ?>" data-status="<?php echo esc_attr( $status ); ?>">
                    <?php
                    while ( $students_query->have_posts() ) {
                        $students_query->the_post();
                        
                        // Get student data
                        $current_student_id = get_the_ID();
                        $student_name = get_the_title();
                        $student_picture = get_the_post_thumbnail_url( $current_student_id, 'medium' );
                        $student_class_grade = get_post_meta( $current_student_id, '_student_class_grade', true );
                        $student_is_active = get_post_meta( $current_student_id, '_student_is_active', true );
                        
                        // Default image if no featured image
                        if ( ! $student_picture ) {
                            $student_picture = 'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200"><rect width="200" height="200" fill="#f8f9fa"/><circle cx="100" cy="80" r="30" fill="#dee2e6"/><path d="M50 180 Q100 140 150 180" stroke="#dee2e6" stroke-width="3" fill="none"/><text x="100" y="195" text-anchor="middle" font-family="Arial" font-size="12" fill="#6c757d">No Image</text></svg>' );
                        }
                        
                        // Student status class
                        $status_class = ( '1' === $student_is_active ) ? 'active' : 'inactive';
                        ?>
                        
                        <div class="student-card <?php echo esc_attr( $status_class ); ?>">
                            <div class="student-image">
                                <a href="<?php echo esc_url( get_permalink() ); ?>">
                                    <img src="<?php echo esc_url( $student_picture ); ?>" 
                                         alt="<?php echo esc_attr( $student_name ); ?>" 
                                         loading="lazy" />
                                </a>
                            </div>
                            
                            <div class="student-info">
                                <h3 class="student-name">
                                    <a href="<?php echo esc_url( get_permalink() ); ?>">
                                        <?php echo esc_html( $student_name ); ?>
                                    </a>
                                </h3>
                                
                                <?php if ( ! empty( $student_class_grade ) ) : ?>
                                    <div class="student-class-grade">
                                        <span class="label"><?php esc_html_e( 'Class/Grade:', 'students' ); ?></span>
                                        <span class="value"><?php echo esc_html( $student_class_grade ); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="student-status">
                                    <span class="status-indicator <?php echo esc_attr( $status_class ); ?>">
                                        <?php echo ( '1' === $student_is_active ) ? esc_html__( 'Active', 'students' ) : esc_html__( 'Inactive', 'students' ); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <?php
                    }
                    ?>
                </div>
                
                <?php if ( empty( $student_id ) && $students_query->found_posts > $count ) : ?>
                    <div class="students-list-footer">
                        <p class="students-count">
                            <?php 
                            $displayed_count = min( $count, $students_query->found_posts );
                            printf(
                                esc_html__( 'Showing %1$d of %2$d students', 'students' ),
                                $displayed_count,
                                $students_query->found_posts
                            );
                            ?>
                        </p>
                        
                        <?php 
                        // Show button if there are more students to load
                        $can_load_more = false;
                        if ( $max_students === 0 ) {
                            // No max limit, show if there are more students
                            $can_load_more = $students_query->found_posts > $count;
                        } else {
                            // Has max limit, show if we haven't reached the max
                            $can_load_more = ( $count < $max_students ) && ( $count < $students_query->found_posts );
                        }
                        
                        if ( $can_load_more ) : ?>
                            <button class="load-more-students" data-page="1" data-nonce="<?php echo wp_create_nonce( 'load_more_students' ); ?>">
                                <?php esc_html_e( 'Show More', 'students' ); ?>
                            </button>
                        <?php endif; ?>
                        
                        <a href="<?php echo esc_url( get_post_type_archive_link( 'student' ) ); ?>" class="view-all-students">
                            <?php esc_html_e( 'View All Students', 'students' ); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            <?php
        } else {
            ?>
            <div class="students-list-shortcode no-students">
                <p><?php esc_html_e( 'No students found.', 'students' ); ?></p>
            </div>
            <?php
        }
        
        // Reset post data
        wp_reset_postdata();
        
        // Return the buffered content
        return ob_get_clean();
    }
    
    /**
     * AJAX handler for loading more students
     */
    public function ajax_load_more_students() {
        // Verify nonce
        if ( ! wp_verify_nonce( $_POST['nonce'], 'load_more_students' ) ) {
            wp_send_json_error( 'Invalid nonce' );
        }
        
        // Get parameters
        $page = absint( $_POST['page'] );
        $count = absint( $_POST['count'] );
        $max_students = absint( $_POST['max'] );
        $orderby = sanitize_text_field( $_POST['orderby'] );
        $order = sanitize_text_field( $_POST['order'] );
        $status = sanitize_text_field( $_POST['status'] );
        
        // Build query arguments
        $args = array(
            'post_type' => 'student',
            'post_status' => 'publish',
            'posts_per_page' => $count,
            'paged' => $page,
            'orderby' => $orderby,
            'order' => $order,
            'meta_query' => array(),
        );
        
        // Add status filter
        if ( 'active' === $status ) {
            $args['meta_query'][] = array(
                'key' => '_student_is_active',
                'value' => '1',
                'compare' => '='
            );
        } elseif ( 'inactive' === $status ) {
            $args['meta_query'][] = array(
                'key' => '_student_is_active',
                'value' => '0',
                'compare' => '='
            );
        }
        
        // If no meta query, remove the array
        if ( empty( $args['meta_query'] ) ) {
            unset( $args['meta_query'] );
        }
        
        // Run the query
        $students_query = new WP_Query( $args );
        
        $html = '';
        $has_more = false;
        
        if ( $students_query->have_posts() ) {
            while ( $students_query->have_posts() ) {
                $students_query->the_post();
                
                // Get student data
                $student_id = get_the_ID();
                $student_name = get_the_title();
                $student_picture = get_the_post_thumbnail_url( $student_id, 'medium' );
                $student_class_grade = get_post_meta( $student_id, '_student_class_grade', true );
                $student_is_active = get_post_meta( $student_id, '_student_is_active', true );
                
                // Default image if no featured image
                if ( ! $student_picture ) {
                    $student_picture = 'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200"><rect width="200" height="200" fill="#f8f9fa"/><circle cx="100" cy="80" r="30" fill="#dee2e6"/><path d="M50 180 Q100 140 150 180" stroke="#dee2e6" stroke-width="3" fill="none"/><text x="100" y="195" text-anchor="middle" font-family="Arial" font-size="12" fill="#6c757d">No Image</text></svg>' );
                }
                
                // Student status class
                $status_class = ( '1' === $student_is_active ) ? 'active' : 'inactive';
                
                $html .= '<div class="student-card ' . esc_attr( $status_class ) . '">';
                $html .= '<div class="student-image">';
                $html .= '<a href="' . esc_url( get_permalink() ) . '">';
                $html .= '<img src="' . esc_url( $student_picture ) . '" alt="' . esc_attr( $student_name ) . '" loading="lazy" />';
                $html .= '</a>';
                $html .= '</div>';
                
                $html .= '<div class="student-info">';
                $html .= '<h3 class="student-name">';
                $html .= '<a href="' . esc_url( get_permalink() ) . '">';
                $html .= esc_html( $student_name );
                $html .= '</a>';
                $html .= '</h3>';
                
                if ( ! empty( $student_class_grade ) ) {
                    $html .= '<div class="student-class-grade">';
                    $html .= '<span class="label">' . esc_html__( 'Class/Grade:', 'students' ) . '</span>';
                    $html .= '<span class="value">' . esc_html( $student_class_grade ) . '</span>';
                    $html .= '</div>';
                }
                
                $html .= '<div class="student-status">';
                $html .= '<span class="status-indicator ' . esc_attr( $status_class ) . '">';
                $html .= ( '1' === $student_is_active ) ? esc_html__( 'Active', 'students' ) : esc_html__( 'Inactive', 'students' );
                $html .= '</span>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
            }
            
            wp_reset_postdata();
            
            // Check if there are more students to load
            $total_shown = ( $page * $count );
            $has_more = ( $total_shown < $students_query->found_posts ) && ( $max_students === 0 || $total_shown < $max_students );
        }
        
        wp_send_json_success( array(
            'html' => $html,
            'has_more' => $has_more,
            'total_found' => $students_query->found_posts,
            'total_shown' => $total_shown,
        ) );
    }
    
    /**
     * Enqueue shortcode styles and scripts
     */
    public function enqueue_shortcode_styles() {
        // Only enqueue if shortcode is used on the page
        global $post;
        
        if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'students_list' ) ) {
            wp_enqueue_style(
                'students-shortcode',
                STUDENTS_PLUGIN_URL . 'assets/css/shortcode.css',
                array(),
                STUDENTS_VERSION
            );
            
            wp_enqueue_script(
                'students-shortcode',
                STUDENTS_PLUGIN_URL . 'assets/js/shortcode.js',
                array( 'jquery' ),
                STUDENTS_VERSION,
                true
            );
            
            wp_localize_script(
                'students-shortcode',
                'students_ajax',
                array(
                    'ajax_url' => admin_url( 'admin-ajax.php' ),
                    'nonce' => wp_create_nonce( 'load_more_students' ),
                    'loading_text' => __( 'Loading...', 'students' ),
                    'no_more_text' => __( 'No more students', 'students' ),
                )
            );
        }
    }
}
