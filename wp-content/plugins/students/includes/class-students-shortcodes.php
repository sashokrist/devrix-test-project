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
    }
    
    /**
     * Students list shortcode
     * 
     * Usage: [students_list count="5"]
     * Usage: [students_list id="123"] - Show specific student
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
        ), $atts, 'students_list' );
        
        // Sanitize attributes
        $count = absint( $atts['count'] );
        $orderby = sanitize_text_field( $atts['orderby'] );
        $order = strtoupper( sanitize_text_field( $atts['order'] ) );
        $status = sanitize_text_field( $atts['status'] );
        $additional_class = sanitize_text_field( $atts['class'] );
        $student_id = absint( $atts['id'] );
        
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
                <div class="students-grid">
                    <?php
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
                            printf(
                                esc_html__( 'Showing %1$d of %2$d students', 'students' ),
                                $count,
                                $students_query->found_posts
                            );
                            ?>
                        </p>
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
     * Enqueue shortcode styles
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
        }
    }
}
